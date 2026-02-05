@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $producto->nombre }}</h1>
            <p class="page-subtitle">{{ $producto->codigo }} — Comparación de precios</p>
        </div>
        <a href="{{ route('analisis-proveedores.productos') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <!-- Info del Producto -->
    <div class="card mb-6">
        <div class="card-body">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Categoría</div>
                    <div class="font-semibold text-slate-900 mt-1">{{ $producto->categoria?->nombre ?? 'Sin Categoría' }}</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tu Precio Compra</div>
                    <div class="font-bold text-sky-600 text-lg mt-1 tabular-nums">{{ number_format($producto->precio_compra, 0, ',', '.') }} Gs.</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tu Precio Venta</div>
                    <div class="font-bold text-emerald-600 text-lg mt-1 tabular-nums">{{ number_format($producto->precio_venta, 0, ',', '.') }} Gs.</div>
                </div>
                <div>
                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wider">Margen</div>
                    <div class="font-bold text-slate-900 text-lg mt-1">{{ $producto->margen }}%</div>
                </div>
            </div>
        </div>
    </div>

    @if($comparacion['cantidad_proveedores'] > 0)
        <!-- Resumen de Comparación -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-value text-emerald-600">{{ number_format($comparacion['precio_minimo'], 0, ',', '.') }}</div>
                <div class="stat-card-label">Precio Mínimo</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-value text-sky-600">{{ number_format($comparacion['precio_promedio'], 0, ',', '.') }}</div>
                <div class="stat-card-label">Precio Promedio</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-value text-rose-600">{{ number_format($comparacion['precio_maximo'], 0, ',', '.') }}</div>
                <div class="stat-card-label">Precio Máximo</div>
            </div>
            <div class="stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-card-value text-amber-600">{{ number_format($comparacion['ahorro_vs_maximo'], 0, ',', '.') }}</div>
                <div class="stat-card-label">Ahorro Posible</div>
            </div>
        </div>

        <!-- Acción Rápida -->
        @if($producto->precio_compra != $comparacion['precio_promedio'])
            <div class="action-panel action-panel-warning mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-800">Tu precio de compra difiere del promedio</div>
                            <div class="text-sm text-slate-600 mt-0.5">
                                Actual: {{ number_format($producto->precio_compra, 0, ',', '.') }} Gs. |
                                Promedio: {{ number_format($comparacion['precio_promedio'], 0, ',', '.') }} Gs.
                                @if($producto->precio_compra > $comparacion['precio_promedio'])
                                    <span class="text-rose-600">({{ number_format($producto->precio_compra - $comparacion['precio_promedio'], 0, ',', '.') }} Gs. más alto)</span>
                                @else
                                    <span class="text-emerald-600">({{ number_format($comparacion['precio_promedio'] - $producto->precio_compra, 0, ',', '.') }} Gs. más bajo)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('analisis-proveedores.actualizar-precios') }}" method="POST">
                        @csrf
                        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Actualizar al Promedio
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Tabla de Proveedores -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-bold text-slate-900">Proveedores ({{ $comparacion['cantidad_proveedores'] }})</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-container border-0 shadow-none rounded-none">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="w-12">#</th>
                                <th>Proveedor</th>
                                <th>Código Proveedor</th>
                                <th class="text-right">Precio</th>
                                <th class="text-center">Entrega</th>
                                <th class="text-center">Disponible</th>
                                <th class="text-center">vs Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparacion['proveedores'] as $index => $pp)
                                <tr class="{{ $index === 0 ? 'bg-emerald-50/50' : '' }}">
                                    <td class="text-center">
                                        @if($index === 0)
                                            <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center mx-auto">
                                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </div>
                                        @else
                                            <span class="text-slate-400 font-medium">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-medium text-slate-900">{{ $pp->proveedor->razon_social }}</div>
                                        @if($pp->nombre_proveedor)
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $pp->nombre_proveedor }}</div>
                                        @endif
                                    </td>
                                    <td class="text-sm text-slate-600">{{ $pp->codigo_proveedor ?? '—' }}</td>
                                    <td class="text-right font-bold tabular-nums {{ $index === 0 ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $pp->precio_formateado }}
                                    </td>
                                    <td class="text-center text-sm text-slate-600">{{ $pp->tiempo_entrega_formateado }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $pp->disponible ? 'badge-success' : 'badge-danger' }}">
                                            {{ $pp->disponible ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($index > 0)
                                            @php
                                                $diferencia = $pp->precio - $comparacion['precio_minimo'];
                                                $porcentaje = round(($diferencia / $comparacion['precio_minimo']) * 100, 1);
                                            @endphp
                                            <span class="text-rose-500 text-sm tabular-nums">+{{ number_format($diferencia, 0, ',', '.') }} ({{ $porcentaje }}%)</span>
                                        @else
                                            <span class="badge badge-success">Mejor precio</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="empty-state-text">No hay proveedores para este producto</p>
                    <p class="text-slate-400 text-sm mt-2">Los proveedores pueden agregar este producto a su catálogo desde su portal.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
