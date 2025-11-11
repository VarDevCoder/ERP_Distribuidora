<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\MovimientoInventario;

class InventarioController extends Controller
{
    public function index()
    {
        $productos = Producto::withCount('movimientos')
            ->orderBy('nombre')
            ->paginate(20);

        return view('inventario.index', compact('productos'));
    }

    public function kardex(Producto $producto)
    {
        $movimientos = MovimientoInventario::where('producto_id', $producto->id)
            ->with('notaRemision')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('inventario.kardex', compact('producto', 'movimientos'));
    }

    public function movimientos()
    {
        $movimientos = MovimientoInventario::with('producto', 'notaRemision')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return view('inventario.movimientos', compact('movimientos'));
    }
}
