@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $solicitud->numero }}</h1>
            <p class="text-gray-600 mt-1">{{ $solicitud->estado_descripcion }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('pdf.solicitud-presupuesto', $solicitud) }}" class="btn-secondary" target="_blank">PDF</a>
            <a href="{{ route('solicitudes-presupuesto.index') }}" class="btn-secondary">Volver</a>
        </div>
    </div>

    <!-- Acciones según estado -->
    @if($solicitud->estado === 'COTIZADA')
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-indigo-900">Cotización recibida del proveedor</p>
                    <p class="text-sm text-indigo-700">Total cotizado: {{ number_format($solicitud->total_cotizado, 0, ',', '.') }} Gs.</p>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('solicitudes-presupuesto.rechazar', $solicitud) }}" method="POST"
                          @submit.prevent="confirmSubmit($event, {
                              title: '¿Rechazar cotización?',
                              text: 'Se rechazará esta cotización del proveedor.',
                              confirmButtonText: 'Sí, rechazar'
                          })">
                        @csrf
                        <button type="submit" class="btn-danger">Rechazar</button>
                    </form>
                    <form action="{{ route('solicitudes-presupuesto.aceptar', $solicitud) }}" method="POST"
                          @submit.prevent="confirmSubmit($event, {
                              title: '¿Aceptar cotización?',
                              text: 'Se creará una orden de compra automáticamente.',
                              icon: 'question',
                              confirmButtonColor: '#16a34a',
                              confirmButtonText: 'Sí, aceptar y crear OC'
                          })">
                        @csrf
                        <button type="submit" class="btn-success">Aceptar y Crear OC</button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($solicitud->estado === 'ENVIADA')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-blue-900">Esperando respuesta del proveedor</p>
            @if($solicitud->fecha_limite_respuesta)
                <p class="text-sm text-blue-700">Fecha límite: {{ $solicitud->fecha_limite_respuesta->format('d/m/Y') }}</p>
            @endif
        </div>
    @elseif($solicitud->estado === 'SIN_STOCK')
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-orange-900">El proveedor indicó que no tiene stock disponible</p>
        </div>
    @elseif($solicitud->estado === 'ACEPTADA')
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-green-900">Cotización aceptada - Orden de compra generada</p>
        </div>
    @elseif($solicitud->estado === 'RECHAZADA')
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-red-900">Cotización rechazada</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Productos Solicitados -->
            <div class="form-section">
                <h2 class="form-section-title">Productos Solicitados</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-right">Solicitado</th>
                                @if(in_array($solicitud->estado, ['COTIZADA', 'ACEPTADA', 'RECHAZADA']))
                                    <th class="text-right">Disponible</th>
                                    <th class="text-right">Precio Unit.</th>
                                    <th class="text-right">Subtotal</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitud->items as $item)
                                <tr>
                                    <td>
                                        <div class="font-medium">{{ $item->producto->nombre }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                                    </td>
                                    <td class="text-right">{{ number_format($item->cantidad_solicitada, 3) }}</td>
                                    @if(in_array($solicitud->estado, ['COTIZADA', 'ACEPTADA', 'RECHAZADA']))
                                        <td class="text-right">
                                            @if($item->tiene_stock)
                                                <span class="text-green-600 font-medium">{{ number_format($item->cantidad_disponible, 3) }}</span>
                                            @else
                                                <span class="text-red-600">Sin stock</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($item->precio_unitario_cotizado, 0, ',', '.') }} Gs.</td>
                                        <td class="text-right font-bold">{{ number_format($item->subtotal_cotizado, 0, ',', '.') }} Gs.</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Solicitud de Cliente relacionada -->
            @if($solicitud->pedidoCliente)
                <div class="form-section">
                    <h2 class="form-section-title">Solicitud de Cliente Relacionada</h2>
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <a href="{{ route('pedidos-cliente.show', $solicitud->pedidoCliente) }}"
                                   class="font-medium text-blue-600 hover:text-blue-800">
                                    {{ $solicitud->pedidoCliente->numero }}
                                </a>
                                <span class="ml-2 text-gray-500">{{ $solicitud->pedidoCliente->cliente_nombre }}</span>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $solicitud->pedidoCliente->estado_color }}">
                                {{ str_replace('_', ' ', $solicitud->pedidoCliente->estado) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Panel lateral -->
        <div class="lg:col-span-1">
            <div class="form-section sticky top-4">
                <h2 class="form-section-title">Información</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $solicitud->estado_color }}">
                            {{ str_replace('_', ' ', $solicitud->estado) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Proveedor:</span>
                        <span class="font-semibold text-right">{{ $solicitud->proveedor->razon_social }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha:</span>
                        <span class="font-semibold">{{ $solicitud->fecha_solicitud->format('d/m/Y') }}</span>
                    </div>
                    @if($solicitud->fecha_limite_respuesta)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Límite resp.:</span>
                        <span class="font-semibold">{{ $solicitud->fecha_limite_respuesta->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($solicitud->total_cotizado > 0)
                    <div class="border-t pt-3 flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span class="text-blue-600">{{ number_format($solicitud->total_cotizado, 0, ',', '.') }} Gs.</span>
                    </div>
                    @endif
                    @if($solicitud->dias_entrega_estimados)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Días entrega:</span>
                        <span class="font-semibold">{{ $solicitud->dias_entrega_estimados }} días</span>
                    </div>
                    @endif
                </div>

                @if($solicitud->mensaje_solicitud)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600 font-medium mb-1">Mensaje:</p>
                    <p class="text-sm text-gray-800">{{ $solicitud->mensaje_solicitud }}</p>
                </div>
                @endif

                @if($solicitud->respuesta_proveedor)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600 font-medium mb-1">Respuesta del proveedor:</p>
                    <p class="text-sm text-gray-800">{{ $solicitud->respuesta_proveedor }}</p>
                </div>
                @endif

                <div class="mt-4 pt-4 border-t text-sm text-gray-500">
                    <p>Creado por: {{ $solicitud->usuario->name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
