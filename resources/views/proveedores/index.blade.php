@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <div>
            <h1 class="page-title">Proveedores</h1>
            <p class="page-subtitle">Gestiona los proveedores del sistema</p>
        </div>
        <a href="{{ route('proveedores.create') }}" class="btn-primary">
            + Nuevo Proveedor
        </a>
    </div>

    <div class="form-section mb-6">
        <form action="{{ route('proveedores.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Buscar por nombre o RUC..."
                       class="form-input">
            </div>
            <button type="submit" class="btn-primary">Buscar</button>
            <a href="{{ route('proveedores.index') }}" class="btn-secondary">Limpiar</a>
        </form>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>RUC</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $proveedor)
                    <tr>
                        <td>
                            <div class="font-bold text-gray-900">{{ $proveedor->razon_social }}</div>
                            <div class="text-xs text-gray-500">{{ $proveedor->user->email }}</div>
                        </td>
                        <td class="font-mono">{{ $proveedor->ruc }}</td>
                        <td>
                            <div>{{ $proveedor->telefono ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $proveedor->ciudad ?? '' }}</div>
                        </td>
                        <td>
                            @if($proveedor->user->activo)
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-200 text-green-800">Activo</span>
                            @else
                                <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-200 text-red-800">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('proveedores.show', $proveedor) }}" class="text-blue-600 hover:text-blue-900 font-medium">Ver</a>
                                <a href="{{ route('proveedores.edit', $proveedor) }}" class="text-yellow-600 hover:text-yellow-900 font-medium">Editar</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">No hay proveedores registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $proveedores->links() }}</div>
</div>
@endsection
