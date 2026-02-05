@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-1">Resumen general de operaciones - {{ now()->format('d/m/Y') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Solicitudes activas -->
        <a href="{{ route('pedidos-cliente.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                @if($stats['solicitudes_hoy'] > 0)
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">+{{ $stats['solicitudes_hoy'] }} hoy</span>
                @endif
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['solicitudes_activas'] }}</div>
            <div class="text-sm text-gray-500 group-hover:text-blue-600 transition-colors">Solicitudes activas</div>
        </a>

        <!-- Cotizaciones -->
        <a href="{{ route('solicitudes-presupuesto.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                @if($stats['cotizaciones_listas'] > 0)
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">{{ $stats['cotizaciones_listas'] }} listas</span>
                @endif
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['cotizaciones_pendientes'] }}</div>
            <div class="text-sm text-gray-500 group-hover:text-indigo-600 transition-colors">Cotizaciones pendientes</div>
        </a>

        <!-- Órdenes de compra -->
        <a href="{{ route('ordenes-compra.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['ordenes_compra_activas'] }}</div>
            <div class="text-sm text-gray-500 group-hover:text-purple-600 transition-colors">Compras en curso</div>
        </a>

        <!-- Envíos -->
        <a href="{{ route('ordenes-envio.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['ordenes_envio_pendientes'] }}</div>
            <div class="text-sm text-gray-500 group-hover:text-green-600 transition-colors">Envíos pendientes</div>
        </a>
    </div>

    <!-- Second row: Inventory alerts + Quick actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Inventory summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h2 class="font-bold text-gray-800 mb-4">Inventario</h2>
            <div class="space-y-3">
                <a href="{{ route('productos.index') }}" class="flex justify-between items-center hover:bg-gray-50 -mx-2 px-2 py-1 rounded transition-colors">
                    <span class="text-gray-600">Productos activos</span>
                    <span class="font-bold text-gray-800">{{ $stats['productos_total'] }}</span>
                </a>
                <a href="{{ route('inventario.index') }}" class="flex justify-between items-center hover:bg-gray-50 -mx-2 px-2 py-1 rounded transition-colors">
                    <span class="text-gray-600">Stock bajo</span>
                    <span class="font-bold {{ $stats['productos_stock_bajo'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $stats['productos_stock_bajo'] }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h2 class="font-bold text-gray-800 mb-4">Acciones rápidas</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('pedidos-cliente.create') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all text-center group">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Nueva Solicitud</span>
                </a>
                <a href="{{ route('solicitudes-presupuesto.create') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all text-center group">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Nueva Cotización</span>
                </a>
                <a href="{{ route('ordenes-compra.create') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all text-center group">
                    <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Nueva Compra</span>
                </a>
                <a href="{{ route('productos.create') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 transition-all text-center group">
                    <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700">Nuevo Producto</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-5 border-b border-gray-200">
            <h2 class="font-bold text-gray-800">Últimas operaciones</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($actividades as $act)
                <a href="{{ $act['url'] }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors">
                    <!-- Icon -->
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                        {{ $act['color'] === 'blue' ? 'bg-blue-100' : '' }}
                        {{ $act['color'] === 'indigo' ? 'bg-indigo-100' : '' }}
                        {{ $act['color'] === 'purple' ? 'bg-purple-100' : '' }}
                        {{ $act['color'] === 'green' ? 'bg-green-100' : '' }}">
                        @if($act['icono'] === 'clipboard')
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        @elseif($act['icono'] === 'document')
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @elseif($act['icono'] === 'cart')
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                        @elseif($act['icono'] === 'truck')
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900 text-sm">{{ $act['titulo'] }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $act['estado_color'] }}">
                                {{ str_replace('_', ' ', $act['estado']) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 truncate">{{ $act['detalle'] }}</div>
                    </div>

                    <!-- Meta -->
                    <div class="text-right shrink-0 hidden sm:block">
                        <div class="text-xs text-gray-500">{{ $act['fecha']->diffForHumans() }}</div>
                        <div class="text-xs text-gray-400">{{ $act['usuario'] }}</div>
                    </div>
                </a>
            @empty
                <div class="px-5 py-8 text-center text-gray-500">
                    No hay operaciones registradas aún.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
