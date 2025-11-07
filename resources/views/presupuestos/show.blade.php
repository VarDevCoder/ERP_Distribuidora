@extends('layouts.app')
@section('title', 'Detalle de Presupuesto')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-file-invoice me-2"></i>Detalle de Presupuesto</h2>
        <div class="d-flex gap-2">
            @if($presupuesto->pre_estado == 'APROBADO')
            <a href="{{ route('presupuestos.convertir', $presupuesto->pre_id) }}" class="btn btn-success">
                <i class="fas fa-exchange-alt me-2"></i>Convertir a Venta
            </a>
            @endif
            <a href="{{ route('presupuestos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Número:</strong><br>
                            <span class="fs-5 fw-bold text-primary">{{ $presupuesto->pre_numero }}</span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Fecha:</strong><br>
                            {{ \Carbon\Carbon::parse($presupuesto->pre_fecha)->format('d/m/Y') }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Vencimiento:</strong><br>
                            {{ \Carbon\Carbon::parse($presupuesto->pre_fecha_vencimiento)->format('d/m/Y') }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Cliente:</strong><br>
                            {{ $presupuesto->cliente->cli_nombre }} {{ $presupuesto->cliente->cli_apellido }}<br>
                            <small class="text-muted">CI: {{ $presupuesto->cliente->cli_ci }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Usuario:</strong><br>
                            {{ $presupuesto->usuario->usu_nombre }} {{ $presupuesto->usuario->usu_apellido }}
                        </div>
                        <div class="col-md-12 mb-3">
                            <strong>Estado:</strong><br>
                            @php
                                $badgeClass = match($presupuesto->pre_estado) {
                                    'PENDIENTE' => 'warning',
                                    'APROBADO' => 'info',
                                    'CONVERTIDO' => 'success',
                                    'RECHAZADO' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} fs-6">{{ $presupuesto->pre_estado }}</span>

                            @if($presupuesto->pre_estado == 'PENDIENTE')
                            <div class="mt-2">
                                <form action="{{ route('presupuestos.updateEstado', $presupuesto->pre_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="estado" value="APROBADO">
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <i class="fas fa-check me-1"></i>Aprobar
                                    </button>
                                </form>
                                <form action="{{ route('presupuestos.updateEstado', $presupuesto->pre_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="estado" value="RECHAZADO">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Rechazar este presupuesto?')">
                                        <i class="fas fa-times me-1"></i>Rechazar
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @if($presupuesto->pre_observaciones)
                        <div class="col-md-12">
                            <strong>Observaciones:</strong><br>
                            {{ $presupuesto->pre_observaciones }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Productos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($presupuesto->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto->pro_nombre }}</td>
                                    <td class="text-center">{{ $detalle->det_pre_cantidad }}</td>
                                    <td class="text-end">Gs. {{ number_format($detalle->det_pre_precio_unitario, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Gs. {{ number_format($detalle->det_pre_subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Totales</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span class="fw-bold">Gs. {{ number_format($presupuesto->pre_subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Descuento:</span>
                        <span class="fw-bold text-danger">Gs. {{ number_format($presupuesto->pre_descuento, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fs-5 fw-bold">Total:</span>
                        <span class="fs-4 fw-bold text-success">Gs. {{ number_format($presupuesto->pre_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
