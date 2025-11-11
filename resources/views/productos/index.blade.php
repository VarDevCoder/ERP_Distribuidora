@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Productos</h1>
            <p class="text-gray-600 mt-1">Gestiona el catálogo de productos</p>
        </div>
        <a href="{{ route('productos.create') }}"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
            + Nuevo Producto
        </a>
    </div>

    <!-- Tabla de Productos -->
    <x-data-table :headers="['Código', 'Nombre', 'Precio Compra', 'Precio Venta', 'Stock', 'Estado', 'Acciones']" color="green">
        @forelse($productos as $producto)
            <x-table-row color="green">
                <x-table-cell class="whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $producto->codigo }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                    @if($producto->descripcion)
                        <div class="text-sm text-gray-500">{{ Str::limit($producto->descripcion, 40) }}</div>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm text-gray-900">
                    ${{ number_format($producto->precio_compra, 2) }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm text-gray-900">
                    ${{ number_format($producto->precio_venta, 2) }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $producto->stock_actual }} {{ $producto->unidad_medida }}</div>
                    @if($producto->stock_actual <= $producto->stock_minimo)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                            Stock bajo
                        </span>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    @if($producto->activo)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Inactivo
                        </span>
                    @endif
                </x-table-cell>
                <x-table-cell :last="true" class="whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                        <a href="{{ route('productos.edit', $producto) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                        <a href="{{ route('inventario.kardex', $producto) }}" class="text-green-600 hover:text-green-900">Kardex</a>
                    </div>
                </x-table-cell>
            </x-table-row>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="text-gray-400 text-lg">
                        <p class="mb-2">No hay productos registrados</p>
                        <a href="{{ route('productos.create') }}" class="text-blue-600 hover:text-blue-800">
                            Crear el primer producto
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <!-- Paginación -->
    @if($productos->hasPages())
        <div class="mt-6">
            {{ $productos->links() }}
        </div>
    @endif
</div>
@endsection
