@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Presupuestos</h1>
            <p class="text-gray-600 mt-1">Gestiona presupuestos de compra y venta</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('presupuestos.create', ['tipo' => 'COMPRA']) }}"
               class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                + Presupuesto de Compra
            </a>
            <a href="{{ route('presupuestos.create', ['tipo' => 'VENTA']) }}"
               class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                + Presupuesto de Venta
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-blue-50 rounded-lg shadow-lg p-5 mb-6 border-2 border-blue-200">
        <div class="flex space-x-4">
            <a href="{{ route('presupuestos.index') }}"
               class="px-4 py-2 rounded-lg font-medium {{ !request('tipo') ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-blue-50 border-2 border-gray-300' }} transition">
                Todos
            </a>
            <a href="{{ route('presupuestos.index', ['tipo' => 'COMPRA']) }}"
               class="px-4 py-2 rounded-lg font-medium {{ request('tipo') === 'COMPRA' ? 'bg-green-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-blue-50 border-2 border-gray-300' }} transition">
                Compras
            </a>
            <a href="{{ route('presupuestos.index', ['tipo' => 'VENTA']) }}"
               class="px-4 py-2 rounded-lg font-medium {{ request('tipo') === 'VENTA' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-blue-50 border-2 border-gray-300' }} transition">
                Ventas
            </a>
        </div>
    </div>

    <!-- Tabla de Presupuestos -->
    <x-data-table :headers="['Número', 'Tipo', 'Contacto', 'Fecha', 'Total', 'Estado', 'Acciones']" color="blue">
        @forelse($presupuestos as $presupuesto)
            <x-table-row color="blue">
                <x-table-cell class="whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $presupuesto->numero }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    @if($presupuesto->tipo === 'COMPRA')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Compra
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Venta
                        </span>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $presupuesto->contacto_nombre }}</div>
                    @if($presupuesto->contacto_empresa)
                        <div class="text-xs text-gray-500">{{ $presupuesto->contacto_empresa }}</div>
                    @endif
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm text-gray-700">
                    {{ \Carbon\Carbon::parse($presupuesto->fecha)->format('d/m/Y') }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap text-sm font-semibold text-gray-900">
                    ${{ number_format($presupuesto->total, 2) }}
                </x-table-cell>
                <x-table-cell class="whitespace-nowrap">
                    @php
                        $estadoClases = [
                            'BORRADOR' => 'bg-gray-100 text-gray-800',
                            'ENVIADO' => 'bg-blue-100 text-blue-800',
                            'APROBADO' => 'bg-green-100 text-green-800',
                            'RECHAZADO' => 'bg-red-100 text-red-800',
                            'CONVERTIDO' => 'bg-purple-100 text-purple-800'
                        ];
                        $clase = $estadoClases[$presupuesto->estado] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $clase }}">
                        {{ $presupuesto->estado }}
                    </span>
                </x-table-cell>
                <x-table-cell :last="true" class="whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('presupuestos.show', $presupuesto) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                        @if($presupuesto->estado !== 'CONVERTIDO')
                            <a href="{{ route('presupuestos.edit', $presupuesto) }}" class="text-green-600 hover:text-green-900">Editar</a>
                        @endif
                    </div>
                </x-table-cell>
            </x-table-row>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="text-gray-400 text-lg">
                        <p class="mb-2">No hay presupuestos registrados</p>
                        <a href="{{ route('presupuestos.create') }}" class="text-blue-600 hover:text-blue-800">
                            Crear el primer presupuesto
                        </a>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-data-table>

    <!-- Paginación -->
    @if($presupuestos->hasPages())
        <div class="mt-6">
            {{ $presupuestos->links() }}
        </div>
    @endif
</div>
@endsection
