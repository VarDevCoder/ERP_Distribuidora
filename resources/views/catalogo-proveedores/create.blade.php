@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title">Agregar Producto a Proveedor</h1>
            <p class="page-subtitle">Asigna un producto del catálogo ANKOR a un proveedor</p>
        </div>
        <a href="{{ route('catalogo-proveedores.index') }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('catalogo-proveedores.store') }}" method="POST" class="space-y-6" x-data="catalogoForm()">
        @csrf

        <!-- Selección de Proveedor -->
        <div class="form-section">
            <h2 class="form-section-title">Proveedor</h2>
            <div class="form-group">
                <label class="form-label form-label-required">Seleccionar Proveedor</label>
                <select name="proveedor_id" required class="form-select"
                        x-model="proveedorId" @change="cargarProductos()">
                    <option value="">Seleccionar proveedor...</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}"
                                {{ (old('proveedor_id') ?? $proveedorSeleccionado?->id) == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->nombre }} ({{ $proveedor->ruc }})
                        </option>
                    @endforeach
                </select>
                @error('proveedor_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Selección de Producto -->
        <div class="form-section" x-show="proveedorId" x-cloak>
            <h2 class="form-section-title">Producto ANKOR</h2>
            <div class="form-group">
                <label class="form-label form-label-required">Producto</label>

                <!-- Si hay productos precargados (server-side) -->
                @if($productosDisponibles->isNotEmpty())
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
                @else
                    <!-- Carga dinámica via Alpine -->
                    <select name="producto_id" required class="form-select" x-model="productoId">
                        <option value="">Seleccionar producto...</option>
                        <template x-for="(productos, categoria) in productosDisponibles" :key="categoria">
                            <optgroup :label="categoria">
                                <template x-for="producto in productos" :key="producto.id">
                                    <option :value="producto.id" x-text="producto.codigo + ' - ' + producto.nombre"></option>
                                </template>
                            </optgroup>
                        </template>
                    </select>
                    <p x-show="cargando" class="text-sm text-slate-500 mt-2">
                        <svg class="inline w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Cargando productos...
                    </p>
                    <p x-show="!cargando && Object.keys(productosDisponibles).length === 0" class="text-sm text-amber-600 mt-2">
                        Este proveedor ya tiene todos los productos asignados.
                    </p>
                @endif

                @error('producto_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Datos del Proveedor para este producto -->
        <div class="form-section" x-show="proveedorId" x-cloak>
            <h2 class="form-section-title">Información del Proveedor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Código del Proveedor</label>
                    <input type="text" name="codigo_proveedor" value="{{ old('codigo_proveedor') }}"
                           class="form-input" placeholder="Código interno del proveedor">
                    <p class="form-hint">Código que el proveedor usa para este producto</p>
                    @error('codigo_proveedor')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre del Proveedor</label>
                    <input type="text" name="nombre_proveedor" value="{{ old('nombre_proveedor') }}"
                           class="form-input" placeholder="Nombre que usa el proveedor">
                    <p class="form-hint">Cómo el proveedor llama a este producto</p>
                    @error('nombre_proveedor')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Precio y Entrega -->
        <div class="form-section" x-show="proveedorId" x-cloak>
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
        <div class="form-section" x-show="proveedorId" x-cloak>
            <h2 class="form-section-title">Notas</h2>
            <div class="form-group">
                <textarea name="notas" rows="3" class="form-textarea"
                          placeholder="Observaciones sobre este producto...">{{ old('notas') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end space-x-4" x-show="proveedorId" x-cloak>
            <a href="{{ route('catalogo-proveedores.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Agregar al Catálogo</button>
        </div>
    </form>
</div>

<script>
function catalogoForm() {
    return {
        proveedorId: '{{ old('proveedor_id') ?? $proveedorSeleccionado?->id ?? '' }}',
        productoId: '{{ old('producto_id') ?? '' }}',
        productosDisponibles: {},
        cargando: false,

        async cargarProductos() {
            if (!this.proveedorId) {
                this.productosDisponibles = {};
                return;
            }

            this.cargando = true;
            try {
                const response = await fetch(`/catalogo-proveedores/productos-disponibles/${this.proveedorId}`);
                this.productosDisponibles = await response.json();
            } catch (error) {
                console.error('Error cargando productos:', error);
            }
            this.cargando = false;
        },

        init() {
            // Si ya hay proveedor seleccionado y no hay productos precargados
            @if(!$productosDisponibles->isNotEmpty() && $proveedorSeleccionado)
                this.cargarProductos();
            @endif
        }
    }
}
</script>
@endsection
