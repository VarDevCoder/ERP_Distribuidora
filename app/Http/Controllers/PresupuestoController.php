<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presupuesto;
use App\Models\DetallePresupuesto;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class PresupuestoController extends Controller
{
    public function index()
    {
        if (!session('usuario')) return redirect()->route('login');

        $presupuestos = Presupuesto::with('cliente', 'usuario')
            ->orderBy('pre_fecha', 'desc')
            ->paginate(10);

        return view('presupuestos.index', compact('presupuestos'));
    }

    public function create()
    {
        if (!session('usuario')) return redirect()->route('login');

        $clientes = Cliente::orderBy('cli_nombre')->get();
        $productos = Producto::where('pro_stock', '>', 0)->orderBy('pro_nombre')->get();

        // Generar número de presupuesto
        $ultimoPresupuesto = Presupuesto::latest('pre_id')->first();
        $numero = $ultimoPresupuesto ? (int)substr($ultimoPresupuesto->pre_numero, -4) + 1 : 1;
        $numeroPresupuesto = 'PRE-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        return view('presupuestos.create', compact('clientes', 'productos', 'numeroPresupuesto'));
    }

    public function store(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $request->validate([
            'cli_id' => 'required|exists:cliente,cli_id',
            'pre_fecha' => 'required|date',
            'pre_fecha_vencimiento' => 'required|date|after:pre_fecha',
            'productos' => 'required|array|min:1',
            'productos.*.pro_id' => 'required|exists:producto,pro_id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $detalles = [];

            foreach ($request->productos as $item) {
                $producto = Producto::find($item['pro_id']);
                $cantidad = $item['cantidad'];
                $precio = $producto->pro_precio_venta;
                $subtotalItem = $cantidad * $precio;

                $subtotal += $subtotalItem;
                $detalles[] = [
                    'pro_id' => $producto->pro_id,
                    'det_pre_cantidad' => $cantidad,
                    'det_pre_precio_unitario' => $precio,
                    'det_pre_subtotal' => $subtotalItem
                ];
            }

            $descuento = $request->pre_descuento ?? 0;
            $total = $subtotal - $descuento;

            $presupuesto = Presupuesto::create([
                'pre_numero' => $request->pre_numero,
                'cli_id' => $request->cli_id,
                'usu_id' => session('usuario')->usu_id,
                'pre_fecha' => $request->pre_fecha,
                'pre_fecha_vencimiento' => $request->pre_fecha_vencimiento,
                'pre_subtotal' => $subtotal,
                'pre_descuento' => $descuento,
                'pre_total' => $total,
                'pre_estado' => 'PENDIENTE',
                'pre_observaciones' => $request->pre_observaciones
            ]);

            foreach ($detalles as $detalle) {
                $presupuesto->detalles()->create($detalle);
            }

            DB::commit();
            return redirect()->route('presupuestos.index')
                ->with('success', 'Presupuesto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear presupuesto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $presupuesto = Presupuesto::with('cliente', 'usuario', 'detalles.producto')->findOrFail($id);
        return view('presupuestos.show', compact('presupuesto'));
    }

    public function updateEstado(Request $request, $id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $presupuesto = Presupuesto::findOrFail($id);
        $presupuesto->pre_estado = $request->estado;
        $presupuesto->save();

        return redirect()->back()->with('success', 'Estado actualizado correctamente');
    }

    public function convertirVenta($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $presupuesto = Presupuesto::with('detalles')->findOrFail($id);

        if ($presupuesto->pre_estado !== 'APROBADO') {
            return back()->with('error', 'Solo se pueden convertir presupuestos aprobados');
        }

        return redirect()->route('ventas.create', ['presupuesto_id' => $id]);
    }
}
