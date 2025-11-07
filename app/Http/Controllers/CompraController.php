<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        if (!session('usuario')) return redirect()->route('login');

        $compras = Compra::with('proveedor', 'usuario')
            ->orderBy('com_fecha', 'desc')
            ->paginate(10);

        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        if (!session('usuario')) return redirect()->route('login');

        $proveedores = Proveedor::where('prov_estado', 'ACTIVO')->orderBy('prov_nombre')->get();
        $productos = Producto::orderBy('pro_nombre')->get();

        // Generar número de compra
        $ultimaCompra = Compra::latest('com_id')->first();
        $numero = $ultimaCompra ? (int)substr($ultimaCompra->com_numero, -4) + 1 : 1;
        $numeroCompra = 'COM-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        return view('compras.create', compact('proveedores', 'productos', 'numeroCompra'));
    }

    public function store(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $request->validate([
            'prov_id' => 'required|exists:proveedor,prov_id',
            'com_fecha' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.pro_id' => 'required|exists:producto,pro_id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $detalles = [];

            foreach ($request->productos as $item) {
                $producto = Producto::find($item['pro_id']);
                $cantidad = $item['cantidad'];
                $precio = $item['precio'];
                $subtotalItem = $cantidad * $precio;

                $subtotal += $subtotalItem;
                $detalles[] = [
                    'pro_id' => $producto->pro_id,
                    'det_com_cantidad' => $cantidad,
                    'det_com_precio_unitario' => $precio,
                    'det_com_subtotal' => $subtotalItem,
                    'producto' => $producto
                ];
            }

            $descuento = $request->com_descuento ?? 0;
            $total = $subtotal - $descuento;

            // Crear compra
            $compra = Compra::create([
                'com_numero' => $request->com_numero,
                'prov_id' => $request->prov_id,
                'usu_id' => session('usuario')->usu_id,
                'com_fecha' => $request->com_fecha,
                'com_factura' => $request->com_factura,
                'com_subtotal' => $subtotal,
                'com_descuento' => $descuento,
                'com_total' => $total,
                'com_estado' => 'COMPLETADA',
                'com_observaciones' => $request->com_observaciones
            ]);

            foreach ($detalles as $detalle) {
                // Crear detalle
                $compra->detalles()->create([
                    'pro_id' => $detalle['pro_id'],
                    'det_com_cantidad' => $detalle['det_com_cantidad'],
                    'det_com_precio_unitario' => $detalle['det_com_precio_unitario'],
                    'det_com_subtotal' => $detalle['det_com_subtotal']
                ]);

                $producto = $detalle['producto'];
                $stockAnterior = $producto->pro_stock;

                // Actualizar stock
                $producto->pro_stock += $detalle['det_com_cantidad'];

                // Actualizar precio de compra (promedio)
                $producto->pro_precio_compra = $detalle['det_com_precio_unitario'];
                $producto->save();

                // Registrar movimiento de inventario
                MovimientoInventario::create([
                    'pro_id' => $producto->pro_id,
                    'usu_id' => session('usuario')->usu_id,
                    'mov_tipo' => 'ENTRADA',
                    'mov_motivo' => 'COMPRA',
                    'mov_cantidad' => $detalle['det_com_cantidad'],
                    'mov_stock_anterior' => $stockAnterior,
                    'mov_stock_nuevo' => $producto->pro_stock,
                    'mov_costo' => $detalle['det_com_precio_unitario'],
                    'mov_referencia' => $compra->com_numero,
                    'mov_referencia_id' => $compra->com_id,
                    'mov_fecha' => now()
                ]);
            }

            DB::commit();
            return redirect()->route('compras.index')
                ->with('success', 'Compra registrada exitosamente. Stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $compra = Compra::with('proveedor', 'usuario', 'detalles.producto')->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function anular($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        DB::beginTransaction();
        try {
            $compra = Compra::with('detalles.producto')->findOrFail($id);

            if ($compra->com_estado === 'ANULADA') {
                return back()->with('error', 'Esta compra ya está anulada');
            }

            // Reversar stock
            foreach ($compra->detalles as $detalle) {
                $producto = $detalle->producto;
                $stockAnterior = $producto->pro_stock;

                $producto->pro_stock -= $detalle->det_com_cantidad;

                if ($producto->pro_stock < 0) {
                    throw new \Exception("No se puede anular: Stock insuficiente para {$producto->pro_nombre}");
                }

                $producto->save();

                // Registrar movimiento de anulación
                MovimientoInventario::create([
                    'pro_id' => $producto->pro_id,
                    'usu_id' => session('usuario')->usu_id,
                    'mov_tipo' => 'SALIDA',
                    'mov_motivo' => 'AJUSTE_INVENTARIO',
                    'mov_cantidad' => $detalle->det_com_cantidad,
                    'mov_stock_anterior' => $stockAnterior,
                    'mov_stock_nuevo' => $producto->pro_stock,
                    'mov_referencia' => 'ANULACION-' . $compra->com_numero,
                    'mov_referencia_id' => $compra->com_id,
                    'mov_observaciones' => 'Anulación de compra',
                    'mov_fecha' => now()
                ]);
            }

            $compra->com_estado = 'ANULADA';
            $compra->save();

            DB::commit();
            return redirect()->route('compras.index')
                ->with('success', 'Compra anulada exitosamente. Stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular compra: ' . $e->getMessage());
        }
    }
}
