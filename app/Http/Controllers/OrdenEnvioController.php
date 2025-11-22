<?php

namespace App\Http\Controllers;

use App\Models\OrdenEnvio;
use App\Models\OrdenEnvioItem;
use App\Models\PedidoCliente;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenEnvioController extends Controller
{
    /**
     * Listar todas las órdenes de envío
     */
    public function index(Request $request)
    {
        $query = OrdenEnvio::with(['items.producto', 'pedidoCliente', 'usuario'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero', 'like', "%{$buscar}%")
                  ->orWhere('numero_guia', 'like', "%{$buscar}%");
            });
        }

        $ordenes = $query->paginate(15);

        return view('ordenes-envio.index', compact('ordenes'));
    }

    /**
     * Formulario de creación (desde pedido de cliente)
     */
    public function create(Request $request)
    {
        if (!$request->filled('pedido_cliente_id')) {
            return redirect()->route('pedidos-cliente.index')
                ->with('error', 'Debe seleccionar un pedido de cliente para generar orden de envío');
        }

        $pedidoCliente = PedidoCliente::with('items.producto')->find($request->pedido_cliente_id);

        if (!$pedidoCliente || !$pedidoCliente->puedeGenerarOrdenEnvio()) {
            return back()->with('error', 'Este pedido no puede generar orden de envío');
        }

        return view('ordenes-envio.create', compact('pedidoCliente'));
    }

    /**
     * Guardar nueva orden de envío
     */
    public function store(Request $request)
    {
        $request->validate([
            'pedido_cliente_id' => 'required|exists:pedidos_cliente,id',
            'direccion_entrega' => 'required|string|max:500',
            'contacto_entrega' => 'nullable|string|max:255',
            'telefono_entrega' => 'nullable|string|max:50',
            'metodo_envio' => 'nullable|string|max:100',
            'transportista' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.001',
        ]);

        $pedidoCliente = PedidoCliente::find($request->pedido_cliente_id);
        if (!$pedidoCliente->puedeGenerarOrdenEnvio()) {
            return back()->with('error', 'Este pedido no puede generar orden de envío');
        }

        DB::beginTransaction();
        try {
            $orden = OrdenEnvio::create([
                'pedido_cliente_id' => $request->pedido_cliente_id,
                'direccion_entrega' => $request->direccion_entrega,
                'contacto_entrega' => $request->contacto_entrega,
                'telefono_entrega' => $request->telefono_entrega,
                'fecha_generacion' => now(),
                'metodo_envio' => $request->metodo_envio,
                'transportista' => $request->transportista,
                'notas' => $request->notas,
                'estado' => OrdenEnvio::ESTADO_PREPARANDO,
                'usuario_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                OrdenEnvioItem::create([
                    'orden_envio_id' => $orden->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                ]);
            }

            // Actualizar estado del pedido
            $pedidoCliente->update(['estado' => PedidoCliente::ESTADO_LISTO_ENVIO]);

            DB::commit();
            return redirect()
                ->route('ordenes-envio.show', $orden)
                ->with('success', "Orden de envío {$orden->numero} creada exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de la orden
     */
    public function show(OrdenEnvio $ordenesEnvio)
    {
        $orden = $ordenesEnvio->load(['items.producto', 'pedidoCliente', 'usuario']);
        return view('ordenes-envio.show', compact('orden'));
    }

    /**
     * Eliminar orden
     */
    public function destroy(OrdenEnvio $ordenesEnvio)
    {
        $orden = $ordenesEnvio;
        if (!in_array($orden->estado, [OrdenEnvio::ESTADO_PREPARANDO, OrdenEnvio::ESTADO_CANCELADO])) {
            return back()->with('error', 'Solo se pueden eliminar órdenes en preparación o canceladas');
        }

        $numero = $orden->numero;

        // Restaurar estado del pedido
        if ($orden->pedidoCliente) {
            $orden->pedidoCliente->update(['estado' => PedidoCliente::ESTADO_MERCADERIA_RECIBIDA]);
        }

        $orden->delete();

        return redirect()
            ->route('ordenes-envio.index')
            ->with('success', "Orden {$numero} eliminada exitosamente");
    }

    /**
     * Marcar como lista para despachar
     */
    public function listaDespachar(OrdenEnvio $orden)
    {
        if ($orden->estado !== OrdenEnvio::ESTADO_PREPARANDO) {
            return back()->with('error', 'La orden debe estar en preparación');
        }

        $orden->update(['estado' => OrdenEnvio::ESTADO_LISTO]);

        return redirect()
            ->route('ordenes-envio.show', $orden)
            ->with('success', 'Orden marcada como lista para despachar');
    }

    /**
     * Despachar (enviar)
     */
    public function despachar(Request $request, OrdenEnvio $orden)
    {
        if (!$orden->puedeSerDespachada()) {
            return back()->with('error', 'Esta orden no puede ser despachada');
        }

        $request->validate([
            'numero_guia' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Generar SALIDA de inventario
            foreach ($orden->items as $item) {
                $producto = $item->producto;
                $stockAnterior = $producto->stock_actual;
                $stockNuevo = $stockAnterior - $item->cantidad;

                if ($stockNuevo < 0) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}. Disponible: {$stockAnterior}, Requerido: {$item->cantidad}");
                }

                $producto->update(['stock_actual' => $stockNuevo]);

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'SALIDA',
                    'cantidad' => $item->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'usuario_id' => Auth::id(),
                    'observaciones' => "Despacho orden {$orden->numero}",
                ]);
            }

            $orden->update([
                'estado' => OrdenEnvio::ESTADO_EN_TRANSITO,
                'fecha_envio' => now(),
                'numero_guia' => $request->numero_guia,
            ]);

            // Actualizar estado del pedido
            if ($orden->pedidoCliente) {
                $orden->pedidoCliente->update(['estado' => PedidoCliente::ESTADO_ENVIADO]);
            }

            DB::commit();
            return redirect()
                ->route('ordenes-envio.show', $orden)
                ->with('success', 'Orden despachada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Marcar como entregada
     */
    public function entregar(Request $request, OrdenEnvio $orden)
    {
        if (!$orden->puedeSerEntregada()) {
            return back()->with('error', 'Esta orden no puede ser marcada como entregada');
        }

        $request->validate([
            'observaciones_entrega' => 'nullable|string|max:500',
        ]);

        $orden->update([
            'estado' => OrdenEnvio::ESTADO_ENTREGADO,
            'fecha_entrega' => now(),
            'observaciones_entrega' => $request->observaciones_entrega,
        ]);

        // Actualizar estado del pedido
        if ($orden->pedidoCliente) {
            $orden->pedidoCliente->update(['estado' => PedidoCliente::ESTADO_ENTREGADO]);
        }

        return redirect()
            ->route('ordenes-envio.show', $orden)
            ->with('success', 'Orden marcada como entregada. Pedido completado.');
    }

    /**
     * Registrar devolución
     */
    public function devolver(Request $request, OrdenEnvio $orden)
    {
        if ($orden->estado !== OrdenEnvio::ESTADO_EN_TRANSITO) {
            return back()->with('error', 'Solo se pueden devolver órdenes en tránsito');
        }

        $request->validate([
            'observaciones_entrega' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Revertir SALIDA de inventario (hacer ENTRADA)
            foreach ($orden->items as $item) {
                $producto = $item->producto;
                $stockAnterior = $producto->stock_actual;
                $stockNuevo = $stockAnterior + $item->cantidad;

                $producto->update(['stock_actual' => $stockNuevo]);

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'ENTRADA',
                    'cantidad' => $item->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'usuario_id' => Auth::id(),
                    'observaciones' => "Devolución orden {$orden->numero}: {$request->observaciones_entrega}",
                ]);
            }

            $orden->update([
                'estado' => OrdenEnvio::ESTADO_DEVUELTO,
                'observaciones_entrega' => $request->observaciones_entrega,
            ]);

            DB::commit();
            return redirect()
                ->route('ordenes-envio.show', $orden)
                ->with('success', 'Devolución registrada. Stock restaurado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar devolución: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar orden
     */
    public function cancelar(Request $request, OrdenEnvio $orden)
    {
        if (!$orden->puedeSerCancelada()) {
            return back()->with('error', 'Esta orden no puede ser cancelada');
        }

        $request->validate([
            'observaciones_entrega' => 'required|string|max:500',
        ]);

        $orden->update([
            'estado' => OrdenEnvio::ESTADO_CANCELADO,
            'observaciones_entrega' => $request->observaciones_entrega,
        ]);

        // Restaurar estado del pedido
        if ($orden->pedidoCliente) {
            $orden->pedidoCliente->update(['estado' => PedidoCliente::ESTADO_MERCADERIA_RECIBIDA]);
        }

        return redirect()
            ->route('ordenes-envio.show', $orden)
            ->with('success', 'Orden de envío cancelada');
    }
}
