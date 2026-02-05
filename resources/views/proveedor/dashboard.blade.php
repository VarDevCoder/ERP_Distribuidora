@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="page-title">Portal Proveedor</h1>
        <p class="page-subtitle">Bienvenido, {{ $proveedor->razon_social }}</p>
    </div>

    <!-- Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card border-l-4 border-l-amber-500">
            <div class="stat-card-icon">📩</div>
            <div class="stat-card-value text-amber-600">{{ $solicitudesPendientes }}</div>
            <div class="stat-card-label">Solicitudes Pendientes</div>
            @if($solicitudesPendientes > 0)
                <a href="{{ route('proveedor.solicitudes', ['estado' => 'ENVIADA']) }}"
                   class="text-amber-600 text-sm hover:underline mt-2 inline-block">Ver pendientes</a>
            @endif
        </div>
        <div class="stat-card border-l-4 border-l-emerald-500">
            <div class="stat-card-icon">✅</div>
            <div class="stat-card-value text-emerald-600">{{ $proveedor->solicitudesPresupuesto()->where('estado', 'ACEPTADA')->count() }}</div>
            <div class="stat-card-label">Cotizaciones Aceptadas</div>
        </div>
        <div class="stat-card border-l-4 border-l-blue-500">
            <div class="stat-card-icon">📋</div>
            <div class="stat-card-value text-blue-600">{{ $proveedor->solicitudesPresupuesto()->count() }}</div>
            <div class="stat-card-label">Total Solicitudes</div>
        </div>
        <div class="stat-card border-l-4 border-l-purple-500">
            <div class="stat-card-icon">📦</div>
            <div class="stat-card-value text-purple-600">{{ $proveedor->proveedorProductos()->count() }}</div>
            <div class="stat-card-label">Productos en Catálogo</div>
            <a href="{{ route('proveedor-productos.index') }}"
               class="text-purple-600 text-sm hover:underline mt-2 inline-block">Gestionar catálogo</a>
        </div>
    </div>

    <!-- Solicitudes Recientes -->
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Solicitudes Recientes</h2>
            <a href="{{ route('proveedor.solicitudes') }}" class="text-blue-600 hover:underline text-sm">Ver todas</a>
        </div>
        <div class="card-body p-0">
            @if($solicitudesRecientes->count() > 0)
                <div class="table-container border-0 shadow-none rounded-none">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Estado</th>
                                <th class="text-center">Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($solicitudesRecientes as $solicitud)
                                <tr>
                                    <td class="font-medium">{{ $solicitud->numero }}</td>
                                    <td class="text-sm text-gray-500">{{ $solicitud->fecha_solicitud->format('d/m/Y') }}</td>
                                    <td class="text-sm">{{ $solicitud->items->count() }} productos</td>
                                    <td>
                                        <span class="badge {{ $solicitud->estado_color }}">
                                            {{ $solicitud->estado }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($solicitud->puedeSerRespondida())
                                            <a href="{{ route('proveedor.solicitud.responder', $solicitud) }}"
                                               class="btn-primary btn-sm">
                                                Responder
                                            </a>
                                        @else
                                            <a href="{{ route('proveedor.solicitud.ver', $solicitud) }}"
                                               class="text-blue-600 hover:underline text-sm">Ver</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p class="empty-state-text">No hay solicitudes recientes</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
