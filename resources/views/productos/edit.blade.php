@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{
    precioCompra: {{ old('precio_compra', $producto->precio_compra) }},
    precioVenta: {{ old('precio_venta', $producto->precio_venta) }},
    margen: {{ $margen }},
    autoCalcular: false,
    calcularVenta() {
        if (this.autoCalcular) {
            this.precioVenta = Math.round(this.precioCompra * (1 + this.margen / 100));
        }
    },
    getMargenReal() {
        if (this.precioCompra <= 0) return 0;
        return ((this.precioVenta - this.precioCompra) / this.precioCompra * 100).toFixed(1);
    }
}">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Editar Producto</h1>
            <p class="text-gray-600 mt-1">{{ $producto->codigo }} - {{ $producto->nombre }}</p>
        </div>
        <a href="{{ route('productos.show', $producto) }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('productos.update', $producto) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h2 class="form-section-title">Información Básica</h2>
            <div class="space-y-4">
                <div class="form-group">
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $producto->codigo) }}"
                           class="form-input" placeholder="Ej: PROD-00001">
                    @error('codigo')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label form-label-required">Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required
                           class="form-input" placeholder="Ej: Laptop Dell Inspiron">
                    @error('nombre')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select name="categoria_id" class="form-select">
                        <option value="">Sin categoria</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" rows="3" class="form-textarea"
                              placeholder="Descripción detallada del producto">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Precios (Guaraníes)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Precio de Compra</label>
                    <div class="relative">
                        <input type="number" name="precio_compra" x-model="precioCompra"
                               @input="calcularVenta()"
                               min="0" required class="form-input-number pr-12">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Gs.</span>
                    </div>
                    @error('precio_compra')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label form-label-required">Precio de Venta</label>
                    <div class="relative">
                        <input type="number" name="precio_venta" x-model="precioVenta"
                               @input="autoCalcular = false"
                               min="0" required class="form-input-number pr-12">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Gs.</span>
                    </div>
                    @error('precio_venta')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between p-3 rounded-lg"
                 :class="getMargenReal() >= {{ $margen }} ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200'">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Margen:</span>
                    <span class="text-lg font-bold" :class="getMargenReal() >= {{ $margen }} ? 'text-green-700' : 'text-yellow-700'"
                          x-text="getMargenReal() + '%'"></span>
                    <span class="text-xs text-gray-500">(objetivo: {{ $margen }}%)</span>
                </div>
                <button type="button" @click="autoCalcular = true; calcularVenta()"
                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    Recalcular al {{ $margen }}%
                </button>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Stock e Inventario</h2>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm font-medium text-blue-900">Stock Actual: {{ number_format($producto->stock_actual, 3) }} {{ $producto->unidad_medida }}</p>
                <p class="text-xs text-blue-700">El stock se actualiza automáticamente al recibir mercadería</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}"
                           min="0" step="0.001" class="form-input-number">
                    @error('stock_minimo')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label form-label-required">Unidad de Medida</label>
                    <select name="unidad_medida" required class="form-select">
                        @foreach($unidades as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ old('unidad_medida', $producto->unidad_medida) == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    @error('unidad_medida')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4 flex items-center p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                <input type="checkbox" name="activo" id="activo" value="1"
                       {{ old('activo', $producto->activo) ? 'checked' : '' }}
                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                <label for="activo" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                    Producto activo (disponible para pedidos y cotizaciones)
                </label>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('productos.show', $producto) }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Actualizar Producto</button>
        </div>
    </form>

    <div class="mt-4 pt-4 border-t border-gray-200">
        <form action="{{ route('productos.destroy', $producto) }}" method="POST"
              @submit.prevent="confirmSubmit($event, {
                  title: '¿Eliminar producto?',
                  text: 'Se eliminará {{ $producto->nombre }} permanentemente.',
                  confirmButtonText: 'Sí, eliminar'
              })">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-danger">Eliminar Producto</button>
        </form>
    </div>
</div>
@endsection
