<?php

namespace App\Http\Controllers;

use App\Models\PedidoCliente;
use App\Models\PedidoClienteItem;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoClienteController extends Controller
{
    /**
     * Listar todos los pedidos
     */
    public function index(Request $request)
    {
        $query = PedidoCliente::with(['items.producto', 'usuario'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('numero', 'like', "%{$buscar}%")
                  ->orWhere('cliente_nombre', 'like', "%{$buscar}%")
                  ->orWhere('cliente_ruc', 'like', "%{$buscar}%");
            });
        }

        $pedidos = $query->paginate(15);

        return view('pedidos-cliente.index', compact('pedidos'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $productos = Producto::orderBy('nombre')->get();
        return view('pedidos-cliente.create', compact('productos'));
    }

    /**
     * Guardar nuevo pedido
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'cliente_ruc' => 'nullable|string|max:50',
            'cliente_telefono' => 'nullable|string|max:50',
            'cliente_email' => 'nullable|email|max:255',
            'cliente_direccion' => 'nullable|string|max:500',
            'fecha_pedido' => 'required|date',
            'fecha_entrega_solicitada' => 'nullable|date|after_or_equal:fecha_pedido',
            'notas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.001',
            'items.*.precio_unitario' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pedido = PedidoCliente::create([
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_ruc' => $request->cliente_ruc,
                'cliente_telefono' => $request->cliente_telefono,
                'cliente_email' => $request->cliente_email,
                'cliente_direccion' => $request->cliente_direccion,
                'fecha_pedido' => $request->fecha_pedido,
                'fecha_entrega_solicitada' => $request->fecha_entrega_solicitada,
                'notas' => $request->notas,
                'estado' => PedidoCliente::ESTADO_RECIBIDO,
                'descuento' => $request->descuento ?? 0,
                'usuario_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                PedidoClienteItem::create([
                    'pedido_cliente_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            DB::commit();
            return redirect()
                ->route('pedidos-cliente.show', $pedido)
                ->with('success', "Pedido {$pedido->numero} creado exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle del pedido
     */
    public function show(PedidoCliente $pedidosCliente)
    {
        $pedido = $pedidosCliente->load(['items.producto', 'ordenesCompra', 'ordenEnvio', 'usuario']);
        return view('pedidos-cliente.show', compact('pedido'));
    }

    /**
     * Formulario de edición
     */
    public function edit(PedidoCliente $pedidosCliente)
    {
        $pedido = $pedidosCliente;
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Este pedido no puede ser editado');
        }

        $productos = Producto::orderBy('nombre')->get();
        return view('pedidos-cliente.edit', compact('pedido', 'productos'));
    }

    /**
     * Actualizar pedido
     */
    public function update(Request $request, PedidoCliente $pedidosCliente)
    {
        $pedido = $pedidosCliente;
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Este pedido no puede ser modificado');
        }

        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'cliente_ruc' => 'nullable|string|max:50',
            'cliente_telefono' => 'nullable|string|max:50',
            'cliente_email' => 'nullable|email|max:255',
            'cliente_direccion' => 'nullable|string|max:500',
            'fecha_entrega_solicitada' => 'nullable|date',
            'notas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.001',
            'items.*.precio_unitario' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pedido->update([
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_ruc' => $request->cliente_ruc,
                'cliente_telefono' => $request->cliente_telefono,
                'cliente_email' => $request->cliente_email,
                'cliente_direccion' => $request->cliente_direccion,
                'fecha_entrega_solicitada' => $request->fecha_entrega_solicitada,
                'notas' => $request->notas,
                'descuento' => $request->descuento ?? 0,
            ]);

            // Eliminar items anteriores y crear nuevos
            $pedido->items()->delete();

            foreach ($request->items as $item) {
                PedidoClienteItem::create([
                    'pedido_cliente_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            DB::commit();
            return redirect()
                ->route('pedidos-cliente.show', $pedido)
                ->with('success', 'Pedido actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar pedido
     */
    public function destroy(PedidoCliente $pedidosCliente)
    {
        $pedido = $pedidosCliente;
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Este pedido no puede ser eliminado');
        }

        $numero = $pedido->numero;
        $pedido->delete();

        return redirect()
            ->route('pedidos-cliente.index')
            ->with('success', "Pedido {$numero} eliminado exitosamente");
    }

    /**
     * Cambiar estado: Procesar pedido (iniciar flujo)
     */
    public function procesar(PedidoCliente $pedido)
    {
        if ($pedido->estado !== PedidoCliente::ESTADO_RECIBIDO) {
            return back()->with('error', 'Solo se pueden procesar pedidos recién recibidos');
        }

        $pedido->update(['estado' => PedidoCliente::ESTADO_EN_PROCESO]);

        return redirect()
            ->route('pedidos-cliente.show', $pedido)
            ->with('success', 'Pedido marcado como en proceso. Ahora puede generar órdenes de compra a proveedores.');
    }

    /**
     * Cancelar pedido
     */
    public function cancelar(Request $request, PedidoCliente $pedido)
    {
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Este pedido no puede ser cancelado');
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:500',
        ]);

        $pedido->update([
            'estado' => PedidoCliente::ESTADO_CANCELADO,
            'motivo_cancelacion' => $request->motivo_cancelacion,
        ]);

        return redirect()
            ->route('pedidos-cliente.show', $pedido)
            ->with('success', 'Pedido cancelado exitosamente');
    }

    /**
     * Marcar mercadería como recibida (del proveedor)
     */
    public function marcarMercaderiaRecibida(PedidoCliente $pedido)
    {
        if ($pedido->estado !== PedidoCliente::ESTADO_ORDEN_COMPRA) {
            return back()->with('error', 'El pedido debe tener orden de compra activa');
        }

        $pedido->update(['estado' => PedidoCliente::ESTADO_MERCADERIA_RECIBIDA]);

        return redirect()
            ->route('pedidos-cliente.show', $pedido)
            ->with('success', 'Mercadería marcada como recibida. Ahora puede generar la orden de envío.');
    }
}
