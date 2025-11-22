@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Órdenes de Compra</h1>
            <p class="text-gray-600 mt-1">Flujo ANKOR - Gestiona las compras a proveedores</p>
        </div>
        <a href="{{ route('ordenes-compra.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            + Nueva Orden
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('ordenes-compra.index') }}" method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Número, proveedor..."
                       class="w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="rounded-lg border-gray-300 shadow-sm">
                    <option value="">Todos</option>
                    <option value="BORRADOR" {{ request('estado') == 'BORRADOR' ? 'selected' : '' }}>Borrador</option>
                    <option value="ENVIADA" {{ request('estado') == 'ENVIADA' ? 'selected' : '' }}>Enviada</option>
                    <option value="CONFIRMADA" {{ request('estado') == 'CONFIRMADA' ? 'selected' : '' }}>Confirmada</option>
                    <option value="EN_TRANSITO" {{ request('estado') == 'EN_TRANSITO' ? 'selected' : '' }}>En Tránsito</option>
                    <option value="RECIBIDA_PARCIAL" {{ request('estado') == 'RECIBIDA_PARCIAL' ? 'selected' : '' }}>Recibida Parcial</option>
                    <option value="RECIBIDA_COMPLETA" {{ request('estado') == 'RECIBIDA_COMPLETA' ? 'selected' : '' }}>Recibida Completa</option>
                    <option value="CANCELADA" {{ request('estado') == 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filtrar</button>
            <a href="{{ route('ordenes-compra.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Limpiar</a>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($ordenes as $orden)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('ordenes-compra.show', $orden) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                {{ $orden->numero }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $orden->proveedor_nombre }}</div>
                            @if($orden->proveedor_ruc)
                                <div class="text-sm text-gray-500">{{ $orden->proveedor_ruc }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($orden->pedidoCliente)
                                <a href="{{ route('pedidos-cliente.show', $orden->pedidoCliente) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $orden->pedidoCliente->numero }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $orden->fecha_orden->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $orden->estado_color }}">
                                {{ str_replace('_', ' ', $orden->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                            {{ number_format($orden->total, 0, ',', '.') }} Gs.
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <a href="{{ route('ordenes-compra.show', $orden) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay órdenes de compra</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $ordenes->links() }}</div>
</div>
@endsection
