<?php

namespace App\Services;

use App\Models\{Presupuesto, CantidadRealDocumento};
use Illuminate\Support\Facades\{DB, Log};
use Exception;

class VentaService
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * Paso 3: registrar FACTURA con cantidades reales
     *
     * @param Presupuesto $p
     * @param string $nro Número de factura
     * @param array $cantidadesReales [producto_id => ['cantidad' => X, 'motivo' => '...']]
     * @param string|null $archivo Archivo adjunto (opcional)
     * @return Presupuesto
     */
    public function registrarFactura(Presupuesto $p, string $nro, array $cantidadesReales = [], ?string $archivo = null): Presupuesto
    {
        if ($p->tipo !== 'VENTA') throw new Exception('Solo VENTA admite factura');
        if ($p->estado !== 'APROBADO') throw new Exception('Venta no aprobada');
        if (!empty($p->factura_numero)) throw new Exception('Factura ya registrada');

        return DB::transaction(function () use ($p, $nro, $cantidadesReales, $archivo) {
            // Actualizar presupuesto con el número de factura
            $p->update([
                'factura_numero' => $nro,
                'factura_fecha'  => now(),
                // 'factura_archivo' => $archivo, // opcional si definiste la columna
            ]);

            // Guardar cantidades reales si se proporcionaron
            if (!empty($cantidadesReales)) {
                foreach ($p->productos as $item) {
                    $datosReales = $cantidadesReales[$item->producto_id] ?? null;

                    if ($datosReales) {
                        CantidadRealDocumento::create([
                            'presupuesto_id' => $p->id,
                            'producto_id' => $item->producto_id,
                            'tipo_documento' => 'FACTURA',
                            'cantidad_presupuestada' => $item->cantidad,
                            'cantidad_real' => $datosReales['cantidad'] ?? $item->cantidad,
                            'motivo_diferencia' => $datosReales['motivo'] ?? null,
                            'usuario_id' => auth()->id(),
                        ]);
                    } else {
                        // Si no se especificó, usar la cantidad presupuestada
                        CantidadRealDocumento::create([
                            'presupuesto_id' => $p->id,
                            'producto_id' => $item->producto_id,
                            'tipo_documento' => 'FACTURA',
                            'cantidad_presupuestada' => $item->cantidad,
                            'cantidad_real' => $item->cantidad,
                            'usuario_id' => auth()->id(),
                        ]);
                    }
                }
            }

            Log::info("Factura registrada {$nro} en venta {$p->id}");
            return $p->refresh();
        });
    }

    // Paso 4: registrar CONTRAFACTURA (dispara SALIDA y resta stock)
    public function registrarContrafactura(Presupuesto $p, string $nro, ?string $archivo = null): Presupuesto
    {
        if ($p->tipo !== 'VENTA') throw new Exception('Solo VENTA admite contrafactura');
        if ($p->estado !== 'APROBADO') throw new Exception('Venta no aprobada');
        if (empty($p->factura_numero)) throw new Exception('Falta FACTURA');
        if (!empty($p->contrafactura_numero)) throw new Exception('Contrafactura ya registrada');

        $p->update([
            'contrafactura_numero' => $nro,
            'contrafactura_fecha'  => now(),
        ]);

        // Triple verificación
        if ($p->tipo !== 'VENTA') throw new Exception('Verificación tipo falló');
        if (empty($p->factura_numero)) throw new Exception('Verificación factura falló');
        if (empty($p->contrafactura_numero)) throw new Exception('Verificación contrafactura falló');

        // SALIDA (resta) usando cantidades reales si existen
        $this->inventory->aplicarMovimiento($p->refresh(), 'SALIDA');

        $p->update(['venta_validada' => true, 'estado' => 'VALIDADO_VENTA']);
        return $p->refresh();
    }
}
