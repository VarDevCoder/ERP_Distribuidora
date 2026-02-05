@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="page-title">Mi Catálogo de Productos</h1>
            <p class="page-subtitle">Administra tus productos y precios</p>
        </div>
        <a href="{{ route('proveedor-productos.create') }}" class="btn-primary">+ Agregar Producto</a>
    </div>

    <!-- Filtros -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                           class="form-input" placeholder="Buscar por código o nombre...">
                </div>
                <div>
                    <select name="disponible" class="form-select">
                        <option value="">Disponibilidad</option>
                        <option value="1" {{ request('disponible') === '1' ? 'selected' : '' }}>Disponible</option>
                        <option value="0" {{ request('disponible') === '0' ? 'selected' : '' }}>No disponible</option>
                    </select>
                </div>
                <div>
                    <select name="categoria_id" class="form-select">
                        <option value="">Categoría</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="btn-primary btn-sm">Filtrar</button>
                    <a href="{{ route('proveedor-productos.index') }}" class="btn-secondary btn-sm">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card">
        <div class="card-body p-0">
            @if($productos->count() > 0)
                <div class="table-container border-0 shadow-none rounded-none">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto ANKOR</th>
                                <th>Mi Código</th>
                                <th class="text-right">Mi Precio</th>
                                <th class="text-center">Entrega</th>
                                <th class="text-center">Disponible</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $pp)
                                <tr>
                                    <td>
                                        <div class="font-medium text-gray-900">{{ $pp->producto->nombre }}</div>
                                        <div class="text-xs text-gray-500">{{ $pp->producto->codigo }} - {{ $pp->producto->categoria?->nombre ?? 'Sin Categoría' }}</div>
                                        @if($pp->nombre_proveedor)
                                            <div class="text-xs text-blue-600">Mi nombre: {{ $pp->nombre_proveedor }}</div>
                                        @endif
                                    </td>
                                    <td class="text-sm text-gray-600">{{ $pp->codigo_proveedor ?? '-' }}</td>
                                    <td class="text-right font-bold text-gray-900">{{ $pp->precio_formateado }}</td>
                                    <td class="text-center text-sm text-gray-600">{{ $pp->tiempo_entrega_formateado }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('proveedor-productos.toggle-disponible', $pp) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="badge {{ $pp->disponible ? 'badge-success' : 'badge-danger' }} cursor-pointer">
                                                {{ $pp->disponible ? 'Sí' : 'No' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('proveedor-productos.edit', $pp) }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm">Editar</a>
                                            <form action="{{ route('proveedor-productos.destroy', $pp) }}" method="POST"
                                                  onsubmit="return confirm('¿Eliminar este producto de su catálogo?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $productos->withQueryString()->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <p class="empty-state-text">No tiene productos en su catálogo</p>
                    <a href="{{ route('proveedor-productos.create') }}" class="btn-primary mt-4">Agregar primer producto</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
