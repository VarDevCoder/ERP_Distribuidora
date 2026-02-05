@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto" x-data="itemFormWithPrices({
    items: @json($pedido->items->map(fn($i) => [
        'producto_id' => $i->producto_id,
        'cantidad' => $i->cantidad,
        'precio_unitario' => $i->precio_unitario,
        'subtotal' => $i->subtotal
    ])),
    descuento: {{ $pedido->descuento }},
    subtotal: {{ $pedido->subtotal }},
    total: {{ $pedido->total }}
})">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Editar {{ $pedido->numero }}</h1>
        </div>
        <a href="{{ route('pedidos-cliente.show', $pedido) }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('pedidos-cliente.update', $pedido) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Datos del Cliente -->
        <div class="form-section">
            <div class="flex justify-between items-center mb-4">
                <h2 class="form-section-title">Datos del Cliente</h2>
                <a href="{{ route('clientes.create') }}" target="_blank" class="btn-sm btn-success">
                    + Nuevo Cliente
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group md:col-span-2">
                    <label class="form-label">Seleccionar Cliente Existente</label>
                    <select id="cliente_selector" class="form-select" onchange="llenarDatosCliente(this)">
                        <option value="">-- O ingresar datos manualmente --</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}"
                                    data-nombre="{{ $cliente->nombre }}"
                                    data-ruc="{{ $cliente->ruc }}"
                                    data-telefono="{{ $cliente->telefono }}"
                                    data-email="{{ $cliente->email }}"
                                    data-direccion="{{ $cliente->direccion }}"
                                    {{ old('cliente_id', $pedido->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} - {{ $cliente->ruc ?? 'Sin RUC' }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="cliente_id" id="cliente_id" value="{{ old('cliente_id', $pedido->cliente_id) }}">
                </div>
                <div class="form-group">
                    <label class="form-label form-label-required">Nombre</label>
                    <input type="text" name="cliente_nombre" id="cliente_nombre" value="{{ old('cliente_nombre', $pedido->cliente_nombre) }}" required
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">RUC</label>
                    <input type="text" name="cliente_ruc" id="cliente_ruc" value="{{ old('cliente_ruc', $pedido->cliente_ruc) }}"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="cliente_telefono" id="cliente_telefono" value="{{ old('cliente_telefono', $pedido->cliente_telefono) }}"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="cliente_email" id="cliente_email" value="{{ old('cliente_email', $pedido->cliente_email) }}"
                           class="form-input">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="cliente_direccion" id="cliente_direccion" value="{{ old('cliente_direccion', $pedido->cliente_direccion) }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <script>
            function llenarDatosCliente(select) {
                const option = select.options[select.selectedIndex];
                if (option.value) {
                    document.getElementById('cliente_id').value = option.value;
                    document.getElementById('cliente_nombre').value = option.dataset.nombre || '';
                    document.getElementById('cliente_ruc').value = option.dataset.ruc || '';
                    document.getElementById('cliente_telefono').value = option.dataset.telefono || '';
                    document.getElementById('cliente_email').value = option.dataset.email || '';
                    document.getElementById('cliente_direccion').value = option.dataset.direccion || '';
                } else {
                    document.getElementById('cliente_id').value = '';
                }
            }
        </script>

        <!-- Fechas -->
        <div class="form-section">
            <h2 class="form-section-title">Fechas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Fecha de Entrega Solicitada</label>
                    <input type="date" name="fecha_entrega_solicitada"
                           value="{{ old('fecha_entrega_solicitada', $pedido->fecha_entrega_solicitada?->format('Y-m-d')) }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="form-section">
            <h2 class="form-section-title">Productos</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-right w-28">Cantidad</th>
                            <th class="text-right w-40">Precio Unit.</th>
                            <th class="text-right w-40">Subtotal</th>
                            <th class="w-16"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td>
                                    <select x-model="item.producto_id" :name="'items['+index+'][producto_id]'" required
                                            @change="updatePrecio(index, $event)" class="form-select">
                                        <option value="">Seleccionar...</option>
                                        @foreach($productos as $categoria => $prods)
                                            <optgroup label="{{ $categoria }}">
                                                @foreach($prods as $producto)
                                                    <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}">
                                                        {{ $producto->codigo }} - {{ $producto->nombre }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" x-model="item.cantidad" :name="'items['+index+'][cantidad]'"
                                           step="0.001" min="0.001" required @input="calcularSubtotal(index)"
                                           class="form-input-number">
                                </td>
                                <td>
                                    <input type="number" x-model="item.precio_unitario" :name="'items['+index+'][precio_unitario]'"
                                           min="0" required @input="calcularSubtotal(index)"
                                           class="form-input-number">
                                </td>
                                <td class="text-right font-bold text-gray-800" x-text="formatGs(item.subtotal)"></td>
                                <td class="text-center">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                            class="text-red-600 hover:text-red-800 p-1">
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
            <button type="button" @click="addItem()" class="btn-success mt-4">
                + Agregar Producto
            </button>
        </div>

        <!-- Resumen y Notas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-section">
                <h2 class="form-section-title">Notas</h2>
                <textarea name="notas" rows="4" class="form-textarea">{{ old('notas', $pedido->notas) }}</textarea>
            </div>
            <div class="form-section">
                <h2 class="form-section-title">Resumen</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 font-medium">Subtotal:</span>
                        <span class="font-bold text-lg" x-text="formatGs(subtotal)"></span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 font-medium">Descuento (%):</span>
                        <div class="flex items-center gap-2">
                            <input type="number" name="descuento" x-model="descuento" min="0" max="100" step="0.1" @input="calcularTotal()"
                                   class="form-input-number w-24">
                            <span class="text-gray-500 text-sm" x-text="descuentoMonto > 0 ? '-' + formatGs(descuentoMonto) : ''"></span>
                        </div>
                    </div>
                    <div class="border-t-2 border-gray-200 pt-4 flex justify-between items-center">
                        <span class="font-bold text-xl text-gray-800">Total:</span>
                        <span class="font-bold text-2xl text-blue-600" x-text="formatGs(total)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('pedidos-cliente.show', $pedido) }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection
