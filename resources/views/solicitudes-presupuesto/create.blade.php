@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto" x-data="itemFormSimple()">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nueva Solicitud de Presupuesto</h1>
            <p class="text-gray-600 mt-1">Generar solicitud de cotización para proveedor</p>
        </div>
        <a href="{{ route('solicitudes-presupuesto.index') }}" class="btn-secondary">Volver</a>
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

    <form action="{{ route('solicitudes-presupuesto.store') }}" method="POST" class="space-y-6">
        @csrf

        @if(isset($pedidoCliente) && $pedidoCliente)
            <input type="hidden" name="pedido_cliente_id" value="{{ $pedidoCliente->id }}">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex justify-between items-center">
                <div>
                    <span class="text-blue-800 font-bold">Vinculado a la Solicitud de Cliente:</span>
                    <span class="ml-2 text-blue-600">{{ $pedidoCliente->numero }}</span>
                    <span class="text-sm text-gray-500 ml-2">({{ $pedidoCliente->cliente_nombre }})</span>
                </div>
            </div>
        @endif

        <div class="form-section">
            <h2 class="form-section-title">Datos de la Solicitud</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Proveedor</label>
                    <select name="proveedor_id" class="form-select" required>
                        <option value="">Seleccionar proveedor...</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                {{ $proveedor->razon_social }} - {{ $proveedor->ruc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Fecha Límite de Respuesta</label>
                    <input type="date" name="fecha_limite_respuesta"
                           value="{{ old('fecha_limite_respuesta') }}"
                           class="form-input">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Mensaje para el Proveedor</label>
                    <textarea name="mensaje_solicitud" rows="3" class="form-textarea"
                              placeholder="Comentarios o instrucciones para el proveedor...">{{ old('mensaje_solicitud') }}</textarea>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Productos a Cotizar</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-right w-32">Cantidad</th>
                            <th class="w-16 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td>
                                    <select :name="'items['+index+'][producto_id]'" x-model="item.producto_id"
                                            class="form-select" required>
                                        <option value="">Seleccionar producto...</option>
                                        @foreach($productos as $categoria => $prods)
                                            <optgroup label="{{ $categoria }}">
                                                @foreach($prods as $producto)
                                                    <option value="{{ $producto->id }}">
                                                        {{ $producto->codigo }} - {{ $producto->nombre }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" :name="'items['+index+'][cantidad_solicitada]'" x-model="item.cantidad"
                                           step="0.001" min="0.001" class="form-input-number" required>
                                </td>
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

        <div class="flex justify-end space-x-4">
            <a href="{{ route('solicitudes-presupuesto.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Enviar Solicitud</button>
        </div>
    </form>
</div>
@endsection
