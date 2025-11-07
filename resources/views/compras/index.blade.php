@extends('layouts.app')
@section('title', 'Compras')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-shopping-bag me-2"></i>Gestión de Compras</h2>
        <a href="{{ route('compras.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Compra
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
                            <th>Proveedor</th>
                            <th>Factura</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compras as $compra)
                        <tr>
                            <td class="fw-bold">{{ $compra->com_numero }}</td>
                            <td>{{ \Carbon\Carbon::parse($compra->com_fecha)->format('d/m/Y') }}</td>
                            <td>{{ $compra->proveedor->prov_nombre }}</td>
                            <td>{{ $compra->com_factura ?? '-' }}</td>
                            <td class="fw-bold text-success">Gs. {{ number_format($compra->com_total, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $compra->com_estado == 'COMPLETADA' ? 'success' : 'danger' }}">
                                    {{ $compra->com_estado }}
                                </span>
                            </td>
                            <td>{{ $compra->usuario->usu_nombre }}</td>
                            <td>
                                <a href="{{ route('compras.show', $compra->com_id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($compra->com_estado == 'COMPLETADA')
                                <form action="{{ route('compras.anular', $compra->com_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Anular esta compra? Se revertirá el stock.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-ban"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay compras registradas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $compras->links() }}
        </div>
    </div>
</div>
@endsection
