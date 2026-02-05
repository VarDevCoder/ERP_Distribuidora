@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Inventario</h1>
            <p class="page-subtitle">Estado actual de stocks y productos</p>
        </div>
        <a href="{{ route('inventario.movimientos') }}" class="btn-primary">
            Ver Movimientos
        </a>
    </div>

    <!-- Resumen de Estadisticas -->
    <div class="grid-responsive-4 mb-6">
        <div class="stat-card border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Total Productos</p>
                    <p class="stat-card-value text-blue-700">{{ $productos->total() }}</p>
                </div>
                <div class="stat-card-icon">📦</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Stock Bajo</p>
                    <p class="stat-card-value text-red-600">
                        {{ $productos->filter(fn($p) => $p->stock_actual <= $p->stock_minimo)->count() }}
                    </p>
                </div>
                <div class="stat-card-icon">⚠️</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Movimientos</p>
                    <p class="stat-card-value text-purple-700">
                        {{ $productos->sum('movimientos_count') }}
                    </p>
                </div>
                <div class="stat-card-icon">📊</div>
            </div>
        </div>

        <div class="stat-card border-l-4 border-l-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="stat-card-label">Activos</p>
                    <p class="stat-card-value text-emerald-600">
                        {{ $productos->filter(fn($p) => $p->activo)->count() }}
                    </p>
                </div>
                <div class="stat-card-icon">✓</div>
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
