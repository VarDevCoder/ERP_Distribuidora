@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Recepción de Mercadería</h1>
            <p class="text-gray-600 mt-1">{{ $orden->numero }} - {{ $orden->proveedor_nombre }}</p>
        </div>
        <a href="{{ route('ordenes-compra.show', $orden) }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('ordenes-compra.recibir', $orden) }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Registrar Cantidades Recibidas</h2>
            <p class="text-sm text-gray-600 mb-4">Ingrese las cantidades recibidas en esta entrega. Las cantidades se acumularán al stock.</p>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Solicitado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ya Recibido</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendiente</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">Recibir Ahora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($orden->items as $item)
                        @php
                            $pendiente = $item->cantidad_solicitada - $item->cantidad_recibida;
                        @endphp
                        <tr class="{{ $pendiente <= 0 ? 'bg-green-50' : '' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $item->producto->nombre }}</div>
                                <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">{{ $item->cantidad_solicitada }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($item->cantidad_recibida > 0)
                                    <span class="text-green-600 font-medium">{{ $item->cantidad_recibida }}</span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($pendiente > 0)
                                    <span class="text-yellow-600 font-medium">{{ $pendiente }}</span>
                                @else
                                    <span class="text-green-600">Completo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($pendiente > 0)
                                    <input type="number" name="cantidades[{{ $item->id }}]" value="0"
                                           step="0.001" min="0" max="{{ $pendiente }}"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-right">
                                @else
                                    <span class="text-green-600 text-sm">Completo</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-medium text-yellow-800">Esta acción afectará el inventario</p>
                    <p class="text-sm text-yellow-700">Las cantidades ingresadas se sumarán al stock de cada producto.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('ordenes-compra.show', $orden) }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Registrar Recepcion</button>
        </div>
    </form>
</div>
@endsection
