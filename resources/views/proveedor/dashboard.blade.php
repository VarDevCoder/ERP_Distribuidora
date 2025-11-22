@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Portal Proveedor</h1>
        <p class="text-gray-600 mt-1">Bienvenido, {{ $proveedor->razon_social }}</p>
    </div>

    <!-- Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl font-bold text-blue-600">{{ $solicitudesPendientes }}</div>
            <div class="text-gray-600">Solicitudes Pendientes</div>
            @if($solicitudesPendientes > 0)
                <a href="{{ route('proveedor.solicitudes', ['estado' => 'ENVIADA']) }}"
                   class="text-blue-600 text-sm hover:underline">Ver pendientes</a>
            @endif
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl font-bold text-green-600">{{ $proveedor->solicitudesPresupuesto()->where('estado', 'ACEPTADA')->count() }}</div>
            <div class="text-gray-600">Cotizaciones Aceptadas</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-3xl font-bold text-gray-600">{{ $proveedor->solicitudesPresupuesto()->count() }}</div>
            <div class="text-gray-600">Total Solicitudes</div>
        </div>
    </div>

    <!-- Solicitudes Recientes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Solicitudes Recientes</h2>
            <a href="{{ route('proveedor.solicitudes') }}" class="text-blue-600 hover:underline">Ver todas</a>
        </div>

        @if($solicitudesRecientes->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Productos</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($solicitudesRecientes as $solicitud)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $solicitud->numero }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $solicitud->fecha_solicitud->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $solicitud->items->count() }} productos</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $solicitud->estado_color }}">
                                    {{ $solicitud->estado }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($solicitud->puedeSerRespondida())
                                    <a href="{{ route('proveedor.solicitud.responder', $solicitud) }}"
                                       class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
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
        @else
            <p class="text-gray-500 text-center py-8">No hay solicitudes recientes</p>
        @endif
    </div>
</div>
@endsection
