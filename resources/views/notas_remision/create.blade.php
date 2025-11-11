@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Crear Nota de Remisión</h1>
        <p class="text-gray-600 mt-1">A partir del presupuesto: {{ $presupuesto->numero }}</p>
    </div>

    <!-- Información del Presupuesto -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Presupuesto</h2>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">Tipo</p>
                <p class="text-gray-900 mt-1">
                    @if($presupuesto->tipo === 'COMPRA')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Compra (ENTRADA al inventario)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Venta (SALIDA del inventario)
                        </span>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Contacto</p>
                <p class="text-gray-900 mt-1">{{ $presupuesto->contacto_nombre }}</p>
                @if($presupuesto->contacto_empresa)
                    <p class="text-sm text-gray-500">{{ $presupuesto->contacto_empresa }}</p>
                @endif
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Total del Presupuesto</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($presupuesto->total, 2) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500">Estado</p>
                <p class="mt-1">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        {{ $presupuesto->estado }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Items del Presupuesto -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Items a incluir</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($presupuesto->items as $item)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $item->producto->nombre }}</div>
                                <div class="text-gray-500">{{ $item->producto->codigo }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $item->cantidad }} {{ $item->producto->unidad_medida }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                ${{ number_format($item->precio_unitario, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                ${{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('notas-remision.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        <input type="hidden" name="presupuesto_id" value="{{ $presupuesto->id }}">

        <div class="space-y-6">
            <h2 class="text-xl font-bold text-gray-800">Datos de la Nota de Remisión</h2>

            <!-- Fecha -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fecha de Remisión <span class="text-red-600">*</span>
                </label>
                <input type="date"
                       name="fecha"
                       value="{{ old('fecha', date('Y-m-d')) }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('fecha')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Observaciones -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones"
                          rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Notas adicionales sobre esta nota de remisión...">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Información Importante -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-yellow-900">Importante</p>
                        <p class="text-xs text-yellow-700 mt-1">
                            La nota de remisión se creará en estado PENDIENTE. Para que afecte al inventario, deberás aplicarla desde la vista de detalle.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('presupuestos.show', $presupuesto) }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Crear Nota de Remisión
            </button>
        </div>
    </form>
</div>
@endsection
