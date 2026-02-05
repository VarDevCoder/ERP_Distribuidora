@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Volver a Clientes
        </a>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="page-title">{{ $cliente->nombre }}</h1>
                <p class="page-subtitle">Detalles del cliente</p>
            </div>
            <div class="flex gap-2">
                <form action="{{ route('clientes.toggleActivo', $cliente) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        {{ $cliente->activo ? 'Desactivar' : 'Activar' }}
                    </button>
                </form>
                <a href="{{ route('clientes.edit', $cliente) }}" class="btn-primary">
                    Editar
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Información del Cliente --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Datos Básicos --}}
            <div class="form-section">
                <h3 class="form-section-title">Datos Básicos</h3>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cliente->nombre }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">RUC</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cliente->ruc ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cliente->telefono ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cliente->email ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ciudad</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $cliente->ciudad ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                        <dd class="mt-1">
                            <span class="badge {{ $cliente->activo ? 'badge-green' : 'badge-gray' }}">
                                {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </dd>
                    </div>

                    @if($cliente->direccion)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $cliente->direccion }}</dd>
                        </div>
                    @endif

                    @if($cliente->notas)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Notas</dt>
                            <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $cliente->notas }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Historial de Pedidos --}}
            <div class="form-section">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Últimos Pedidos</h3>
                    <a href="{{ route('pedidos-cliente.create') }}?cliente_id={{ $cliente->id }}" class="btn-sm btn-primary">
                        Nuevo Pedido
                    </a>
                </div>

                @if($cliente->pedidos->count() > 0)
                    <div class="overflow-hidden">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->pedidos as $pedido)
                                    <tr>
                                        <td class="font-medium">{{ $pedido->numero }}</td>
                                        <td>{{ $pedido->fecha_pedido->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge {{ $pedido->estado_color }}">
                                                {{ $pedido::getEstados()[$pedido->estado] ?? $pedido->estado }}
                                            </span>
                                        </td>
                                        <td class="text-right">{{ number_format($pedido->total, 0, ',', '.') }} Gs.</td>
                                        <td class="text-right">
                                            <a href="{{ route('pedidos-cliente.show', $pedido) }}" class="btn-sm btn-secondary">
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Este cliente aún no tiene pedidos.</p>
                @endif
            </div>
        </div>

        {{-- Acciones y Estadísticas --}}
        <div class="space-y-6">
            {{-- Estadísticas --}}
            <div class="form-section">
                <h3 class="form-section-title">Estadísticas</h3>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Total de Pedidos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $cliente->pedidos->count() }}</p>
                    </div>

                    @if($cliente->pedidos->count() > 0)
                        <div>
                            <p class="text-sm text-gray-500">Monto Total</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($cliente->pedidos->sum('total'), 0, ',', '.') }} Gs.
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Último Pedido</p>
                            <p class="text-sm text-gray-900">
                                {{ $cliente->pedidos->first()->fecha_pedido->format('d/m/Y') }}
                            </p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm text-gray-500">Cliente desde</p>
                        <p class="text-sm text-gray-900">
                            {{ $cliente->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="form-section">
                <h3 class="form-section-title">Acciones</h3>

                <div class="space-y-2">
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn-secondary w-full text-center block">
                        Editar Cliente
                    </a>

                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                          onsubmit="return confirm('¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full">
                            Eliminar Cliente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
