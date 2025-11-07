@extends('layouts.app')
@section('title', 'Clientes')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-users me-2"></i>Gestión de Clientes</h2>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Cliente
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, CI o teléfono..." value="{{ request('buscar') }}">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>CI</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                        <tr>
                            <td class="fw-bold">{{ $cliente->cli_nombre }} {{ $cliente->cli_apellido }}</td>
                            <td>{{ $cliente->cli_ci }}</td>
                            <td>{{ $cliente->cli_telefono ?? '-' }}</td>
                            <td>{{ $cliente->cli_email ?? '-' }}</td>
                            <td>{{ $cliente->cli_direccion ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $cliente->cli_tipo == 'MAYORISTA' ? 'primary' : 'info' }}">
                                    {{ $cliente->cli_tipo }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('clientes.edit', $cliente->cli_id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clientes.destroy', $cliente->cli_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay clientes registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $clientes->links() }}
        </div>
    </div>
</div>
@endsection
