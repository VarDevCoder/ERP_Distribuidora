@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Editar Producto</h1>
        <p class="text-gray-600 mt-1">{{ $producto->codigo }} - {{ $producto->nombre }}</p>
    </div>

    <!-- Formulario -->
    <form action="{{ route('productos.update', $producto) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Código -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Código
                </label>
                <input type="text"
                       name="codigo"
                       value="{{ old('codigo', $producto->codigo) }}"
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
                       value="{{ old('nombre', $producto->nombre) }}"
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
                          placeholder="Descripción detallada del producto">{{ old('descripcion', $producto->descripcion) }}</textarea>
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
                               value="{{ old('precio_compra', $producto->precio_compra) }}"
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
                               value="{{ old('precio_venta', $producto->precio_venta) }}"
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

            <!-- Stock Actual (Solo lectura - se modifica desde inventario) -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm font-medium text-blue-900">Stock Actual: {{ $producto->stock_actual }} {{ $producto->unidad_medida }}</p>
                </div>
                <p class="text-xs text-blue-700">El stock se actualiza automáticamente al aplicar notas de remisión</p>
            </div>

            <!-- Stock Mínimo y Unidad de Medida -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Stock Mínimo
                    </label>
                    <input type="number"
                           name="stock_minimo"
                           value="{{ old('stock_minimo', $producto->stock_minimo) }}"
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
                        <option value="pz" {{ old('unidad_medida', $producto->unidad_medida) == 'pz' ? 'selected' : '' }}>Pieza</option>
                        <option value="kg" {{ old('unidad_medida', $producto->unidad_medida) == 'kg' ? 'selected' : '' }}>Kilogramo</option>
                        <option value="lt" {{ old('unidad_medida', $producto->unidad_medida) == 'lt' ? 'selected' : '' }}>Litro</option>
                        <option value="caja" {{ old('unidad_medida', $producto->unidad_medida) == 'caja' ? 'selected' : '' }}>Caja</option>
                        <option value="mt" {{ old('unidad_medida', $producto->unidad_medida) == 'mt' ? 'selected' : '' }}>Metro</option>
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
                       {{ old('activo', $producto->activo) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="activo" class="ml-2 block text-sm text-gray-900">
                    Producto activo (disponible para presupuestos)
                </label>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
            <!-- Botón Eliminar -->
            <button type="button"
                    onclick="if(confirm('¿Estás seguro de eliminar este producto?')) document.getElementById('delete-form').submit()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Eliminar
            </button>

            <!-- Botones de Acción -->
            <div class="flex space-x-4">
                <a href="{{ route('productos.show', $producto) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Actualizar Producto
                </button>
            </div>
        </div>
    </form>

    <!-- Formulario de Eliminación (oculto) -->
    <form id="delete-form"
          action="{{ route('productos.destroy', $producto) }}"
          method="POST"
          class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
