<?php

namespace App\Services;

use App\Models\{Presupuesto, CantidadRealDocumento};
use Illuminate\Support\Facades\DB;
use Exception;

class CompraService
{
    public function __construct(private InventoryService $inventory) {}

    /**
     * Paso 3 en compra: registrar REMISIÓN con cantidades reales
     *
     * @param Presupuesto $p
     * @param string $nro Número de remisión del proveedor
     * @param array $cantidadesReales [producto_id => ['cantidad' => X, 'motivo' => '...']]
     * @return Presupuesto
     */
    public function registrarRemision(Presupuesto $p, string $nro, array $cantidadesReales = []): Presupuesto
    {
        if ($p->tipo !== 'COMPRA') throw new Exception('Solo COMPRA admite remisión');
        if ($p->estado !== 'APROBADO') throw new Exception('Compra no aprobada');
        if (!empty($p->remision_numero)) throw new Exception('Remisión ya registrada');

        return DB::transaction(function () use ($p, $nro, $cantidadesReales) {
            // Actualizar presupuesto con el número de remisión
            $p->update([
                'remision_numero' => $nro,
                'remision_fecha'  => now(),
            ]);

            // Guardar cantidades reales si se proporcionaron
            if (!empty($cantidadesReales)) {
                foreach ($p->productos as $item) {
                    $datosReales = $cantidadesReales[$item->producto_id] ?? null;

                    if ($datosReales) {
                        CantidadRealDocumento::create([
                            'presupuesto_id' => $p->id,
                            'producto_id' => $item->producto_id,
                            'tipo_documento' => 'REMISION',
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
                            'tipo_documento' => 'REMISION',
                            'cantidad_presupuestada' => $item->cantidad,
                            'cantidad_real' => $item->cantidad,
                            'usuario_id' => auth()->id(),
                        ]);
                    }
                }
            }

            return $p->refresh();
        });
    }

    // Paso 4: registrar CONTRAFACTURA (dispara ENTRADA y suma stock)
    public function registrarContrafactura(Presupuesto $p, string $nro): Presupuesto
    {
        if ($p->tipo !== 'COMPRA') throw new Exception('Solo COMPRA admite contrafactura');
        if ($p->estado !== 'APROBADO') throw new Exception('Compra no aprobada');
        if (empty($p->remision_numero)) throw new Exception('Falta REMISIÓN');
        if (!empty($p->contrafactura_numero)) throw new Exception('Contrafactura ya registrada');

        $p->update([
            'contrafactura_numero' => $nro,
            'contrafactura_fecha'  => now(),
        ]);

        // Triple verificación
        if ($p->tipo !== 'COMPRA') throw new Exception('Verificación tipo falló');
        if (empty($p->remision_numero)) throw new Exception('Verificación remisión falló');
        if (empty($p->contrafactura_numero)) throw new Exception('Verificación contrafactura falló');

        // ENTRADA (suma) usando cantidades reales si existen
        $this->inventory->aplicarMovimiento($p->refresh(), 'ENTRADA');

        $p->update(['compra_validada' => true, 'estado' => 'VALIDADO_COMPRA']);
        return $p->refresh();
    }
}
