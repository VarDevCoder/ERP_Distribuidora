@extends('layouts.app')
@section('title', 'Nueva Compra')
@section('content')
<div class="container-fluid">
    <h2 class="fw-bold mb-4"><i class="fas fa-shopping-bag me-2"></i>Nueva Compra</h2>

    <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Información de la Compra</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número <span class="text-danger">*</span></label>
                                <input type="text" name="com_numero" class="form-control" value="{{ $numeroCompra }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="com_fecha" class="form-control @error('com_fecha') is-invalid @enderror" value="{{ old('com_fecha', date('Y-m-d')) }}" required>
                                @error('com_fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Factura</label>
                                <input type="text" name="com_factura" class="form-control" value="{{ old('com_factura') }}">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                                <select name="prov_id" class="form-select @error('prov_id') is-invalid @enderror" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($proveedores as $prov)
                                    <option value="{{ $prov->prov_id }}" {{ old('prov_id') == $prov->prov_id ? 'selected' : '' }}>
                                        {{ $prov->prov_nombre }} - {{ $prov->prov_ruc }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('prov_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Observaciones</label>
                                <textarea name="com_observaciones" class="form-control" rows="2">{{ old('com_observaciones') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Productos</h5>
                        <button type="button" class="btn btn-sm btn-light" onclick="agregarProducto()">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="tablaProductos">
                                <thead>
                                    <tr>
                                        <th width="40%">Producto</th>
                                        <th width="20%">Cantidad</th>
                                        <th width="20%">Precio Unit.</th>
                                        <th width="15%">Subtotal</th>
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
                            <input type="number" name="com_descuento" class="form-control" id="descuento" value="0" min="0" step="1" onchange="calcularTotal()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control fw-bold text-success fs-4" id="totalDisplay" readonly value="Gs. 0">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Guardar Compra
                        </button>
                        <a href="{{ route('compras.index') }}" class="btn btn-secondary w-100">Cancelar</a>
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

function agregarProducto() {
    let html = `
        <tr id="fila${contador}">
            <td>
                <select name="productos[${contador}][pro_id]" class="form-select form-select-sm" required onchange="actualizarPrecio(${contador})">
                    <option value="">Seleccionar...</option>
                    ${productos.map(p => `<option value="${p.pro_id}" data-precio="${p.pro_precio_compra}">${p.pro_nombre}</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" name="productos[${contador}][cantidad]" class="form-control form-control-sm" min="1" value="1" required onchange="calcularSubtotal(${contador})">
            </td>
            <td>
                <input type="number" name="productos[${contador}][precio]" class="form-control form-control-sm" id="precio${contador}" min="0" step="1" required onchange="calcularSubtotal(${contador})">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm fw-bold" id="subtotal${contador}" readonly value="0">
            </td>
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

function actualizarPrecio(index) {
    let select = document.querySelector(`select[name="productos[${index}][pro_id]"]`);
    let precio = select.options[select.selectedIndex].getAttribute('data-precio');
    document.getElementById(`precio${index}`).value = precio || 0;
    calcularSubtotal(index);
}

function calcularSubtotal(index) {
    let cantidad = parseFloat(document.querySelector(`input[name="productos[${index}][cantidad]"]`).value) || 0;
    let precio = parseFloat(document.getElementById(`precio${index}`).value) || 0;
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

document.getElementById('formCompra').addEventListener('submit', function(e) {
    if (contador === 0 || document.querySelectorAll('#productosBody tr').length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto');
    }
});

// Agregar primera fila automáticamente
agregarProducto();
</script>
@endpush
@endsection
