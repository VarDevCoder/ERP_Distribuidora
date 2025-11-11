@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Nuevo Producto</h1>
        <p class="text-gray-600 mt-1">Registra un nuevo producto en el catálogo</p>
    </div>

    <!-- Formulario -->
    <form action="{{ route('productos.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf

        <div class="space-y-6">
            <!-- Código (opcional - se genera automáticamente) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Código
                    <span class="text-gray-500 font-normal">(opcional - se genera automáticamente)</span>
                </label>
                <input type="text"
                       name="codigo"
                       value="{{ old('codigo') }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Ej: PROD-00001">
                @error('codigo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre <span class="text-red-600">*</span>
                </label>
                <input type="text"
                       name="nombre"
                       value="{{ old('nombre') }}"
                       required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Ej: Laptop Dell Inspiron">
                @error('nombre')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción
                </label>
                <textarea name="descripcion"
                          rows="3"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Descripción detallada del producto">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precios -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Precio de Compra <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number"
                               name="precio_compra"
                               value="{{ old('precio_compra', 0) }}"
                               step="0.01"
                               min="0"
                               required
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    @error('precio_compra')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Precio de Venta <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number"
                               name="precio_venta"
                               value="{{ old('precio_venta', 0) }}"
                               step="0.01"
                               min="0"
                               required
                               class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    @error('precio_venta')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stock -->
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stock Inicial
                    </label>
                    <input type="number"
                           name="stock_actual"
                           value="{{ old('stock_actual', 0) }}"
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('stock_actual')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stock Mínimo
                    </label>
                    <input type="number"
                           name="stock_minimo"
                           value="{{ old('stock_minimo', 0) }}"
                           min="0"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('stock_minimo')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Unidad de Medida <span class="text-red-600">*</span>
                    </label>
                    <select name="unidad_medida"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pz" {{ old('unidad_medida') == 'pz' ? 'selected' : '' }}>Pieza</option>
                        <option value="kg" {{ old('unidad_medida') == 'kg' ? 'selected' : '' }}>Kilogramo</option>
                        <option value="lt" {{ old('unidad_medida') == 'lt' ? 'selected' : '' }}>Litro</option>
                        <option value="caja" {{ old('unidad_medida') == 'caja' ? 'selected' : '' }}>Caja</option>
                        <option value="mt" {{ old('unidad_medida') == 'mt' ? 'selected' : '' }}>Metro</option>
                    </select>
                    @error('unidad_medida')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Estado Activo -->
            <div class="flex items-center">
                <input type="checkbox"
                       name="activo"
                       id="activo"
                       {{ old('activo', true) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="activo" class="ml-2 block text-sm text-gray-900">
                    Producto activo (disponible para presupuestos)
                </label>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('productos.index') }}"
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Crear Producto
            </button>
        </div>
    </form>
</div>
@endsection
