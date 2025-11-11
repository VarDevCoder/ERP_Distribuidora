@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $notaRemision->numero }}</h1>
            <p class="text-gray-600 mt-1">Nota de Remisión</p>
        </div>
        <div class="flex space-x-3">
            @if($notaRemision->estado === 'PENDIENTE')
                <form action="{{ route('notas-remision.aplicar', $notaRemision) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('¿Aplicar esta nota al inventario? Los stocks se actualizarán y esta acción no se puede deshacer.')"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Aplicar al Inventario
                    </button>
                </form>
            @endif

            @if($notaRemision->estado === 'PENDIENTE')
                <form action="{{ route('notas-remision.destroy', $notaRemision) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('¿Eliminar esta nota de remisión?')"
                            class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        Eliminar
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Estado de la Nota -->
    <div class="mb-6">
        @if($notaRemision->estado === 'APLICADA')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-900">Nota de Remisión Aplicada</p>
                        <p class="text-xs text-green-700 mt-1">Esta nota ya fue aplicada al inventario el {{ $notaRemision->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Nota de Remisión Pendiente</p>
                        <p class="text-xs text-yellow-700 mt-1">Esta nota aún no ha sido aplicada al inventario. Haz clic en "Aplicar al Inventario" para actualizar los stocks.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Información General -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Datos de la Nota -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Información</h2>

            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tipo</p>
                    <p class="mt-1">
                        @if($notaRemision->tipo === 'ENTRADA')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                📥 Entrada al Inventario
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                📤 Salida del Inventario
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Fecha</p>
                    <p class="text-gray-900 mt-1">{{ \Carbon\Carbon::parse($notaRemision->fecha)->format('d/m/Y') }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Estado</p>
                    <p class="mt-1">
                        @if($notaRemision->estado === 'APLICADA')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                ✓ Aplicada
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                ⏳ Pendiente
                            </span>
                        @endif
                    </p>
                </div>

                @if($notaRemision->presupuesto)
                    <div>
                        <p class="text-sm font-medium text-gray-500">Presupuesto Origen</p>
                        <a href="{{ route('presupuestos.show', $notaRemision->presupuesto) }}"
                           class="text-blue-600 hover:text-blue-900 mt-1 inline-block">
                            {{ $notaRemision->presupuesto->numero }} →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Datos del Contacto -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Contacto</h2>

            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nombre</p>
                    <p class="text-gray-900 mt-1">{{ $notaRemision->contacto_nombre }}</p>
                </div>

                @if($notaRemision->contacto_empresa)
                    <div>
                        <p class="text-sm font-medium text-gray-500">Empresa</p>
                        <p class="text-gray-900 mt-1">{{ $notaRemision->contacto_empresa }}</p>
                    </div>
                @endif

                @if($notaRemision->observaciones)
                    <div>
                        <p class="text-sm font-medium text-gray-500">Observaciones</p>
                        <p class="text-gray-900 mt-1">{{ $notaRemision->observaciones }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Items de la Nota -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Items</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notaRemision->items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('productos.show', $item->producto) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                {{ $item->producto->nombre }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $item->producto->codigo }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->cantidad }} {{ $item->producto->unidad_medida }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($item->precio_unitario, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Movimientos de Inventario (si la nota está aplicada) -->
    @if($notaRemision->estado === 'APLICADA' && $notaRemision->movimientos->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Movimientos de Inventario Generados</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Anterior</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Nuevo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($notaRemision->movimientos as $movimiento)
                            <tr>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('productos.show', $movimiento->producto) }}"
                                       class="text-blue-600 hover:text-blue-900">
                                        {{ $movimiento->producto->nombre }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($movimiento->tipo === 'ENTRADA')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Entrada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Salida
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium {{ $movimiento->tipo === 'ENTRADA' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movimiento->tipo === 'ENTRADA' ? '+' : '-' }}{{ $movimiento->cantidad }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $movimiento->stock_anterior }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $movimiento->stock_nuevo }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $movimiento->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
