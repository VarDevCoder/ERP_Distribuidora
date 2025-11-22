@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto" x-data="pedidoForm()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nuevo Pedido de Cliente</h1>
            <p class="text-gray-600 mt-1">Flujo ANKOR - Registra un nuevo pedido</p>
        </div>
        <a href="{{ route('pedidos-cliente.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
    </div>

    <form action="{{ route('pedidos-cliente.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Datos del Cliente -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Cliente</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="cliente_nombre" value="{{ old('cliente_nombre') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC</label>
                    <input type="text" name="cliente_ruc" value="{{ old('cliente_ruc') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="cliente_telefono" value="{{ old('cliente_telefono') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="cliente_email" value="{{ old('cliente_email') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="cliente_direccion" value="{{ old('cliente_direccion') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Fechas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Fechas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha del Pedido *</label>
                    <input type="date" name="fecha_pedido" value="{{ old('fecha_pedido', date('Y-m-d')) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Entrega Solicitada</label>
                    <input type="date" name="fecha_entrega_solicitada" value="{{ old('fecha_entrega_solicitada') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Items del Pedido -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Productos</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-28">Cantidad</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-40">Precio Unit. (Gs.)</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-40">Subtotal</th>
                            <th class="px-4 py-3 w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-4 py-3">
                                    <select x-model="item.producto_id" :name="'items['+index+'][producto_id]'" required
                                            @change="updatePrecio(index)"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Seleccionar...</option>
                                        @foreach($productos as $producto)
                                            <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}">
                                                {{ $producto->codigo }} - {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" x-model="item.cantidad" :name="'items['+index+'][cantidad]'"
                                           step="0.001" min="0.001" required @input="calcularSubtotal(index)"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-right focus:border-blue-500 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" x-model="item.precio_unitario" :name="'items['+index+'][precio_unitario]'"
                                           min="0" required @input="calcularSubtotal(index)"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-right focus:border-blue-500 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3 text-right font-medium" x-text="formatGs(item.subtotal)"></td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                            class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <button type="button" @click="addItem()" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                + Agregar Producto
            </button>
        </div>

        <!-- Resumen y Notas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Notas</h2>
                <textarea name="notas" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                          placeholder="Observaciones del pedido...">{{ old('notas') }}</textarea>
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
                               class="w-32 rounded-lg border-gray-300 shadow-sm text-right focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="border-t pt-3 flex justify-between text-lg">
                        <span class="font-bold text-gray-800">Total:</span>
                        <span class="font-bold text-blue-600" x-text="formatGs(total)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('pedidos-cliente.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">Crear Pedido</button>
        </div>
    </form>
</div>

<script>
function pedidoForm() {
    return {
        items: [{ producto_id: '', cantidad: 1, precio_unitario: 0, subtotal: 0 }],
        descuento: 0,
        subtotal: 0,
        total: 0,

        addItem() {
            this.items.push({ producto_id: '', cantidad: 1, precio_unitario: 0, subtotal: 0 });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calcularTotal();
        },

        updatePrecio(index) {
            const select = document.querySelector(`select[name="items[${index}][producto_id]"]`);
            const option = select.options[select.selectedIndex];
            if (option && option.dataset.precio) {
                this.items[index].precio_unitario = parseInt(option.dataset.precio);
                this.calcularSubtotal(index);
            }
        },

        calcularSubtotal(index) {
            this.items[index].subtotal = Math.round(this.items[index].cantidad * this.items[index].precio_unitario);
            this.calcularTotal();
        },

        calcularTotal() {
            this.subtotal = this.items.reduce((sum, item) => sum + item.subtotal, 0);
            this.total = this.subtotal - this.descuento;
        },

        formatGs(value) {
            return new Intl.NumberFormat('es-PY').format(value) + ' Gs.';
        }
    }
}
</script>
@endsection
