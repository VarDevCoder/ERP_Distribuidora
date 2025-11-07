@extends('layouts.app')
@section('title', 'Nuevo Proveedor')
@section('content')
<div class="container">
    <h2 class="fw-bold mb-4"><i class="fas fa-truck me-2"></i>Nuevo Proveedor</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('proveedores.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="prov_nombre" class="form-control @error('prov_nombre') is-invalid @enderror" value="{{ old('prov_nombre') }}" required>
                        @error('prov_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">RUC <span class="text-danger">*</span></label>
                        <input type="text" name="prov_ruc" class="form-control @error('prov_ruc') is-invalid @enderror" value="{{ old('prov_ruc') }}" required>
                        @error('prov_ruc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="prov_telefono" class="form-control" value="{{ old('prov_telefono') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="prov_email" class="form-control @error('prov_email') is-invalid @enderror" value="{{ old('prov_email') }}">
                        @error('prov_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="prov_ciudad" class="form-control" value="{{ old('prov_ciudad') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Persona de Contacto</label>
                        <input type="text" name="prov_contacto" class="form-control" value="{{ old('prov_contacto') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea name="prov_direccion" class="form-control" rows="2">{{ old('prov_direccion') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <select name="prov_estado" class="form-select" required>
                            <option value="ACTIVO" {{ old('prov_estado') == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                            <option value="INACTIVO" {{ old('prov_estado') == 'INACTIVO' ? 'selected' : '' }}>INACTIVO</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar</button>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
