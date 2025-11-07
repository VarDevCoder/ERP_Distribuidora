@extends('layouts.app')
@section('title', 'Editar Cliente')
@section('content')
<div class="container">
    <h2 class="fw-bold mb-4"><i class="fas fa-user-edit me-2"></i>Editar Cliente</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('clientes.update', $cliente->cli_id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="cli_nombre" class="form-control @error('cli_nombre') is-invalid @enderror" value="{{ old('cli_nombre', $cliente->cli_nombre) }}" required>
                        @error('cli_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" name="cli_apellido" class="form-control @error('cli_apellido') is-invalid @enderror" value="{{ old('cli_apellido', $cliente->cli_apellido) }}" required>
                        @error('cli_apellido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CI <span class="text-danger">*</span></label>
                        <input type="text" name="cli_ci" class="form-control @error('cli_ci') is-invalid @enderror" value="{{ old('cli_ci', $cliente->cli_ci) }}" required>
                        @error('cli_ci')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="cli_telefono" class="form-control" value="{{ old('cli_telefono', $cliente->cli_telefono) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="cli_email" class="form-control @error('cli_email') is-invalid @enderror" value="{{ old('cli_email', $cliente->cli_email) }}">
                        @error('cli_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                        <select name="cli_tipo" class="form-select" required>
                            <option value="MINORISTA" {{ old('cli_tipo', $cliente->cli_tipo) == 'MINORISTA' ? 'selected' : '' }}>MINORISTA</option>
                            <option value="MAYORISTA" {{ old('cli_tipo', $cliente->cli_tipo) == 'MAYORISTA' ? 'selected' : '' }}>MAYORISTA</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea name="cli_direccion" class="form-control" rows="2">{{ old('cli_direccion', $cliente->cli_direccion) }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Actualizar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
