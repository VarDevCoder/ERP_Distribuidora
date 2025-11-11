<?php

namespace App\Services;

use App\Models\Presupuesto;
use Exception;

class CompraService
{
    public function __construct(private InventoryService $inventory) {}

    // Paso 3 en compra: registrar REMISIÓN (no afecta stock todavía)
    public function registrarRemision(Presupuesto $p, string $nro): Presupuesto
    {
        if ($p->tipo !== 'COMPRA') throw new Exception('Solo COMPRA admite remisión');
        if ($p->estado !== 'APROBADO') throw new Exception('Compra no aprobada');
        if (!empty($p->remision_numero)) throw new Exception('Remisión ya registrada');

        $p->update([
            'remision_numero' => $nro,
            'remision_fecha'  => now(),
        ]);

        return $p->refresh();
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

        // ENTRADA (suma)
        $this->inventory->aplicarMovimiento($p->refresh(), 'ENTRADA');

        $p->update(['compra_validada' => true, 'estado' => 'VALIDADO_COMPRA']);
        return $p->refresh();
    }
}
