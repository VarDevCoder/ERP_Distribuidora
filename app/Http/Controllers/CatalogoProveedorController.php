<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\ProveedorProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CatalogoProveedorController extends Controller
{
    /**
     * Listado de todos los productos de proveedores
     */
    public function index(Request $request)
    {
        $query = ProveedorProducto::with(['proveedor', 'producto.categoria']);

        // Filtro por proveedor
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        // Filtro por producto
        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->whereHas('producto', function ($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        // Filtro por disponibilidad
        if ($request->filled('disponible')) {
            $query->where('disponible', $request->disponible === '1');
        }

        // Búsqueda general
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_proveedor', 'like', "%{$buscar}%")
                  ->orWhere('nombre_proveedor', 'like', "%{$buscar}%")
                  ->orWhereHas('proveedor', function ($pq) use ($buscar) {
                      $pq->where('nombre', 'like', "%{$buscar}%");
                  })
                  ->orWhereHas('producto', function ($pq) use ($buscar) {
                      $pq->where('codigo', 'like', "%{$buscar}%")
                         ->orWhere('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        $catalogos = $query->orderBy('proveedor_id')
            ->orderBy('updated_at', 'desc')
            ->paginate(config('ankor.pagination.per_page', 20));

        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $productos = Producto::active()->orderBy('nombre')->get();

        return view('catalogo-proveedores.index', compact('catalogos', 'proveedores', 'categorias', 'productos'));
    }

    /**
     * Formulario para agregar producto a proveedor
     */
    public function create(Request $request)
    {
        $proveedores = Proveedor::where('activo', true)->orderBy('nombre')->get();

        // Si viene con proveedor preseleccionado
        $proveedorSeleccionado = null;
        $productosDisponibles = collect();

        if ($request->filled('proveedor_id')) {
            $proveedorSeleccionado = Proveedor::find($request->proveedor_id);

            if ($proveedorSeleccionado) {
                $productosDisponibles = Producto::active()
                    ->whereNotIn('id', function ($query) use ($proveedorSeleccionado) {
                        $query->select('producto_id')
                            ->from('proveedor_productos')
                            ->where('proveedor_id', $proveedorSeleccionado->id);
                    })
                    ->with('categoria')
                    ->orderBy('nombre')
                    ->get()
                    ->groupBy(fn($p) => $p->categoria?->nombre ?? 'Sin Categoría');
            }
        }

        return view('catalogo-proveedores.create', compact('proveedores', 'proveedorSeleccionado', 'productosDisponibles'));
    }

    /**
     * Guardar nuevo producto de proveedor
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'producto_id' => 'required|exists:productos,id',
            'codigo_proveedor' => 'nullable|string|max:255',
            'nombre_proveedor' => 'nullable|string|max:255',
            'precio' => 'required|integer|min:0',
            'disponible' => 'boolean',
            'tiempo_entrega_dias' => 'nullable|integer|min:1',
            'notas' => 'nullable|string',
        ]);

        // Verificar que no exista ya
        $existe = ProveedorProducto::where('proveedor_id', $request->proveedor_id)
            ->where('producto_id', $request->producto_id)
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Este producto ya está en el catálogo del proveedor.');
        }

        try {
            ProveedorProducto::create([
                'proveedor_id' => $request->proveedor_id,
                'producto_id' => $request->producto_id,
                'codigo_proveedor' => $request->codigo_proveedor,
                'nombre_proveedor' => $request->nombre_proveedor,
                'precio' => $request->precio,
                'disponible' => $request->has('disponible'),
                'tiempo_entrega_dias' => $request->tiempo_entrega_dias,
                'notas' => $request->notas,
            ]);

            return redirect()
                ->route('catalogo-proveedores.index', ['proveedor_id' => $request->proveedor_id])
                ->with('success', 'Producto agregado al catálogo del proveedor');

        } catch (\Exception $e) {
            Log::error('Error al crear producto de proveedor', ['exception' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Error al agregar el producto.');
        }
    }

    /**
     * Formulario de edición
     */
    public function edit(ProveedorProducto $catalogoProveedor)
    {
        $catalogoProveedor->load(['proveedor', 'producto.categoria']);

        return view('catalogo-proveedores.edit', compact('catalogoProveedor'));
    }

    /**
     * Actualizar producto de proveedor
     */
    public function update(Request $request, ProveedorProducto $catalogoProveedor)
    {
        $request->validate([
            'codigo_proveedor' => 'nullable|string|max:255',
            'nombre_proveedor' => 'nullable|string|max:255',
            'precio' => 'required|integer|min:0',
            'disponible' => 'boolean',
            'tiempo_entrega_dias' => 'nullable|integer|min:1',
            'notas' => 'nullable|string',
        ]);

        try {
            $catalogoProveedor->update([
                'codigo_proveedor' => $request->codigo_proveedor,
                'nombre_proveedor' => $request->nombre_proveedor,
                'precio' => $request->precio,
                'disponible' => $request->has('disponible'),
                'tiempo_entrega_dias' => $request->tiempo_entrega_dias,
                'notas' => $request->notas,
            ]);

            return redirect()
                ->route('catalogo-proveedores.index', ['proveedor_id' => $catalogoProveedor->proveedor_id])
                ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar producto de proveedor', ['exception' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Error al actualizar el producto.');
        }
    }

    /**
     * Eliminar producto del catálogo
     */
    public function destroy(ProveedorProducto $catalogoProveedor)
    {
        $proveedorId = $catalogoProveedor->proveedor_id;
        $proveedorNombre = $catalogoProveedor->proveedor->nombre;
        $productoNombre = $catalogoProveedor->producto->nombre;

        try {
            $catalogoProveedor->delete();

            return redirect()
                ->route('catalogo-proveedores.index', ['proveedor_id' => $proveedorId])
                ->with('success', "Producto '{$productoNombre}' eliminado del catálogo de {$proveedorNombre}");

        } catch (\Exception $e) {
            Log::error('Error al eliminar producto de proveedor', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al eliminar el producto.');
        }
    }

    /**
     * Toggle disponibilidad
     */
    public function toggleDisponible(ProveedorProducto $catalogoProveedor)
    {
        $catalogoProveedor->update([
            'disponible' => !$catalogoProveedor->disponible
        ]);

        $estado = $catalogoProveedor->disponible ? 'disponible' : 'no disponible';

        return redirect()
            ->back()
            ->with('success', "Producto marcado como {$estado}");
    }

    /**
     * Carga masiva de productos para un proveedor
     */
    public function cargarMasivo(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.precio' => 'required|integer|min:0',
        ]);

        $proveedor = Proveedor::findOrFail($request->proveedor_id);
        $agregados = 0;
        $omitidos = 0;

        foreach ($request->productos as $item) {
            $existe = ProveedorProducto::where('proveedor_id', $proveedor->id)
                ->where('producto_id', $item['producto_id'])
                ->exists();

            if ($existe) {
                $omitidos++;
                continue;
            }

            ProveedorProducto::create([
                'proveedor_id' => $proveedor->id,
                'producto_id' => $item['producto_id'],
                'precio' => $item['precio'],
                'disponible' => true,
                'tiempo_entrega_dias' => $item['tiempo_entrega_dias'] ?? null,
            ]);

            $agregados++;
        }

        return redirect()
            ->route('catalogo-proveedores.index', ['proveedor_id' => $proveedor->id])
            ->with('success', "Se agregaron {$agregados} productos. {$omitidos} ya existían.");
    }

    /**
     * API: Obtener productos disponibles para un proveedor
     */
    public function productosDisponibles(Proveedor $proveedor)
    {
        $productos = Producto::active()
            ->whereNotIn('id', function ($query) use ($proveedor) {
                $query->select('producto_id')
                    ->from('proveedor_productos')
                    ->where('proveedor_id', $proveedor->id);
            })
            ->with('categoria')
            ->orderBy('nombre')
            ->get()
            ->groupBy(fn($p) => $p->categoria?->nombre ?? 'Sin Categoría');

        return response()->json($productos);
    }
}
