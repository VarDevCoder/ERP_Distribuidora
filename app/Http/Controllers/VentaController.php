<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Exception;

class VentaController extends Controller
{
    public function __construct(private VentaService $service) {}

    public function registrarFactura(Request $request, Presupuesto $presupuesto)
    {
        $request->validate([
            'factura_numero' => 'required|string|unique:presupuestos,factura_numero'
        ]);

        try {
            $this->service->registrarFactura($presupuesto, $request->factura_numero);
            return back()->with('success', 'Factura registrada correctamente');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function registrarContrafactura(Request $request, Presupuesto $presupuesto)
    {
        $request->validate([
            'contrafactura_numero' => 'required|string|unique:presupuestos,contrafactura_numero'
        ]);

        try {
            $this->service->registrarContrafactura($presupuesto, $request->contrafactura_numero);
            return back()->with('success', 'Contrafactura registrada e inventario actualizado');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
