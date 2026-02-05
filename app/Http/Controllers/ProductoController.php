<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Producto;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria')->orderBy('nombre');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $productos = $query->paginate(config('ankor.pagination.per_page', 15));
        $categorias = Categoria::activas()->ordenadas()->get();
        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $unidades = config('ankor.unidades_medida');
        $margen = config('ankor.margen.porcentaje', 25);
        $categorias = Categoria::activas()->ordenadas()->get();
        return view('productos.create', compact('unidades', 'margen', 'categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'nullable|exists:categorias,id',
            'codigo' => 'nullable|string|max:50|unique:productos',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|integer|min:0',
            'precio_venta' => 'required|integer|min:0',
            'stock_actual' => 'nullable|numeric|min:0',
            'stock_minimo' => 'nullable|numeric|min:0',
            'unidad_medida' => 'required|string|max:20',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');
        $validated['categoria_id'] = $request->categoria_id ?: null;

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function show(Producto $producto)
    {
        $producto->load('movimientos');
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $unidades = config('ankor.unidades_medida');
        $margen = config('ankor.margen.porcentaje', 25);
        $categorias = Categoria::activas()->ordenadas()->get();
        return view('productos.edit', compact('producto', 'unidades', 'margen', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'categoria_id' => 'nullable|exists:categorias,id',
            'codigo' => 'nullable|string|max:50|unique:productos,codigo,' . $producto->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|integer|min:0',
            'precio_venta' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|numeric|min:0',
            'unidad_medida' => 'required|string|max:20',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');
        $validated['categoria_id'] = $request->categoria_id ?: null;

        $producto->update($validated);

        return redirect()->route('productos.show', $producto)
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Producto $producto)
    {
        try {
            $producto->delete();
            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el producto porque tiene registros relacionados');
        }
    }
}
