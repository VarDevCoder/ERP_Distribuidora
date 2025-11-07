<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
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

        $productos = $query->orderBy('pro_nombre')->paginate(10);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        if (!session('usuario')) return redirect()->route('login');
        return view('productos.create');
    }

    public function store(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $request->validate([
            'pro_codigo' => 'required|unique:producto,pro_codigo|max:50',
            'pro_nombre' => 'required|max:100',
            'pro_categoria' => 'required|max:50',
            'pro_precio_compra' => 'required|numeric|min:0',
            'pro_precio_venta' => 'required|numeric|min:0',
            'pro_stock' => 'required|integer|min:0',
            'pro_stock_minimo' => 'required|integer|min:0',
            'pro_unidad_medida' => 'required'
        ]);

        Producto::create($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function edit($id)
    {
        if (!session('usuario')) return redirect()->route('login');
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $producto = Producto::findOrFail($id);

        $request->validate([
            'pro_codigo' => 'required|max:50|unique:producto,pro_codigo,' . $id . ',pro_id',
            'pro_nombre' => 'required|max:100',
            'pro_categoria' => 'required|max:50',
            'pro_precio_compra' => 'required|numeric|min:0',
            'pro_precio_venta' => 'required|numeric|min:0',
            'pro_stock' => 'required|integer|min:0',
            'pro_stock_minimo' => 'required|integer|min:0',
            'pro_unidad_medida' => 'required'
        ]);

        $producto->update($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }
}
