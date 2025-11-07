@extends('layouts.app')
@section('title', 'Ajuste de Inventario')
@section('content')
<div class="container">
    <h2 class="fw-bold mb-4"><i class="fas fa-adjust me-2"></i>Ajuste de Inventario</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('inventario.ajuste.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Producto <span class="text-danger">*</span></label>
                        <select name="pro_id" class="form-select @error('pro_id') is-invalid @enderror" required id="producto">
                            <option value="">Seleccionar producto...</option>
                            @foreach($productos as $producto)
                            <option value="{{ $producto->pro_id }}" data-stock="{{ $producto->pro_stock }}" {{ old('pro_id') == $producto->pro_id ? 'selected' : '' }}>
                                {{ $producto->pro_codigo }} - {{ $producto->pro_nombre }} (Stock actual: {{ $producto->pro_stock }})
                            </option>
                            @endforeach
                        </select>
                        @error('pro_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stock Actual</label>
                        <input type="text" class="form-control fw-bold text-primary" id="stockActual" readonly value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
                        <select name="mov_tipo" class="form-select @error('mov_tipo') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="ENTRADA" {{ old('mov_tipo') == 'ENTRADA' ? 'selected' : '' }}>ENTRADA (Agregar stock)</option>
                            <option value="SALIDA" {{ old('mov_tipo') == 'SALIDA' ? 'selected' : '' }}>SALIDA (Reducir stock)</option>
                        </select>
                        @error('mov_tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="mov_cantidad" class="form-control @error('mov_cantidad') is-invalid @enderror" min="1" value="{{ old('mov_cantidad') }}" required>
                        @error('mov_cantidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Observaciones <span class="text-danger">*</span></label>
                        <textarea name="mov_observaciones" class="form-control @error('mov_observaciones') is-invalid @enderror" rows="3" required placeholder="Describe el motivo del ajuste (mínimo 10 caracteres)...">{{ old('mov_observaciones') }}</textarea>
                        @error('mov_observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Realizar Ajuste</button>
                    <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('producto').addEventListener('change', function() {
    let stock = this.options[this.selectedIndex].getAttribute('data-stock');
    document.getElementById('stockActual').value = stock ? stock : '0';
});
</script>
@endpush
@endsection
