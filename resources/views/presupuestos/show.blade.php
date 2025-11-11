@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $presupuesto->numero }}</h1>
            <p class="text-gray-600 mt-1">
                @if($presupuesto->tipo === 'COMPRA')
                    <span class="text-green-600">Presupuesto de Compra</span>
                @else
                    <span class="text-blue-600">Presupuesto de Venta</span>
                @endif
            </p>
        </div>
        <div class="flex space-x-2">
            @if($presupuesto->estado !== 'CONVERTIDO')
                <a href="{{ route('presupuestos.edit', $presupuesto) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Editar
                </a>
            @endif
            <a href="{{ route('presupuestos.index') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                Volver
            </a>
        </div>
    </div>

    <!-- Acciones según estado -->
    @if($presupuesto->estado === 'APROBADO')
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-green-900">Presupuesto Aprobado</p>
                        <p class="text-xs text-green-700 mt-1">Este presupuesto está listo para convertirse en una nota de remisión</p>
                    </div>
                </div>
                <a href="{{ route('notas-remision.create', ['presupuesto_id' => $presupuesto->id]) }}"
                   class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    Convertir a Nota de Remisión
                </a>
            </div>
        </div>
    @elseif($presupuesto->estado === 'CONVERTIDO')
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-purple-900">Presupuesto Convertido</p>
                        <p class="text-xs text-purple-700 mt-1">Este presupuesto ya fue convertido a nota de remisión</p>
                    </div>
                </div>
                @if($presupuesto->notaRemision)
                    <a href="{{ route('notas-remision.show', $presupuesto->notaRemision) }}"
                       class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        Ver Nota de Remisión →
                    </a>
                @endif
            </div>
        </div>
    @elseif(in_array($presupuesto->estado, ['BORRADOR', 'ENVIADO']))
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Presupuesto Pendiente</p>
                        <p class="text-xs text-yellow-700 mt-1">Aprueba este presupuesto para poder convertirlo a nota de remisión</p>
                    </div>
                </div>
                <form action="{{ route('presupuestos.aprobar', $presupuesto) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('¿Aprobar este presupuesto?')"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                        Aprobar Presupuesto
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información del Contacto -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Contacto</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Nombre:</label>
                        <p class="font-semibold text-gray-800">{{ $presupuesto->contacto_nombre }}</p>
                    </div>
                    @if($presupuesto->contacto_email)
                        <div>
                            <label class="text-sm text-gray-600">Email:</label>
                            <p class="font-semibold text-gray-800">{{ $presupuesto->contacto_email }}</p>
                        </div>
                    @endif
                    @if($presupuesto->contacto_telefono)
                        <div>
                            <label class="text-sm text-gray-600">Teléfono:</label>
                            <p class="font-semibold text-gray-800">{{ $presupuesto->contacto_telefono }}</p>
                        </div>
                    @endif
                    @if($presupuesto->contacto_empresa)
                        <div>
                            <label class="text-sm text-gray-600">Empresa:</label>
                            <p class="font-semibold text-gray-800">{{ $presupuesto->contacto_empresa }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fechas y Estado -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Información del Presupuesto</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Tipo:</label>
                        <p class="mt-1">
                            @if($presupuesto->tipo === 'COMPRA')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Compra
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    Venta
                                </span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Fecha:</label>
                        <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Vencimiento:</label>
                        <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($presupuesto->fecha_vencimiento)->format('d/m/Y') }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm text-gray-600">Estado:</label>
                    @php
                        $estadoClases = [
                            'BORRADOR' => 'bg-gray-100 text-gray-800',
                            'ENVIADO' => 'bg-blue-100 text-blue-800',
                            'APROBADO' => 'bg-green-100 text-green-800',
                            'RECHAZADO' => 'bg-red-100 text-red-800',
                            'CONVERTIDO' => 'bg-purple-100 text-purple-800'
                        ];
                        $clase = $estadoClases[$presupuesto->estado] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <p><span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $clase }}">
                        {{ $presupuesto->estado }}
                    </span></p>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Items del Presupuesto</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($presupuesto->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('productos.show', $item->producto) }}"
                                           class="text-blue-600 hover:text-blue-900 font-medium">
                                            {{ $item->producto->nombre }}
                                        </a>
                                        <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">
                                        {{ $item->cantidad }} {{ $item->producto->unidad_medida }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">
                                        ${{ number_format($item->precio_unitario, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-gray-900">
                                        ${{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notas -->
            @if($presupuesto->notas)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Notas</h2>
                    <p class="text-gray-700">{{ $presupuesto->notas }}</p>
                </div>
            @endif
        </div>

        <!-- Resumen -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Resumen</h2>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold">${{ number_format($presupuesto->subtotal, 2) }}</span>
                    </div>

                    @if($presupuesto->descuento > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Descuento:</span>
                            <span class="font-semibold text-red-600">-${{ number_format($presupuesto->descuento, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Impuesto (16%):</span>
                        <span class="font-semibold">${{ number_format($presupuesto->impuesto, 2) }}</span>
                    </div>

                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-bold text-gray-800">Total:</span>
                            <span class="font-bold text-blue-600 text-xl">${{ number_format($presupuesto->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($presupuesto->estado !== 'CONVERTIDO')
                    <div class="mt-6 pt-6 border-t">
                        <form action="{{ route('presupuestos.destroy', $presupuesto) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este presupuesto?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                                Eliminar Presupuesto
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
