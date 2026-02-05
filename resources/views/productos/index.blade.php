@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Productos</h1>
            <p class="page-subtitle">Gestiona el catalogo de productos</p>
        </div>
        <a href="{{ route('productos.create') }}" class="btn-primary">
            + Nuevo Producto
        </a>
    </div>

    <!-- Filtros -->
    <div class="form-section mb-6">
        <form action="{{ route('productos.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="form-label">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Nombre, codigo..." class="form-input">
            </div>
            <div class="w-48">
                <label class="form-label">Categoria</label>
                <select name="categoria_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-36">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrar</button>
            <a href="{{ route('productos.index') }}" class="btn-secondary">Limpiar</a>
        </form>
    </div>

    <!-- Tabla de Productos -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Categoria</th>
                    <th class="text-right">Precio Compra</th>
                    <th class="text-right">Precio Venta</th>
                    <th class="text-right">Stock</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr>
                        <td class="font-bold text-gray-900">
                            {{ $producto->codigo }}
                        </td>
                        <td>
                            <div class="font-medium text-gray-900">{{ $producto->nombre }}</div>
                            @if($producto->descripcion)
                                <div class="text-xs text-gray-500">{{ Str::limit($producto->descripcion, 40) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($producto->categoria)
                                <span class="badge badge-info">{{ $producto->categoria->nombre }}</span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="text-right font-mono">
                            {{ number_format($producto->precio_compra, 0, ',', '.') }} Gs.
                        </td>
                        <td class="text-right font-mono font-bold">
                            {{ number_format($producto->precio_venta, 0, ',', '.') }} Gs.
                        </td>
                        <td class="text-right">
                            <span class="{{ $producto->stock_actual <= $producto->stock_minimo ? 'text-red-600 font-bold' : '' }}">
                                {{ number_format($producto->stock_actual, 2) }} {{ $producto->unidad_medida }}
                            </span>
                            @if($producto->stock_actual <= $producto->stock_minimo)
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-bold bg-red-200 text-red-800">
                                    Bajo
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($producto->activo)
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-200 text-green-800">
                                    Activo
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-700">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:text-blue-900 font-medium">Ver</a>
                                <a href="{{ route('productos.edit', $producto) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Editar</a>
                                <a href="{{ route('inventario.kardex', $producto) }}" class="text-green-600 hover:text-green-900 font-medium">Kardex</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            No hay productos registrados
                            <br>
                            <a href="{{ route('productos.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                Crear el primer producto
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($productos->hasPages())
        <div class="mt-4">{{ $productos->links() }}</div>
    @endif
</div>
@endsection
