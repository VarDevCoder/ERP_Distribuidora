@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Catálogo de Proveedores</h1>
        <p class="page-subtitle">Gestiona los productos y precios de cada proveedor</p>
    </div>
    <a href="{{ route('catalogo-proveedores.create') }}" class="btn-primary">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Agregar Producto
    </a>
</div>

<!-- Filtros -->
<div class="card mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="form-label">Proveedor</label>
            <select name="proveedor_id" class="form-select" onchange="this.form.submit()">
                <option value="">Todos los proveedores</option>
                @foreach($proveedores as $proveedor)
                    <option value="{{ $proveedor->id }}" {{ request('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                        {{ $proveedor->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Categoría</label>
            <select name="categoria_id" class="form-select" onchange="this.form.submit()">
                <option value="">Todas</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Disponibilidad</label>
            <select name="disponible" class="form-select" onchange="this.form.submit()">
                <option value="">Todos</option>
                <option value="1" {{ request('disponible') === '1' ? 'selected' : '' }}>Disponibles</option>
                <option value="0" {{ request('disponible') === '0' ? 'selected' : '' }}>No disponibles</option>
            </select>
        </div>
        <div>
            <label class="form-label">Buscar</label>
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                   class="form-input" placeholder="Código, nombre...">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn-primary">Filtrar</button>
            <a href="{{ route('catalogo-proveedores.index') }}" class="btn-secondary">Limpiar</a>
        </div>
    </form>
</div>

<!-- Estadísticas rápidas -->
@if(request('proveedor_id'))
    @php
        $proveedorActual = $proveedores->find(request('proveedor_id'));
        $totalProductos = $catalogos->total();
        $disponibles = \App\Models\ProveedorProducto::where('proveedor_id', request('proveedor_id'))->where('disponible', true)->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div class="stat-card-value text-sky-600">{{ $proveedorActual?->nombre }}</div>
            <div class="stat-card-label">Proveedor seleccionado</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value">{{ $totalProductos }}</div>
            <div class="stat-card-label">Productos en catálogo</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-value text-emerald-600">{{ $disponibles }}</div>
            <div class="stat-card-label">Disponibles</div>
        </div>
    </div>
@endif

<!-- Tabla de productos -->
<div class="card overflow-hidden">
    @if($catalogos->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="text-lg font-medium text-slate-600 mb-2">No hay productos registrados</h3>
            <p class="text-slate-500 mb-4">Comienza agregando productos al catálogo de proveedores</p>
            <a href="{{ route('catalogo-proveedores.create') }}" class="btn-primary">
                Agregar primer producto
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Proveedor</th>
                        <th>Producto ANKOR</th>
                        <th>Código Prov.</th>
                        <th class="text-right">Precio</th>
                        <th class="text-center">Entrega</th>
                        <th class="text-center">Estado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($catalogos as $catalogo)
                        <tr class="hover:bg-slate-50">
                            <td>
                                <div class="font-medium text-slate-900">{{ $catalogo->proveedor->nombre }}</div>
                                <div class="text-xs text-slate-500">{{ $catalogo->proveedor->ruc }}</div>
                            </td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $catalogo->producto->nombre }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $catalogo->producto->codigo }}
                                    @if($catalogo->producto->categoria)
                                        <span class="text-slate-400">•</span>
                                        {{ $catalogo->producto->categoria->nombre }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($catalogo->codigo_proveedor)
                                    <span class="font-mono text-sm">{{ $catalogo->codigo_proveedor }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="price-amount font-semibold">
                                    {{ number_format($catalogo->precio, 0, ',', '.') }}
                                </span>
                                <span class="price-currency">Gs.</span>
                            </td>
                            <td class="text-center">
                                @if($catalogo->tiempo_entrega_dias)
                                    <span class="text-sm">{{ $catalogo->tiempo_entrega_dias }} días</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('catalogo-proveedores.toggle-disponible', $catalogo) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="focus:outline-none">
                                        @if($catalogo->disponible)
                                            <span class="badge badge-success">Disponible</span>
                                        @else
                                            <span class="badge badge-secondary">No disponible</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('catalogo-proveedores.edit', $catalogo) }}"
                                       class="text-sky-600 hover:text-sky-800" title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('catalogo-proveedores.destroy', $catalogo) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar este producto del catálogo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($catalogos->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $catalogos->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
