@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $proveedor->razon_social }}</h1>
            <p class="page-subtitle">Scorecard del Proveedor</p>
        </div>
        <a href="{{ route('analisis-proveedores.index') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Dashboard
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-sky-600">{{ $stats['total_productos'] }}</div>
            <div class="stat-card-label">Total Productos</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-emerald-600">{{ $stats['productos_disponibles'] }}</div>
            <div class="stat-card-label">Disponibles</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-amber-600">{{ number_format($stats['precio_promedio'] ?? 0, 0, ',', '.') }}</div>
            <div class="stat-card-label">Precio Promedio</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-violet-600">{{ round($stats['tiempo_entrega_promedio'] ?? 0) }}</div>
            <div class="stat-card-label">Días Entrega Prom.</div>
        </div>
    </div>

    <!-- Mejores Precios -->
    @if($mejoresPrecios->count() > 0)
        <div class="card mb-6">
            <div class="card-header flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">Productos con Mejor Precio ({{ $mejoresPrecios->count() }})</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-container border-0 shadow-none rounded-none">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-right">Su Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mejoresPrecios as $pp)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-slate-900">{{ $pp->producto->nombre }}</div>
                                                <div class="text-xs text-slate-400">{{ $pp->producto->codigo }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right font-bold text-emerald-600 tabular-nums">{{ $pp->precio_formateado }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Todos sus Productos -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-bold text-slate-900">Catálogo Completo ({{ $proveedor->proveedorProductos->count() }})</h2>
        </div>
        <div class="card-body p-0">
            @if($proveedor->proveedorProductos->count() > 0)
                <div class="table-container border-0 shadow-none rounded-none">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto ANKOR</th>
                                <th>Su Código</th>
                                <th class="text-right">Precio</th>
                                <th class="text-center">Entrega</th>
                                <th class="text-center">Disponible</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proveedor->proveedorProductos as $pp)
                                <tr>
                                    <td>
                                        <a href="{{ route('analisis-proveedores.producto', $pp->producto) }}"
                                           class="font-medium text-sky-600 hover:text-sky-700">
                                            {{ $pp->producto->nombre }}
                                        </a>
                                        <div class="text-xs text-slate-400">{{ $pp->producto->codigo }} - {{ $pp->producto->categoria?->nombre ?? 'Sin Cat.' }}</div>
                                    </td>
                                    <td class="text-sm text-slate-600">{{ $pp->codigo_proveedor ?? '—' }}</td>
                                    <td class="text-right font-bold tabular-nums">{{ $pp->precio_formateado }}</td>
                                    <td class="text-center text-sm text-slate-600">{{ $pp->tiempo_entrega_formateado }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $pp->disponible ? 'badge-success' : 'badge-danger' }}">
                                            {{ $pp->disponible ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="empty-state-text">Este proveedor no tiene productos en su catálogo</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
