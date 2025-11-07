@extends('layouts.app')
@section('title', 'Movimientos de Inventario')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-list me-2"></i>Movimientos de Inventario</h2>
        <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Stock Anterior</th>
                            <th class="text-center">Stock Nuevo</th>
                            <th>Referencia</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $mov)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov->mov_fecha)->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong>{{ $mov->producto->pro_codigo }}</strong><br>
                                <small>{{ $mov->producto->pro_nombre }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $mov->mov_tipo == 'ENTRADA' ? 'success' : 'danger' }}">
                                    {{ $mov->mov_tipo }}
                                </span>
                            </td>
                            <td><span class="badge bg-secondary">{{ $mov->mov_motivo }}</span></td>
                            <td class="text-center fw-bold">{{ $mov->mov_cantidad }}</td>
                            <td class="text-center">{{ $mov->mov_stock_anterior }}</td>
                            <td class="text-center fw-bold text-primary">{{ $mov->mov_stock_nuevo }}</td>
                            <td>{{ $mov->mov_referencia ?? '-' }}</td>
                            <td>{{ $mov->usuario->usu_nombre }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No hay movimientos registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $movimientos->links() }}
        </div>
    </div>
</div>
@endsection
