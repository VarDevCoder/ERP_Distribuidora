@extends('layouts.app')
@section('title', 'Presupuestos')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-file-invoice me-2"></i>Gestión de Presupuestos</h2>
        <a href="{{ route('presupuestos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nuevo Presupuesto
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
                            <th>Vencimiento</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($presupuestos as $pre)
                        <tr>
                            <td class="fw-bold">{{ $pre->pre_numero }}</td>
                            <td>{{ \Carbon\Carbon::parse($pre->pre_fecha)->format('d/m/Y') }}</td>
                            <td>{{ $pre->cliente->cli_nombre }} {{ $pre->cliente->cli_apellido }}</td>
                            <td>{{ \Carbon\Carbon::parse($pre->pre_fecha_vencimiento)->format('d/m/Y') }}</td>
                            <td class="fw-bold text-success">Gs. {{ number_format($pre->pre_total, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $badgeClass = match($pre->pre_estado) {
                                        'PENDIENTE' => 'warning',
                                        'APROBADO' => 'info',
                                        'CONVERTIDO' => 'success',
                                        'RECHAZADO' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $pre->pre_estado }}</span>
                            </td>
                            <td>{{ $pre->usuario->usu_nombre }}</td>
                            <td>
                                <a href="{{ route('presupuestos.show', $pre->pre_id) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($pre->pre_estado == 'APROBADO')
                                <a href="{{ route('presupuestos.convertir', $pre->pre_id) }}" class="btn btn-sm btn-success" title="Convertir a Venta">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay presupuestos registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $presupuestos->links() }}
        </div>
    </div>
</div>
@endsection
