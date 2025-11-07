@extends('layouts.app')
@section('title', 'Nueva Venta')
@section('content')
<div class="container-fluid">
    <h2 class="fw-bold mb-4"><i class="fas fa-shopping-cart me-2"></i>Nueva Venta</h2>

    <form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
        @csrf
        @if(isset($presupuesto))
            <input type="hidden" name="presupuesto_id" value="{{ $presupuesto->pre_id }}">
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Información de la Venta</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número <span class="text-danger">*</span></label>
                                <input type="text" name="ven_numero" class="form-control" value="{{ $numeroVenta }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="ven_fecha" class="form-control @error('ven_fecha') is-invalid @enderror" value="{{ old('ven_fecha', date('Y-m-d')) }}" required>
                                @error('ven_fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                <select name="cli_id" class="form-select @error('cli_id') is-invalid @enderror" required {{ isset($presupuesto) ? 'disabled' : '' }}>
                                    <option value="">Seleccionar...</option>
                                    @foreach($clientes as $cli)
                                    <option value="{{ $cli->cli_id }}" {{ (isset($presupuesto) && $presupuesto->cli_id == $cli->cli_id) || old('cli_id') == $cli->cli_id ? 'selected' : '' }}>
                                        {{ $cli->cli_nombre }} {{ $cli->cli_apellido }}
                                    </option>
                                    @endforeach
                                </select>
                                @if(isset($presupuesto))<input type="hidden" name="cli_id" value="{{ $presupuesto->cli_id }}">@endif
                                @error('cli_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea name="ven_observaciones" class="form-control" rows="2">{{ old('ven_observaciones', isset($presupuesto) ? $presupuesto->pre_observaciones : '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Productos</h5>
                        @if(!isset($presupuesto))
                        <button type="button" class="btn btn-sm btn-light" onclick="agregarProducto()">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="tablaProductos">
                                <thead>
                                    <tr>
                                        <th width="40%">Producto</th>
                                        <th width="15%">Stock</th>
                                        <th width="15%">Cantidad</th>
                                        <th width="15%">Precio</th>
                                        <th width="10%">Subtotal</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="productosBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Resumen</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Subtotal</label>
                            <input type="text" class="form-control fw-bold" id="subtotalDisplay" readonly value="Gs. 0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descuento</label>
                            <input type="number" name="ven_descuento" class="form-control" id="descuento" value="{{ old('ven_descuento', isset($presupuesto) ? $presupuesto->pre_descuento : 0) }}" min="0" step="1" onchange="calcularTotal()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control fw-bold text-success fs-4" id="totalDisplay" readonly value="Gs. 0">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Guardar Venta
                        </button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-secondary w-100">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let productos = @json($productos);
let contador = 0;
@if(isset($presupuesto))
    let presupuesto = @json($presupuesto);
@endif

function agregarProducto() {
    let html = `
        <tr id="fila${contador}">
            <td>
                <select name="productos[${contador}][pro_id]" class="form-select form-select-sm" required onchange="actualizarProducto(${contador})">
                    <option value="">Seleccionar...</option>
                    ${productos.map(p => `<option value="${p.pro_id}" data-precio="${p.pro_precio_venta}" data-stock="${p.pro_stock}">${p.pro_nombre} (Stock: ${p.pro_stock})</option>`).join('')}
                </select>
            </td>
            <td><input type="text" class="form-control form-control-sm" id="stock${contador}" readonly value="0"></td>
            <td>
                <input type="number" name="productos[${contador}][cantidad]" class="form-control form-control-sm" id="cantidad${contador}" min="1" value="1" required onchange="calcularSubtotal(${contador})">
            </td>
            <td><input type="text" class="form-control form-control-sm" id="precio${contador}" readonly value="0"></td>
            <td><input type="text" class="form-control form-control-sm fw-bold" id="subtotal${contador}" readonly value="0"></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(${contador})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    document.getElementById('productosBody').insertAdjacentHTML('beforeend', html);
    contador++;
}

function actualizarProducto(index) {
    let select = document.querySelector(`select[name="productos[${index}][pro_id]"]`);
    let precio = select.options[select.selectedIndex].getAttribute('data-precio');
    let stock = select.options[select.selectedIndex].getAttribute('data-stock');

    document.getElementById(`precio${index}`).value = 'Gs. ' + parseFloat(precio || 0).toLocaleString('es-PY');
    document.getElementById(`stock${index}`).value = stock || 0;
    document.getElementById(`cantidad${index}`).max = stock || 0;
    calcularSubtotal(index);
}

function calcularSubtotal(index) {
    let select = document.querySelector(`select[name="productos[${index}][pro_id]"]`);
    let cantidad = parseFloat(document.getElementById(`cantidad${index}`).value) || 0;
    let precio = parseFloat(select.options[select.selectedIndex].getAttribute('data-precio')) || 0;
    let stock = parseFloat(document.getElementById(`stock${index}`).value) || 0;

    if(cantidad > stock) {
        alert('La cantidad no puede ser mayor al stock disponible');
        document.getElementById(`cantidad${index}`).value = stock;
        cantidad = stock;
    }

    let subtotal = cantidad * precio;
    document.getElementById(`subtotal${index}`).value = 'Gs. ' + subtotal.toLocaleString('es-PY');
    calcularTotal();
}

function calcularTotal() {
    let subtotal = 0;
    for (let i = 0; i < contador; i++) {
        let input = document.getElementById(`subtotal${i}`);
        if (input) {
            let valor = parseFloat(input.value.replace('Gs. ', '').replace(/\./g, '')) || 0;
            subtotal += valor;
        }
    }
    let descuento = parseFloat(document.getElementById('descuento').value) || 0;
    let total = subtotal - descuento;

    document.getElementById('subtotalDisplay').value = 'Gs. ' + subtotal.toLocaleString('es-PY');
    document.getElementById('totalDisplay').value = 'Gs. ' + total.toLocaleString('es-PY');
}

function eliminarFila(index) {
    document.getElementById(`fila${index}`).remove();
    calcularTotal();
}

document.getElementById('formVenta').addEventListener('submit', function(e) {
    if (contador === 0 || document.querySelectorAll('#productosBody tr').length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto');
    }
});

// Cargar productos del presupuesto o agregar primera fila
@if(isset($presupuesto))
    @foreach($presupuesto->detalles as $detalle)
        agregarProducto();
        let idx = contador - 1;
        document.querySelector(`select[name="productos[${idx}][pro_id]"]`).value = {{ $detalle->pro_id }};
        actualizarProducto(idx);
        document.getElementById(`cantidad${idx}`).value = {{ $detalle->det_pre_cantidad }};
        calcularSubtotal(idx);
    @endforeach
@else
    agregarProducto();
@endif
</script>
@endpush
@endsection
