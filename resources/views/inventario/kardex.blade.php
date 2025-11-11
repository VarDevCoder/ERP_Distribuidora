@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Kardex: {{ $producto->nombre }}</h1>
            <p class="text-gray-600 mt-1">{{ $producto->codigo }} - Historial de movimientos</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('productos.show', $producto) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Ver Producto
            </a>
            <a href="{{ route('inventario.index') }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Volver al Inventario
            </a>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Stock Actual -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Stock Actual</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $producto->stock_actual }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">{{ $producto->unidad_medida }}</p>
                </div>
                <div class="text-4xl">📦</div>
            </div>
            @if($producto->stock_actual <= $producto->stock_minimo)
                <div class="mt-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Stock Bajo
                    </span>
                </div>
            @endif
        </div>

        <!-- Stock Mínimo -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Stock Mínimo</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
                        {{ $producto->stock_minimo }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">{{ $producto->unidad_medida }}</p>
                </div>
                <div class="text-4xl">📊</div>
            </div>
        </div>

        <!-- Precio de Compra -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Precio Compra</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">
                        ${{ number_format($producto->precio_compra, 2) }}
                    </p>
                </div>
                <div class="text-4xl">💰</div>
            </div>
        </div>

        <!-- Precio de Venta -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Precio Venta</p>
                    <p class="text-2xl font-bold text-green-600 mt-2">
                        ${{ number_format($producto->precio_venta, 2) }}
                    </p>
                </div>
                <div class="text-4xl">💵</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Movimientos de Inventario</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Anterior</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Nuevo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($movimientos as $movimiento)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->created_at->format('d/m/Y') }}
                                <div class="text-xs text-gray-500">{{ $movimiento->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movimiento->tipo === 'ENTRADA')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        📥 Entrada
                                    </span>
                                @elseif($movimiento->tipo === 'SALIDA')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        📤 Salida
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        🔧 Ajuste
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold {{ $movimiento->tipo === 'ENTRADA' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimiento->tipo === 'ENTRADA' ? '+' : '-' }}{{ $movimiento->cantidad }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">{{ $producto->unidad_medida }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movimiento->stock_anterior }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $movimiento->stock_nuevo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($movimiento->notaRemision)
                                    <a href="{{ route('notas-remision.show', $movimiento->notaRemision) }}"
                                       class="text-blue-600 hover:text-blue-900">
                                        {{ $movimiento->notaRemision->numero }}
                                    </a>
                                    <div class="text-xs text-gray-500">
                                        {{ $movimiento->notaRemision->contacto_nombre }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $movimiento->observaciones ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400 text-lg">
                                    <p>No hay movimientos registrados para este producto</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    @if($movimientos->hasPages())
        <div class="mt-6">
            {{ $movimientos->links() }}
        </div>
    @endif

    <!-- Resumen -->
    @if($movimientos->count() > 0)
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-900">
                    Total de movimientos registrados: <span class="font-bold">{{ $movimientos->total() }}</span>
                </p>
            </div>
        </div>
    @endif
</div>
@endsection
