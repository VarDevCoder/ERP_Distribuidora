@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto" x-data="cotizacionForm()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Responder Solicitud {{ $solicitud->numero }}</h1>
            <p class="text-gray-600 mt-1">Fecha: {{ $solicitud->fecha_solicitud->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('proveedor.solicitudes') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
    </div>

    @if($solicitud->mensaje_solicitud)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="font-medium text-blue-900">Mensaje de ANKOR:</p>
            <p class="text-blue-800">{{ $solicitud->mensaje_solicitud }}</p>
        </div>
    @endif

    <!-- Opción rápida: Sin Stock -->
    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
        <form action="{{ route('proveedor.solicitud.sin-stock', $solicitud) }}" method="POST"
              onsubmit="return confirm('¿Confirmar que no tiene disponibilidad de estos productos?')">
            @csrf
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-orange-900">¿No tiene stock de estos productos?</p>
                    <p class="text-sm text-orange-700">Use esta opción para notificar rápidamente</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" name="respuesta_proveedor" placeholder="Motivo (ej: Sin stock hasta marzo)"
                           class="rounded-lg border-gray-300 text-sm" required>
                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                        Notificar Sin Stock
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Formulario de Cotización -->
    <form action="{{ route('proveedor.solicitud.cotizar', $solicitud) }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Cotizar Productos</h2>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Solicitado</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Tiene Stock</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Disponible</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-36">Precio Unit.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-36">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($solicitud->items as $i => $item)
                        <tr x-data="{ tieneStock: true }">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $item->producto->nombre }}</div>
                                <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">{{ $item->cantidad_solicitada }}</td>
                            <td class="px-4 py-3 text-center">
                                <input type="hidden" name="items[{{ $item->id }}][tiene_stock]" :value="tieneStock ? 1 : 0">
                                <input type="checkbox" x-model="tieneStock"
                                       @change="if(!tieneStock) { items[{{ $i }}].cantidad = 0; items[{{ $i }}].precio = 0; calcularTotal(); }"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $item->id }}][cantidad_disponible]"
                                       x-model="items[{{ $i }}].cantidad"
                                       :disabled="!tieneStock"
                                       step="0.001" min="0" max="{{ $item->cantidad_solicitada }}"
                                       @input="calcularSubtotal({{ $i }})"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-right text-sm disabled:bg-gray-100">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $item->id }}][precio_unitario_cotizado]"
                                       x-model="items[{{ $i }}].precio"
                                       :disabled="!tieneStock"
                                       min="0"
                                       @input="calcularSubtotal({{ $i }})"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-right text-sm disabled:bg-gray-100">
                            </td>
                            <td class="px-4 py-3 text-right font-medium" x-text="formatGs(items[{{ $i }}].subtotal)"></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-bold">Total Cotización:</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600" x-text="formatGs(total)"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Días estimados de entrega *</label>
                    <input type="number" name="dias_entrega_estimados" min="1" value="7" required
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje/Observaciones</label>
                    <input type="text" name="respuesta_proveedor"
                           placeholder="Ej: Precio válido por 15 días..."
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('proveedor.solicitudes') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">Cancelar</a>
            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 font-medium">
                Enviar Cotización
            </button>
        </div>
    </form>
</div>

<script>
function cotizacionForm() {
    return {
        items: [
            @foreach($solicitud->items as $item)
            { cantidad: {{ $item->cantidad_solicitada }}, precio: 0, subtotal: 0 },
            @endforeach
        ],
        total: 0,

        calcularSubtotal(index) {
            this.items[index].subtotal = Math.round(this.items[index].cantidad * this.items[index].precio);
            this.calcularTotal();
        },

        calcularTotal() {
            this.total = this.items.reduce((sum, item) => sum + item.subtotal, 0);
        },

        formatGs(value) {
            return new Intl.NumberFormat('es-PY').format(value) + ' Gs.';
        }
    }
}
</script>
@endsection
