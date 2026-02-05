@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="page-header">
        <div>
            <h1 class="page-title">Categorias</h1>
            <p class="page-subtitle">Organiza los productos por tipo</p>
        </div>
    </div>

    <!-- Form nueva categoria -->
    <div class="form-section mb-6">
        <h2 class="form-section-title">Agregar Categoria</h2>
        <form action="{{ route('categorias.store') }}" method="POST" class="flex flex-wrap gap-4 items-end">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="form-label form-label-required">Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-input" placeholder="Ej: Herramientas" required>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="form-label">Descripcion</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}" class="form-input" placeholder="Opcional">
            </div>
            <div class="w-24">
                <label class="form-label">Orden</label>
                <input type="number" name="orden" value="{{ old('orden', 0) }}" class="form-input-number" min="0">
            </div>
            <button type="submit" class="btn-primary">Agregar</button>
        </form>

        @if($errors->any())
            <div class="mt-3 text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Lista de categorias -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">Orden</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th class="text-center">Productos</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $categoria)
                    <tr x-data="{ editing: false }">
                        {{-- Modo lectura --}}
                        <template x-if="!editing">
                            <td>{{ $categoria->orden }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="font-medium text-gray-900">{{ $categoria->nombre }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="text-sm text-gray-500">{{ $categoria->descripcion ?? '-' }}</td>
                        </template>
                        <template x-if="!editing">
                            <td class="text-center">
                                <span class="badge badge-info">{{ $categoria->productos_count }}</span>
                            </td>
                        </template>
                        <template x-if="!editing">
                            <td>
                                @if($categoria->activo)
                                    <span class="badge badge-success">Activa</span>
                                @else
                                    <span class="badge badge-neutral">Inactiva</span>
                                @endif
                            </td>
                        </template>
                        <template x-if="!editing">
                            <td class="text-center">
                                <div class="flex justify-center gap-2">
                                    <button @click="editing = true" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Editar</button>
                                    @if($categoria->productos_count === 0)
                                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline"
                                              @submit.prevent="confirmSubmit($event, { title: 'Eliminar categoria', text: 'Se eliminara {{ $categoria->nombre }}' })">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Eliminar</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </template>

                        {{-- Modo edicion --}}
                        <template x-if="editing">
                            <td colspan="6">
                                <form action="{{ route('categorias.update', $categoria) }}" method="POST" class="flex flex-wrap gap-3 items-center py-1">
                                    @csrf
                                    @method('PUT')
                                    <div class="w-20">
                                        <input type="number" name="orden" value="{{ $categoria->orden }}" class="form-input-number text-sm" min="0">
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <input type="text" name="nombre" value="{{ $categoria->nombre }}" class="form-input text-sm" required>
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <input type="text" name="descripcion" value="{{ $categoria->descripcion }}" class="form-input text-sm" placeholder="Descripcion">
                                    </div>
                                    <label class="flex items-center gap-1.5 text-sm">
                                        <input type="hidden" name="activo" value="0">
                                        <input type="checkbox" name="activo" value="1" {{ $categoria->activo ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 rounded">
                                        Activa
                                    </label>
                                    <button type="submit" class="btn-primary btn-sm">Guardar</button>
                                    <button type="button" @click="editing = false" class="btn-secondary btn-sm">Cancelar</button>
                                </form>
                            </td>
                        </template>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">🏷️</div>
                                <p class="empty-state-text">No hay categorias creadas</p>
                                <p class="text-sm text-gray-400 mt-1">Usa el formulario de arriba para crear la primera</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
