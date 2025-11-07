@extends('layouts.app')
@section('title', 'Kardex de Producto')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-clipboard-list me-2"></i>Kardex - {{ $producto->pro_nombre }}</h2>
        <a href="{{ route('inventario.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Información del Producto</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Código:</strong><br>
                    <span class="fs-5 fw-bold">{{ $producto->pro_codigo }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Stock Actual:</strong><br>
                    <span class="fs-4 fw-bold text-success">{{ $producto->pro_stock }} {{ $producto->pro_unidad_medida }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Stock Mínimo:</strong><br>
                    {{ $producto->pro_stock_minimo }} {{ $producto->pro_unidad_medida }}
                </div>
                <div class="col-md-3">
                    <strong>Categoría:</strong><br>
                    {{ $producto->pro_categoria }}
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Historial de Movimientos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Stock Anterior</th>
                            <th class="text-center">Stock Nuevo</th>
                            <th>Referencia</th>
                            <th>Usuario</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $mov)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov->mov_fecha)->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $mov->mov_tipo == 'ENTRADA' ? 'success' : 'danger' }}">
                                    {{ $mov->mov_tipo }}
                                </span>
                            </td>
                            <td>{{ $mov->mov_motivo }}</td>
                            <td class="text-center fw-bold">{{ $mov->mov_cantidad }}</td>
                            <td class="text-center">{{ $mov->mov_stock_anterior }}</td>
                            <td class="text-center fw-bold text-primary">{{ $mov->mov_stock_nuevo }}</td>
                            <td>{{ $mov->mov_referencia ?? '-' }}</td>
                            <td>{{ $mov->usuario->usu_nombre }}</td>
                            <td>{{ $mov->mov_observaciones ?? '-' }}</td>
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
