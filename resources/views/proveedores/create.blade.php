@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nuevo Proveedor</h1>
            <p class="text-gray-600 mt-1">Registra un proveedor y crea sus credenciales de acceso</p>
        </div>
        <a href="{{ route('proveedores.index') }}" class="btn-secondary">Volver</a>
    </div>

    <form action="{{ route('proveedores.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="form-section">
            <h2 class="form-section-title">Datos del Proveedor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group md:col-span-2">
                    <label class="form-label form-label-required">Razon Social</label>
                    <input type="text" name="razon_social" value="{{ old('razon_social') }}" required
                           class="form-input" placeholder="Nombre de la empresa">
                    @error('razon_social')<p class="form-error-message">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label form-label-required">RUC</label>
                    <input type="text" name="ruc" value="{{ old('ruc') }}" required
                           class="form-input" placeholder="Ej: 80012345-6">
                    @error('ruc')<p class="form-error-message">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Telefono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="form-input" placeholder="Ej: 021 123 456">
                </div>
                <div class="form-group">
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                           class="form-input" placeholder="Ej: Asuncion">
                </div>
                <div class="form-group">
                    <label class="form-label">Direccion</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}"
                           class="form-input" placeholder="Direccion comercial">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Rubros/Productos que maneja</label>
                    <input type="text" name="rubros" value="{{ old('rubros') }}"
                           placeholder="Ej: Ferreteria, Herramientas electricas, Pinturas..."
                           class="form-input">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="form-section-title">Credenciales de Acceso al Portal</h2>
            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-700">
                    <strong>Importante:</strong> Estas credenciales permitiran al proveedor acceder a su portal para responder cotizaciones.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label form-label-required">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="form-input" placeholder="proveedor@empresa.com">
                    @error('email')<p class="form-error-message">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label form-label-required">Contrasena</label>
                    <input type="password" name="password" required minlength="6"
                           class="form-input" placeholder="Minimo 6 caracteres">
                    @error('password')<p class="form-error-message">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('proveedores.index') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Crear Proveedor</button>
        </div>
    </form>
</div>
@endsection
