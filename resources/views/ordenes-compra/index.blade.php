@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <div>
            <h1 class="page-title">Ordenes de Compra</h1>
            <p class="page-subtitle">Gestiona las compras a proveedores</p>
        </div>
        <a href="{{ route('ordenes-compra.create') }}" class="btn-primary">
            + Nueva Orden
        </a>
    </div>

    <!-- Filtros -->
    <div class="form-section mb-6">
        <form action="{{ route('ordenes-compra.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Numero, proveedor..."
                       class="form-input">
            </div>
            <div class="w-48">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="BORRADOR" {{ request('estado') == 'BORRADOR' ? 'selected' : '' }}>Borrador</option>
                    <option value="ENVIADA" {{ request('estado') == 'ENVIADA' ? 'selected' : '' }}>Enviada</option>
                    <option value="CONFIRMADA" {{ request('estado') == 'CONFIRMADA' ? 'selected' : '' }}>Confirmada</option>
                    <option value="EN_TRANSITO" {{ request('estado') == 'EN_TRANSITO' ? 'selected' : '' }}>En Transito</option>
                    <option value="RECIBIDA_PARCIAL" {{ request('estado') == 'RECIBIDA_PARCIAL' ? 'selected' : '' }}>Recibida Parcial</option>
                    <option value="RECIBIDA_COMPLETA" {{ request('estado') == 'RECIBIDA_COMPLETA' ? 'selected' : '' }}>Recibida Completa</option>
                    <option value="CANCELADA" {{ request('estado') == 'CANCELADA' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrar</button>
            <a href="{{ route('ordenes-compra.index') }}" class="btn-secondary">Limpiar</a>
        </form>
    </div>

    <!-- Tabla -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Proveedor</th>
                    <th>Solicitud</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                    <tr>
                        <td>
                            <a href="{{ route('ordenes-compra.show', $orden) }}" class="text-blue-600 hover:text-blue-900 font-bold">
                                {{ $orden->numero }}
                            </a>
                        </td>
                        <td>
                            <div class="font-medium text-gray-900">{{ $orden->proveedor_nombre }}</div>
                            @if($orden->proveedor_ruc)
                                <div class="text-xs text-gray-500">{{ $orden->proveedor_ruc }}</div>
                            @endif
                        </td>
                        <td>
                            @if($orden->pedidoCliente)
                                <a href="{{ route('pedidos-cliente.show', $orden->pedidoCliente) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $orden->pedidoCliente->numero }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $orden->fecha_orden->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $orden->estado_color }}">
                                {{ str_replace('_', ' ', $orden->estado) }}
                            </span>
                        </td>
                        <td class="text-right font-bold whitespace-nowrap">
                            {{ number_format($orden->total, 0, ',', '.') }} Gs.
                        </td>
                        <td class="text-center">
                            <a href="{{ route('ordenes-compra.show', $orden) }}" class="text-blue-600 hover:text-blue-900 font-medium">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">No hay ordenes de compra</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $ordenes->links() }}</div>
</div>
@endsection
