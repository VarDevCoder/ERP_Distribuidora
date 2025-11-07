@extends('layouts.app')
@section('title', 'Ventas')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-shopping-cart me-2"></i>Gestión de Ventas</h2>
        <a href="{{ route('ventas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Venta
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td class="fw-bold">{{ $venta->ven_numero }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->ven_fecha)->format('d/m/Y') }}</td>
                            <td>{{ $venta->cliente->cli_nombre }} {{ $venta->cliente->cli_apellido }}</td>
                            <td class="fw-bold text-success">Gs. {{ number_format($venta->ven_total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $venta->ven_estado == 'COMPLETADA' ? 'success' : 'warning' }}">
                                    {{ $venta->ven_estado }}
                                </span>
                            </td>
                            <td>{{ $venta->usuario->usu_nombre }}</td>
                            <td>
                                <a href="{{ route('ventas.show', $venta->ven_id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay ventas registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $ventas->links() }}
        </div>
    </div>
</div>
@endsection
