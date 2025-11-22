@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Inventario</h1>
            <p class="text-gray-600 mt-1">Estado actual de stocks y productos</p>
        </div>
        <a href="{{ route('inventario.movimientos') }}" class="btn-primary">
            Ver Todos los Movimientos
        </a>
    </div>

    <!-- Resumen de Estadisticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-100 to-blue-50 rounded-lg shadow-lg p-6 border-2 border-blue-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-blue-700">Total Productos</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $productos->total() }}</p>
                </div>
                <div class="text-4xl">📦</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-100 to-red-50 rounded-lg shadow-lg p-6 border-2 border-red-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-red-700">Stock Bajo</p>
                    <p class="text-3xl font-bold text-red-700 mt-2">
                        {{ $productos->filter(fn($p) => $p->stock_actual <= $p->stock_minimo)->count() }}
                    </p>
                </div>
                <div class="text-4xl">⚠️</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-100 to-purple-50 rounded-lg shadow-lg p-6 border-2 border-purple-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-purple-700">Movimientos (Todos)</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">
                        {{ $productos->sum('movimientos_count') }}
                    </p>
                </div>
                <div class="text-4xl">📊</div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-100 to-green-50 rounded-lg shadow-lg p-6 border-2 border-green-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-green-700">Activos</p>
                    <p class="text-3xl font-bold text-green-700 mt-2">
                        {{ $productos->filter(fn($p) => $p->activo)->count() }}
                    </p>
                </div>
                <div class="text-4xl">✓</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Inventario -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-right">Stock Actual</th>
                    <th class="text-right">Stock Minimo</th>
                    <th>Estado</th>
                    <th class="text-center">Movimientos</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr class="{{ $producto->stock_actual <= $producto->stock_minimo ? 'bg-red-50' : '' }}">
                        <td>
                            <div class="font-bold text-gray-900">{{ $producto->nombre }}</div>
                            <div class="text-xs text-gray-500">{{ $producto->codigo }}</div>
                        </td>
                        <td class="text-right">
                            <span class="font-bold {{ $producto->stock_actual <= $producto->stock_minimo ? 'text-red-600' : 'text-gray-900' }}">
                                {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}
                            </span>
                        </td>
                        <td class="text-right text-gray-600">
                            {{ number_format($producto->stock_minimo, 2) }} {{ $producto->unidad_medida }}
                        </td>
                        <td>
                            @if($producto->stock_actual <= $producto->stock_minimo)
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-200 text-red-800">
                                    Stock Bajo
                                </span>
                            @elseif($producto->stock_actual <= ($producto->stock_minimo * 1.5))
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-200 text-yellow-800">
                                    Alerta
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-200 text-green-800">
                                    Normal
                                </span>
                            @endif
                        </td>
                        <td class="text-center text-gray-600">
                            {{ $producto->movimientos_count }} mov.
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('inventario.kardex', $producto) }}" class="text-blue-600 hover:text-blue-900 font-medium">Kardex</a>
                                <a href="{{ route('productos.show', $producto) }}" class="text-green-600 hover:text-green-900 font-medium">Ver</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">
                            No hay productos en el inventario
                            <br>
                            <a href="{{ route('productos.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                Crear primer producto
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($productos->hasPages())
        <div class="mt-4">{{ $productos->links() }}</div>
    @endif
</div>
@endsection
