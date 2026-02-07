@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title">Editar Producto de Proveedor</h1>
            <p class="page-subtitle">Modifica los datos del producto en el catálogo</p>
        </div>
        <a href="{{ route('catalogo-proveedores.index', ['proveedor_id' => $catalogoProveedor->proveedor_id]) }}" class="btn-secondary">Volver</a>
    </div>

    <!-- Info del producto y proveedor -->
    <div class="card mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-1">Proveedor</h3>
                <p class="text-lg font-semibold text-slate-900">{{ $catalogoProveedor->proveedor->nombre }}</p>
                <p class="text-sm text-slate-500">{{ $catalogoProveedor->proveedor->ruc }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-slate-500 mb-1">Producto ANKOR</h3>
                <p class="text-lg font-semibold text-slate-900">{{ $catalogoProveedor->producto->nombre }}</p>
                <p class="text-sm text-slate-500">
                    {{ $catalogoProveedor->producto->codigo }}
                    @if($catalogoProveedor->producto->categoria)
                        <span class="text-slate-400">•</span>
                        {{ $catalogoProveedor->producto->categoria->nombre }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('catalogo-proveedores.update', $catalogoProveedor) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Datos del Proveedor para este producto -->
        <div class="form-section">
            <h2 class="form-section-title">Información del Proveedor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Código del Proveedor</label>
                    <input type="text" name="codigo_proveedor"
                           value="{{ old('codigo_proveedor', $catalogoProveedor->codigo_proveedor) }}"
                           class="form-input" placeholder="Código interno del proveedor">
                    <p class="form-hint">Código que el proveedor usa para este producto</p>
                    @error('codigo_proveedor')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre del Proveedor</label>
                    <input type="text" name="nombre_proveedor"
                           value="{{ old('nombre_proveedor', $catalogoProveedor->nombre_proveedor) }}"
                           class="form-input" placeholder="Nombre que usa el proveedor">
                    <p class="form-hint">Cómo el proveedor llama a este producto</p>
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
                           value="{{ old('precio', $catalogoProveedor->precio) }}" required min="0"
                           class="form-input" placeholder="Precio en guaraníes">
                    @error('precio')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tiempo de Entrega (días)</label>
                    <input type="number" name="tiempo_entrega_dias"
                           value="{{ old('tiempo_entrega_dias', $catalogoProveedor->tiempo_entrega_dias) }}" min="1"
                           class="form-input" placeholder="Ej: 3">
                    @error('tiempo_entrega_dias')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="form-group mt-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="disponible" value="1"
                           {{ old('disponible', $catalogoProveedor->disponible) ? 'checked' : '' }}
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
                          placeholder="Observaciones sobre este producto...">{{ old('notas', $catalogoProveedor->notas) }}</textarea>
            </div>
        </div>

        <!-- Historial -->
        <div class="form-section bg-slate-50">
            <h2 class="form-section-title">Información del Registro</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-slate-500">Creado:</span>
                    <span class="text-slate-700">{{ $catalogoProveedor->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Última actualización:</span>
                    <span class="text-slate-700">{{ $catalogoProveedor->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="flex justify-between">
            <form action="{{ route('catalogo-proveedores.destroy', $catalogoProveedor) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar este producto del catálogo del proveedor?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar del catálogo
                </button>
            </form>
            <div class="flex gap-4">
                <a href="{{ route('catalogo-proveedores.index', ['proveedor_id' => $catalogoProveedor->proveedor_id]) }}"
                   class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </form>
</div>
@endsection
