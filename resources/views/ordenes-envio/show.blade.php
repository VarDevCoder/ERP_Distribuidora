@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $orden->numero }}</h1>
            <p class="text-gray-600 mt-1">{{ $orden->estado_descripcion }}</p>
        </div>
        <a href="{{ route('ordenes-envio.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
    </div>

    <!-- Acciones según estado -->
    @if($orden->estado === 'PREPARANDO')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-yellow-900">Preparando envío</p>
                    <p class="text-sm text-yellow-700">Marca como listo cuando termine de preparar el envío</p>
                </div>
                <form action="{{ route('ordenes-envio.lista-despachar', $orden) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Marcar como Listo
                    </button>
                </form>
            </div>
        </div>
    @elseif($orden->estado === 'LISTO')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-blue-900">Listo para despachar</p>
                    <p class="text-sm text-blue-700">Despacha el envío para descontar el stock</p>
                </div>
                <form action="{{ route('ordenes-envio.despachar', $orden) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="numero_guia" placeholder="Número de guía (opcional)"
                           class="rounded-lg border-gray-300 shadow-sm text-sm">
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        Despachar Envío
                    </button>
                </form>
            </div>
        </div>
    @elseif($orden->estado === 'EN_TRANSITO')
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-indigo-900">En tránsito</p>
                    <p class="text-sm text-indigo-700">
                        Enviado el {{ $orden->fecha_envio?->format('d/m/Y') }}
                        @if($orden->numero_guia) - Guía: {{ $orden->numero_guia }} @endif
                    </p>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('ordenes-envio.entregar', $orden) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            Marcar Entregado
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($orden->estado === 'ENTREGADO')
        <div class="bg-green-100 border border-green-300 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium text-green-900">Entregado exitosamente</p>
                    @if($orden->fecha_entrega)
                        <p class="text-sm text-green-700">Entregado el {{ $orden->fecha_entrega->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
        </div>
    @elseif($orden->estado === 'DEVUELTO')
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-orange-900">Envío Devuelto</p>
            @if($orden->observaciones_entrega)
                <p class="text-sm text-orange-700 mt-1">{{ $orden->observaciones_entrega }}</p>
            @endif
        </div>
    @elseif($orden->estado === 'CANCELADO')
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-red-900">Envío Cancelado</p>
            @if($orden->observaciones_entrega)
                <p class="text-sm text-red-700 mt-1">{{ $orden->observaciones_entrega }}</p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos de Entrega -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Datos de Entrega</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-sm text-gray-600">Dirección:</label>
                        <p class="font-semibold">{{ $orden->direccion_entrega }}</p>
                    </div>
                    @if($orden->contacto_entrega)
                        <div>
                            <label class="text-sm text-gray-600">Contacto:</label>
                            <p class="font-semibold">{{ $orden->contacto_entrega }}</p>
                        </div>
                    @endif
                    @if($orden->telefono_entrega)
                        <div>
                            <label class="text-sm text-gray-600">Teléfono:</label>
                            <p class="font-semibold">{{ $orden->telefono_entrega }}</p>
                        </div>
                    @endif
                    @if($orden->metodo_envio)
                        <div>
                            <label class="text-sm text-gray-600">Método:</label>
                            <p class="font-semibold">{{ str_replace('_', ' ', $orden->metodo_envio) }}</p>
                        </div>
                    @endif
                    @if($orden->transportista)
                        <div>
                            <label class="text-sm text-gray-600">Transportista:</label>
                            <p class="font-semibold">{{ $orden->transportista }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Productos -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Productos</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($orden->items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $item->producto->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">{{ $item->cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pedido Cliente -->
            @if($orden->pedidoCliente)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Pedido de Cliente</h2>
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

            <!-- Notas -->
            @if($orden->notas)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Notas</h2>
                    <p class="text-gray-700">{{ $orden->notas }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información</h2>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $orden->estado_color }}">
                            {{ str_replace('_', ' ', $orden->estado) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Generación:</span>
                        <span class="font-semibold">{{ $orden->fecha_generacion->format('d/m/Y') }}</span>
                    </div>
                    @if($orden->fecha_envio)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Envío:</span>
                            <span class="font-semibold">{{ $orden->fecha_envio->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($orden->fecha_entrega)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Entrega:</span>
                            <span class="font-semibold">{{ $orden->fecha_entrega->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($orden->numero_guia)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Guía:</span>
                            <span class="font-semibold">{{ $orden->numero_guia }}</span>
                        </div>
                    @endif
                </div>

                @if($orden->estado === 'EN_TRANSITO')
                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('ordenes-envio.devolver', $orden) }}" method="POST"
                              onsubmit="return confirm('¿Registrar devolución? Se restaurará el stock.')">
                            @csrf
                            <input type="text" name="observaciones_entrega" placeholder="Motivo de devolución..."
                                   class="w-full mb-2 rounded-lg border-gray-300 text-sm" required>
                            <button type="submit" class="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700">
                                Registrar Devolución
                            </button>
                        </form>
                    </div>
                @endif

                @if($orden->puedeSerCancelada())
                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('ordenes-envio.cancelar', $orden) }}" method="POST"
                              onsubmit="return confirm('¿Cancelar este envío?')">
                            @csrf
                            <input type="text" name="observaciones_entrega" placeholder="Motivo de cancelación..."
                                   class="w-full mb-2 rounded-lg border-gray-300 text-sm" required>
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                                Cancelar Envío
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
