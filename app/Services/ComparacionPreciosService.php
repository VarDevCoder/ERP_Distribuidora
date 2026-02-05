<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\ProveedorProducto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComparacionPreciosService
{
    /**
     * Obtener comparación de precios para un producto específico
     */
    public function compararPreciosProducto(int $productoId): array
    {
        $producto = Producto::find($productoId);

        if (!$producto) {
            return [];
        }

        $preciosProveedores = ProveedorProducto::where('producto_id', $productoId)
            ->where('disponible', true)
            ->with('proveedor')
            ->orderBy('precio')
            ->get();

        if ($preciosProveedores->isEmpty()) {
            return [
                'producto' => $producto,
                'proveedores' => collect(),
                'precio_minimo' => null,
                'precio_maximo' => null,
                'precio_promedio' => null,
                'mejor_proveedor' => null,
                'cantidad_proveedores' => 0,
            ];
        }

        $precios = $preciosProveedores->pluck('precio');
        $precioPromedio = (int) round($precios->avg());

        return [
            'producto' => $producto,
            'proveedores' => $preciosProveedores,
            'precio_minimo' => $precios->min(),
            'precio_maximo' => $precios->max(),
            'precio_promedio' => $precioPromedio,
            'mejor_proveedor' => $preciosProveedores->first(),
            'cantidad_proveedores' => $preciosProveedores->count(),
            'ahorro_vs_maximo' => $precios->max() - $precios->min(),
        ];
    }

    /**
     * Obtener comparación de todos los productos con sus proveedores
     */
    public function obtenerMatrizComparacion(): Collection
    {
        $productos = Producto::active()
            ->with(['proveedorProductos' => function ($query) {
                $query->where('disponible', true)->with('proveedor');
            }])
            ->whereHas('proveedorProductos')
            ->orderBy('nombre')
            ->get();

        return $productos->map(function ($producto) {
            $precios = $producto->proveedorProductos->pluck('precio');

            return [
                'producto' => $producto,
                'proveedores' => $producto->proveedorProductos,
                'precio_minimo' => $precios->min(),
                'precio_maximo' => $precios->max(),
                'precio_promedio' => $precios->count() > 0 ? (int) round($precios->avg()) : 0,
                'cantidad_proveedores' => $precios->count(),
                'diferencia_porcentual' => $precios->count() > 0 && $precios->min() > 0
                    ? round((($precios->max() - $precios->min()) / $precios->min()) * 100, 1)
                    : 0,
            ];
        });
    }

    /**
     * Actualizar precio de compra basado en promedio de proveedores
     */
    public function actualizarPrecioCompraDesdePromedio(int $productoId): ?array
    {
        $comparacion = $this->compararPreciosProducto($productoId);

        if (!$comparacion['precio_promedio']) {
            return null;
        }

        $producto = $comparacion['producto'];
        $precioAnterior = $producto->precio_compra;
        $precioVentaAnterior = $producto->precio_venta;

        // Actualizar precio de compra con el promedio
        $producto->actualizarPrecios($comparacion['precio_promedio']);

        return [
            'producto' => $producto,
            'precio_compra_anterior' => $precioAnterior,
            'precio_compra_nuevo' => $producto->precio_compra,
            'precio_venta_anterior' => $precioVentaAnterior,
            'precio_venta_nuevo' => $producto->precio_venta,
            'basado_en' => $comparacion['cantidad_proveedores'] . ' proveedores',
        ];
    }

    /**
     * Actualizar todos los precios de compra basados en promedios
     */
    public function actualizarTodosLosPrecios(): array
    {
        $resultados = [];

        $productosConProveedores = Producto::active()
            ->whereHas('proveedorProductos', function ($query) {
                $query->where('disponible', true);
            })
            ->get();

        DB::beginTransaction();
        try {
            foreach ($productosConProveedores as $producto) {
                $resultado = $this->actualizarPrecioCompraDesdePromedio($producto->id);
                if ($resultado) {
                    $resultados[] = $resultado;
                }
            }

            DB::commit();
            Log::info('Precios actualizados desde promedios de proveedores', [
                'productos_actualizados' => count($resultados)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar precios desde promedios', [
                'exception' => $e->getMessage()
            ]);
            throw $e;
        }

        return $resultados;
    }

    /**
     * Obtener estadísticas de proveedores (scorecards)
     */
    public function obtenerScorecardsProveedores(): Collection
    {
        return DB::table('proveedor_productos')
            ->join('proveedores', 'proveedor_productos.proveedor_id', '=', 'proveedores.id')
            ->join('users', 'proveedores.user_id', '=', 'users.id')
            ->where('users.activo', true)
            ->groupBy('proveedores.id', 'proveedores.razon_social')
            ->select([
                'proveedores.id',
                'proveedores.razon_social',
                DB::raw('COUNT(proveedor_productos.id) as total_productos'),
                DB::raw('SUM(CASE WHEN proveedor_productos.disponible = 1 THEN 1 ELSE 0 END) as productos_disponibles'),
                DB::raw('AVG(proveedor_productos.precio) as precio_promedio'),
                DB::raw('AVG(proveedor_productos.tiempo_entrega_dias) as tiempo_entrega_promedio'),
            ])
            ->orderByDesc('total_productos')
            ->get()
            ->map(function ($proveedor) {
                // Calcular score de competitividad (comparando con otros proveedores)
                $proveedor->disponibilidad_porcentaje = $proveedor->total_productos > 0
                    ? round(($proveedor->productos_disponibles / $proveedor->total_productos) * 100, 1)
                    : 0;

                return $proveedor;
            });
    }

    /**
     * Obtener productos donde un proveedor tiene el mejor precio
     */
    public function obtenerMejoresPreciosPorProveedor(int $proveedorId): Collection
    {
        $subquery = DB::table('proveedor_productos')
            ->where('disponible', true)
            ->groupBy('producto_id')
            ->select('producto_id', DB::raw('MIN(precio) as min_precio'));

        return ProveedorProducto::where('proveedor_id', $proveedorId)
            ->where('disponible', true)
            ->whereIn(DB::raw('(producto_id, precio)'), function ($query) {
                $query->select('producto_id', DB::raw('MIN(precio)'))
                    ->from('proveedor_productos')
                    ->where('disponible', true)
                    ->groupBy('producto_id');
            })
            ->with('producto')
            ->get();
    }

    /**
     * Obtener resumen general del análisis
     */
    public function obtenerResumenGeneral(): array
    {
        $totalProductos = Producto::active()->count();
        $productosConProveedores = Producto::active()
            ->whereHas('proveedorProductos')->count();

        $totalProveedoresActivos = DB::table('proveedores')
            ->join('users', 'proveedores.user_id', '=', 'users.id')
            ->where('users.activo', true)
            ->count();

        $promedioProveedoresPorProducto = $productosConProveedores > 0
            ? round(ProveedorProducto::count() / $productosConProveedores, 1)
            : 0;

        return [
            'total_productos' => $totalProductos,
            'productos_con_proveedores' => $productosConProveedores,
            'productos_sin_proveedores' => $totalProductos - $productosConProveedores,
            'cobertura_porcentaje' => $totalProductos > 0
                ? round(($productosConProveedores / $totalProductos) * 100, 1)
                : 0,
            'total_proveedores_activos' => $totalProveedoresActivos,
            'promedio_proveedores_por_producto' => $promedioProveedoresPorProducto,
        ];
    }
}
