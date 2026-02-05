@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="page-header">
        <div>
            <h1 class="page-title">Análisis de Proveedores</h1>
            <p class="page-subtitle">Compara precios y optimiza tus compras</p>
        </div>
        <div class="actions-row">
            <a href="{{ route('analisis-proveedores.productos') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Ver Productos
            </a>
            <form action="{{ route('analisis-proveedores.actualizar-precios') }}" method="POST" class="inline"
                  onsubmit="return confirm('¿Actualizar todos los precios de compra con el promedio de proveedores?')">
                @csrf
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar Precios
                </button>
            </form>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-sky-600">{{ $resumen['total_productos'] }}</div>
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
            <div class="stat-card-value text-emerald-600">{{ $resumen['productos_con_proveedores'] }}</div>
            <div class="stat-card-label">Con Proveedores</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-amber-600">{{ $resumen['productos_sin_proveedores'] }}</div>
            <div class="stat-card-label">Sin Proveedores</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-violet-600">{{ $resumen['cobertura_porcentaje'] }}%</div>
            <div class="stat-card-label">Cobertura</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-indigo-600">{{ $resumen['total_proveedores_activos'] }}</div>
            <div class="stat-card-label">Proveedores Activos</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-value text-cyan-600">{{ $resumen['promedio_proveedores_por_producto'] }}</div>
            <div class="stat-card-label">Prov/Producto</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Scorecards de Proveedores -->
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-bold text-slate-900">Ranking de Proveedores</h2>
            </div>
            <div class="card-body p-0">
                @if($scorecards->count() > 0)
                    <div class="table-container border-0 shadow-none rounded-none">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="w-12">#</th>
                                    <th>Proveedor</th>
                                    <th class="text-center">Productos</th>
                                    <th class="text-center">Disponibilidad</th>
                                    <th class="text-right">Precio Prom.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scorecards as $index => $proveedor)
                                    <tr>
                                        <td class="text-center">
                                            @if($index === 0)
                                                <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center mx-auto">
                                                    <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </div>
                                            @elseif($index === 1)
                                                <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center mx-auto">
                                                    <span class="text-xs font-bold text-slate-600">2</span>
                                                </div>
                                            @elseif($index === 2)
                                                <div class="w-7 h-7 rounded-full bg-amber-50 flex items-center justify-center mx-auto">
                                                    <span class="text-xs font-bold text-amber-700">3</span>
                                                </div>
                                            @else
                                                <span class="text-slate-400 font-medium">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('analisis-proveedores.proveedor', $proveedor->id) }}"
                                               class="font-medium text-sky-600 hover:text-sky-700">
                                                {{ $proveedor->razon_social }}
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-bold text-slate-900">{{ $proveedor->total_productos }}</span>
                                            <span class="text-xs text-slate-400">({{ $proveedor->productos_disponibles }} disp.)</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 mb-1">
                                                <div class="h-1.5 rounded-full {{ $proveedor->disponibilidad_porcentaje >= 80 ? 'bg-emerald-500' : ($proveedor->disponibilidad_porcentaje >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                     style="width: {{ $proveedor->disponibilidad_porcentaje }}%"></div>
                                            </div>
                                            <span class="text-xs text-slate-500">{{ $proveedor->disponibilidad_porcentaje }}%</span>
                                        </td>
                                        <td class="text-right tabular-nums text-sm font-medium">
                                            {{ number_format($proveedor->precio_promedio, 0, ',', '.') }} Gs.
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <p class="empty-state-text">No hay datos de proveedores</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Comparación de Precios -->
        <div class="card">
            <div class="card-header flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900">Comparación de Precios</h2>
                <a href="{{ route('analisis-proveedores.productos') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Ver todos →</a>
            </div>
            <div class="card-body p-0">
                @if($matrizComparacion->count() > 0)
                    <div class="table-container border-0 shadow-none rounded-none">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-right">Mínimo</th>
                                    <th class="text-right">Promedio</th>
                                    <th class="text-right">Máximo</th>
                                    <th class="text-center">Dif %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matrizComparacion as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('analisis-proveedores.producto', $item['producto']) }}"
                                               class="font-medium text-sky-600 hover:text-sky-700">
                                                {{ Str::limit($item['producto']->nombre, 25) }}
                                            </a>
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $item['cantidad_proveedores'] }} proveedores</div>
                                        </td>
                                        <td class="text-right text-emerald-600 font-medium tabular-nums">
                                            {{ number_format($item['precio_minimo'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-right font-bold tabular-nums">
                                            {{ number_format($item['precio_promedio'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-right text-rose-600 tabular-nums">
                                            {{ number_format($item['precio_maximo'], 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @if($item['diferencia_porcentual'] > 20)
                                                <span class="badge badge-danger">{{ $item['diferencia_porcentual'] }}%</span>
                                            @elseif($item['diferencia_porcentual'] > 10)
                                                <span class="badge badge-warning">{{ $item['diferencia_porcentual'] }}%</span>
                                            @else
                                                <span class="badge badge-success">{{ $item['diferencia_porcentual'] }}%</span>
                                            @endif
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <p class="empty-state-text">No hay comparaciones disponibles</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
