@extends('layouts.app')

@section('title', 'Productos - ERP Distribuidora')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-gray-700"><i class="fas fa-box-open me-2"></i>PRODUCTOS</h4>
    <a href="{{ route('productos.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nuevo Producto</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, código o categoría..." value="{{ request('buscar') }}">
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                    <tr>
                        <td><code>{{ $producto->pro_codigo }}</code></td>
                        <td>{{ $producto->pro_nombre }}</td>
                        <td><span class="badge bg-info">{{ $producto->pro_categoria }}</span></td>
                        <td>Gs. {{ number_format($producto->pro_precio_venta, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $producto->pro_stock <= $producto->pro_stock_minimo ? 'bg-danger' : 'bg-success' }}">
                                {{ $producto->pro_stock }} {{ $producto->pro_unidad_medida }}
                            </span>
                        </td>
                        <td>
                            @if($producto->pro_stock <= $producto->pro_stock_minimo)
                                <span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</span>
                            @else
                                <span class="badge bg-success"><i class="fas fa-check"></i> OK</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('productos.edit', $producto->pro_id) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('productos.destroy', $producto->pro_id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto?')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No hay productos registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $productos->links() }}
        </div>
    </div>
</div>
@endsection
