@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Pedidos de Clientes</h1>
            <p class="text-gray-600 mt-1">Flujo ANKOR - Gestiona los pedidos de tus clientes</p>
        </div>
        <a href="{{ route('pedidos-cliente.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-medium">
            + Nuevo Pedido
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('pedidos-cliente.index') }}" method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Número, cliente, RUC..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="RECIBIDO" {{ request('estado') == 'RECIBIDO' ? 'selected' : '' }}>Recibido</option>
                    <option value="EN_PROCESO" {{ request('estado') == 'EN_PROCESO' ? 'selected' : '' }}>En Proceso</option>
                    <option value="ORDEN_COMPRA" {{ request('estado') == 'ORDEN_COMPRA' ? 'selected' : '' }}>Orden Compra</option>
                    <option value="MERCADERIA_RECIBIDA" {{ request('estado') == 'MERCADERIA_RECIBIDA' ? 'selected' : '' }}>Mercadería Recibida</option>
                    <option value="LISTO_ENVIO" {{ request('estado') == 'LISTO_ENVIO' ? 'selected' : '' }}>Listo Envío</option>
                    <option value="ENVIADO" {{ request('estado') == 'ENVIADO' ? 'selected' : '' }}>Enviado</option>
                    <option value="ENTREGADO" {{ request('estado') == 'ENTREGADO' ? 'selected' : '' }}>Entregado</option>
                    <option value="CANCELADO" {{ request('estado') == 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filtrar</button>
            <a href="{{ route('pedidos-cliente.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Limpiar</a>
        </form>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pedidos as $pedido)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('pedidos-cliente.show', $pedido) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                {{ $pedido->numero }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $pedido->cliente_nombre }}</div>
                            @if($pedido->cliente_ruc)
                                <div class="text-sm text-gray-500">{{ $pedido->cliente_ruc }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $pedido->fecha_pedido->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $pedido->estado_color }}">
                                {{ str_replace('_', ' ', $pedido->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            {{ number_format($pedido->total, 0, ',', '.') }} Gs.
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <a href="{{ route('pedidos-cliente.show', $pedido) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay pedidos registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $pedidos->links() }}
    </div>
</div>
@endsection
