<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('usuario')) return redirect()->route('login');

        $user = session('usuario');
        $productos = Producto::count();
        $clientes = Cliente::count();
        $ventasHoy = Venta::whereDate('ven_fecha', Carbon::today())->sum('ven_total');
        $stockBajo = Producto::whereColumn('pro_stock', '<', 'pro_stock_minimo')->count();

        $ventasAnuales = [];
        for ($m = 1; $m <= 12; $m++) {
            $ventasAnuales[] = Venta::whereYear('ven_fecha', date('Y'))
                ->whereMonth('ven_fecha', $m)
                ->sum('ven_total');
        }

        $ventasRecientes = Venta::with('cliente')->latest('ven_fecha')->take(5)->get();

        // Variables ejemplo para micro-charts
        $ventasHoyMes = [10, 20, 15, 30, 25, 40];
        $productosMes = [5, 6, 7, 8, 6, 5];
        $clientesMes = [2, 3, 4, 5, 3, 2];
        $stockMes = [3, 2, 4, 1, 5, 2];

        return view('dashboard.index', compact(
            'user',
            'productos',
            'clientes',
            'ventasHoy',
            'stockBajo',
            'ventasAnuales',
            'ventasRecientes',
            'ventasHoyMes',
            'productosMes',
            'clientesMes',
            'stockMes'
        ));
    }
}
