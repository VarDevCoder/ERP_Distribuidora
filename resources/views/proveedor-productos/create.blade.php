@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title">Agregar Producto al Catálogo</h1>
            <p class="page-subtitle">Selecciona un producto de ANKOR y define tu precio</p>
        </div>
        <a href="{{ route('proveedor-productos.index') }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('proveedor-productos.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Selección de Producto -->
        <div class="form-section">
            <h2 class="form-section-title">Producto ANKOR</h2>
            <div class="form-group">
                <label class="form-label form-label-required">Producto</label>
                <select name="producto_id" required class="form-select">
                    <option value="">Seleccionar producto...</option>
                    @foreach($productosDisponibles as $categoria => $prods)
                        <optgroup label="{{ $categoria }}">
                            @foreach($prods as $producto)
                                <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                    {{ $producto->codigo }} - {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('producto_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Datos del Proveedor -->
        <div class="form-section">
            <h2 class="form-section-title">Mi Información</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Mi Código</label>
                    <input type="text" name="codigo_proveedor" value="{{ old('codigo_proveedor') }}"
                           class="form-input" placeholder="Tu código interno para este producto">
                    @error('codigo_proveedor')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Mi Nombre para este producto</label>
                    <input type="text" name="nombre_proveedor" value="{{ old('nombre_proveedor') }}"
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
                    <input type="number" name="precio" value="{{ old('precio', 0) }}" required min="0"
                           class="form-input" placeholder="Precio en guaraníes">
                    @error('precio')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tiempo de Entrega (días)</label>
                    <input type="number" name="tiempo_entrega_dias" value="{{ old('tiempo_entrega_dias') }}" min="1"
                           class="form-input" placeholder="Ej: 3">
                    @error('tiempo_entrega_dias')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-group mt-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="disponible" value="1" {{ old('disponible', true) ? 'checked' : '' }}
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
                          placeholder="Observaciones sobre este producto...">{{ old('notas') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('proveedor-productos.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Agregar al Catálogo</button>
        </div>
    </form>
</div>
@endsection
