@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Proveedores</h1>
        <a href="{{ route('proveedores.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            + Nuevo Proveedor
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('proveedores.index') }}" method="GET" class="flex gap-4">
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o RUC..."
                   class="flex-1 rounded-lg border-gray-300 shadow-sm">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Buscar</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RUC</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($proveedores as $proveedor)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $proveedor->razon_social }}</div>
                            <div class="text-sm text-gray-500">{{ $proveedor->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $proveedor->ruc }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $proveedor->telefono ?? '-' }}<br>
                            {{ $proveedor->ciudad ?? '' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($proveedor->user->activo)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ route('proveedores.show', $proveedor) }}" class="text-blue-600 hover:underline">Ver</a>
                            <a href="{{ route('proveedores.edit', $proveedor) }}" class="text-yellow-600 hover:underline">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay proveedores registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $proveedores->links() }}</div>
</div>
@endsection
