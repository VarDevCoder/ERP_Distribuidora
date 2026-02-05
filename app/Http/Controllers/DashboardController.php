<?php

namespace App\Http\Controllers;

use App\Models\PedidoCliente;
use App\Models\OrdenCompra;
use App\Models\OrdenEnvio;
use App\Models\SolicitudPresupuesto;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'solicitudes_activas' => PedidoCliente::whereNotIn('estado', [
                PedidoCliente::ESTADO_ENTREGADO,
                PedidoCliente::ESTADO_CANCELADO,
            ])->count(),
            'solicitudes_hoy' => PedidoCliente::whereDate('created_at', today())->count(),
            'cotizaciones_pendientes' => SolicitudPresupuesto::whereIn('estado', [
                SolicitudPresupuesto::ESTADO_ENVIADA,
                SolicitudPresupuesto::ESTADO_VISTA,
            ])->count(),
            'cotizaciones_listas' => SolicitudPresupuesto::where('estado', SolicitudPresupuesto::ESTADO_COTIZADA)->count(),
            'ordenes_compra_activas' => OrdenCompra::whereNotIn('estado', [
                OrdenCompra::ESTADO_RECIBIDA_COMPLETA,
                OrdenCompra::ESTADO_CANCELADA,
            ])->count(),
            'ordenes_envio_pendientes' => OrdenEnvio::whereNotIn('estado', [
                'ENTREGADO',
                'CANCELADO',
                'DEVUELTO',
            ])->count(),
            'productos_total' => Producto::active()->count(),
            'productos_stock_bajo' => Producto::active()
                ->whereColumn('stock_actual', '<=', 'stock_minimo')
                ->where('stock_minimo', '>', 0)
                ->count(),
        ];

        // Feed de actividades recientes (últimas 20 operaciones)
        $actividades = collect();

        // Solicitudes recientes
        $solicitudes = PedidoCliente::with('usuario')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($s) => [
                'tipo' => 'solicitud',
                'icono' => 'clipboard',
                'color' => 'blue',
                'titulo' => $s->numero,
                'detalle' => $s->cliente_nombre,
                'estado' => $s->estado,
                'estado_color' => $s->estado_color,
                'fecha' => $s->created_at,
                'usuario' => $s->usuario->name ?? '-',
                'url' => route('pedidos-cliente.show', $s),
            ]);

        // Cotizaciones recientes
        $cotizaciones = SolicitudPresupuesto::with(['proveedor', 'usuario'])
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($c) => [
                'tipo' => 'cotizacion',
                'icono' => 'document',
                'color' => 'indigo',
                'titulo' => $c->numero,
                'detalle' => $c->proveedor->nombre_empresa ?? '-',
                'estado' => $c->estado,
                'estado_color' => $c->estado_color,
                'fecha' => $c->created_at,
                'usuario' => $c->usuario->name ?? '-',
                'url' => route('solicitudes-presupuesto.show', $c),
            ]);

        // Órdenes de compra recientes
        $compras = OrdenCompra::with('usuario')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($o) => [
                'tipo' => 'compra',
                'icono' => 'cart',
                'color' => 'purple',
                'titulo' => $o->numero,
                'detalle' => $o->proveedor_nombre,
                'estado' => $o->estado,
                'estado_color' => $o->estado_color,
                'fecha' => $o->created_at,
                'usuario' => $o->usuario->name ?? '-',
                'url' => route('ordenes-compra.show', $o),
            ]);

        // Órdenes de envío recientes
        $envios = OrdenEnvio::with(['pedidoCliente', 'usuario'])
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($e) => [
                'tipo' => 'envio',
                'icono' => 'truck',
                'color' => 'green',
                'titulo' => $e->numero,
                'detalle' => $e->pedidoCliente->cliente_nombre ?? '-',
                'estado' => $e->estado,
                'estado_color' => $e->estado_color ?? 'bg-gray-100 text-gray-800',
                'fecha' => $e->created_at,
                'usuario' => $e->usuario->name ?? '-',
                'url' => route('ordenes-envio.show', $e),
            ]);

        $actividades = $solicitudes
            ->concat($cotizaciones)
            ->concat($compras)
            ->concat($envios)
            ->sortByDesc('fecha')
            ->take(15)
            ->values();

        return view('dashboard', compact('stats', 'actividades'));
    }
}
