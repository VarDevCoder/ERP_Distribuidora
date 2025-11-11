@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $producto->nombre }}</h1>
            <p class="text-gray-600 mt-1">{{ $producto->codigo }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('inventario.kardex', $producto) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                Ver Kardex
            </a>
            <a href="{{ route('productos.edit', $producto) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Editar
            </a>
        </div>
    </div>

    <!-- Información del Producto -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-3">
                    <p class="text-sm text-red-800 font-medium">Stock bajo</p>
                    <p class="text-xs text-red-600 mt-1">Mínimo: {{ $producto->stock_minimo }} {{ $producto->unidad_medida }}</p>
                </div>
            @endif
        </div>

        <!-- Precio de Compra -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Precio de Compra</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">
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
                    <p class="text-sm font-medium text-gray-500">Precio de Venta</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        ${{ number_format($producto->precio_venta, 2) }}
                    </p>
                    @php
                        $margen = $producto->precio_compra > 0
                            ? (($producto->precio_venta - $producto->precio_compra) / $producto->precio_compra * 100)
                            : 0;
                    @endphp
                    <p class="text-sm text-gray-500 mt-1">
                        Margen: {{ number_format($margen, 1) }}%
                    </p>
                </div>
                <div class="text-4xl">💵</div>
            </div>
        </div>
    </div>

    <!-- Detalles del Producto -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Detalles</h2>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Descripción</p>
                <p class="text-gray-900 mt-1">{{ $producto->descripcion ?: 'Sin descripción' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Estado</p>
                <p class="mt-1">
                    @if($producto->activo)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Inactivo
                        </span>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Stock Mínimo</p>
                <p class="text-gray-900 mt-1">{{ $producto->stock_minimo }} {{ $producto->unidad_medida }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Fecha de Registro</p>
                <p class="text-gray-900 mt-1">{{ $producto->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Movimientos Recientes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Movimientos Recientes</h2>
            <a href="{{ route('inventario.kardex', $producto) }}"
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Ver todos →
            </a>
        </div>

        @if($producto->movimientos->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Anterior</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Nuevo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referencia</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($producto->movimientos->take(10) as $movimiento)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $movimiento->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($movimiento->tipo === 'ENTRADA')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Entrada
                                        </span>
                                    @elseif($movimiento->tipo === 'SALIDA')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Salida
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Ajuste
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-medium {{ $movimiento->tipo === 'ENTRADA' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimiento->tipo === 'ENTRADA' ? '+' : '-' }}{{ $movimiento->cantidad }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $movimiento->stock_anterior }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $movimiento->stock_nuevo }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($movimiento->notaRemision)
                                        <a href="{{ route('notas-remision.show', $movimiento->notaRemision) }}"
                                           class="text-blue-600 hover:text-blue-800">
                                            {{ $movimiento->notaRemision->numero }}
                                        </a>
                                    @else
                                        {{ $movimiento->observaciones ?: '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-lg">
                    <p>No hay movimientos registrados</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
