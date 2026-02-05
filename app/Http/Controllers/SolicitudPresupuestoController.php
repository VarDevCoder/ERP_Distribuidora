<?php

namespace App\Http\Controllers;

use App\Models\PedidoCliente;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SolicitudPresupuesto;
use App\Models\SolicitudPresupuestoItem;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudPresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = SolicitudPresupuesto::with(['proveedor', 'pedidoCliente', 'usuario'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $solicitudes = $query->paginate(config('ankor.pagination.per_page', 15));
        $proveedores = Proveedor::orderBy('razon_social')->get();

        return view('solicitudes-presupuesto.index', compact('solicitudes', 'proveedores'));
    }

    public function create(Request $request)
    {
        $proveedores = Proveedor::orderBy('razon_social')->get();
        $productos = Producto::active()->with('categoria')->orderBy('nombre')->get()->groupBy(fn($p) => $p->categoria?->nombre ?? 'Sin Categoría');
        $pedidoCliente = null;

        if ($request->filled('pedido_cliente_id')) {
            $pedidoCliente = PedidoCliente::with('items.producto')->find($request->pedido_cliente_id);
        }

        return view('solicitudes-presupuesto.create', compact('proveedores', 'productos', 'pedidoCliente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_limite_respuesta' => 'nullable|date|after:today',
            'mensaje_solicitud' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad_solicitada' => 'required|numeric|min:0.001',
        ]);

        DB::beginTransaction();
        try {
            $solicitud = SolicitudPresupuesto::create([
                'pedido_cliente_id' => $request->pedido_cliente_id,
                'proveedor_id' => $request->proveedor_id,
                'usuario_id' => Auth::id(),
                'fecha_solicitud' => now(),
                'fecha_limite_respuesta' => $request->fecha_limite_respuesta,
                'mensaje_solicitud' => $request->mensaje_solicitud,
                'estado' => SolicitudPresupuesto::ESTADO_ENVIADA,
            ]);

            foreach ($request->items as $item) {
                SolicitudPresupuestoItem::create([
                    'solicitud_presupuesto_id' => $solicitud->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad_solicitada' => $item['cantidad_solicitada'],
                ]);
            }

            if ($request->pedido_cliente_id) {
                $pedido = PedidoCliente::find($request->pedido_cliente_id);
                if ($pedido && $pedido->estado === PedidoCliente::ESTADO_EN_PROCESO) {
                    $pedido->update(['estado' => PedidoCliente::ESTADO_PRESUPUESTADO]);
                }
            }

            DB::commit();
            return redirect()
                ->route('solicitudes-presupuesto.show', $solicitud)
                ->with('success', "Solicitud {$solicitud->numero} enviada al proveedor");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear solicitud de presupuesto', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al crear la solicitud. Intente nuevamente.');
        }
    }

    public function show(SolicitudPresupuesto $solicitud)
    {
        $solicitud->load(['items.producto', 'proveedor', 'pedidoCliente', 'usuario']);
        return view('solicitudes-presupuesto.show', compact('solicitud'));
    }

    public function destroy(SolicitudPresupuesto $solicitud)
    {
        if ($solicitud->estado !== SolicitudPresupuesto::ESTADO_ENVIADA) {
            return back()->with('error', 'Solo se pueden eliminar solicitudes en estado enviada');
        }

        $numero = $solicitud->numero;
        $solicitud->delete();

        return redirect()
            ->route('solicitudes-presupuesto.index')
            ->with('success', "Solicitud {$numero} eliminada exitosamente");
    }

    public function aceptar(SolicitudPresupuesto $solicitud)
    {
        if (!$solicitud->puedeSerAceptada()) {
            return back()->with('error', 'Esta cotización no puede ser aceptada');
        }

        DB::beginTransaction();
        try {
            $proveedor = $solicitud->proveedor;

            $orden = OrdenCompra::create([
                'pedido_cliente_id' => $solicitud->pedido_cliente_id,
                'proveedor_nombre' => $proveedor->razon_social,
                'proveedor_ruc' => $proveedor->ruc,
                'proveedor_telefono' => $proveedor->telefono,
                'proveedor_email' => $proveedor->user->email,
                'proveedor_direccion' => $proveedor->direccion,
                'presupuesto_proveedor_id' => null,
                'fecha_orden' => now(),
                'fecha_entrega_esperada' => now()->addDays($solicitud->dias_entrega_estimados ?? 7),
                'estado' => OrdenCompra::ESTADO_BORRADOR,
                'total' => $solicitud->total_cotizado,
                'usuario_id' => Auth::id(),
            ]);

            foreach ($solicitud->items as $item) {
                if ($item->tiene_stock && $item->cantidad_disponible > 0) {
                    OrdenCompraItem::create([
                        'orden_compra_id' => $orden->id,
                        'producto_id' => $item->producto_id,
                        'cantidad_solicitada' => $item->cantidad_disponible,
                        'precio_unitario' => $item->precio_unitario_cotizado,
                    ]);
                }
            }

            $orden->calcularTotales();

            $solicitud->update(['estado' => SolicitudPresupuesto::ESTADO_ACEPTADA]);

            if ($solicitud->pedidoCliente) {
                $solicitud->pedidoCliente->update(['estado' => PedidoCliente::ESTADO_ORDEN_COMPRA]);
            }

            DB::commit();
            return redirect()
                ->route('ordenes-compra.show', $orden)
                ->with('success', "Orden de compra {$orden->numero} creada desde cotización");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al aceptar cotización', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al aceptar la cotización. Intente nuevamente.');
        }
    }

    public function rechazar(Request $request, SolicitudPresupuesto $solicitud)
    {
        if (!$solicitud->puedeSerAceptada()) {
            return back()->with('error', 'Esta cotización no puede ser rechazada');
        }

        $solicitud->update(['estado' => SolicitudPresupuesto::ESTADO_RECHAZADA]);

        return redirect()
            ->route('solicitudes-presupuesto.show', $solicitud)
            ->with('success', 'Cotización rechazada');
    }
}
