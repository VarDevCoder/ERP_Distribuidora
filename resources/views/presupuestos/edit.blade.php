@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Editar Presupuesto {{ $presupuesto->numero }}</h1>
        <p class="text-gray-600 mt-1">Actualiza la información del presupuesto</p>
    </div>

    <form action="{{ route('presupuestos.update', $presupuesto) }}" method="POST" x-data="presupuestoForm({{ json_encode($presupuesto->items) }})" x-on:submit.prevent="submitForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Datos del Cliente -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Cliente</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cliente *</label>
                            <input type="text" name="cliente_nombre" required value="{{ $presupuesto->cliente_nombre }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="cliente_email" value="{{ $presupuesto->cliente_email }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <input type="text" name="cliente_telefono" value="{{ $presupuesto->cliente_telefono }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Fechas</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                            <input type="date" name="fecha" required value="{{ $presupuesto->fecha->format('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vencimiento *</label>
                            <input type="date" name="fecha_vencimiento" required value="{{ $presupuesto->fecha_vencimiento->format('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Items del Presupuesto -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Items del Presupuesto</h2>
                        <button type="button" @click="addItem" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            + Agregar Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="border border-gray-200 rounded-lg p-4 relative">
                                <button type="button" @click="removeItem(index)"
                                        class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                                    ✕
                                </button>

                                <div class="grid grid-cols-12 gap-3">
                                    <div class="col-span-12">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                                        <input type="text" :name="`items[${index}][descripcion]`" x-model="item.descripcion" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                                        <input type="number" :name="`items[${index}][cantidad]`" x-model="item.cantidad"
                                               @input="calculateSubtotal(index)" required min="0.01" step="0.01"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario *</label>
                                        <input type="number" :name="`items[${index}][precio_unitario]`" x-model="item.precio_unitario"
                                               @input="calculateSubtotal(index)" required min="0" step="0.01"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                        <input type="text" x-model="'$' + item.subtotal.toFixed(2)" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 font-semibold">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Resumen</h2>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-semibold" x-text="'$' + subtotal.toFixed(2)"></span>
                        </div>

                        <div class="border-t pt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descuento</label>
                            <input type="number" name="descuento" x-model="descuento" @input="calculateTotals"
                                   min="0" step="0.01" value="{{ $presupuesto->descuento }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="border-t pt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Impuesto (16%):</span>
                                <span class="font-semibold" x-text="'$' + impuesto.toFixed(2)"></span>
                            </div>
                        </div>

                        <div class="border-t pt-3">
                            <div class="flex justify-between items-center text-lg">
                                <span class="font-bold text-gray-800">Total:</span>
                                <span class="font-bold text-blue-600 text-xl" x-text="'$' + total.toFixed(2)"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                        Actualizar Presupuesto
                    </button>

                    <a href="{{ route('presupuestos.show', $presupuesto) }}"
                       class="block w-full text-center bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold mt-3">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function presupuestoForm(existingItems) {
    return {
        items: existingItems.map(item => ({
            descripcion: item.descripcion,
            cantidad: parseFloat(item.cantidad),
            precio_unitario: parseFloat(item.precio_unitario),
            subtotal: parseFloat(item.subtotal)
        })),
        descuento: {{ $presupuesto->descuento }},
        subtotal: 0,
        impuesto: 0,
        total: 0,

        init() {
            this.calculateTotals();
        },

        addItem() {
            this.items.push({
                descripcion: '',
                cantidad: 1,
                precio_unitario: 0,
                subtotal: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        calculateSubtotal(index) {
            const item = this.items[index];
            item.subtotal = item.cantidad * item.precio_unitario;
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => sum + item.subtotal, 0);
            const base = this.subtotal - this.descuento;
            this.impuesto = base * 0.16;
            this.total = base + this.impuesto;
        },

        submitForm(e) {
            if (this.items.length === 0) {
                alert('Debes tener al menos un item en el presupuesto');
                return;
            }
            e.target.submit();
        }
    }
}
</script>
@endsection
