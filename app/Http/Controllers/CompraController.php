<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Services\CompraService;
use Illuminate\Http\Request;
use Exception;

class CompraController extends Controller
{
    public function __construct(private CompraService $service) {}

    /**
     * Muestra formulario para registrar remisión con cantidades reales
     */
    public function mostrarFormularioRemision(Presupuesto $presupuesto)
    {
        if ($presupuesto->tipo !== 'COMPRA') {
            return back()->with('error', 'Solo las compras admiten remisión');
        }

        if ($presupuesto->estado !== 'APROBADO') {
            return back()->with('error', 'La compra debe estar aprobada');
        }

        if (!empty($presupuesto->remision_numero)) {
            return back()->with('error', 'Ya se registró la remisión');
        }

        $presupuesto->load('productos.producto');

        return view('compras.registrar_remision', compact('presupuesto'));
    }

    /**
     * Registra la remisión con cantidades reales
     */
    public function registrarRemision(Request $request, Presupuesto $presupuesto)
    {
        $request->validate([
            'remision_numero' => 'required|string|unique:presupuestos,remision_numero',
            'cantidades' => 'nullable|array',
            'cantidades.*.producto_id' => 'required|exists:productos,id',
            'cantidades.*.cantidad' => 'required|numeric|min:0',
            'cantidades.*.motivo' => 'nullable|string|max:500',
        ]);

        try {
            // Transformar cantidades al formato esperado por el servicio
            $cantidadesReales = [];
            if ($request->has('cantidades')) {
                foreach ($request->cantidades as $item) {
                    $cantidadesReales[$item['producto_id']] = [
                        'cantidad' => $item['cantidad'],
                        'motivo' => $item['motivo'] ?? null,
                    ];
                }
            }

            $this->service->registrarRemision($presupuesto, $request->remision_numero, $cantidadesReales);
            return redirect()
                ->route('presupuestos.show', $presupuesto)
                ->with('success', 'Remisión registrada correctamente');
        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
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
