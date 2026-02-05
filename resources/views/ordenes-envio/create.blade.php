@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nueva Orden de Envío</h1>
            <p class="text-gray-600 mt-1">Para solicitud: {{ $pedidoCliente->numero }} - {{ $pedidoCliente->cliente_nombre }}</p>
        </div>
        <a href="{{ route('pedidos-cliente.show', $pedidoCliente) }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('ordenes-envio.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="pedido_cliente_id" value="{{ $pedidoCliente->id }}">

        <!-- Datos de Entrega -->
        <div class="form-section">
            <h2 class="form-section-title">Datos de Entrega</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group md:col-span-2">
                    <label class="form-label form-label-required">Dirección de Entrega</label>
                    <input type="text" name="direccion_entrega"
                           value="{{ old('direccion_entrega', $pedidoCliente->cliente_direccion) }}" required
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Contacto</label>
                    <input type="text" name="contacto_entrega"
                           value="{{ old('contacto_entrega', $pedidoCliente->cliente_nombre) }}"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono_entrega"
                           value="{{ old('telefono_entrega', $pedidoCliente->cliente_telefono) }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <!-- Método de Envío -->
        <div class="form-section">
            <h2 class="form-section-title">Método de Envío</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Método</label>
                    <select name="metodo_envio" class="form-select">
                        <option value="">Seleccionar...</option>
                        @foreach(\App\Models\OrdenEnvio::getMetodosEnvio() as $valor => $etiqueta)
                            <option value="{{ $valor }}" {{ old('metodo_envio') == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Transportista</label>
                    <input type="text" name="transportista" value="{{ old('transportista') }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <!-- Productos a Enviar -->
        <div class="form-section">
            <h2 class="form-section-title">Productos a Enviar</h2>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-right">Solicitado</th>
                            <th class="text-right">Stock Actual</th>
                            <th class="text-right w-32">A Enviar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidoCliente->items as $i => $item)
                            <tr>
                                <td>
                                    <input type="hidden" name="items[{{ $i }}][producto_id]" value="{{ $item->producto_id }}">
                                    <div class="font-medium">{{ $item->producto->nombre }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->producto->codigo }}</div>
                                </td>
                                <td class="text-right">{{ $item->cantidad }}</td>
                                <td class="text-right">
                                    @if($item->producto->stock_actual >= $item->cantidad)
                                        <span class="text-green-600 font-medium">{{ $item->producto->stock_actual }}</span>
                                    @else
                                        <span class="text-red-600 font-medium">{{ $item->producto->stock_actual }}</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][cantidad]"
                                           value="{{ min($item->cantidad, $item->producto->stock_actual) }}"
                                           step="0.001" min="0.001" max="{{ $item->producto->stock_actual }}"
                                           class="form-input-number" required>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notas -->
        <div class="form-section">
            <h2 class="form-section-title">Notas</h2>
            <textarea name="notas" rows="3" class="form-textarea"
                      placeholder="Instrucciones especiales de entrega...">{{ old('notas') }}</textarea>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>Nota:</strong> El stock se descontará cuando la orden sea despachada (marcada en tránsito).
            </p>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('pedidos-cliente.show', $pedidoCliente) }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Crear Orden de Envío</button>
        </div>
    </form>
</div>
@endsection
