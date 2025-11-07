<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Presupuesto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        if (!session('usuario')) return redirect()->route('login');

        $ventas = Venta::with('cliente', 'usuario')
            ->orderBy('ven_fecha', 'desc')
            ->paginate(10);

        return view('ventas.index', compact('ventas'));
    }

    public function create(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $clientes = Cliente::orderBy('cli_nombre')->get();
        $productos = Producto::where('pro_stock', '>', 0)->orderBy('pro_nombre')->get();

        // Generar número de venta
        $ultimaVenta = Venta::latest('ven_id')->first();
        $numero = $ultimaVenta ? (int)substr($ultimaVenta->ven_numero, -4) + 1 : 1;
        $numeroVenta = 'VTA-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        $presupuesto = null;
        if ($request->has('presupuesto_id')) {
            $presupuesto = Presupuesto::with('detalles.producto')->findOrFail($request->presupuesto_id);
        }

        return view('ventas.create', compact('clientes', 'productos', 'numeroVenta', 'presupuesto'));
    }

    public function store(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $request->validate([
            'cli_id' => 'required|exists:cliente,cli_id',
            'ven_fecha' => 'required|date',
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

                // Verificar stock
                if ($producto->pro_stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para: {$producto->pro_nombre}");
                }

                $precio = $producto->pro_precio_venta;
                $subtotalItem = $cantidad * $precio;

                $subtotal += $subtotalItem;
                $detalles[] = [
                    'pro_id' => $producto->pro_id,
                    'det_cantidad' => $cantidad,
                    'det_precio_unitario' => $precio,
                    'det_subtotal' => $subtotalItem,
                    'producto' => $producto
                ];
            }

            $descuento = $request->ven_descuento ?? 0;
            $total = $subtotal - $descuento;

            $venta = Venta::create([
                'ven_numero' => $request->ven_numero,
                'cli_id' => $request->cli_id,
                'usu_id' => session('usuario')->usu_id,
                'ven_fecha' => $request->ven_fecha,
                'ven_subtotal' => $subtotal,
                'ven_descuento' => $descuento,
                'ven_total' => $total,
                'ven_estado' => 'COMPLETADA',
                'ven_observaciones' => $request->ven_observaciones
            ]);

            foreach ($detalles as $detalle) {
                // Crear detalle
                $venta->detalles()->create([
                    'pro_id' => $detalle['pro_id'],
                    'det_cantidad' => $detalle['det_cantidad'],
                    'det_precio_unitario' => $detalle['det_precio_unitario'],
                    'det_subtotal' => $detalle['det_subtotal']
                ]);

                // Actualizar stock
                $producto = $detalle['producto'];
                $stockAnterior = $producto->pro_stock;
                $producto->pro_stock -= $detalle['det_cantidad'];
                $producto->save();

                // Registrar movimiento de inventario
                MovimientoInventario::create([
                    'pro_id' => $producto->pro_id,
                    'usu_id' => session('usuario')->usu_id,
                    'mov_tipo' => 'SALIDA',
                    'mov_motivo' => 'VENTA',
                    'mov_cantidad' => $detalle['det_cantidad'],
                    'mov_stock_anterior' => $stockAnterior,
                    'mov_stock_nuevo' => $producto->pro_stock,
                    'mov_costo' => $detalle['det_precio_unitario'],
                    'mov_referencia' => $venta->ven_numero,
                    'mov_referencia_id' => $venta->ven_id,
                    'mov_fecha' => now()
                ]);
            }

            // Si viene de un presupuesto, marcarlo como convertido
            if ($request->has('presupuesto_id')) {
                $presupuesto = Presupuesto::find($request->presupuesto_id);
                $presupuesto->pre_estado = 'CONVERTIDO';
                $presupuesto->save();
            }

            DB::commit();
            return redirect()->route('ventas.index')
                ->with('success', 'Venta registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar venta: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $venta = Venta::with('cliente', 'usuario', 'detalles.producto')->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }
}
