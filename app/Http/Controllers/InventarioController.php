<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\MovimientoInventario;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::withCount('movimientos')
            ->orderBy('nombre');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        }

        $productos = $query->paginate(config('ankor.pagination.per_page', 15));

        return view('inventario.index', compact('productos'));
    }

    public function kardex(Producto $producto)
    {
        $movimientos = MovimientoInventario::where('producto_id', $producto->id)
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->paginate(config('ankor.pagination.per_page', 30));

        return view('inventario.kardex', compact('producto', 'movimientos'));
    }

    public function movimientos(Request $request)
    {
        $query = MovimientoInventario::with(['producto', 'usuario'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $movimientos = $query->paginate(config('ankor.pagination.per_page', 30));

        return view('inventario.movimientos', compact('movimientos'));
    }
}
