<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\DetalleVenta;
use App\Models\DetalleCompra;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('usuario')) return redirect()->route('login');

        $user = session('usuario');

        // ==================== SECCIÓN VENTAS ====================

        // Ventas del día actual
        $ventasHoy = Venta::whereDate('ven_fecha', Carbon::today())
            ->where('ven_estado', 'completada')
            ->sum('ven_total');

        // Ventas del mes actual
        $ventasMes = Venta::whereYear('ven_fecha', Carbon::now()->year)
            ->whereMonth('ven_fecha', Carbon::now()->month)
            ->where('ven_estado', 'completada')
            ->sum('ven_total');

        // Ventas mes anterior para comparación
        $ventasMesAnterior = Venta::whereYear('ven_fecha', Carbon::now()->subMonth()->year)
            ->whereMonth('ven_fecha', Carbon::now()->subMonth()->month)
            ->where('ven_estado', 'completada')
            ->sum('ven_total');

        // Calcular porcentaje de crecimiento
        $crecimientoVentas = 0;
        if ($ventasMesAnterior > 0) {
            $crecimientoVentas = (($ventasMes - $ventasMesAnterior) / $ventasMesAnterior) * 100;
        }

        // Ventas anuales por mes (para el gráfico)
        $ventasAnuales = [];
        for ($m = 1; $m <= 12; $m++) {
            $ventasAnuales[] = Venta::whereYear('ven_fecha', date('Y'))
                ->whereMonth('ven_fecha', $m)
                ->where('ven_estado', 'completada')
                ->sum('ven_total');
        }

        // Ventas diarias del mes actual (para micro-chart)
        $ventasDiarias = [];
        for ($d = 1; $d <= 7; $d++) {
            $fecha = Carbon::now()->subDays(7 - $d);
            $ventasDiarias[] = Venta::whereDate('ven_fecha', $fecha)
                ->where('ven_estado', 'completada')
                ->sum('ven_total');
        }

        // Últimos clientes que compraron
        $ultimosClientes = Venta::with('cliente')
            ->where('ven_estado', 'completada')
            ->select('cli_id', DB::raw('MAX(ven_fecha) as ultima_compra'), DB::raw('SUM(ven_total) as total_compras'))
            ->groupBy('cli_id')
            ->orderBy('ultima_compra', 'desc')
            ->take(5)
            ->get();

        // ==================== SECCIÓN INVENTARIO ====================

        // Total de productos
        $totalProductos = Producto::count();

        // Productos con stock bajo
        $stockBajo = Producto::whereColumn('pro_stock', '<=', 'pro_stock_minimo')->count();

        // Productos con stock bajo (detallados)
        $productosStockBajo = Producto::whereColumn('pro_stock', '<=', 'pro_stock_minimo')
            ->orderBy('pro_stock', 'asc')
            ->take(5)
            ->get();

        // Valor total del inventario
        $valorInventario = Producto::selectRaw('SUM(pro_stock * pro_precio_compra) as total')->first()->total ?? 0;

        // Productos más vendidos
        $productosMasVendidos = DetalleVenta::select('pro_id', DB::raw('SUM(det_cantidad) as total_vendido'))
            ->groupBy('pro_id')
            ->orderBy('total_vendido', 'desc')
            ->with('producto')
            ->take(5)
            ->get();

        // Evolución de productos (últimos 6 meses)
        $evolucionProductos = [];
        for ($m = 5; $m >= 0; $m--) {
            $fecha = Carbon::now()->subMonths($m);
            $evolucionProductos[] = Producto::whereYear('created_at', '<=', $fecha->year)
                ->whereMonth('created_at', '<=', $fecha->month)
                ->count();
        }

        // ==================== SECCIÓN COMPRAS ====================

        // Gastos del mes actual en compras
        $gastosMes = Compra::whereYear('com_fecha', Carbon::now()->year)
            ->whereMonth('com_fecha', Carbon::now()->month)
            ->where('com_estado', 'completada')
            ->sum('com_total');

        // Gastos mensuales del año (para gráfico)
        $gastosMensuales = [];
        for ($m = 1; $m <= 12; $m++) {
            $gastosMensuales[] = Compra::whereYear('com_fecha', date('Y'))
                ->whereMonth('com_fecha', $m)
                ->where('com_estado', 'completada')
                ->sum('com_total');
        }

        // Proveedores ordenados por gasto total (donde más gastamos)
        $topProveedoresGasto = Compra::select('prov_id', DB::raw('SUM(com_total) as total_gastado'))
            ->where('com_estado', 'completada')
            ->groupBy('prov_id')
            ->orderBy('total_gastado', 'desc')
            ->with('proveedor')
            ->take(5)
            ->get();

        // Proveedores con precios más económicos (precio promedio más bajo)
        $proveedoresEconomicos = DetalleCompra::select('detalle_compra.pro_id', 'compra.prov_id',
                DB::raw('AVG(detalle_compra.det_com_precio_unitario) as precio_promedio'))
            ->join('compra', 'detalle_compra.com_id', '=', 'compra.com_id')
            ->where('compra.com_estado', 'completada')
            ->groupBy('detalle_compra.pro_id', 'compra.prov_id')
            ->with(['producto', 'compra.proveedor'])
            ->orderBy('precio_promedio', 'asc')
            ->take(5)
            ->get();

        // Total de proveedores activos
        $totalProveedores = Proveedor::where('prov_estado', 'activo')->count();

        // Compras últimos 7 días (para micro-chart)
        $comprasSemanales = [];
        for ($d = 1; $d <= 7; $d++) {
            $fecha = Carbon::now()->subDays(7 - $d);
            $comprasSemanales[] = Compra::whereDate('com_fecha', $fecha)
                ->where('com_estado', 'completada')
                ->sum('com_total');
        }

        // ==================== OTROS DATOS ====================

        // Total de clientes
        $totalClientes = Cliente::count();

        // Nuevos clientes del mes
        $nuevosClientes = Cliente::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        return view('dashboard.index', compact(
            'user',
            // Ventas
            'ventasHoy',
            'ventasMes',
            'ventasMesAnterior',
            'crecimientoVentas',
            'ventasAnuales',
            'ventasDiarias',
            'ultimosClientes',
            // Inventario
            'totalProductos',
            'stockBajo',
            'productosStockBajo',
            'valorInventario',
            'productosMasVendidos',
            'evolucionProductos',
            // Compras
            'gastosMes',
            'gastosMensuales',
            'topProveedoresGasto',
            'proveedoresEconomicos',
            'totalProveedores',
            'comprasSemanales',
            // Otros
            'totalClientes',
            'nuevosClientes'
        ));
    }
}
