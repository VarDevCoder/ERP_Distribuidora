@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Todos los Movimientos de Inventario</h1>
            <p class="text-gray-600 mt-1">Historial completo de entradas y salidas</p>
        </div>
        <a href="{{ route('inventario.index') }}"
           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
            Volver al Inventario
        </a>
    </div>

    <!-- Tabla de Movimientos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Anterior</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Nuevo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
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
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('productos.show', $movimiento->producto) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                {{ $movimiento->producto->nombre }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $movimiento->producto->codigo }}</div>
                                    </div>
                                </div>
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
                                <span class="text-xs text-gray-500 ml-1">{{ $movimiento->producto->unidad_medida }}</span>
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
                                    @if($movimiento->observaciones)
                                        <span class="text-sm text-gray-600">{{ Str::limit($movimiento->observaciones, 30) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('inventario.kardex', $movimiento->producto) }}"
                                       class="text-blue-600 hover:text-blue-900">Kardex</a>
                                    @if($movimiento->notaRemision)
                                        <a href="{{ route('notas-remision.show', $movimiento->notaRemision) }}"
                                           class="text-green-600 hover:text-green-900">Ver Nota</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="text-gray-400 text-lg">
                                    <p class="mb-2">No hay movimientos de inventario registrados</p>
                                    <p class="text-sm">Los movimientos se generan automáticamente al aplicar notas de remisión</p>
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

    <!-- Información -->
    @if($movimientos->count() > 0)
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm font-medium text-blue-900">Total de movimientos: {{ $movimientos->total() }}</p>
                    <p class="text-xs text-blue-700 mt-1">
                        Los movimientos se crean automáticamente cuando aplicas una nota de remisión al inventario.
                        Las notas de tipo COMPRA generan ENTRADAs (aumentan el stock) y las de tipo VENTA generan SALIDAs (disminuyen el stock).
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
