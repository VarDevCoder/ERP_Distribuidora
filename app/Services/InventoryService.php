<?php

namespace App\Services;

use App\Models\{Presupuesto, Producto, MovimientoInventario, NotaRemision};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryService
{
    /**
     * Crea nota de remisión y aplica movimientos (ENTRADA o SALIDA).
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

            foreach ($p->productos as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item->producto_id);
                $stockAnterior = $producto->stock_actual;

                if ($tipo === 'SALIDA') {
                    if ($producto->stock_actual < $item->cantidad) {
                        throw new Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock_actual}, Solicitado: {$item->cantidad}");
                    }
                    $producto->stock_actual -= $item->cantidad;
                } else { // ENTRADA
                    $producto->stock_actual += $item->cantidad;
                }

                $producto->save();

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => $tipo,
                    'cantidad' => $item->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $producto->stock_actual,
                    'referencia_tipo' => $p->tipo, // 'VENTA' | 'COMPRA'
                    'referencia_id' => $p->id,
                    'nota_remision_id' => $nota->id,
                    'factura_numero' => $p->factura_numero,
                    'contrafactura_numero' => $p->contrafactura_numero,
                    'remision_numero' => $p->remision_numero,
                    'usuario_id' => auth()->id(),
                    'observaciones' => "Movimiento {$tipo} generado por {$p->tipo} #{$p->id}",
                ]);
            }

            Log::info("Inventario aplicado ({$tipo}) para presupuesto {$p->id} con nota {$nota->numero}");
            return $nota;
        });
    }
}
