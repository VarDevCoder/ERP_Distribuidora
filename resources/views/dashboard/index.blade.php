@extends('layouts.app')
@section('title', 'Dashboard - ERP Distribuidora')

@push('styles')
<style>
    :root {
        --primary-color: #3b82f6;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;
        --dark-color: #1f2937;
    }

    body {
        background: #f3f4f6;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    /* Tabs personalizados */
    .nav-tabs-custom {
        background: white;
        border-radius: 1rem;
        padding: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        border: none;
        margin-bottom: 2rem;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #6b7280;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        margin: 0 0.25rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }

    .nav-tabs-custom .nav-link:hover {
        background: #f3f4f6;
        color: var(--primary-color);
    }

    .nav-tabs-custom .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .nav-tabs-custom .nav-link i {
        margin-right: 0.5rem;
    }

    .metric-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: all 0.3s ease;
        border-left: 4px solid;
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .metric-card.ventas { border-left-color: var(--success-color); }
    .metric-card.inventario { border-left-color: var(--info-color); }
    .metric-card.compras { border-left-color: var(--warning-color); }
    .metric-card.clientes { border-left-color: var(--primary-color); }
    .metric-card.stock { border-left-color: var(--danger-color); }

    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .metric-icon.ventas { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
    .metric-icon.inventario { background: rgba(6, 182, 212, 0.1); color: var(--info-color); }
    .metric-icon.compras { background: rgba(245, 158, 11, 0.1); color: var(--warning-color); }
    .metric-icon.clientes { background: rgba(59, 130, 246, 0.1); color: var(--primary-color); }
    .metric-icon.stock { background: rgba(239, 68, 68, 0.1); color: var(--danger-color); }

    .chart-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        height: 100%;
    }

    .table-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        max-height: 400px;
        overflow-y: auto;
    }

    .table-card table {
        margin-bottom: 0;
    }

    .table-card thead {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .badge-growth {
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-growth.positive {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }

    .badge-growth.negative {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    .mini-chart {
        height: 60px !important;
    }

    .tab-content {
        min-height: 500px;
    }

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }

        .metric-card {
            margin-bottom: 1rem;
        }

        .nav-tabs-custom .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="dashboard-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2"><i class="fas fa-chart-line me-2"></i>Dashboard ERP</h2>
                <p class="mb-0 opacity-90">Bienvenido, {{ $user->usu_nombre }} | {{ \Carbon\Carbon::now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="badge bg-white text-dark px-3 py-2">
                    <i class="fas fa-clock me-1"></i>
                    Última actualización: {{ \Carbon\Carbon::now()->format('H:i') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs nav-tabs-custom" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                <i class="fas fa-home"></i>Vista General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab">
                <i class="fas fa-shopping-cart"></i>Ventas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="inventario-tab" data-bs-toggle="tab" data-bs-target="#inventario" type="button" role="tab">
                <i class="fas fa-warehouse"></i>Inventario
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="compras-tab" data-bs-toggle="tab" data-bs-target="#compras" type="button" role="tab">
                <i class="fas fa-truck"></i>Compras
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="dashboardTabsContent">

        <!-- ==================== VISTA GENERAL ==================== -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="row g-4 mb-4">
                <!-- Métrica Ventas Hoy -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="metric-card ventas">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-muted mb-1 small">Ventas Hoy</p>
                                <h3 class="mb-0 fw-bold">Gs. {{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                            </div>
                            <div class="metric-icon ventas">
                                <i class="fas fa-cash-register"></i>
                            </div>
                        </div>
                        <canvas id="ventasHoyChart" class="mini-chart"></canvas>
                    </div>
                </div>

                <!-- Métrica Ventas del Mes -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="metric-card ventas">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-muted mb-1 small">Ventas del Mes</p>
                                <h3 class="mb-0 fw-bold">Gs. {{ number_format($ventasMes, 0, ',', '.') }}</h3>
                                @if($crecimientoVentas != 0)
                                    <span class="badge-growth {{ $crecimientoVentas > 0 ? 'positive' : 'negative' }}">
                                        <i class="fas fa-arrow-{{ $crecimientoVentas > 0 ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($crecimientoVentas), 1) }}%
                                    </span>
                                @endif
                            </div>
                            <div class="metric-icon ventas">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métrica Productos -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="metric-card inventario">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-muted mb-1 small">Productos</p>
                                <h3 class="mb-0 fw-bold">{{ $totalProductos }}</h3>
                                <small class="text-muted">Valor: Gs. {{ number_format($valorInventario, 0, ',', '.') }}</small>
                            </div>
                            <div class="metric-icon inventario">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <canvas id="productosChart" class="mini-chart"></canvas>
                    </div>
                </div>

                <!-- Métrica Stock Bajo -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="metric-card stock">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <p class="text-muted mb-1 small">Stock Bajo</p>
                                <h3 class="mb-0 fw-bold text-danger">{{ $stockBajo }}</h3>
                                <small class="text-muted">Productos críticos</small>
                            </div>
                            <div class="metric-icon stock">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen Gráfico General -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="chart-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-chart-area me-2"></i>Resumen de Ventas {{ date('Y') }}</h6>
                        <canvas id="ventasGeneralChart" style="height: 300px;"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-chart-bar me-2"></i>Resumen de Compras {{ date('Y') }}</h6>
                        <canvas id="comprasGeneralChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== VENTAS ==================== -->
        <div class="tab-pane fade" id="ventas" role="tabpanel">
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="chart-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-chart-line me-2"></i>Ventas Mensuales {{ date('Y') }}</h6>
                        <canvas id="ventasAnualesChart" style="height: 350px;"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="metric-card ventas mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Ventas Hoy</p>
                                <h3 class="mb-0 fw-bold text-success">Gs. {{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                            </div>
                            <div class="metric-icon ventas">
                                <i class="fas fa-cash-register"></i>
                            </div>
                        </div>
                    </div>
                    <div class="metric-card ventas">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Ventas del Mes</p>
                                <h3 class="mb-0 fw-bold text-success">Gs. {{ number_format($ventasMes, 0, ',', '.') }}</h3>
                                @if($crecimientoVentas != 0)
                                    <span class="badge-growth {{ $crecimientoVentas > 0 ? 'positive' : 'negative' }} mt-2">
                                        <i class="fas fa-arrow-{{ $crecimientoVentas > 0 ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($crecimientoVentas), 1) }}% vs mes anterior
                                    </span>
                                @endif
                            </div>
                            <div class="metric-icon ventas">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="table-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-users me-2"></i>Últimos Clientes</h6>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Última Compra</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimosClientes as $uc)
                                    <tr>
                                        <td>
                                            <i class="fas fa-user-circle text-primary me-2"></i>
                                            <strong>{{ $uc->cliente->cli_nombre }} {{ $uc->cliente->cli_apellido }}</strong>
                                        </td>
                                        <td><small class="text-muted">{{ \Carbon\Carbon::parse($uc->ultima_compra)->format('d/m/Y') }}</small></td>
                                        <td class="text-success fw-bold">Gs. {{ number_format($uc->total_compras, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-2 d-block opacity-50"></i>
                                            No hay datos de clientes
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="table-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-chart-pie me-2"></i>Ventas por Período</h6>
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div>
                                    <p class="mb-1 text-muted small">Hoy</p>
                                    <h5 class="mb-0 fw-bold">Gs. {{ number_format($ventasHoy, 0, ',', '.') }}</h5>
                                </div>
                                <i class="fas fa-calendar-day fa-2x text-primary opacity-50"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div>
                                    <p class="mb-1 text-muted small">Este Mes</p>
                                    <h5 class="mb-0 fw-bold">Gs. {{ number_format($ventasMes, 0, ',', '.') }}</h5>
                                </div>
                                <i class="fas fa-calendar-week fa-2x text-success opacity-50"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-muted small">Mes Anterior</p>
                                    <h5 class="mb-0 fw-bold">Gs. {{ number_format($ventasMesAnterior, 0, ',', '.') }}</h5>
                                </div>
                                <i class="fas fa-calendar-alt fa-2x text-info opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== INVENTARIO ==================== -->
        <div class="tab-pane fade" id="inventario" role="tabpanel">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="metric-card inventario">
                        <div class="text-center">
                            <div class="metric-icon inventario mx-auto mb-3">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $totalProductos }}</h3>
                            <p class="text-muted mb-0 small">Total Productos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card stock">
                        <div class="text-center">
                            <div class="metric-icon stock mx-auto mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3 class="fw-bold mb-1 text-danger">{{ $stockBajo }}</h3>
                            <p class="text-muted mb-0 small">Stock Bajo</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="metric-card inventario">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Valor Total del Inventario</p>
                                <h3 class="mb-0 fw-bold text-info">Gs. {{ number_format($valorInventario, 0, ',', '.') }}</h3>
                                <small class="text-muted">Basado en precio de compra</small>
                            </div>
                            <div class="metric-icon inventario">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="table-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-exclamation-circle text-danger me-2"></i>Productos con Stock Bajo</h6>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Mínimo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productosStockBajo as $p)
                                    <tr>
                                        <td>
                                            <strong>{{ $p->pro_nombre }}</strong><br>
                                            <small class="text-muted">{{ $p->pro_codigo }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $p->pro_stock }}</span>
                                        </td>
                                        <td>{{ $p->pro_stock_minimo }}</td>
                                        <td>
                                            <i class="fas fa-exclamation-circle text-danger"></i>
                                            Crítico
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-3x mb-2 d-block text-success opacity-50"></i>
                                            Stock en niveles normales
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="table-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-trophy text-warning me-2"></i>Productos Más Vendidos</h6>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Cantidad Vendida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productosMasVendidos as $index => $pv)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index == 0 ? 'bg-warning' : ($index == 1 ? 'bg-secondary' : 'bg-info') }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $pv->producto->pro_nombre }}</strong><br>
                                            <small class="text-muted">{{ $pv->producto->pro_codigo }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $pv->total_vendido }} {{ $pv->producto->pro_unidad_medida }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-2 d-block opacity-50"></i>
                                            No hay ventas registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== COMPRAS ==================== -->
        <div class="tab-pane fade" id="compras" role="tabpanel">
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="chart-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-chart-bar me-2"></i>Gastos Mensuales en Compras {{ date('Y') }}</h6>
                        <canvas id="comprasMensualesChart" style="height: 350px;"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="metric-card compras mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Gastos del Mes</p>
                                <h3 class="mb-0 fw-bold text-warning">Gs. {{ number_format($gastosMes, 0, ',', '.') }}</h3>
                            </div>
                            <div class="metric-icon compras">
                                <i class="fas fa-shopping-basket"></i>
                            </div>
                        </div>
                    </div>
                    <div class="metric-card clientes">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Proveedores Activos</p>
                                <h3 class="mb-0 fw-bold text-primary">{{ $totalProveedores }}</h3>
                                <small class="text-muted">Nuevos clientes: {{ $nuevosClientes }}</small>
                            </div>
                            <div class="metric-icon clientes">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="table-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-building text-warning me-2"></i>Proveedores - Mayor Gasto</h6>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Proveedor</th>
                                    <th>Total Gastado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProveedoresGasto as $pg)
                                    <tr>
                                        <td>
                                            <i class="fas fa-building text-warning me-2"></i>
                                            <strong>{{ $pg->proveedor->prov_nombre }}</strong><br>
                                            <small class="text-muted">{{ $pg->proveedor->prov_ciudad }}</small>
                                        </td>
                                        <td class="text-danger fw-bold">Gs. {{ number_format($pg->total_gastado, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-2 d-block opacity-50"></i>
                                            No hay compras registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="table-card">
                        <h6 class="fw-semibold mb-3"><i class="fas fa-store text-success me-2"></i>Proveedores - Precios Más Económicos</h6>
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Proveedor</th>
                                    <th>Precio Prom.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proveedoresEconomicos as $pe)
                                    <tr>
                                        <td>
                                            <strong>{{ $pe->producto->pro_nombre ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-store text-success me-1"></i>
                                            {{ $pe->compra->proveedor->prov_nombre ?? 'N/A' }}
                                        </td>
                                        <td class="text-success fw-bold">Gs. {{ number_format($pe->precio_promedio, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-2 d-block opacity-50"></i>
                                            No hay datos de comparación
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ==================== GESTIÓN DE INSTANCIAS DE GRÁFICOS ====================
    // Objeto para almacenar todas las instancias de Chart.js
    const chartInstances = {};

    // Función para destruir un gráfico existente antes de crear uno nuevo
    function destroyChart(chartId) {
        if (chartInstances[chartId]) {
            chartInstances[chartId].destroy();
            chartInstances[chartId] = null;
        }
    }

    // Función para crear un gráfico de forma segura
    function createChart(canvasId, config) {
        // Destruir gráfico existente si existe
        destroyChart(canvasId);

        // Verificar que el canvas existe
        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            console.warn(`Canvas ${canvasId} no encontrado`);
            return null;
        }

        // Crear nueva instancia y almacenarla
        chartInstances[canvasId] = new Chart(canvas, config);
        return chartInstances[canvasId];
    }

    // Configuración común para todos los gráficos
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.color = '#6b7280';
    Chart.defaults.animation.duration = 750; // Reducir duración de animación

    // ==================== INICIALIZACIÓN AL CARGAR PÁGINA ====================
    document.addEventListener('DOMContentLoaded', function() {

        // ==================== VISTA GENERAL (Tab activo por defecto) ====================

        // Mini chart - Ventas Hoy
        createChart('ventasHoyChart', {
            type: 'line',
            data: {
                labels: ['', '', '', '', '', '', ''],
                datasets: [{
                    data: @json($ventasDiarias),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 500 },
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { display: false } }
            }
        });

        // Mini chart - Productos
        createChart('productosChart', {
            type: 'bar',
            data: {
                labels: ['', '', '', '', '', ''],
                datasets: [{
                    data: @json($evolucionProductos),
                    backgroundColor: '#06b6d4',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 500 },
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { display: false } }
            }
        });

        // Gráfico Ventas General
        const ctxVentasGeneral = document.getElementById('ventasGeneralChart');
        if (ctxVentasGeneral) {
            const gradVentas = ctxVentasGeneral.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradVentas.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            gradVentas.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            createChart('ventasGeneralChart', {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    datasets: [{
                        label: 'Ventas (Gs.)',
                        data: @json($ventasAnuales),
                        borderColor: '#10b981',
                        backgroundColor: gradVentas,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 750 },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: (context) => 'Gs. ' + context.parsed.y.toLocaleString('es-PY')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (value) => 'Gs. ' + (value / 1000).toFixed(0) + 'K' },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Gráfico Compras General
        const ctxComprasGeneral = document.getElementById('comprasGeneralChart');
        if (ctxComprasGeneral) {
            const gradCompras = ctxComprasGeneral.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradCompras.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
            gradCompras.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

            createChart('comprasGeneralChart', {
                type: 'bar',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    datasets: [{
                        label: 'Gastos (Gs.)',
                        data: @json($gastosMensuales),
                        backgroundColor: gradCompras,
                        borderColor: '#f59e0b',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 750 },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: (context) => 'Gs. ' + context.parsed.y.toLocaleString('es-PY')
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (value) => 'Gs. ' + (value / 1000).toFixed(0) + 'K' },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // ==================== INICIALIZACIÓN LAZY DE TABS ====================

        // Flags para controlar si los tabs ya fueron inicializados
        let ventasTabInitialized = false;
        let comprasTabInitialized = false;

        // Event listener para tabs
        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function (event) {
                const targetTab = event.target.getAttribute('data-bs-target');

                // Inicializar gráficos solo cuando se muestra el tab
                if (targetTab === '#ventas' && !ventasTabInitialized) {
                    initVentasTab();
                    ventasTabInitialized = true;
                } else if (targetTab === '#compras' && !comprasTabInitialized) {
                    initComprasTab();
                    comprasTabInitialized = true;
                }
            });
        });

        // Función para inicializar tab de Ventas
        function initVentasTab() {
            const ctxVentasAnuales = document.getElementById('ventasAnualesChart');
            if (ctxVentasAnuales && !chartInstances['ventasAnualesChart']) {
                const gradVentasTab = ctxVentasAnuales.getContext('2d').createLinearGradient(0, 0, 0, 350);
                gradVentasTab.addColorStop(0, 'rgba(16, 185, 129, 0.5)');
                gradVentasTab.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                createChart('ventasAnualesChart', {
                    type: 'line',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [{
                            label: 'Ventas Mensuales (Gs.)',
                            data: @json($ventasAnuales),
                            borderColor: '#10b981',
                            backgroundColor: gradVentasTab,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 750 },
                        plugins: {
                            legend: { display: true, position: 'top', labels: { font: { weight: 'bold' } } },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14 },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: (context) => 'Gs. ' + context.parsed.y.toLocaleString('es-PY')
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: (value) => 'Gs. ' + (value / 1000).toFixed(0) + 'K' },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }

        // Función para inicializar tab de Compras
        function initComprasTab() {
            const ctxComprasMensuales = document.getElementById('comprasMensualesChart');
            if (ctxComprasMensuales && !chartInstances['comprasMensualesChart']) {
                const gradComprasTab = ctxComprasMensuales.getContext('2d').createLinearGradient(0, 0, 0, 350);
                gradComprasTab.addColorStop(0, 'rgba(245, 158, 11, 0.5)');
                gradComprasTab.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

                createChart('comprasMensualesChart', {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [{
                            label: 'Gastos en Compras (Gs.)',
                            data: @json($gastosMensuales),
                            backgroundColor: gradComprasTab,
                            borderColor: '#f59e0b',
                            borderWidth: 2,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 750 },
                        plugins: {
                            legend: { display: true, position: 'top', labels: { font: { weight: 'bold' } } },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                callbacks: {
                                    label: (context) => 'Gs. ' + context.parsed.y.toLocaleString('es-PY')
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: (value) => 'Gs. ' + (value / 1000).toFixed(0) + 'K' },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }
    });

    // Limpiar todas las instancias cuando se abandona la página
    window.addEventListener('beforeunload', function() {
        Object.keys(chartInstances).forEach(chartId => {
            destroyChart(chartId);
        });
    });
</script>
@endpush
