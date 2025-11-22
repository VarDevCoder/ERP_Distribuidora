@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Solicitudes de Cotizacion</h1>
            <p class="text-gray-600 mt-1">Cotizaciones enviadas a proveedores</p>
        </div>
        <a href="{{ route('solicitudes-presupuesto.create') }}" class="btn-primary">
            + Nueva Solicitud
        </a>
    </div>

    <div class="form-section mb-6">
        <form action="{{ route('solicitudes-presupuesto.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="w-48">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="ENVIADA" {{ request('estado') == 'ENVIADA' ? 'selected' : '' }}>Enviada</option>
                    <option value="VISTA" {{ request('estado') == 'VISTA' ? 'selected' : '' }}>Vista</option>
                    <option value="COTIZADA" {{ request('estado') == 'COTIZADA' ? 'selected' : '' }}>Cotizada</option>
                    <option value="SIN_STOCK" {{ request('estado') == 'SIN_STOCK' ? 'selected' : '' }}>Sin Stock</option>
                    <option value="ACEPTADA" {{ request('estado') == 'ACEPTADA' ? 'selected' : '' }}>Aceptada</option>
                    <option value="RECHAZADA" {{ request('estado') == 'RECHAZADA' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>
            <div class="w-64">
                <label class="form-label">Proveedor</label>
                <select name="proveedor_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}" {{ request('proveedor_id') == $prov->id ? 'selected' : '' }}>
                            {{ $prov->razon_social }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrar</button>
            <a href="{{ route('solicitudes-presupuesto.index') }}" class="btn-secondary">Limpiar</a>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Proveedor</th>
                    <th>Pedido Cliente</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th class="text-right">Total Cotizado</th>
                    <th class="text-center">Accion</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $solicitud)
                    <tr class="{{ $solicitud->estado == 'COTIZADA' ? 'bg-green-50' : '' }}">
                        <td>
                            <a href="{{ route('solicitudes-presupuesto.show', $solicitud) }}" class="text-blue-600 hover:text-blue-900 font-bold">
                                {{ $solicitud->numero }}
                            </a>
                        </td>
                        <td class="font-medium">{{ $solicitud->proveedor->razon_social }}</td>
                        <td>
                            @if($solicitud->pedidoCliente)
                                <a href="{{ route('pedidos-cliente.show', $solicitud->pedidoCliente) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $solicitud->pedidoCliente->numero }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $solicitud->fecha_solicitud->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $solicitud->estado_color }}">
                                {{ str_replace('_', ' ', $solicitud->estado) }}
                            </span>
                        </td>
                        <td class="text-right font-bold whitespace-nowrap">
                            @if($solicitud->total_cotizado)
                                {{ number_format($solicitud->total_cotizado, 0, ',', '.') }} Gs.
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($solicitud->estado == 'COTIZADA')
                                <form action="{{ route('solicitudes-presupuesto.aceptar', $solicitud) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-success text-sm px-3 py-1">
                                        Aceptar
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('solicitudes-presupuesto.show', $solicitud) }}" class="text-blue-600 hover:underline font-medium">Ver</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">No hay solicitudes</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $solicitudes->links() }}</div>
</div>
@endsection
