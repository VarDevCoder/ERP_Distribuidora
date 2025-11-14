<?php

namespace App\Services;

use App\Models\{Presupuesto, Producto, MovimientoInventario, NotaRemision, CantidadRealDocumento};
use Illuminate\Support\Facades\{DB, Log};
use Exception;

class InventoryService
{
    /**
     * Crea nota de remisión y aplica movimientos (ENTRADA o SALIDA).
     * Usa cantidades reales si fueron registradas, si no usa las del presupuesto.
     * - ENTRADA (COMPRA): suma stock
     * - SALIDA (VENTA): resta stock (verifica stock suficiente)
     */
    public function aplicarMovimiento(Presupuesto $p, string $tipo /* ENTRADA|SALIDA */): NotaRemision
    {
        if (!in_array($tipo, ['ENTRADA','SALIDA'])) {
            throw new Exception('Tipo de movimiento inválido');
        }

        return DB::transaction(function () use ($p, $tipo) {
            // Generar número simple; Claude puede reemplazar por secuencia
            $numero = sprintf('NR-%s-%06d', $tipo === 'ENTRADA' ? 'COMPRA' : 'VENTA', $p->id);

            $nota = NotaRemision::create([
                'presupuesto_id' => $p->id,
                'tipo' => $tipo,
                'numero' => $numero,
                'fecha' => now(),
                'factura_numero' => $p->factura_numero,
                'contrafactura_numero' => $p->contrafactura_numero,
                'remision_numero' => $p->remision_numero,
                'estado' => 'APLICADO',
            ]);

            // Obtener cantidades reales si existen
            $tipoDoc = $tipo === 'ENTRADA' ? 'REMISION' : 'FACTURA';
            $cantidadesReales = CantidadRealDocumento::where('presupuesto_id', $p->id)
                ->where('tipo_documento', $tipoDoc)
                ->get()
                ->keyBy('producto_id');

            foreach ($p->productos as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item->producto_id);
                $stockAnterior = $producto->stock_actual;

                // Determinar cantidad a mover (real si existe, si no presupuestada)
                $cantidadReal = $cantidadesReales->has($item->producto_id)
                    ? $cantidadesReales[$item->producto_id]->cantidad_real
                    : $item->cantidad;

                $cantidadPresupuestada = $item->cantidad;
                $diferencia = $cantidadReal - $cantidadPresupuestada;
                $motivoDiferencia = $cantidadesReales->has($item->producto_id)
                    ? $cantidadesReales[$item->producto_id]->motivo_diferencia
                    : null;

                // Aplicar movimiento
                if ($tipo === 'SALIDA') {
                    if ($producto->stock_actual < $cantidadReal) {
                        throw new Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock_actual}, Solicitado: {$cantidadReal}");
                    }
                    $producto->stock_actual -= $cantidadReal;
                } else { // ENTRADA
                    $producto->stock_actual += $cantidadReal;
                }

                $producto->save();

                // Registrar movimiento con trazabilidad completa
                $movimiento = MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => $tipo,
                    'cantidad' => $cantidadReal,
                    'cantidad_presupuestada' => $cantidadPresupuestada,
                    'diferencia' => abs($diferencia) > 0.001 ? $diferencia : null,
                    'motivo_diferencia' => $motivoDiferencia,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $producto->stock_actual,
                    'referencia_tipo' => $p->tipo, // 'VENTA' | 'COMPRA'
                    'referencia_id' => $p->id,
                    'nota_remision_id' => $nota->id,
                    'factura_numero' => $p->factura_numero,
                    'contrafactura_numero' => $p->contrafactura_numero,
                    'remision_numero' => $p->remision_numero,
                    'usuario_id' => auth()->id(),
                    'observaciones' => $this->generarObservacion($tipo, $p, $diferencia),
                ]);

                // Log detallado
                $diffText = abs($diferencia) > 0.001 ? " (Diferencia: {$diferencia})" : "";
                Log::info("Inventario: {$tipo} de {$cantidadReal} unidades de {$producto->nombre}. Stock: {$stockAnterior} → {$producto->stock_actual}{$diffText}");
            }

            Log::info("Inventario aplicado ({$tipo}) para presupuesto {$p->id} con nota {$nota->numero}");
            return $nota;
        });
    }

    /**
     * Genera observación descriptiva para el movimiento
     */
    private function generarObservacion(string $tipo, Presupuesto $p, float $diferencia): string
    {
        $obs = "Movimiento {$tipo} generado por {$p->tipo} #{$p->id}";

        if (abs($diferencia) > 0.001) {
            $diffText = $diferencia > 0 ? "sobrante de +{$diferencia}" : "faltante de {$diferencia}";
            $obs .= " ({$diffText} vs presupuesto)";
        }

        return $obs;
    }
}
