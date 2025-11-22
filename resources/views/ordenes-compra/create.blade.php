@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto" x-data="ordenForm()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nueva Orden de Compra</h1>
            @if($pedidoCliente)
                <p class="text-gray-600 mt-1">Para pedido: {{ $pedidoCliente->numero }} - {{ $pedidoCliente->cliente_nombre }}</p>
            @endif
        </div>
        <a href="{{ route('ordenes-compra.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
    </div>

    <form action="{{ route('ordenes-compra.store') }}" method="POST" class="space-y-6">
        @csrf
        @if($pedidoCliente)
            <input type="hidden" name="pedido_cliente_id" value="{{ $pedidoCliente->id }}">
        @endif

        <!-- Datos del Proveedor -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Proveedor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Proveedor *</label>
                    <input type="text" name="proveedor_nombre" value="{{ old('proveedor_nombre') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                    <input type="text" name="proveedor_ruc" value="{{ old('proveedor_ruc') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="proveedor_telefono" value="{{ old('proveedor_telefono') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="proveedor_email" value="{{ old('proveedor_email') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="proveedor_direccion" value="{{ old('proveedor_direccion') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        <!-- Fechas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Fechas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Orden *</label>
                    <input type="date" name="fecha_orden" value="{{ old('fecha_orden', date('Y-m-d')) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Entrega Esperada</label>
                    <input type="date" name="fecha_entrega_esperada" value="{{ old('fecha_entrega_esperada') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Productos a Comprar</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Cantidad</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-40">Precio Unit.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-40">Subtotal</th>
                        <th class="px-4 py-3 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td class="px-4 py-3">
                                <select x-model="item.producto_id" :name="'items['+index+'][producto_id]'" required
                                        @change="updatePrecio(index)" class="w-full rounded-lg border-gray-300 shadow-sm">
                                    <option value="">Seleccionar...</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}">
                                            {{ $producto->codigo }} - {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" x-model="item.cantidad_solicitada" :name="'items['+index+'][cantidad_solicitada]'"
                                       step="0.001" min="0.001" required @input="calcularSubtotal(index)"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-right">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" x-model="item.precio_unitario" :name="'items['+index+'][precio_unitario]'"
                                       min="0" required @input="calcularSubtotal(index)"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-right">
                            </td>
                            <td class="px-4 py-3 text-right font-medium" x-text="formatGs(item.subtotal)"></td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                        class="text-red-600 hover:text-red-800">X</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <button type="button" @click="addItem()" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                + Agregar Producto
            </button>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <textarea name="notas" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm">{{ old('notas') }}</textarea>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Resumen</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-semibold" x-text="formatGs(subtotal)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Descuento:</span>
                        <input type="number" name="descuento" x-model="descuento" min="0" @input="calcularTotal()"
                               class="w-32 rounded-lg border-gray-300 shadow-sm text-right">
                    </div>
                    <div class="border-t pt-3 flex justify-between text-lg">
                        <span class="font-bold">Total:</span>
                        <span class="font-bold text-blue-600" x-text="formatGs(total)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('ordenes-compra.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">Crear Orden</button>
        </div>
    </form>
</div>

<script>
function ordenForm() {
    return {
        items: @json($pedidoCliente ?
            $pedidoCliente->items->map(fn($i) => [
                'producto_id' => $i->producto_id,
                'cantidad_solicitada' => $i->cantidad,
                'precio_unitario' => 0,
                'subtotal' => 0
            ]) :
            [['producto_id' => '', 'cantidad_solicitada' => 1, 'precio_unitario' => 0, 'subtotal' => 0]]
        ),
        descuento: 0, subtotal: 0, total: 0,

        addItem() { this.items.push({ producto_id: '', cantidad_solicitada: 1, precio_unitario: 0, subtotal: 0 }); },
        removeItem(index) { this.items.splice(index, 1); this.calcularTotal(); },
        updatePrecio(index) {
            const select = document.querySelector(`select[name="items[${index}][producto_id]"]`);
            const option = select.options[select.selectedIndex];
            if (option?.dataset.precio) {
                this.items[index].precio_unitario = parseInt(option.dataset.precio);
                this.calcularSubtotal(index);
            }
        },
        calcularSubtotal(index) {
            this.items[index].subtotal = Math.round(this.items[index].cantidad_solicitada * this.items[index].precio_unitario);
            this.calcularTotal();
        },
        calcularTotal() {
            this.subtotal = this.items.reduce((sum, item) => sum + item.subtotal, 0);
            this.total = this.subtotal - this.descuento;
        },
        formatGs(value) { return new Intl.NumberFormat('es-PY').format(value) + ' Gs.'; }
    }
}
</script>
@endsection
