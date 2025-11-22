@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Pedidos de Clientes</h1>
            <p class="text-gray-600 mt-1">Flujo ANKOR - Gestiona los pedidos de tus clientes</p>
        </div>
        <a href="{{ route('pedidos-cliente.create') }}" class="btn-primary">
            + Nuevo Pedido
        </a>
    </div>

    <!-- Filtros -->
    <div class="form-section mb-6">
        <form action="{{ route('pedidos-cliente.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Numero, cliente, RUC..."
                       class="form-input">
            </div>
            <div class="w-48">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    @foreach(\App\Models\PedidoCliente::getEstados() as $valor => $etiqueta)
                        <option value="{{ $valor }}" {{ request('estado') == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrar</button>
            <a href="{{ route('pedidos-cliente.index') }}" class="btn-secondary">Limpiar</a>
        </form>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                    <tr>
                        <td>
                            <a href="{{ route('pedidos-cliente.show', $pedido) }}" class="text-blue-600 hover:text-blue-900 font-bold">
                                {{ $pedido->numero }}
                            </a>
                        </td>
                        <td>
                            <div class="font-medium text-gray-900">{{ $pedido->cliente_nombre }}</div>
                            @if($pedido->cliente_ruc)
                                <div class="text-xs text-gray-500">{{ $pedido->cliente_ruc }}</div>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $pedido->fecha_pedido->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $pedido->estado_color }}">
                                {{ str_replace('_', ' ', $pedido->estado) }}
                            </span>
                        </td>
                        <td class="text-right font-bold whitespace-nowrap">
                            {{ number_format($pedido->total, 0, ',', '.') }} Gs.
                        </td>
                        <td class="text-center">
                            <a href="{{ route('pedidos-cliente.show', $pedido) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">
                            No hay pedidos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginacion -->
    <div class="mt-4">
        {{ $pedidos->links() }}
    </div>
</div>
@endsection
