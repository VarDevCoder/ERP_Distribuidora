<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\ComparacionPreciosService;
use Illuminate\Http\Request;

class AnalisisProveedoresController extends Controller
{
    protected ComparacionPreciosService $comparacionService;

    public function __construct(ComparacionPreciosService $comparacionService)
    {
        $this->comparacionService = $comparacionService;
    }

    /**
     * Dashboard principal de análisis
     */
    public function index()
    {
        $resumen = $this->comparacionService->obtenerResumenGeneral();
        $scorecards = $this->comparacionService->obtenerScorecardsProveedores();
        $matrizComparacion = $this->comparacionService->obtenerMatrizComparacion()->take(20);

        return view('analisis-proveedores.index', compact('resumen', 'scorecards', 'matrizComparacion'));
    }

    /**
     * Ver comparación de un producto específico
     */
    public function producto(Producto $producto)
    {
        $comparacion = $this->comparacionService->compararPreciosProducto($producto->id);

        return view('analisis-proveedores.producto', compact('producto', 'comparacion'));
    }

    /**
     * Ver todos los productos con comparaciones
     */
    public function productos(Request $request)
    {
        $query = Producto::active()
            ->with(['proveedorProductos' => function ($q) {
                $q->where('disponible', true)->with('proveedor')->orderBy('precio');
            }, 'categoria']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('con_proveedores')) {
            if ($request->con_proveedores === '1') {
                $query->whereHas('proveedorProductos');
            } else {
                $query->whereDoesntHave('proveedorProductos');
            }
        }

        $productos = $query->orderBy('nombre')->paginate(20);

        // Agregar estadísticas a cada producto
        $productos->getCollection()->transform(function ($producto) {
            $precios = $producto->proveedorProductos->pluck('precio');
            $producto->precio_minimo = $precios->min();
            $producto->precio_maximo = $precios->max();
            $producto->precio_promedio = $precios->count() > 0 ? (int) round($precios->avg()) : null;
            $producto->cantidad_proveedores = $precios->count();
            return $producto;
        });

        $categorias = \App\Models\Categoria::orderBy('nombre')->get();

        return view('analisis-proveedores.productos', compact('productos', 'categorias'));
    }

    /**
     * Ver scorecard de un proveedor específico
     */
    public function proveedor(Proveedor $proveedor)
    {
        $proveedor->load(['proveedorProductos' => function ($q) {
            $q->with('producto.categoria')->orderBy('precio');
        }]);

        // Calcular estadísticas
        $productos = $proveedor->proveedorProductos;
        $stats = [
            'total_productos' => $productos->count(),
            'productos_disponibles' => $productos->where('disponible', true)->count(),
            'precio_promedio' => $productos->avg('precio'),
            'tiempo_entrega_promedio' => $productos->avg('tiempo_entrega_dias'),
        ];

        // Productos donde tiene el mejor precio
        $mejoresPrecios = $this->comparacionService->obtenerMejoresPreciosPorProveedor($proveedor->id);

        return view('analisis-proveedores.proveedor', compact('proveedor', 'stats', 'mejoresPrecios'));
    }

    /**
     * Actualizar precios de compra desde promedios
     */
    public function actualizarPrecios(Request $request)
    {
        try {
            if ($request->filled('producto_id')) {
                // Actualizar un solo producto
                $resultado = $this->comparacionService->actualizarPrecioCompraDesdePromedio($request->producto_id);

                if ($resultado) {
                    return back()->with('success', "Precio actualizado: {$resultado['producto']->nombre} - Compra: " . number_format($resultado['precio_compra_nuevo'], 0, ',', '.') . " Gs.");
                }

                return back()->with('error', 'No hay proveedores con precios para este producto.');
            }

            // Actualizar todos
            $resultados = $this->comparacionService->actualizarTodosLosPrecios();

            return back()->with('success', count($resultados) . ' productos actualizados con precios promedio de proveedores.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar precios: ' . $e->getMessage());
        }
    }
}
