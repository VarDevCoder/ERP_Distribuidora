@extends('layouts.app')
@section('title', 'Detalle de Compra')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-shopping-bag me-2"></i>Detalle de Compra</h2>
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
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
                            <span class="fs-5 fw-bold text-primary">{{ $compra->com_numero }}</span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Fecha:</strong><br>
                            {{ \Carbon\Carbon::parse($compra->com_fecha)->format('d/m/Y') }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Factura:</strong><br>
                            {{ $compra->com_factura ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Proveedor:</strong><br>
                            {{ $compra->proveedor->prov_nombre }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Usuario:</strong><br>
                            {{ $compra->usuario->usu_nombre }} {{ $compra->usuario->usu_apellido }}
                        </div>
                        <div class="col-md-12 mb-3">
                            <strong>Estado:</strong><br>
                            <span class="badge bg-{{ $compra->com_estado == 'COMPLETADA' ? 'success' : 'danger' }} fs-6">
                                {{ $compra->com_estado }}
                            </span>
                        </div>
                        @if($compra->com_observaciones)
                        <div class="col-md-12">
                            <strong>Observaciones:</strong><br>
                            {{ $compra->com_observaciones }}
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
                                @foreach($compra->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto->pro_nombre }}</td>
                                    <td class="text-center">{{ $detalle->det_com_cantidad }}</td>
                                    <td class="text-end">Gs. {{ number_format($detalle->det_com_precio_unitario, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold">Gs. {{ number_format($detalle->det_com_subtotal, 0, ',', '.') }}</td>
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
                        <span class="fw-bold">Gs. {{ number_format($compra->com_subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Descuento:</span>
                        <span class="fw-bold text-danger">Gs. {{ number_format($compra->com_descuento, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fs-5 fw-bold">Total:</span>
                        <span class="fs-4 fw-bold text-success">Gs. {{ number_format($compra->com_total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
