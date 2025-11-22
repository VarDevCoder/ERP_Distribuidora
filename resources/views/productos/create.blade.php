@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nuevo Producto</h1>
            <p class="text-gray-600 mt-1">Registra un nuevo producto en el catalogo</p>
        </div>
        <a href="{{ route('productos.index') }}" class="btn-secondary">Volver</a>
    </div>

    <!-- Formulario -->
    <form action="{{ route('productos.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="form-section">
            <h2 class="form-section-title">Informacion Basica</h2>

            <div class="space-y-4">
                <!-- Codigo -->
                <div class="form-group">
                    <label class="form-label">Codigo <span class="text-gray-400 font-normal">(opcional - se genera automaticamente)</span></label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}"
                           class="form-input" placeholder="Ej: PROD-00001">
                    @error('codigo')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre -->
                <div class="form-group">
                    <label class="form-label form-label-required">Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                           class="form-input" placeholder="Ej: Laptop Dell Inspiron">
                    @error('nombre')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripcion -->
                <div class="form-group">
                    <label class="form-label">Descripcion</label>
                    <textarea name="descripcion" rows="3" class="form-textarea"
                              placeholder="Descripcion detallada del producto">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Precios (Guaranies)</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Precio de Compra</label>
                    <div class="relative">
                        <input type="number" name="precio_compra" value="{{ old('precio_compra', 0) }}"
                               min="0" required class="form-input text-right pr-12">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Gs.</span>
                    </div>
                    @error('precio_compra')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label form-label-required">Precio de Venta</label>
                    <div class="relative">
                        <input type="number" name="precio_venta" value="{{ old('precio_venta', 0) }}"
                               min="0" required class="form-input text-right pr-12">
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Gs.</span>
                    </div>
                    @error('precio_venta')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Stock e Inventario</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label">Stock Inicial</label>
                    <input type="number" name="stock_actual" value="{{ old('stock_actual', 0) }}"
                           min="0" step="0.001" class="form-input text-right">
                    @error('stock_actual')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Stock Minimo</label>
                    <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 0) }}"
                           min="0" step="0.001" class="form-input text-right">
                    @error('stock_minimo')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label form-label-required">Unidad de Medida</label>
                    <select name="unidad_medida" required class="form-select">
                        <option value="pz" {{ old('unidad_medida') == 'pz' ? 'selected' : '' }}>Pieza</option>
                        <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>Kilogramo</option>
                        <option value="lt" {{ old('unidad_medida') == 'lt' ? 'selected' : '' }}>Litro</option>
                        <option value="caja" {{ old('unidad_medida') == 'caja' ? 'selected' : '' }}>Caja</option>
                        <option value="mt" {{ old('unidad_medida') == 'mt' ? 'selected' : '' }}>Metro</option>
                    </select>
                    @error('unidad_medida')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Estado Activo -->
            <div class="mt-4 flex items-center p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                <input type="checkbox" name="activo" id="activo" value="1"
                       {{ old('activo', true) ? 'checked' : '' }}
                       class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                <label for="activo" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
                    Producto activo (disponible para pedidos y cotizaciones)
                </label>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('productos.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Crear Producto</button>
        </div>
    </form>
</div>
@endsection
