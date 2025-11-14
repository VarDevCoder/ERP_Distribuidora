<?php

namespace App\Http\Controllers;

use App\Models\Presupuesto;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Exception;

class VentaController extends Controller
{
    public function __construct(private VentaService $service) {}

    /**
     * Muestra formulario para registrar factura con cantidades reales
     */
    public function mostrarFormularioFactura(Presupuesto $presupuesto)
    {
        if ($presupuesto->tipo !== 'VENTA') {
            return back()->with('error', 'Solo las ventas admiten factura');
        }

        if ($presupuesto->estado !== 'APROBADO') {
            return back()->with('error', 'La venta debe estar aprobada');
        }

        if (!empty($presupuesto->factura_numero)) {
            return back()->with('error', 'Ya se registró la factura');
        }

        $presupuesto->load('productos.producto');

        return view('ventas.registrar_factura', compact('presupuesto'));
    }

    /**
     * Registra la factura con cantidades reales
     */
    public function registrarFactura(Request $request, Presupuesto $presupuesto)
    {
        $request->validate([
            'factura_numero' => 'required|string|unique:presupuestos,factura_numero',
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

            $this->service->registrarFactura($presupuesto, $request->factura_numero, $cantidadesReales);
            return redirect()
                ->route('presupuestos.show', $presupuesto)
                ->with('success', 'Factura registrada correctamente');
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
