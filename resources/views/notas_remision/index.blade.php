@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Notas de Remisión</h1>
        <p class="text-gray-600 mt-1">Gestiona las notas de remisión y aplicación al inventario</p>
    </div>

    <!-- Información -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="h-5 w-5 text-blue-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-900">¿Cómo crear una nota de remisión?</p>
                <p class="text-xs text-blue-700 mt-1">
                    Las notas de remisión se crean desde presupuestos en estado APROBADO. Ve a Presupuestos, selecciona uno aprobado y haz clic en "Convertir a Nota de Remisión".
                </p>
            </div>
        </div>
    </div>

    <!-- Tabla de Notas de Remisión -->
    <x-data-table :headers="['Número', 'Tipo', 'Contacto', 'Fecha', 'Estado', 'Presupuesto', 'Acciones']" color="purple">
        @forelse($notas as $nota)
            <x-table-row color="purple">
                <x-table-cell class="whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $nota->numero }}</div>
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    @if($nota->tipo === 'ENTRADA')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            📥 Entrada
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            📤 Salida
                        </span>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $nota->contacto_nombre }}</div>
                    @if($nota->contacto_empresa)
                        <div class="text-sm text-gray-500">{{ $nota->contacto_empresa }}</div>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm text-gray-900">
                    {{ \Carbon\Carbon::parse($nota->fecha)->format('d/m/Y') }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    @if($nota->estado === 'APLICADA')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Aplicada
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            ⏳ Pendiente
                        </span>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm">
                    @if($nota->presupuesto)
                        <a href="{{ route('presupuestos.show', $nota->presupuesto) }}"
                           class="text-blue-600 hover:text-blue-900">
                            {{ $nota->presupuesto->numero }}
                        </a>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </x-table-cell>
                <x-table-cell :last="true" class="whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('notas-remision.show', $nota) }}"
                           class="text-blue-600 hover:text-blue-900">Ver</a>
                        @if($nota->estado === 'PENDIENTE')
                            <form action="{{ route('notas-remision.aplicar', $nota) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('¿Aplicar esta nota al inventario? Esta acción no se puede deshacer.')"
                                        class="text-green-600 hover:text-green-900">
                                    Aplicar
                                </button>
                            </form>
                        @endif
                    </div>
                </x-table-cell>
            </x-table-row>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="text-gray-400 text-lg">
                        <p class="mb-2">No hay notas de remisión</p>
                        <a href="{{ route('presupuestos.index') }}" class="text-blue-600 hover:text-blue-800">
                            Crear desde un presupuesto aprobado
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <!-- Paginación -->
    @if($notas->hasPages())
        <div class="mt-6">
            {{ $notas->links() }}
        </div>
    @endif
</div>
@endsection
