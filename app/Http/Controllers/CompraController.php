<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Services\CompraService;
use Illuminate\Http\Request;
use Exception;

class CompraController extends Controller
{
    public function __construct(private CompraService $service) {}

    public function registrarRemision(Request $request, Presupuesto $presupuesto)
    {
        $request->validate([
            'remision_numero' => 'required|string|unique:presupuestos,remision_numero'
        ]);

        try {
            $this->service->registrarRemision($presupuesto, $request->remision_numero);
            return back()->with('success', 'Remisión registrada correctamente');
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
