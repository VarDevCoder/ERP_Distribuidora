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

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #e5e7eb;
        position: relative;
    }

    .section-title::before {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(to right, var(--primary-color), var(--success-color));
    }

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

    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }

        .metric-card {
            margin-bottom: 1rem;
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

    <!-- Métricas Principales -->
    <div class="row g-4 mb-4">
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

    <!-- SECCIÓN VENTAS -->
    <h5 class="section-title">
        <i class="fas fa-shopping-cart me-2"></i>Análisis de Ventas
    </h5>
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-card">
                <h6 class="fw-semibold mb-3">Ventas Anuales {{ date('Y') }}</h6>
                <canvas id="ventasAnualesChart" style="height: 300px;"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="table-card">
                <h6 class="fw-semibold mb-3">Últimos Clientes</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosClientes as $uc)
                                <tr>
                                    <td>
                                        <i class="fas fa-user-circle text-primary me-2"></i>
                                        <strong>{{ $uc->cliente->cli_nombre }} {{ $uc->cliente->cli_apellido }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($uc->ultima_compra)->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-success fw-bold">Gs. {{ number_format($uc->total_compras, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No hay datos
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN INVENTARIO -->
    <h5 class="section-title">
        <i class="fas fa-warehouse me-2"></i>Gestión de Inventario
    </h5>
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="table-card">
                <h6 class="fw-semibold mb-3">Productos con Stock Bajo</h6>
                <div class="table-responsive">
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
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                        Stock en niveles normales
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="table-card">
                <h6 class="fw-semibold mb-3">Productos Más Vendidos</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
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
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
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

    <!-- SECCIÓN COMPRAS -->
    <h5 class="section-title">
        <i class="fas fa-truck me-2"></i>Análisis de Compras
    </h5>
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-card">
                <h6 class="fw-semibold mb-3">Gastos Mensuales en Compras {{ date('Y') }}</h6>
                <canvas id="comprasChart" style="height: 300px;"></canvas>
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

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="table-card">
                <h6 class="fw-semibold mb-3">Proveedores - Mayor Gasto</h6>
                <div class="table-responsive">
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
                                    <td colspan="2" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No hay compras registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="table-card">
                <h6 class="fw-semibold mb-3">Proveedores - Precios Más Económicos</h6>
                <div class="table-responsive">
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
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración común de gráficos
    Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.color = '#6b7280';

    // Mini chart - Ventas Hoy (últimos 7 días)
    new Chart(document.getElementById('ventasHoyChart'), {
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
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: { display: false }
            }
        }
    });

    // Mini chart - Productos (últimos 6 meses)
    new Chart(document.getElementById('productosChart'), {
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
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: { display: false }
            }
        }
    });

    // Gráfico Ventas Anuales
    const ctxVentas = document.getElementById('ventasAnualesChart');
    const gradientVentas = ctxVentas.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradientVentas.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    gradientVentas.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ventas Mensuales (Gs.)',
                data: @json($ventasAnuales),
                borderColor: '#10b981',
                backgroundColor: gradientVentas,
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
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { font: { weight: 'bold' } }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return 'Gs. ' + context.parsed.y.toLocaleString('es-PY');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Gs. ' + (value / 1000).toFixed(0) + 'K';
                        }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Gráfico Compras Mensuales
    const ctxCompras = document.getElementById('comprasChart');
    const gradientCompras = ctxCompras.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradientCompras.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
    gradientCompras.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

    new Chart(ctxCompras, {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Gastos en Compras (Gs.)',
                data: @json($gastosMensuales),
                backgroundColor: gradientCompras,
                borderColor: '#f59e0b',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { font: { weight: 'bold' } }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return 'Gs. ' + context.parsed.y.toLocaleString('es-PY');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Gs. ' + (value / 1000).toFixed(0) + 'K';
                        }
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
