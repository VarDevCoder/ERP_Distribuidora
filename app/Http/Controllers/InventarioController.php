<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    // Ver todos los productos con su stock actual
    public function index(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $query = Producto::query();

        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('pro_nombre', 'ILIKE', "%$buscar%")
                  ->orWhere('pro_codigo', 'ILIKE', "%$buscar%")
                  ->orWhere('pro_categoria', 'ILIKE', "%$buscar%");
            });
        }

        if ($request->has('stock_bajo') && $request->stock_bajo == '1') {
            $query->whereColumn('pro_stock', '<=', 'pro_stock_minimo');
        }

        $productos = $query->orderBy('pro_nombre')->paginate(15);

        // Estadísticas
        $totalProductos = Producto::count();
        $stockBajo = Producto::whereColumn('pro_stock', '<=', 'pro_stock_minimo')->count();
        $sinStock = Producto::where('pro_stock', 0)->count();
        $valorInventario = Producto::sum(DB::raw('pro_stock * pro_precio_compra'));

        return view('inventario.index', compact('productos', 'totalProductos', 'stockBajo', 'sinStock', 'valorInventario'));
    }

    // Ver kardex de un producto
    public function kardex($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $producto = Producto::findOrFail($id);
        $movimientos = MovimientoInventario::with('usuario')
            ->where('pro_id', $id)
            ->orderBy('mov_fecha', 'desc')
            ->paginate(20);

        return view('inventario.kardex', compact('producto', 'movimientos'));
    }

    // Formulario de ajuste de inventario
    public function ajusteForm()
    {
        if (!session('usuario')) return redirect()->route('login');

        $productos = Producto::orderBy('pro_nombre')->get();
        return view('inventario.ajuste', compact('productos'));
    }

    // Guardar ajuste de inventario
    public function ajusteStore(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $request->validate([
            'pro_id' => 'required|exists:producto,pro_id',
            'mov_tipo' => 'required|in:ENTRADA,SALIDA',
            'mov_cantidad' => 'required|integer|min:1',
            'mov_observaciones' => 'required|min:10'
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::findOrFail($request->pro_id);
            $stockAnterior = $producto->pro_stock;
            $cantidad = $request->mov_cantidad;

            if ($request->mov_tipo === 'ENTRADA') {
                $producto->pro_stock += $cantidad;
            } else {
                if ($producto->pro_stock < $cantidad) {
                    throw new \Exception('Stock insuficiente para realizar el ajuste');
                }
                $producto->pro_stock -= $cantidad;
            }

            $producto->save();

            // Registrar movimiento
            MovimientoInventario::create([
                'pro_id' => $producto->pro_id,
                'usu_id' => session('usuario')->usu_id,
                'mov_tipo' => $request->mov_tipo,
                'mov_motivo' => 'AJUSTE_INVENTARIO',
                'mov_cantidad' => $cantidad,
                'mov_stock_anterior' => $stockAnterior,
                'mov_stock_nuevo' => $producto->pro_stock,
                'mov_observaciones' => $request->mov_observaciones,
                'mov_fecha' => now()
            ]);

            DB::commit();
            return redirect()->route('inventario.index')
                ->with('success', 'Ajuste de inventario realizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al realizar ajuste: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Ver movimientos recientes
    public function movimientos()
    {
        if (!session('usuario')) return redirect()->route('login');

        $movimientos = MovimientoInventario::with('producto', 'usuario')
            ->orderBy('mov_fecha', 'desc')
            ->paginate(20);

        return view('inventario.movimientos', compact('movimientos'));
    }
}
