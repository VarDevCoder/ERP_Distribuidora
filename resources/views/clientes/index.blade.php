@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Clientes</h1>
            <p class="page-subtitle">Gestiona tu cartera de clientes</p>
        </div>
        <a href="{{ route('clientes.create') }}" class="btn-primary">
            Nuevo Cliente
        </a>
    </div>

    {{-- Filtros --}}
    <div class="form-section mb-6">
        <form method="GET" action="{{ route('clientes.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Búsqueda --}}
                <div class="md:col-span-2">
                    <input
                        type="text"
                        name="busqueda"
                        value="{{ request('busqueda') }}"
                        placeholder="Buscar por nombre, RUC, email o teléfono..."
                        class="form-input">
                </div>

                {{-- Filtro Estado --}}
                <div>
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                {{-- Filtro Ciudad --}}
                <div>
                    <select name="ciudad" class="form-select">
                        <option value="">Todas las ciudades</option>
                        @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad }}" {{ request('ciudad') === $ciudad ? 'selected' : '' }}>
                                {{ $ciudad }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">
                    Filtrar
                </button>
                <a href="{{ route('clientes.index') }}" class="btn-secondary">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="table-container">
        @if($clientes->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RUC</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                        <tr>
                            <td class="font-medium text-gray-900">
                                {{ $cliente->nombre }}
                            </td>
                            <td>{{ $cliente->ruc ?? '-' }}</td>
                            <td>{{ $cliente->telefono ?? '-' }}</td>
                            <td>{{ $cliente->email ?? '-' }}</td>
                            <td>{{ $cliente->ciudad ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $cliente->activo ? 'badge-green' : 'badge-gray' }}">
                                    {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('clientes.show', $cliente) }}"
                                       class="btn-secondary btn-sm">
                                        Ver
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}"
                                       class="btn-primary btn-sm">
                                        Editar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $clientes->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No se encontraron clientes.</p>
                @if(request()->hasAny(['busqueda', 'activo', 'ciudad']))
                    <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        Limpiar filtros
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
