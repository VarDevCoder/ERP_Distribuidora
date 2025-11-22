@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Nuevo Proveedor</h1>
        <a href="{{ route('proveedores.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Volver</a>
    </div>

    <form action="{{ route('proveedores.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Datos del Proveedor</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social *</label>
                    <input type="text" name="razon_social" value="{{ old('razon_social') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                    @error('razon_social')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC *</label>
                    <input type="text" name="ruc" value="{{ old('ruc') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                    @error('ruc')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <input type="text" name="ciudad" value="{{ old('ciudad') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion') }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rubros/Productos que maneja</label>
                    <input type="text" name="rubros" value="{{ old('rubros') }}"
                           placeholder="Ej: Ferretería, Herramientas eléctricas, Pinturas..."
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Credenciales de Acceso</h2>
            <p class="text-sm text-gray-600 mb-4">Estas credenciales permitirán al proveedor acceder a su portal</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full rounded-lg border-gray-300 shadow-sm">
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('proveedores.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">Cancelar</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Crear Proveedor</button>
        </div>
    </form>
</div>
@endsection
