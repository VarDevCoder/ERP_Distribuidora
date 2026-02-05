@extends('layouts.app')

@section('content')
@php
    $itemsIniciales = [['producto_id' => '', 'cantidad_solicitada' => 1, 'precio_unitario' => 0, 'subtotal' => 0]];

    if (isset($pedidoCliente) && $pedidoCliente) {
        $itemsIniciales = $pedidoCliente->items->map(function($i) {
            return [
                'producto_id' => $i->producto_id,
                'cantidad_solicitada' => $i->cantidad,
                'precio_unitario' => 0,
                'subtotal' => 0
            ];
        });
    }
@endphp

<div class="max-w-5xl mx-auto" x-data="itemFormWithPrices({
    items: @json($itemsIniciales),
    quantityField: 'cantidad_solicitada'
})">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nueva Orden de Compra</h1>
            @if(isset($pedidoCliente) && $pedidoCliente)
                <p class="text-gray-600 mt-1">
                    Vinculada a la Solicitud: <span class="font-bold">{{ $pedidoCliente->numero }}</span>
                    - {{ $pedidoCliente->cliente_nombre }}
                </p>
            @endif
        </div>
        <a href="{{ route('ordenes-compra.index') }}" class="btn-secondary">Volver</a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p class="font-bold">Por favor corrige los siguientes errores:</p>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('ordenes-compra.store') }}" method="POST" class="space-y-6">
        @csrf

        @if(isset($pedidoCliente) && $pedidoCliente)
            <input type="hidden" name="pedido_cliente_id" value="{{ $pedidoCliente->id }}">
        @endif

        <div class="form-section">
            <h2 class="form-section-title">Datos del Proveedor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Nombre del Proveedor</label>
                    <input type="text" name="proveedor_nombre" value="{{ old('proveedor_nombre') }}" required
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">RUC</label>
                    <input type="text" name="proveedor_ruc" value="{{ old('proveedor_ruc') }}"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="proveedor_telefono" value="{{ old('proveedor_telefono') }}"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="proveedor_email" value="{{ old('proveedor_email') }}"
                           class="form-input">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="proveedor_direccion" value="{{ old('proveedor_direccion') }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Fechas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Fecha de Orden</label>
                    <input type="date" name="fecha_orden" value="{{ old('fecha_orden', date('Y-m-d')) }}" required
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha de Entrega Esperada</label>
                    <input type="date" name="fecha_entrega_esperada" value="{{ old('fecha_entrega_esperada') }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Productos a Comprar</h2>
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
                                                    <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_compra }}">
                                                        {{ $producto->codigo }} - {{ $producto->nombre }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" x-model="item.cantidad_solicitada" :name="'items['+index+'][cantidad_solicitada]'"
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-section">
                <h2 class="form-section-title">Notas</h2>
                <textarea name="notas" rows="4" class="form-textarea"
                          placeholder="Detalles adicionales para la orden...">{{ old('notas') }}</textarea>
            </div>
            <div class="form-section">
                <h2 class="form-section-title">Resumen Financiero</h2>
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
            <a href="{{ route('ordenes-compra.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Crear Orden de Compra</button>
        </div>
    </form>
</div>
@endsection
