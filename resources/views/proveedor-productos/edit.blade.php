@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title">Editar Producto</h1>
            <p class="page-subtitle">{{ $producto->producto->nombre }}</p>
        </div>
        <a href="{{ route('proveedor-productos.index') }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('proveedor-productos.update', $producto) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Producto ANKOR (solo lectura) -->
        <div class="form-section">
            <h2 class="form-section-title">Producto ANKOR</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="font-medium text-gray-900">{{ $producto->producto->nombre }}</div>
                <div class="text-sm text-gray-500">Código: {{ $producto->producto->codigo }} - {{ $producto->producto->categoria?->nombre ?? 'Sin Categoría' }}</div>
            </div>
        </div>

        <!-- Datos del Proveedor -->
        <div class="form-section">
            <h2 class="form-section-title">Mi Información</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Mi Código</label>
                    <input type="text" name="codigo_proveedor"
                           value="{{ old('codigo_proveedor', $producto->codigo_proveedor) }}"
                           class="form-input" placeholder="Tu código interno">
                    @error('codigo_proveedor')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Mi Nombre para este producto</label>
                    <input type="text" name="nombre_proveedor"
                           value="{{ old('nombre_proveedor', $producto->nombre_proveedor) }}"
                           class="form-input" placeholder="Cómo llamas a este producto">
                    @error('nombre_proveedor')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Precio y Entrega -->
        <div class="form-section">
            <h2 class="form-section-title">Precio y Entrega</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Precio (Gs.)</label>
                    <input type="number" name="precio"
                           value="{{ old('precio', $producto->precio) }}" required min="0"
                           class="form-input">
                    @error('precio')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tiempo de Entrega (días)</label>
                    <input type="number" name="tiempo_entrega_dias"
                           value="{{ old('tiempo_entrega_dias', $producto->tiempo_entrega_dias) }}" min="1"
                           class="form-input">
                    @error('tiempo_entrega_dias')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-group mt-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="disponible" value="1"
                           {{ old('disponible', $producto->disponible) ? 'checked' : '' }}
                           class="form-checkbox">
                    <span class="text-sm text-gray-700">Producto disponible actualmente</span>
                </label>
            </div>
        </div>

        <!-- Notas -->
        <div class="form-section">
            <h2 class="form-section-title">Notas</h2>
            <div class="form-group">
                <textarea name="notas" rows="3" class="form-textarea"
                          placeholder="Observaciones...">{{ old('notas', $producto->notas) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('proveedor-productos.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection
