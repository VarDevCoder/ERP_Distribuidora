<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\PedidoCliente;
use App\Models\PedidoClienteItem;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\ProveedorProducto;
use App\Models\SolicitudPresupuesto;
use App\Models\SolicitudPresupuestoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PedidoClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = PedidoCliente::with(['items.producto', 'usuario'])
            ->orderBy('created_at', 'desc');

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

        $pedidos = $query->paginate(config('ankor.pagination.per_page', 15));

        return view('pedidos-cliente.index', compact('pedidos'));
    }

    public function create()
    {
        $clientes = Cliente::active()->orderBy('nombre')->get();
        $productos = Producto::active()->with('categoria')->orderBy('nombre')->get()->groupBy(fn($p) => $p->categoria?->nombre ?? 'Sin Categoría');
        return view('pedidos-cliente.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
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
            // Si se seleccionó un cliente, obtener sus datos para snapshot
            $clienteData = [
                'cliente_id' => $request->cliente_id,
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_ruc' => $request->cliente_ruc,
                'cliente_telefono' => $request->cliente_telefono,
                'cliente_email' => $request->cliente_email,
                'cliente_direccion' => $request->cliente_direccion,
            ];

            // Si hay cliente_id, sobrescribir con datos actuales del cliente
            if ($request->cliente_id) {
                $cliente = Cliente::find($request->cliente_id);
                if ($cliente) {
                    $clienteData = array_merge($clienteData, [
                        'cliente_nombre' => $cliente->nombre,
                        'cliente_ruc' => $cliente->ruc,
                        'cliente_telefono' => $cliente->telefono,
                        'cliente_email' => $cliente->email,
                        'cliente_direccion' => $cliente->direccion,
                    ]);
                }
            }

            $pedido = PedidoCliente::create(array_merge($clienteData, [
                'fecha_pedido' => $request->fecha_pedido,
                'fecha_entrega_solicitada' => $request->fecha_entrega_solicitada,
                'notas' => $request->notas,
                'estado' => PedidoCliente::ESTADO_RECIBIDO,
                'descuento' => $request->descuento ?? 0,
                'usuario_id' => Auth::id(),
            ]));

            foreach ($request->items as $item) {
                PedidoClienteItem::create([
                    'pedido_cliente_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            $pedido->load('items');
            $pedido->calcularTotales();

            DB::commit();
            return redirect()
                ->route('pedidos-cliente.show', $pedido)
                ->with('success', "Solicitud {$pedido->numero} creada exitosamente");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear solicitud', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al crear la solicitud. Intente nuevamente.');
        }
    }

    public function show(PedidoCliente $pedidoCliente)
    {
        $pedido = $pedidoCliente->load([
            'items.producto',
            'ordenesCompra',
            'ordenEnvio',
            'usuario',
            'solicitudesPresupuesto.proveedor',
            'solicitudesPresupuesto.items.producto',
        ]);

        $cotizacionesCotizadas = $pedido->solicitudesPresupuesto
            ->where('estado', SolicitudPresupuesto::ESTADO_COTIZADA);

        // Obtener comparación de precios del catálogo de proveedores
        $productosIds = $pedido->items->pluck('producto_id')->toArray();

        $preciosCatalogo = ProveedorProducto::whereIn('producto_id', $productosIds)
            ->where('disponible', true)
            ->with(['proveedor.user', 'producto'])
            ->get()
            ->groupBy('producto_id');

        // Construir matriz de comparación desde catálogo
        $comparacionCatalogo = [];
        $proveedoresEnCatalogo = collect();

        foreach ($pedido->items as $item) {
            $preciosProducto = $preciosCatalogo->get($item->producto_id, collect());

            foreach ($preciosProducto as $pp) {
                if ($pp->proveedor && $pp->proveedor->user && $pp->proveedor->user->activo) {
                    $proveedoresEnCatalogo->put($pp->proveedor_id, $pp->proveedor);

                    if (!isset($comparacionCatalogo[$item->producto_id])) {
                        $comparacionCatalogo[$item->producto_id] = [
                            'producto' => $item->producto,
                            'cantidad' => $item->cantidad,
                            'precios' => [],
                        ];
                    }

                    $comparacionCatalogo[$item->producto_id]['precios'][$pp->proveedor_id] = [
                        'precio' => $pp->precio,
                        'tiempo_entrega' => $pp->tiempo_entrega_dias,
                        'disponible' => $pp->disponible,
                    ];
                }
            }
        }

        return view('pedidos-cliente.show', compact('pedido', 'cotizacionesCotizadas', 'comparacionCatalogo', 'proveedoresEnCatalogo'));
    }

    public function edit(PedidoCliente $pedidoCliente)
    {
        $pedido = $pedidoCliente;
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Esta solicitud no puede ser editada');
        }

        $clientes = Cliente::active()->orderBy('nombre')->get();
        $productos = Producto::active()->with('categoria')->orderBy('nombre')->get()->groupBy(fn($p) => $p->categoria?->nombre ?? 'Sin Categoría');
        return view('pedidos-cliente.edit', compact('pedido', 'clientes', 'productos'));
    }

    public function update(Request $request, PedidoCliente $pedidoCliente)
    {
        $pedido = $pedidoCliente;
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Esta solicitud no puede ser modificada');
        }

        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
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
            // Si se seleccionó un cliente, obtener sus datos para snapshot
            $clienteData = [
                'cliente_id' => $request->cliente_id,
                'cliente_nombre' => $request->cliente_nombre,
                'cliente_ruc' => $request->cliente_ruc,
                'cliente_telefono' => $request->cliente_telefono,
                'cliente_email' => $request->cliente_email,
                'cliente_direccion' => $request->cliente_direccion,
            ];

            // Si hay cliente_id, sobrescribir con datos actuales del cliente
            if ($request->cliente_id) {
                $cliente = Cliente::find($request->cliente_id);
                if ($cliente) {
                    $clienteData = array_merge($clienteData, [
                        'cliente_nombre' => $cliente->nombre,
                        'cliente_ruc' => $cliente->ruc,
                        'cliente_telefono' => $cliente->telefono,
                        'cliente_email' => $cliente->email,
                        'cliente_direccion' => $cliente->direccion,
                    ]);
                }
            }

            $pedido->update(array_merge($clienteData, [
                'fecha_entrega_solicitada' => $request->fecha_entrega_solicitada,
                'notas' => $request->notas,
                'descuento' => $request->descuento ?? 0,
            ]));

            $pedido->items()->delete();

            foreach ($request->items as $item) {
                PedidoClienteItem::create([
                    'pedido_cliente_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            $pedido->load('items');
            $pedido->calcularTotales();

            DB::commit();
            return redirect()
                ->route('pedidos-cliente.show', $pedido)
                ->with('success', 'Solicitud actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar solicitud', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al actualizar la solicitud. Intente nuevamente.');
        }
    }

    public function destroy(PedidoCliente $pedidoCliente)
    {
        $pedido = $pedidoCliente;
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Esta solicitud no puede ser eliminada');
        }

        $numero = $pedido->numero;
        $pedido->delete();

        return redirect()
            ->route('pedidos-cliente.index')
            ->with('success', "Solicitud {$numero} eliminada exitosamente");
    }

    public function procesar(PedidoCliente $pedido)
    {
        if ($pedido->estado !== PedidoCliente::ESTADO_RECIBIDO) {
            return back()->with('error', 'Solo se pueden procesar solicitudes recién recibidas');
        }

        $pedido->update(['estado' => PedidoCliente::ESTADO_EN_PROCESO]);

        return redirect()
            ->route('pedidos-cliente.show', $pedido)
            ->with('success', 'Solicitud marcada como en proceso. Ahora puede solicitar cotizaciones a proveedores.');
    }

    public function cancelar(Request $request, PedidoCliente $pedido)
    {
        if (!$pedido->puedeSerCancelado()) {
            return back()->with('error', 'Esta solicitud no puede ser cancelada');
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
            ->with('success', 'Solicitud cancelada exitosamente');
    }

    public function marcarMercaderiaRecibida(PedidoCliente $pedido)
    {
        if ($pedido->estado !== PedidoCliente::ESTADO_ORDEN_COMPRA) {
            return back()->with('error', 'La solicitud debe tener orden de compra activa');
        }

        $pedido->update(['estado' => PedidoCliente::ESTADO_MERCADERIA_RECIBIDA]);

        return redirect()
            ->route('pedidos-cliente.show', $pedido)
            ->with('success', 'Mercadería marcada como recibida. Ahora puede generar la orden de envío.');
    }

    public function solicitarCotizacionTodos(PedidoCliente $pedido)
    {
        if (!in_array($pedido->estado, [PedidoCliente::ESTADO_RECIBIDO, PedidoCliente::ESTADO_EN_PROCESO, PedidoCliente::ESTADO_PRESUPUESTADO])) {
            return back()->with('error', 'La solicitud no está en un estado válido para cotizar.');
        }

        $proveedores = Proveedor::whereHas('user', fn($q) => $q->where('activo', true))->get();

        if ($proveedores->isEmpty()) {
            return back()->with('error', 'No hay proveedores activos en el sistema.');
        }

        $pedido->load('items');

        DB::beginTransaction();
        try {
            $creadas = 0;

            foreach ($proveedores as $proveedor) {
                $yaExiste = SolicitudPresupuesto::where('pedido_cliente_id', $pedido->id)
                    ->where('proveedor_id', $proveedor->id)
                    ->whereNotIn('estado', [SolicitudPresupuesto::ESTADO_RECHAZADA, SolicitudPresupuesto::ESTADO_VENCIDA])
                    ->exists();

                if ($yaExiste) {
                    continue;
                }

                $solicitud = SolicitudPresupuesto::create([
                    'pedido_cliente_id' => $pedido->id,
                    'proveedor_id' => $proveedor->id,
                    'usuario_id' => Auth::id(),
                    'fecha_solicitud' => now(),
                    'estado' => SolicitudPresupuesto::ESTADO_ENVIADA,
                    'mensaje_solicitud' => "Solicitud de cotización generada automáticamente desde {$pedido->numero}.",
                ]);

                foreach ($pedido->items as $item) {
                    SolicitudPresupuestoItem::create([
                        'solicitud_presupuesto_id' => $solicitud->id,
                        'producto_id' => $item->producto_id,
                        'cantidad_solicitada' => $item->cantidad,
                    ]);
                }

                $creadas++;
            }

            if ($pedido->estado === PedidoCliente::ESTADO_RECIBIDO) {
                $pedido->update(['estado' => PedidoCliente::ESTADO_EN_PROCESO]);
            }

            DB::commit();

            if ($creadas === 0) {
                return back()->with('error', 'Ya existen cotizaciones activas para todos los proveedores.');
            }

            return redirect()
                ->route('pedidos-cliente.show', $pedido)
                ->with('success', "Se enviaron {$creadas} solicitudes de cotización a proveedores.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al solicitar cotización masiva', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Error al enviar las solicitudes. Intente nuevamente.');
        }
    }
}
