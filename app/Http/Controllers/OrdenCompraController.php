<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\OrdenCompraItem;
use App\Models\PedidoCliente;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    /**
     * Listar todas las órdenes de compra
     */
    public function index(Request $request)
    {
        $query = OrdenCompra::with(['items.producto', 'pedidoCliente', 'usuario'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero', 'like', "%{$buscar}%")
                  ->orWhere('proveedor_nombre', 'like', "%{$buscar}%")
                  ->orWhere('proveedor_ruc', 'like', "%{$buscar}%");
            });
        }

        $ordenes = $query->paginate(15);

        return view('ordenes-compra.index', compact('ordenes'));
    }

    /**
     * Formulario de creación
     */
    public function create(Request $request)
    {
        $productos = Producto::orderBy('nombre')->get();
        $pedidoCliente = null;

        // Si viene desde un pedido de cliente
        if ($request->filled('pedido_cliente_id')) {
            $pedidoCliente = PedidoCliente::with('items.producto')->find($request->pedido_cliente_id);
        }

        return view('ordenes-compra.create', compact('productos', 'pedidoCliente'));
    }

    /**
     * Guardar nueva orden de compra
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_nombre' => 'required|string|max:255',
            'proveedor_ruc' => 'nullable|string|max:50',
            'proveedor_telefono' => 'nullable|string|max:50',
            'proveedor_email' => 'nullable|email|max:255',
            'proveedor_direccion' => 'nullable|string|max:500',
            'fecha_orden' => 'required|date',
            'fecha_entrega_esperada' => 'nullable|date|after_or_equal:fecha_orden',
            'notas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad_solicitada' => 'required|numeric|min:0.001',
            'items.*.precio_unitario' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $orden = OrdenCompra::create([
                'pedido_cliente_id' => $request->pedido_cliente_id,
                'proveedor_nombre' => $request->proveedor_nombre,
                'proveedor_ruc' => $request->proveedor_ruc,
                'proveedor_telefono' => $request->proveedor_telefono,
                'proveedor_email' => $request->proveedor_email,
                'proveedor_direccion' => $request->proveedor_direccion,
                'fecha_orden' => $request->fecha_orden,
                'fecha_entrega_esperada' => $request->fecha_entrega_esperada,
                'notas' => $request->notas,
                'estado' => OrdenCompra::ESTADO_BORRADOR,
                'descuento' => $request->descuento ?? 0,
                'usuario_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                OrdenCompraItem::create([
                    'orden_compra_id' => $orden->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad_solicitada' => $item['cantidad_solicitada'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            // Si viene de un pedido de cliente, actualizar su estado
            if ($request->pedido_cliente_id) {
                $pedido = PedidoCliente::find($request->pedido_cliente_id);
                if ($pedido && $pedido->puedeGenerarOrdenCompra()) {
                    $pedido->update(['estado' => PedidoCliente::ESTADO_ORDEN_COMPRA]);
                }
            }

            DB::commit();
            return redirect()
                ->route('ordenes-compra.show', $orden)
                ->with('success', "Orden de compra {$orden->numero} creada exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la orden: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle de la orden
     */
    public function show(OrdenCompra $ordenesCompra)
    {
        $orden = $ordenesCompra->load(['items.producto', 'pedidoCliente', 'usuario']);
        return view('ordenes-compra.show', compact('orden'));
    }

    /**
     * Formulario de edición
     */
    public function edit(OrdenCompra $ordenesCompra)
    {
        $orden = $ordenesCompra;
        if (!in_array($orden->estado, [OrdenCompra::ESTADO_BORRADOR])) {
            return back()->with('error', 'Solo se pueden editar órdenes en borrador');
        }

        $productos = Producto::orderBy('nombre')->get();
        return view('ordenes-compra.edit', compact('orden', 'productos'));
    }

    /**
     * Actualizar orden
     */
    public function update(Request $request, OrdenCompra $ordenesCompra)
    {
        $orden = $ordenesCompra;
        if (!in_array($orden->estado, [OrdenCompra::ESTADO_BORRADOR])) {
            return back()->with('error', 'Solo se pueden modificar órdenes en borrador');
        }

        $request->validate([
            'proveedor_nombre' => 'required|string|max:255',
            'proveedor_ruc' => 'nullable|string|max:50',
            'proveedor_telefono' => 'nullable|string|max:50',
            'proveedor_email' => 'nullable|email|max:255',
            'proveedor_direccion' => 'nullable|string|max:500',
            'fecha_entrega_esperada' => 'nullable|date',
            'notas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad_solicitada' => 'required|numeric|min:0.001',
            'items.*.precio_unitario' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $orden->update([
                'proveedor_nombre' => $request->proveedor_nombre,
                'proveedor_ruc' => $request->proveedor_ruc,
                'proveedor_telefono' => $request->proveedor_telefono,
                'proveedor_email' => $request->proveedor_email,
                'proveedor_direccion' => $request->proveedor_direccion,
                'fecha_entrega_esperada' => $request->fecha_entrega_esperada,
                'notas' => $request->notas,
                'descuento' => $request->descuento ?? 0,
            ]);

            // Eliminar items anteriores y crear nuevos
            $orden->items()->delete();

            foreach ($request->items as $item) {
                OrdenCompraItem::create([
                    'orden_compra_id' => $orden->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad_solicitada' => $item['cantidad_solicitada'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            DB::commit();
            return redirect()
                ->route('ordenes-compra.show', $orden)
                ->with('success', 'Orden actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la orden: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar orden
     */
    public function destroy(OrdenCompra $ordenesCompra)
    {
        $orden = $ordenesCompra;
        if (!in_array($orden->estado, [OrdenCompra::ESTADO_BORRADOR, OrdenCompra::ESTADO_CANCELADA])) {
            return back()->with('error', 'Solo se pueden eliminar órdenes en borrador o canceladas');
        }

        $numero = $orden->numero;
        $orden->delete();

        return redirect()
            ->route('ordenes-compra.index')
            ->with('success', "Orden {$numero} eliminada exitosamente");
    }

    /**
     * Enviar orden al proveedor
     */
    public function enviar(OrdenCompra $orden)
    {
        if (!$orden->puedeSerEnviada()) {
            return back()->with('error', 'Esta orden no puede ser enviada');
        }

        $orden->update(['estado' => OrdenCompra::ESTADO_ENVIADA]);

        return redirect()
            ->route('ordenes-compra.show', $orden)
            ->with('success', 'Orden marcada como enviada al proveedor');
    }

    /**
     * Confirmar orden (proveedor confirmó)
     */
    public function confirmar(OrdenCompra $orden)
    {
        if (!$orden->puedeSerConfirmada()) {
            return back()->with('error', 'Esta orden no puede ser confirmada');
        }

        $orden->update(['estado' => OrdenCompra::ESTADO_CONFIRMADA]);

        return redirect()
            ->route('ordenes-compra.show', $orden)
            ->with('success', 'Orden confirmada por el proveedor');
    }

    /**
     * Marcar en tránsito
     */
    public function enTransito(OrdenCompra $orden)
    {
        if ($orden->estado !== OrdenCompra::ESTADO_CONFIRMADA) {
            return back()->with('error', 'La orden debe estar confirmada');
        }

        $orden->update(['estado' => OrdenCompra::ESTADO_EN_TRANSITO]);

        return redirect()
            ->route('ordenes-compra.show', $orden)
            ->with('success', 'Orden marcada en tránsito');
    }

    /**
     * Formulario de recepción de mercadería
     */
    public function formRecepcion(OrdenCompra $orden)
    {
        if (!$orden->puedeRecibirMercaderia()) {
            return back()->with('error', 'No se puede recibir mercadería para esta orden');
        }

        $orden->load('items.producto');
        return view('ordenes-compra.recepcion', compact('orden'));
    }

    /**
     * Registrar recepción de mercadería
     */
    public function recibirMercaderia(Request $request, OrdenCompra $orden)
    {
        if (!$orden->puedeRecibirMercaderia()) {
            return back()->with('error', 'No se puede recibir mercadería para esta orden');
        }

        $request->validate([
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->cantidades as $itemId => $cantidadRecibida) {
                $item = OrdenCompraItem::find($itemId);
                if (!$item || $item->orden_compra_id !== $orden->id) continue;

                $cantidadAnterior = $item->cantidad_recibida;
                $nuevaCantidad = $cantidadAnterior + $cantidadRecibida;
                $item->update(['cantidad_recibida' => $nuevaCantidad]);

                // Crear movimiento de inventario (ENTRADA)
                if ($cantidadRecibida > 0) {
                    $producto = $item->producto;
                    $stockAnterior = $producto->stock_actual;
                    $stockNuevo = $stockAnterior + $cantidadRecibida;

                    $producto->update(['stock_actual' => $stockNuevo]);

                    MovimientoInventario::create([
                        'producto_id' => $producto->id,
                        'tipo' => 'ENTRADA',
                        'cantidad' => $cantidadRecibida,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $stockNuevo,
                        'usuario_id' => Auth::id(),
                        'observaciones' => "Recepción de OC {$orden->numero}",
                    ]);
                }
            }

            // Actualizar estado de la orden
            $orden->refresh();
            if ($orden->verificarRecepcionCompleta()) {
                $orden->update([
                    'estado' => OrdenCompra::ESTADO_RECIBIDA_COMPLETA,
                    'fecha_recepcion' => now(),
                ]);

                // Actualizar pedido del cliente si existe
                if ($orden->pedidoCliente) {
                    $orden->pedidoCliente->update(['estado' => PedidoCliente::ESTADO_MERCADERIA_RECIBIDA]);
                }
            } else {
                $orden->update(['estado' => OrdenCompra::ESTADO_RECIBIDA_PARCIAL]);
            }

            DB::commit();
            return redirect()
                ->route('ordenes-compra.show', $orden)
                ->with('success', 'Recepción registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar recepción: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar orden
     */
    public function cancelar(Request $request, OrdenCompra $orden)
    {
        if (!$orden->puedeSerCancelada()) {
            return back()->with('error', 'Esta orden no puede ser cancelada');
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:500',
        ]);

        $orden->update([
            'estado' => OrdenCompra::ESTADO_CANCELADA,
            'motivo_cancelacion' => $request->motivo_cancelacion,
        ]);

        return redirect()
            ->route('ordenes-compra.show', $orden)
            ->with('success', 'Orden cancelada exitosamente');
    }
}
