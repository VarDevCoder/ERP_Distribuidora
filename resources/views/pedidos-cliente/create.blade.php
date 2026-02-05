@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto" x-data="itemFormWithPrices()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nueva Solicitud de Cliente</h1>
            <p class="text-gray-600 mt-1">Flujo ANKOR - Registra una nueva solicitud</p>
        </div>
        <a href="{{ route('pedidos-cliente.index') }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('pedidos-cliente.store') }}" method="POST" class="space-y-6">
        @csrf

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
                                    {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} - {{ $cliente->ruc ?? 'Sin RUC' }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="cliente_id" id="cliente_id" value="{{ old('cliente_id') }}">
                </div>
                <div class="form-group">
                    <label class="form-label form-label-required">Nombre</label>
                    <input type="text" name="cliente_nombre" id="cliente_nombre" value="{{ old('cliente_nombre') }}" required
                           class="form-input" placeholder="Nombre completo del cliente">
                </div>
                <div class="form-group">
                    <label class="form-label">RUC</label>
                    <input type="text" name="cliente_ruc" id="cliente_ruc" value="{{ old('cliente_ruc') }}"
                           class="form-input" placeholder="Ej: 80012345-6">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="cliente_telefono" id="cliente_telefono" value="{{ old('cliente_telefono') }}"
                           class="form-input" placeholder="Ej: 0981 123 456">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="cliente_email" id="cliente_email" value="{{ old('cliente_email') }}"
                           class="form-input" placeholder="cliente@email.com">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="cliente_direccion" id="cliente_direccion" value="{{ old('cliente_direccion') }}"
                           class="form-input" placeholder="Dirección de entrega">
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
                    document.getElementById('cliente_nombre').value = '';
                    document.getElementById('cliente_ruc').value = '';
                    document.getElementById('cliente_telefono').value = '';
                    document.getElementById('cliente_email').value = '';
                    document.getElementById('cliente_direccion').value = '';
                }
            }
        </script>

        <!-- Fechas -->
        <div class="form-section">
            <h2 class="form-section-title">Fechas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Fecha de Solicitud</label>
                    <input type="date" name="fecha_pedido" value="{{ old('fecha_pedido', date('Y-m-d')) }}" required
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha de Entrega Solicitada</label>
                    <input type="date" name="fecha_entrega_solicitada" value="{{ old('fecha_entrega_solicitada') }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <!-- Items del Pedido -->
        <div class="form-section">
            <h2 class="form-section-title">Productos</h2>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-right w-28">Cantidad</th>
                            <th class="text-right w-40">Precio Unit. (Gs.)</th>
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
                                        <option value="">Seleccionar producto...</option>
                                        @foreach($productos as $categoria => $prods)
                                            <optgroup label="{{ $categoria }}">
                                                @foreach($prods as $producto)
                                                    <option value="{{ $producto->id }}"
                                                            data-precio="{{ $producto->precio_venta }}"
                                                            data-costo="{{ $producto->precio_compra }}">
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
                <textarea name="notas" rows="4" class="form-textarea"
                          placeholder="Observaciones de la solicitud...">{{ old('notas') }}</textarea>
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
            <a href="{{ route('pedidos-cliente.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Crear Solicitud</button>
        </div>
    </form>
</div>
@endsection
