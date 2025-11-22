@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nueva Orden de Envío</h1>
            <p class="text-gray-600 mt-1">Para pedido: {{ $pedidoCliente->numero }} - {{ $pedidoCliente->cliente_nombre }}</p>
        </div>
        <a href="{{ route('pedidos-cliente.show', $pedidoCliente) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
    </div>

    <form action="{{ route('ordenes-envio.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="pedido_cliente_id" value="{{ $pedidoCliente->id }}">

        <!-- Datos de Entrega -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Datos de Entrega</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección de Entrega *</label>
                    <input type="text" name="direccion_entrega"
                           value="{{ old('direccion_entrega', $pedidoCliente->cliente_direccion) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contacto</label>
                    <input type="text" name="contacto_entrega"
                           value="{{ old('contacto_entrega', $pedidoCliente->cliente_nombre) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono_entrega"
                           value="{{ old('telefono_entrega', $pedidoCliente->cliente_telefono) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        <!-- Método de Envío -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Método de Envío</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                    <select name="metodo_envio" class="w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">Seleccionar...</option>
                        @foreach(\App\Models\OrdenEnvio::getMetodosEnvio() as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ old('metodo_envio') == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transportista</label>
                    <input type="text" name="transportista" value="{{ old('transportista') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        <!-- Productos a Enviar -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Productos a Enviar</h2>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Solicitado</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Actual</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-32">A Enviar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pedidoCliente->items as $i => $item)
                        <tr>
                            <td class="px-4 py-3">
                                <input type="hidden" name="items[{{ $i }}][producto_id]" value="{{ $item->producto_id }}">
                                <div class="font-medium">{{ $item->producto->nombre }}</div>
                                <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">{{ $item->cantidad }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($item->producto->stock_actual >= $item->cantidad)
                                    <span class="text-green-600">{{ $item->producto->stock_actual }}</span>
                                @else
                                    <span class="text-red-600">{{ $item->producto->stock_actual }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $i }}][cantidad]"
                                       value="{{ min($item->cantidad, $item->producto->stock_actual) }}"
                                       step="0.001" min="0.001" max="{{ $item->producto->stock_actual }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm text-right" required>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Notas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
            <textarea name="notas" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm"
                      placeholder="Instrucciones especiales de entrega...">{{ old('notas') }}</textarea>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>Nota:</strong> El stock se descontará cuando la orden sea despachada (marcada en tránsito).
            </p>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('pedidos-cliente.show', $pedidoCliente) }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">Crear Orden de Envío</button>
        </div>
    </form>
</div>
@endsection
