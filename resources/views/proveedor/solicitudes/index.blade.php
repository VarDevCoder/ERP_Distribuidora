@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Mis Solicitudes de Presupuesto</h1>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('proveedor.solicitudes') }}" method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="rounded-lg border-gray-300 shadow-sm">
                    <option value="">Todos</option>
                    <option value="ENVIADA" {{ request('estado') == 'ENVIADA' ? 'selected' : '' }}>Pendientes</option>
                    <option value="VISTA" {{ request('estado') == 'VISTA' ? 'selected' : '' }}>Vistas</option>
                    <option value="COTIZADA" {{ request('estado') == 'COTIZADA' ? 'selected' : '' }}>Cotizadas</option>
                    <option value="ACEPTADA" {{ request('estado') == 'ACEPTADA' ? 'selected' : '' }}>Aceptadas</option>
                    <option value="RECHAZADA" {{ request('estado') == 'RECHAZADA' ? 'selected' : '' }}>Rechazadas</option>
                    <option value="SIN_STOCK" {{ request('estado') == 'SIN_STOCK' ? 'selected' : '' }}>Sin Stock</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filtrar</button>
            <a href="{{ route('proveedor.solicitudes') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Limpiar</a>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Productos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Cotizado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($solicitudes as $solicitud)
                    <tr class="{{ $solicitud->puedeSerRespondida() ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $solicitud->numero }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $solicitud->fecha_solicitud->format('d/m/Y') }}
                            @if($solicitud->fecha_limite_respuesta)
                                <div class="text-xs text-red-500">Límite: {{ $solicitud->fecha_limite_respuesta->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $solicitud->items->count() }} productos</td>
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
                            @if($solicitud->puedeSerRespondida())
                                <a href="{{ route('proveedor.solicitud.responder', $solicitud) }}"
                                   class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                    Responder
                                </a>
                            @else
                                <a href="{{ route('proveedor.solicitud.ver', $solicitud) }}"
                                   class="text-blue-600 hover:underline text-sm">Ver</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay solicitudes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>
</div>
@endsection
