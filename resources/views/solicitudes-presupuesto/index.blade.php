@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Solicitudes de Cotización</h1>
            <p class="text-gray-600 mt-1">Cotizaciones enviadas a proveedores</p>
        </div>
        <a href="{{ route('solicitudes-presupuesto.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            + Nueva Solicitud
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('solicitudes-presupuesto.index') }}" method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="rounded-lg border-gray-300 shadow-sm">
                    <option value="">Todos</option>
                    <option value="ENVIADA" {{ request('estado') == 'ENVIADA' ? 'selected' : '' }}>Enviada</option>
                    <option value="VISTA" {{ request('estado') == 'VISTA' ? 'selected' : '' }}>Vista</option>
                    <option value="COTIZADA" {{ request('estado') == 'COTIZADA' ? 'selected' : '' }}>Cotizada</option>
                    <option value="SIN_STOCK" {{ request('estado') == 'SIN_STOCK' ? 'selected' : '' }}>Sin Stock</option>
                    <option value="ACEPTADA" {{ request('estado') == 'ACEPTADA' ? 'selected' : '' }}>Aceptada</option>
                    <option value="RECHAZADA" {{ request('estado') == 'RECHAZADA' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                <select name="proveedor_id" class="rounded-lg border-gray-300 shadow-sm">
                    <option value="">Todos</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}" {{ request('proveedor_id') == $prov->id ? 'selected' : '' }}>
                            {{ $prov->razon_social }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filtrar</button>
            <a href="{{ route('solicitudes-presupuesto.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Limpiar</a>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Cotizado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($solicitudes as $solicitud)
                    <tr class="{{ $solicitud->estado == 'COTIZADA' ? 'bg-green-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('solicitudes-presupuesto.show', $solicitud) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                {{ $solicitud->numero }}
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ $solicitud->proveedor->razon_social }}</td>
                        <td class="px-6 py-4">
                            @if($solicitud->pedidoCliente)
                                <a href="{{ route('pedidos-cliente.show', $solicitud->pedidoCliente) }}" class="text-blue-600 hover:underline">
                                    {{ $solicitud->pedidoCliente->numero }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $solicitud->fecha_solicitud->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $solicitud->estado_color }}">
                                {{ str_replace('_', ' ', $solicitud->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            @if($solicitud->total_cotizado)
                                {{ number_format($solicitud->total_cotizado, 0, ',', '.') }} Gs.
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($solicitud->estado == 'COTIZADA')
                                <form action="{{ route('solicitudes-presupuesto.aceptar', $solicitud) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                        Aceptar
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('solicitudes-presupuesto.show', $solicitud) }}" class="text-blue-600 hover:underline">Ver</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay solicitudes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $solicitudes->links() }}</div>
</div>
@endsection
