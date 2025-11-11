<?php

namespace App\Services;

use App\Models\Presupuesto;
use Illuminate\Support\Facades\Log;
use Exception;

class VentaService
{
    public function __construct(private InventoryService $inventory) {}

    // Paso 3: registrar FACTURA (no afecta stock)
    public function registrarFactura(Presupuesto $p, string $nro, ?string $archivo = null): Presupuesto
    {
        if ($p->tipo !== 'VENTA') throw new Exception('Solo VENTA admite factura');
        if ($p->estado !== 'APROBADO') throw new Exception('Venta no aprobada');
        if (!empty($p->factura_numero)) throw new Exception('Factura ya registrada');

        $p->update([
            'factura_numero' => $nro,
            'factura_fecha'  => now(),
            // 'factura_archivo' => $archivo, // opcional si definiste la columna
        ]);

        Log::info("Factura registrada {$nro} en venta {$p->id}");
        return $p->refresh();
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

        // SALIDA (resta)
        $this->inventory->aplicarMovimiento($p->refresh(), 'SALIDA');

        $p->update(['venta_validada' => true, 'estado' => 'VALIDADO_VENTA']);
        return $p->refresh();
    }
}
