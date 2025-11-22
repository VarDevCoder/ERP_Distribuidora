@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Ordenes de Envio</h1>
            <p class="text-gray-600 mt-1">Flujo ANKOR - Gestiona los envios a clientes</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="form-section mb-6">
        <form action="{{ route('ordenes-envio.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Numero, guia..."
                       class="form-input">
            </div>
            <div class="w-48">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="PREPARANDO" {{ request('estado') == 'PREPARANDO' ? 'selected' : '' }}>Preparando</option>
                    <option value="LISTO" {{ request('estado') == 'LISTO' ? 'selected' : '' }}>Listo</option>
                    <option value="EN_TRANSITO" {{ request('estado') == 'EN_TRANSITO' ? 'selected' : '' }}>En Transito</option>
                    <option value="ENTREGADO" {{ request('estado') == 'ENTREGADO' ? 'selected' : '' }}>Entregado</option>
                    <option value="DEVUELTO" {{ request('estado') == 'DEVUELTO' ? 'selected' : '' }}>Devuelto</option>
                    <option value="CANCELADO" {{ request('estado') == 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrar</button>
            <a href="{{ route('ordenes-envio.index') }}" class="btn-secondary">Limpiar</a>
        </form>
    </div>

    <!-- Tabla -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Pedido</th>
                    <th>Direccion</th>
                    <th>Generacion</th>
                    <th>Estado</th>
                    <th>Guia</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                    <tr>
                        <td>
                            <a href="{{ route('ordenes-envio.show', $orden) }}" class="text-blue-600 hover:text-blue-900 font-bold">
                                {{ $orden->numero }}
                            </a>
                        </td>
                        <td>
                            @if($orden->pedidoCliente)
                                <a href="{{ route('pedidos-cliente.show', $orden->pedidoCliente) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    {{ $orden->pedidoCliente->numero }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $orden->pedidoCliente->cliente_nombre }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm">{{ Str::limit($orden->direccion_entrega, 40) }}</div>
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $orden->fecha_generacion->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="px-3 py-1 text-xs font-bold rounded-full {{ $orden->estado_color }}">
                                {{ str_replace('_', ' ', $orden->estado) }}
                            </span>
                        </td>
                        <td>
                            {{ $orden->numero_guia ?? '-' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('ordenes-envio.show', $orden) }}" class="text-blue-600 hover:text-blue-900 font-medium">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">No hay ordenes de envio</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $ordenes->links() }}</div>
</div>
@endsection
