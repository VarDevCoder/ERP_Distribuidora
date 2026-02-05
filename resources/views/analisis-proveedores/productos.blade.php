@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <div>
            <h1 class="page-title">Comparación de Precios</h1>
            <p class="page-subtitle">Todos los productos con sus proveedores y precios</p>
        </div>
        <a href="{{ route('analisis-proveedores.index') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Dashboard
        </a>
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
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="con_proveedores" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('con_proveedores') === '1' ? 'selected' : '' }}>Con proveedores</option>
                        <option value="0" {{ request('con_proveedores') === '0' ? 'selected' : '' }}>Sin proveedores</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('analisis-proveedores.productos') }}" class="btn-secondary btn-sm">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="card">
        <div class="card-body p-0">
            @if($productos->count() > 0)
                <div class="table-container border-0 shadow-none rounded-none">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Proveedores</th>
                                <th class="text-right">Precio Mín.</th>
                                <th class="text-right">Precio Prom.</th>
                                <th class="text-right">Precio Máx.</th>
                                <th class="text-right">Tu Precio Compra</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                                <tr>
                                    <td>
                                        <div class="font-medium text-slate-900">{{ $producto->nombre }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $producto->codigo }} — {{ $producto->categoria?->nombre ?? 'Sin Categoría' }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($producto->cantidad_proveedores > 0)
                                            <span class="badge badge-info">{{ $producto->cantidad_proveedores }}</span>
                                        @else
                                            <span class="badge badge-neutral">0</span>
                                        @endif
                                    </td>
                                    <td class="text-right tabular-nums">
                                        @if($producto->precio_minimo)
                                            <span class="text-emerald-600 font-medium">{{ number_format($producto->precio_minimo, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right tabular-nums">
                                        @if($producto->precio_promedio)
                                            <span class="font-bold text-slate-900">{{ number_format($producto->precio_promedio, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right tabular-nums">
                                        @if($producto->precio_maximo)
                                            <span class="text-rose-600">{{ number_format($producto->precio_maximo, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <span class="font-bold text-sky-600 tabular-nums">{{ number_format($producto->precio_compra, 0, ',', '.') }}</span>
                                        @if($producto->precio_promedio && $producto->precio_compra != $producto->precio_promedio)
                                            @if($producto->precio_compra > $producto->precio_promedio)
                                                <div class="text-xs text-rose-500 flex items-center justify-end gap-0.5 mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                                    </svg>
                                                    {{ number_format($producto->precio_compra - $producto->precio_promedio, 0, ',', '.') }} más
                                                </div>
                                            @else
                                                <div class="text-xs text-emerald-500 flex items-center justify-end gap-0.5 mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                                    </svg>
                                                    {{ number_format($producto->precio_promedio - $producto->precio_compra, 0, ',', '.') }} menos
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($producto->cantidad_proveedores > 0)
                                            <div class="flex gap-2 justify-center">
                                                <a href="{{ route('analisis-proveedores.producto', $producto) }}"
                                                   class="btn-ghost btn-xs text-sky-600">
                                                    Ver
                                                </a>
                                                @if($producto->precio_compra != $producto->precio_promedio)
                                                    <form action="{{ route('analisis-proveedores.actualizar-precios') }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                                        <button type="submit" class="btn-ghost btn-xs text-emerald-600"
                                                                title="Actualizar precio a promedio">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100">
                    {{ $productos->withQueryString()->links() }}
                </div>
            @else
                <div class="empty-state py-12">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="empty-state-text">No se encontraron productos</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
