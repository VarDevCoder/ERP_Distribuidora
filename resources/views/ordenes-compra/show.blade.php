@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $orden->numero }}</h1>
            <p class="text-gray-600 mt-1">{{ $orden->estado_descripcion }}</p>
        </div>
        <div class="flex space-x-2">
            @if($orden->estado === 'BORRADOR')
                <a href="{{ route('ordenes-compra.edit', $orden) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Editar</a>
            @endif
            <a href="{{ route('ordenes-compra.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
        </div>
    </div>

    <!-- Acciones según estado -->
    @if($orden->estado === 'BORRADOR')
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900">Orden en borrador</p>
                    <p class="text-sm text-gray-700">Envía la orden al proveedor cuando esté lista</p>
                </div>
                <form action="{{ route('ordenes-compra.enviar', $orden) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Enviar al Proveedor
                    </button>
                </form>
            </div>
        </div>
    @elseif($orden->estado === 'ENVIADA')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-blue-900">Esperando confirmación del proveedor</p>
                    <p class="text-sm text-blue-700">Confirma cuando el proveedor acepte la orden</p>
                </div>
                <form action="{{ route('ordenes-compra.confirmar', $orden) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                        Proveedor Confirmó
                    </button>
                </form>
            </div>
        </div>
    @elseif($orden->estado === 'CONFIRMADA')
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-indigo-900">Orden confirmada</p>
                    <p class="text-sm text-indigo-700">Marca cuando la mercadería esté en camino</p>
                </div>
                <form action="{{ route('ordenes-compra.en-transito', $orden) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 font-medium">
                        Marcar En Tránsito
                    </button>
                </form>
            </div>
        </div>
    @elseif(in_array($orden->estado, ['CONFIRMADA', 'EN_TRANSITO', 'RECIBIDA_PARCIAL']))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-yellow-900">Mercadería en camino o pendiente</p>
                    <p class="text-sm text-yellow-700">Registra la recepción de mercadería</p>
                </div>
                <a href="{{ route('ordenes-compra.form-recepcion', $orden) }}"
                   class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                    Registrar Recepción
                </a>
            </div>
        </div>
    @elseif($orden->estado === 'RECIBIDA_COMPLETA')
        <div class="bg-green-100 border border-green-300 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium text-green-900">Orden completada - Mercadería recibida</p>
                    @if($orden->fecha_recepcion)
                        <p class="text-sm text-green-700">Recibida el {{ $orden->fecha_recepcion->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    @elseif($orden->estado === 'CANCELADA')
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-red-900">Orden Cancelada</p>
            @if($orden->motivo_cancelacion)
                <p class="text-sm text-red-700 mt-1">Motivo: {{ $orden->motivo_cancelacion }}</p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Proveedor -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Proveedor</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Nombre:</label>
                        <p class="font-semibold">{{ $orden->proveedor_nombre }}</p>
                    </div>
                    @if($orden->proveedor_ruc)
                        <div>
                            <label class="text-sm text-gray-600">RUC:</label>
                            <p class="font-semibold">{{ $orden->proveedor_ruc }}</p>
                        </div>
                    @endif
                    @if($orden->proveedor_telefono)
                        <div>
                            <label class="text-sm text-gray-600">Teléfono:</label>
                            <p class="font-semibold">{{ $orden->proveedor_telefono }}</p>
                        </div>
                    @endif
                    @if($orden->proveedor_email)
                        <div>
                            <label class="text-sm text-gray-600">Email:</label>
                            <p class="font-semibold">{{ $orden->proveedor_email }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Productos</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Solicitado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Recibido</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orden->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->producto->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">{{ $item->cantidad_solicitada }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($item->cantidad_recibida >= $item->cantidad_solicitada)
                                        <span class="text-green-600 font-medium">{{ $item->cantidad_recibida }}</span>
                                    @elseif($item->cantidad_recibida > 0)
                                        <span class="text-yellow-600 font-medium">{{ $item->cantidad_recibida }}</span>
                                    @else
                                        <span class="text-gray-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->precio_unitario, 0, ',', '.') }} Gs.</td>
                                <td class="px-4 py-3 text-right font-semibold">{{ number_format($item->subtotal, 0, ',', '.') }} Gs.</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pedido Cliente relacionado -->
            @if($orden->pedidoCliente)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Pedido de Cliente Relacionado</h2>
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <a href="{{ route('pedidos-cliente.show', $orden->pedidoCliente) }}"
                                   class="font-medium text-blue-600 hover:text-blue-800">
                                    {{ $orden->pedidoCliente->numero }}
                                </a>
                                <span class="ml-2 text-gray-500">{{ $orden->pedidoCliente->cliente_nombre }}</span>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $orden->pedidoCliente->estado_color }}">
                                {{ str_replace('_', ' ', $orden->pedidoCliente->estado) }}
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
                        <span class="px-2 py-1 text-xs rounded-full {{ $orden->estado_color }}">
                            {{ str_replace('_', ' ', $orden->estado) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha Orden:</span>
                        <span class="font-semibold">{{ $orden->fecha_orden->format('d/m/Y') }}</span>
                    </div>
                    @if($orden->fecha_entrega_esperada)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Entrega Esperada:</span>
                            <span class="font-semibold">{{ $orden->fecha_entrega_esperada->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>

                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span>{{ number_format($orden->subtotal, 0, ',', '.') }} Gs.</span>
                    </div>
                    @if($orden->descuento > 0)
                        <div class="flex justify-between text-red-600">
                            <span>Descuento:</span>
                            <span>-{{ number_format($orden->descuento, 0, ',', '.') }} Gs.</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total:</span>
                        <span class="text-blue-600">{{ number_format($orden->total, 0, ',', '.') }} Gs.</span>
                    </div>
                </div>

                @if($orden->puedeSerCancelada())
                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('ordenes-compra.cancelar', $orden) }}" method="POST"
                              onsubmit="return confirm('¿Cancelar esta orden?')">
                            @csrf
                            <input type="text" name="motivo_cancelacion" placeholder="Motivo de cancelación..."
                                   class="w-full mb-2 rounded-lg border-gray-300 text-sm" required>
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                                Cancelar Orden
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
