@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">
            ← Volver a Clientes
        </a>
        <h1 class="page-title">Nuevo Cliente</h1>
        <p class="page-subtitle">Completa los datos del nuevo cliente</p>
    </div>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        {{-- Datos Básicos --}}
        <div class="form-section mb-6">
            <h3 class="form-section-title">Datos Básicos</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nombre --}}
                <div class="md:col-span-2 form-group">
                    <label for="nombre" class="form-label form-label-required">Nombre</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        class="form-input"
                        required>
                    @error('nombre')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                {{-- RUC --}}
                <div class="form-group">
                    <label for="ruc" class="form-label">RUC</label>
                    <input
                        type="text"
                        id="ruc"
                        name="ruc"
                        value="{{ old('ruc') }}"
                        class="form-input"
                        placeholder="Ej: 80012345-6">
                    @error('ruc')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Teléfono --}}
                <div class="form-group">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        value="{{ old('telefono') }}"
                        class="form-input"
                        placeholder="Ej: 0981-123-456">
                    @error('telefono')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-input"
                        placeholder="cliente@email.com">
                    @error('email')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ciudad --}}
                <div class="form-group">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <input
                        type="text"
                        id="ciudad"
                        name="ciudad"
                        value="{{ old('ciudad') }}"
                        class="form-input"
                        placeholder="Ej: Asunción">
                    @error('ciudad')
                        <p class="form-error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Dirección --}}
        <div class="form-section mb-6">
            <h3 class="form-section-title">Dirección</h3>

            <div class="form-group">
                <label for="direccion" class="form-label">Dirección Completa</label>
                <textarea
                    id="direccion"
                    name="direccion"
                    rows="3"
                    class="form-textarea"
                    placeholder="Calle, número, barrio, referencias...">{{ old('direccion') }}</textarea>
                @error('direccion')
                    <p class="form-error-message">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Notas --}}
        <div class="form-section mb-6">
            <h3 class="form-section-title">Información Adicional</h3>

            <div class="form-group">
                <label for="notas" class="form-label">Notas</label>
                <textarea
                    id="notas"
                    name="notas"
                    rows="4"
                    class="form-textarea"
                    placeholder="Notas internas sobre el cliente...">{{ old('notas') }}</textarea>
                @error('notas')
                    <p class="form-error-message">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado Activo --}}
            <div class="form-group">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        name="activo"
                        value="1"
                        {{ old('activo', true) ? 'checked' : '' }}
                        class="form-checkbox h-4 w-4 text-blue-600">
                    <span class="ml-2 text-sm text-gray-700">Cliente activo</span>
                </label>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">
                Crear Cliente
            </button>
            <a href="{{ route('clientes.index') }}" class="btn-secondary">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
