<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\ProveedorProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProveedorProductoController extends Controller
{
    /**
     * Obtener el proveedor autenticado
     */
    private function getProveedorAutenticado()
    {
        return Proveedor::where('user_id', Auth::id())->first();
    }

    /**
     * Mostrar listado de productos del proveedor
     */
    public function index(Request $request)
    {
        $proveedor = $this->getProveedorAutenticado();

        if (!$proveedor) {
            return redirect()->route('home')->with('error', 'No tiene un perfil de proveedor asociado.');
        }

        $query = ProveedorProducto::where('proveedor_id', $proveedor->id)
            ->with(['producto.categoria']);

        // Filtros
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_proveedor', 'like', "%{$buscar}%")
                  ->orWhere('nombre_proveedor', 'like', "%{$buscar}%")
                  ->orWhereHas('producto', function ($pq) use ($buscar) {
                      $pq->where('codigo', 'like', "%{$buscar}%")
                         ->orWhere('nombre', 'like', "%{$buscar}%");
                  });
            });
        }

        if ($request->filled('disponible')) {
            $query->where('disponible', $request->disponible === '1');
        }

        if ($request->filled('categoria_id')) {
            $query->whereHas('producto', function ($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        $productos = $query->orderBy('created_at', 'desc')
            ->paginate(config('ankor.pagination.per_page', 15));

        $categorias = \App\Models\Categoria::orderBy('nombre')->get();

        return view('proveedor-productos.index', compact('productos', 'categorias'));
    }

    /**
     * Mostrar formulario para agregar producto
     */
    public function create()
    {
        $proveedor = $this->getProveedorAutenticado();

        if (!$proveedor) {
            return redirect()->route('home')->with('error', 'No tiene un perfil de proveedor asociado.');
        }

        // Obtener productos que aún no están en el catálogo del proveedor
        $productosDisponibles = Producto::active()
            ->whereNotIn('id', function ($query) use ($proveedor) {
                $query->select('producto_id')
                    ->from('proveedor_productos')
                    ->where('proveedor_id', $proveedor->id);
            })
            ->with('categoria')
            ->orderBy('nombre')
            ->get()
            ->groupBy(fn($p) => $p->categoria?->nombre ?? 'Sin Categoría');

        return view('proveedor-productos.create', compact('productosDisponibles'));
    }

    /**
     * Guardar nuevo producto
     */
    public function store(Request $request)
    {
        $proveedor = $this->getProveedorAutenticado();

        if (!$proveedor) {
            return redirect()->route('home')->with('error', 'No tiene un perfil de proveedor asociado.');
        }

        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'codigo_proveedor' => 'nullable|string|max:255',
            'nombre_proveedor' => 'nullable|string|max:255',
            'precio' => 'required|integer|min:0',
            'disponible' => 'boolean',
            'tiempo_entrega_dias' => 'nullable|integer|min:1',
            'notas' => 'nullable|string',
        ]);

        try {
            ProveedorProducto::create([
                'proveedor_id' => $proveedor->id,
                'producto_id' => $request->producto_id,
                'codigo_proveedor' => $request->codigo_proveedor,
                'nombre_proveedor' => $request->nombre_proveedor,
                'precio' => $request->precio,
                'disponible' => $request->has('disponible'),
                'tiempo_entrega_dias' => $request->tiempo_entrega_dias,
                'notas' => $request->notas,
            ]);

            return redirect()
                ->route('proveedor-productos.index')
                ->with('success', 'Producto agregado a su catálogo exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear producto de proveedor', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al agregar el producto. Intente nuevamente.');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(ProveedorProducto $proveedorProducto)
    {
        $proveedor = $this->getProveedorAutenticado();

        if (!$proveedor || $proveedorProducto->proveedor_id !== $proveedor->id) {
            return redirect()->route('proveedor-productos.index')
                ->with('error', 'No tiene permiso para editar este producto.');
        }

        $producto = $proveedorProducto;
        $producto->load('producto.categoria');

        return view('proveedor-productos.edit', compact('producto'));
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, ProveedorProducto $proveedorProducto)
    {
        $proveedor = $this->getProveedorAutenticado();

        if (!$proveedor || $proveedorProducto->proveedor_id !== $proveedor->id) {
            return redirect()->route('proveedor-productos.index')
                ->with('error', 'No tiene permiso para editar este producto.');
        }

        $request->validate([
            'codigo_proveedor' => 'nullable|string|max:255',
            'nombre_proveedor' => 'nullable|string|max:255',
            'precio' => 'required|integer|min:0',
            'disponible' => 'boolean',
            'tiempo_entrega_dias' => 'nullable|integer|min:1',
            'notas' => 'nullable|string',
        ]);

        try {
            $proveedorProducto->update([
                'codigo_proveedor' => $request->codigo_proveedor,
                'nombre_proveedor' => $request->nombre_proveedor,
                'precio' => $request->precio,
                'disponible' => $request->has('disponible'),
                'tiempo_entrega_dias' => $request->tiempo_entrega_dias,
                'notas' => $request->notas,
            ]);

            return redirect()
                ->route('proveedor-productos.index')
                ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar producto de proveedor', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al actualizar el producto. Intente nuevamente.');
        }
    }

    /**
     * Eliminar producto del catálogo
     */
    public function destroy(ProveedorProducto $proveedorProducto)
    {
        $proveedor = $this->getProveedorAutenticado();

        if (!$proveedor || $proveedorProducto->proveedor_id !== $proveedor->id) {
            return redirect()->route('proveedor-productos.index')
                ->with('error', 'No tiene permiso para eliminar este producto.');
        }

        try {
            $proveedorProducto->delete();

            return redirect()
                ->route('proveedor-productos.index')
                ->with('success', 'Producto eliminado de su catálogo');

        } catch (\Exception $e) {
            Log::error('Error al eliminar producto de proveedor', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al eliminar el producto. Intente nuevamente.');
        }
    }

    /**
     * Toggle disponibilidad
     */
    public function toggleDisponible(ProveedorProducto $proveedorProducto)
    {
        $proveedor = $this->getProveedorAutenticado();

        if (!$proveedor || $proveedorProducto->proveedor_id !== $proveedor->id) {
            return redirect()->route('proveedor-productos.index')
                ->with('error', 'No tiene permiso para modificar este producto.');
        }

        $proveedorProducto->update([
            'disponible' => !$proveedorProducto->disponible
        ]);

        return redirect()
            ->route('proveedor-productos.index')
            ->with('success', 'Disponibilidad actualizada');
    }
}
