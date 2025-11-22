@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $pedido->numero }}</h1>
            <p class="text-gray-600 mt-1">{{ $pedido->estado_descripcion }}</p>
        </div>
        <div class="flex space-x-2">
            @if($pedido->puedeSerCancelado() && !in_array($pedido->estado, ['ENVIADO', 'ENTREGADO']))
                <a href="{{ route('pedidos-cliente.edit', $pedido) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Editar</a>
            @endif
            <a href="{{ route('pedidos-cliente.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
        </div>
    </div>

    <!-- Flujo de Estado -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Estado del Flujo</h2>
        <div class="flex items-center justify-between overflow-x-auto pb-2">
            @php
                $estados = ['RECIBIDO', 'EN_PROCESO', 'ORDEN_COMPRA', 'MERCADERIA_RECIBIDA', 'LISTO_ENVIO', 'ENVIADO', 'ENTREGADO'];
                $estadoActualIndex = array_search($pedido->estado, $estados);
            @endphp
            @foreach($estados as $i => $estado)
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $i <= $estadoActualIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                            {{ $i + 1 }}
                        </div>
                        <span class="text-xs mt-1 text-center w-20">{{ str_replace('_', ' ', $estado) }}</span>
                    </div>
                    @if($i < count($estados) - 1)
                        <div class="w-8 h-1 mx-1 {{ $i < $estadoActualIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Acciones según estado -->
    @if($pedido->estado === 'RECIBIDO')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-blue-900">Pedido recibido - Iniciar procesamiento</p>
                    <p class="text-sm text-blue-700">Marca el pedido como en proceso para comenzar a gestionar las compras</p>
                </div>
                <form action="{{ route('pedidos-cliente.procesar', $pedido) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Procesar Pedido
                    </button>
                </form>
            </div>
        </div>
    @elseif(in_array($pedido->estado, ['EN_PROCESO', 'PRESUPUESTADO']))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-yellow-900">En proceso - Generar orden de compra</p>
                    <p class="text-sm text-yellow-700">Crea una orden de compra para el proveedor</p>
                </div>
                <a href="{{ route('ordenes-compra.create', ['pedido_cliente_id' => $pedido->id]) }}"
                   class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                    Crear Orden de Compra
                </a>
            </div>
        </div>
    @elseif($pedido->estado === 'MERCADERIA_RECIBIDA')
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-green-900">Mercadería recibida - Generar orden de envío</p>
                    <p class="text-sm text-green-700">Crea la orden de envío para el cliente</p>
                </div>
                <a href="{{ route('ordenes-envio.create', ['pedido_cliente_id' => $pedido->id]) }}"
                   class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Generar Orden de Envío
                </a>
            </div>
        </div>
    @elseif($pedido->estado === 'ENTREGADO')
        <div class="bg-green-100 border border-green-300 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="font-medium text-green-900">Pedido completado y entregado al cliente</p>
            </div>
        </div>
    @elseif($pedido->estado === 'CANCELADO')
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-red-900">Pedido Cancelado</p>
            @if($pedido->motivo_cancelacion)
                <p class="text-sm text-red-700 mt-1">Motivo: {{ $pedido->motivo_cancelacion }}</p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos del Cliente -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Cliente</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Nombre:</label>
                        <p class="font-semibold">{{ $pedido->cliente_nombre }}</p>
                    </div>
                    @if($pedido->cliente_ruc)
                        <div>
                            <label class="text-sm text-gray-600">RUC:</label>
                            <p class="font-semibold">{{ $pedido->cliente_ruc }}</p>
                        </div>
                    @endif
                    @if($pedido->cliente_telefono)
                        <div>
                            <label class="text-sm text-gray-600">Teléfono:</label>
                            <p class="font-semibold">{{ $pedido->cliente_telefono }}</p>
                        </div>
                    @endif
                    @if($pedido->cliente_email)
                        <div>
                            <label class="text-sm text-gray-600">Email:</label>
                            <p class="font-semibold">{{ $pedido->cliente_email }}</p>
                        </div>
                    @endif
                    @if($pedido->cliente_direccion)
                        <div class="col-span-2">
                            <label class="text-sm text-gray-600">Dirección:</label>
                            <p class="font-semibold">{{ $pedido->cliente_direccion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items del Pedido -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Productos Solicitados</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pedido->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->producto->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ $item->cantidad }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->precio_unitario, 0, ',', '.') }} Gs.</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ number_format($item->subtotal, 0, ',', '.') }} Gs.</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Órdenes de Compra Relacionadas -->
            @if($pedido->ordenesCompra->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Órdenes de Compra</h2>
                    @foreach($pedido->ordenesCompra as $oc)
                        <div class="border rounded-lg p-4 mb-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    <a href="{{ route('ordenes-compra.show', $oc) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ $oc->numero }}
                                    </a>
                                    <span class="ml-2 text-sm text-gray-500">{{ $oc->proveedor_nombre }}</span>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $oc->estado_color }}">{{ $oc->estado }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Orden de Envío -->
            @if($pedido->ordenEnvio)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Orden de Envío</h2>
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('ordenes-envio.show', $pedido->ordenEnvio) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                {{ $pedido->ordenEnvio->numero }}
                            </a>
                            <span class="px-2 py-1 text-xs rounded-full {{ $pedido->ordenEnvio->estado_color }}">
                                {{ $pedido->ordenEnvio->estado }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Resumen</h2>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $pedido->estado_color }}">
                            {{ str_replace('_', ' ', $pedido->estado) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha Pedido:</span>
                        <span class="font-semibold">{{ $pedido->fecha_pedido->format('d/m/Y') }}</span>
                    </div>
                    @if($pedido->fecha_entrega_solicitada)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Entrega Solicitada:</span>
                            <span class="font-semibold">{{ $pedido->fecha_entrega_solicitada->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>

                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span>{{ number_format($pedido->subtotal, 0, ',', '.') }} Gs.</span>
                    </div>
                    @if($pedido->descuento > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Descuento:</span>
                            <span>-{{ number_format($pedido->descuento, 0, ',', '.') }} Gs.</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total:</span>
                        <span class="text-blue-600">{{ number_format($pedido->total, 0, ',', '.') }} Gs.</span>
                    </div>
                </div>

                @if($pedido->puedeSerCancelado())
                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('pedidos-cliente.cancelar', $pedido) }}" method="POST"
                              onsubmit="return confirm('¿Cancelar este pedido?')">
                            @csrf
                            <input type="text" name="motivo_cancelacion" placeholder="Motivo de cancelación..."
                                   class="w-full mb-2 rounded-lg border-gray-300 text-sm" required>
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                                Cancelar Pedido
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
