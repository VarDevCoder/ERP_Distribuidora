@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
            <p class="text-gray-600 mt-1">Estado actual de stocks y productos</p>
        </div>
        <a href="{{ route('inventario.movimientos') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Ver Todos los Movimientos
        </a>
    </div>

    <!-- Resumen de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Productos -->
        <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow-lg p-6 border-2 border-blue-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700">Total Productos</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $productos->total() }}</p>
                </div>
                <div class="text-4xl">📦</div>
            </div>
        </div>

        <!-- Productos con Stock Bajo -->
        <div class="bg-gradient-to-br from-red-100 to-red-50 rounded-lg shadow-lg p-6 border-2 border-red-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700">Stock Bajo</p>
                    <p class="text-3xl font-bold text-red-700 mt-2">
                        {{ $productos->filter(fn($p) => $p->stock_actual <= $p->stock_minimo)->count() }}
                    </p>
                </div>
                <div class="text-4xl">⚠️</div>
            </div>
        </div>

        <!-- Total Movimientos -->
        <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg shadow-lg p-6 border-2 border-purple-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-700">Movimientos (Todos)</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">
                        {{ $productos->sum('movimientos_count') }}
                    </p>
                </div>
                <div class="text-4xl">📊</div>
            </div>
        </div>

        <!-- Productos Activos -->
        <div class="bg-gradient-to-br from-green-100 to-green-50 rounded-lg shadow-lg p-6 border-2 border-green-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700">Activos</p>
                    <p class="text-3xl font-bold text-green-700 mt-2">
                        {{ $productos->filter(fn($p) => $p->activo)->count() }}
                    </p>
                </div>
                <div class="text-4xl">✓</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Inventario -->
    <x-data-table :headers="['Producto', 'Stock Actual', 'Stock Mínimo', 'Estado', 'Movimientos', 'Acciones']" color="indigo">
        @forelse($productos as $producto)
            <x-table-row :color="$producto->stock_actual <= $producto->stock_minimo ? 'yellow' : 'indigo'">
                <x-table-cell class="whitespace-nowrap">
                    <div class="flex items-center">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ $producto->codigo }}</div>
                        </div>
                    </div>
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    <div class="text-sm font-bold {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $producto->stock_actual }} {{ $producto->unidad_medida }}
                    </div>
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm text-gray-500">
                    {{ $producto->stock_minimo }} {{ $producto->unidad_medida }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    @if($producto->stock_actual <= $producto->stock_minimo)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Stock Bajo
                        </span>
                    @elseif($producto->stock_actual <= ($producto->stock_minimo * 1.5))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Alerta
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Normal
                        </span>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm text-gray-500">
                    {{ $producto->movimientos_count }} movimientos
                </x-table-cell>
                <x-table-cell :last="true" class="whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('inventario.kardex', $producto) }}" class="text-blue-600 hover:text-blue-900">Kardex</a>
                        <a href="{{ route('productos.show', $producto) }}" class="text-green-600 hover:text-green-900">Ver Producto</a>
                    </div>
                </x-table-cell>
            </x-table-row>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="text-gray-400 text-lg">
                        <p class="mb-2">No hay productos en el inventario</p>
                        <a href="{{ route('productos.create') }}" class="text-blue-600 hover:text-blue-800">
                            Crear primer producto
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
