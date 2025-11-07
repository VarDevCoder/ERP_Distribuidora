@extends('layouts.app')
@section('title', 'Inventario')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-warehouse me-2"></i>Gestión de Inventario</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('inventario.ajuste') }}" class="btn btn-warning">
                <i class="fas fa-adjust me-2"></i>Ajustar Stock
            </a>
            <a href="{{ route('inventario.movimientos') }}" class="btn btn-info">
                <i class="fas fa-list me-2"></i>Ver Movimientos
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6>Total Productos</h6>
                    <h2 class="fw-bold">{{ $totalProductos }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-white">
                <div class="card-body">
                    <h6>Stock Bajo</h6>
                    <h2 class="fw-bold">{{ $stockBajo }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-danger text-white">
                <div class="card-body">
                    <h6>Sin Stock</h6>
                    <h2 class="fw-bold">{{ $sinStock }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6>Valor Inventario</h6>
                    <h2 class="fw-bold">Gs. {{ number_format($valorInventario, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-8">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, código o categoría..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-2">
                    <select name="stock_bajo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('stock_bajo') == '1' ? 'selected' : '' }}>Solo Stock Bajo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock Actual</th>
                            <th class="text-center">Stock Mínimo</th>
                            <th>Estado</th>
                            <th>Valor Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr>
                            <td class="fw-bold">{{ $producto->pro_codigo }}</td>
                            <td>{{ $producto->pro_nombre }}</td>
                            <td>{{ $producto->pro_categoria }}</td>
                            <td class="text-center fw-bold">{{ $producto->pro_stock }} {{ $producto->pro_unidad_medida }}</td>
                            <td class="text-center">{{ $producto->pro_stock_minimo }}</td>
                            <td>
                                @if($producto->pro_stock == 0)
                                    <span class="badge bg-danger">SIN STOCK</span>
                                @elseif($producto->pro_stock <= $producto->pro_stock_minimo)
                                    <span class="badge bg-warning">STOCK BAJO</span>
                                @else
                                    <span class="badge bg-success">DISPONIBLE</span>
                                @endif
                            </td>
                            <td class="fw-bold">Gs. {{ number_format($producto->pro_stock * $producto->pro_precio_compra, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('inventario.kardex', $producto->pro_id) }}" class="btn btn-sm btn-info" title="Ver Kardex">
                                    <i class="fas fa-clipboard-list"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay productos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $productos->links() }}
        </div>
    </div>
</div>
@endsection
