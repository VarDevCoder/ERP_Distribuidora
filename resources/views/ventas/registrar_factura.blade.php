@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto" x-data="facturaForm()">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">📄 Registrar Factura</h1>
                <p class="text-gray-600 mt-1">Presupuesto: <span class="font-semibold">{{ $presupuesto->numero }}</span></p>
            </div>
            <a href="{{ route('presupuestos.show', $presupuesto) }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Información del Presupuesto -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm text-blue-700 font-medium">Cliente:</label>
                <p class="text-gray-900 font-semibold">{{ $presupuesto->contacto_nombre }}</p>
            </div>
            <div>
                <label class="text-sm text-blue-700 font-medium">Empresa:</label>
                <p class="text-gray-900">{{ $presupuesto->contacto_empresa ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="text-sm text-blue-700 font-medium">Total Presupuestado:</label>
                <p class="text-gray-900 font-bold text-lg">Gs. {{ number_format($presupuesto->total, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form method="POST" action="{{ route('ventas.registrar-factura', $presupuesto) }}" @submit="validarFormulario">
        @csrf

        <!-- Número de Factura -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📋 Datos de la Factura</h2>
            <div class="max-w-md">
                <label for="factura_numero" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Factura <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="factura_numero"
                       id="factura_numero"
                       required
                       value="{{ old('factura_numero') }}"
                       placeholder="Ej: 001-001-0001234"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('factura_numero')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Productos y Cantidades Reales -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">📦 Cantidades Enviadas</h2>
                <div class="text-sm text-gray-600">
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full font-medium">
                        Ajusta las cantidades reales enviadas al cliente
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cant. Presupuestada</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cant. Real Enviada</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Diferencia</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo (si difiere)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($presupuesto->productos as $index => $item)
                            <tr x-data="{
                                cantidadPresupuestada: {{ $item->cantidad }},
                                cantidadReal: {{ old('cantidades.' . $index . '.cantidad', $item->cantidad) }},
                                get diferencia() { return this.cantidadReal - this.cantidadPresupuestada; },
                                get tieneDiferencia() { return Math.abs(this.diferencia) > 0.001; },
                                get colorDiferencia() {
                                    if (!this.tieneDiferencia) return 'text-gray-500';
                                    return this.diferencia > 0 ? 'text-green-600' : 'text-red-600';
                                }
                            }">
                                <!-- Producto -->
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium text-gray-900">{{ $item->producto->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                                </td>

                                <!-- Cantidad Presupuestada -->
                                <td class="px-4 py-3 text-center">
                                    <span class="text-gray-700 font-semibold">
                                        <span x-text="cantidadPresupuestada"></span> {{ $item->producto->unidad_medida }}
                                    </span>
                                </td>

                                <!-- Cantidad Real -->
                                <td class="px-4 py-3">
                                    <input type="hidden" name="cantidades[{{ $index }}][producto_id]" value="{{ $item->producto_id }}">
                                    <input type="number"
                                           name="cantidades[{{ $index }}][cantidad]"
                                           x-model.number="cantidadReal"
                                           step="0.01"
                                           min="0"
                                           required
                                           class="w-28 px-3 py-2 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           :class="tieneDiferencia ? 'border-yellow-400 bg-yellow-50' : ''">
                                </td>

                                <!-- Diferencia -->
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-lg" :class="colorDiferencia">
                                        <template x-if="diferencia > 0">
                                            <span>+<span x-text="diferencia.toFixed(2)"></span></span>
                                        </template>
                                        <template x-if="diferencia < 0">
                                            <span x-text="diferencia.toFixed(2)"></span>
                                        </template>
                                        <template x-if="!tieneDiferencia">
                                            <span>-</span>
                                        </template>
                                    </span>
                                </td>

                                <!-- Motivo -->
                                <td class="px-4 py-3">
                                    <template x-if="tieneDiferencia">
                                        <textarea name="cantidades[{{ $index }}][motivo]"
                                                  rows="2"
                                                  placeholder="Explicar el motivo de la diferencia..."
                                                  class="w-full px-3 py-2 border border-yellow-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-500 bg-yellow-50"
                                                  required>{{ old('cantidades.' . $index . '.motivo') }}</textarea>
                                    </template>
                                    <template x-if="!tieneDiferencia">
                                        <span class="text-gray-400 text-sm">Sin diferencia</span>
                                    </template>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Ayuda -->
            <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Instrucciones:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1">
                            <li>Ingresa las cantidades <strong>realmente enviadas</strong> al cliente</li>
                            <li>Si hay diferencias, se te pedirá explicar el motivo</li>
                            <li>Las diferencias se registrarán automáticamente para trazabilidad</li>
                            <li>El inventario se actualizará con las cantidades reales</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-between items-center">
            <a href="{{ route('presupuestos.show', $presupuesto) }}"
               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Cancelar
            </a>
            <button type="submit"
                    class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold shadow-lg hover:shadow-xl">
                ✅ Registrar Factura
            </button>
        </div>
    </form>
</div>

<script>
function facturaForm() {
    return {
        validarFormulario(e) {
            // Validación adicional si es necesaria
            return true;
        }
    }
}
</script>
@endsection
