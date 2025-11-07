@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-truck me-2"></i>Gestión de Proveedores</h2>
        <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Proveedor
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, RUC o ciudad..." value="{{ request('buscar') }}">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>RUC</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Ciudad</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $proveedor)
                        <tr>
                            <td class="fw-bold">{{ $proveedor->prov_nombre }}</td>
                            <td>{{ $proveedor->prov_ruc }}</td>
                            <td>{{ $proveedor->prov_telefono ?? '-' }}</td>
                            <td>{{ $proveedor->prov_email ?? '-' }}</td>
                            <td>{{ $proveedor->prov_ciudad ?? '-' }}</td>
                            <td>{{ $proveedor->prov_contacto ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $proveedor->prov_estado == 'ACTIVO' ? 'success' : 'secondary' }}">
                                    {{ $proveedor->prov_estado }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('proveedores.edit', $proveedor->prov_id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('proveedores.destroy', $proveedor->prov_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este proveedor?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay proveedores registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $proveedores->links() }}
        </div>
    </div>
</div>
@endsection
