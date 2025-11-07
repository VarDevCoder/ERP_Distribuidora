@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
.metric-card {
  border-radius: 1rem;
  transition: transform 0.4s, box-shadow 0.4s;
  max-height: 160px;
  overflow: hidden;
  font-style: italic;
  background: linear-gradient(135deg, #f3f4f6, #e0f2fe);
}

.metric-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 25px rgba(0,0,0,0.18);
}

.metric-card canvas {
  height: 50px !important;
  background: rgba(255,255,255,0.1);
  border-radius: 0.5rem;
}

.table-responsive {
  max-height: 300px;
  overflow-y: auto;
}

.table-hover tbody tr:hover {
  background: linear-gradient(to right, #f0f9ff, #bae6fd);
  transform: scale(1.02);
  transition: all 0.3s ease;
}

.text-green-600 { color: #10b981 !important; }
.text-blue-600 { color: #3b82f6 !important; }
.text-indigo-600 { color: #6366f1 !important; }
.text-red-600 { color: #ef4444 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <h4 class="mb-3 fw-bold text-gray-700">DASHBOARD</h4>
    <p class="text-gray-500 mb-4 fst-italic">Resumen general de la distribuidora</p>

    <!-- Cards con microcharts -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card metric-card shadow-sm border-start border-success border-5 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="text-gray-500 fw-semibold mb-2"><i class="fas fa-shopping-cart me-1"></i> Ventas Hoy</h6>
                        <h3 class="text-green-600 fw-bold display-6">Gs. {{ number_format($ventasHoy,0,',','.') }}</h3>
                    </div>
                    <i class="fas fa-cart-arrow-down fa-3x text-green-600"></i>
                </div>
                <canvas class="micro-chart" id="ventasHoyChart"></canvas>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card metric-card shadow-sm border-start border-primary border-5 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="text-gray-500 fw-semibold mb-2"><i class="fas fa-boxes me-1"></i> Productos</h6>
                        <h3 class="text-blue-600 fw-bold display-6">{{ $productos }}</h3>
                    </div>
                    <i class="fas fa-box-open fa-3x text-blue-600"></i>
                </div>
                <canvas class="micro-chart" id="productosChart"></canvas>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card metric-card shadow-sm border-start border-info border-5 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="text-gray-500 fw-semibold mb-2"><i class="fas fa-users me-1"></i> Clientes</h6>
                        <h3 class="text-indigo-600 fw-bold display-6">{{ $clientes }}</h3>
                    </div>
                    <i class="fas fa-user-friends fa-3x text-indigo-600"></i>
                </div>
                <canvas class="micro-chart" id="clientesChart"></canvas>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card metric-card shadow-sm border-start border-danger border-5 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <h6 class="text-gray-500 fw-semibold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Stock Bajo</h6>
                        <h3 class="text-red-600 fw-bold display-6">{{ $stockBajo }}</h3>
                    </div>
                    <i class="fas fa-box fa-3x text-red-600"></i>
                </div>
                <canvas class="micro-chart" id="stockChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Dashboard Gráfico y Ventas Recientes -->
    <div class="row g-3">
        <div class="col-lg-6 col-md-12">
            <div class="card p-3 shadow-sm">
                <h6 class="fw-semibold mb-3"><i class="fas fa-chart-line me-1"></i> Ventas Anuales</h6>
                <canvas id="ventasChart" style="height:250px; max-height:300px;"></canvas>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card p-3 shadow-sm">
                <h6 class="fw-semibold mb-3"><i class="fas fa-receipt me-1"></i> Últimas Ventas</h6>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventasRecientes as $v)
                            <tr>
                                <td><i class="fas fa-user me-1 text-blue-600"></i>{{ $v->cliente->cli_nombre }} {{ $v->cliente->cli_apellido }}</td>
                                <td>{{ \Carbon\Carbon::parse($v->ven_fecha)->format('d/m/Y') }}</td>
                                <td class="fw-bold text-success">Gs. {{ number_format($v->ven_total,0,',','.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay ventas recientes</td>
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
/* Gráfico principal con degradado */
const ctx = document.getElementById('ventasChart');
const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
gradient.addColorStop(0, 'rgba(59,130,246,0.5)');
gradient.addColorStop(1, 'rgba(37,99,235,0.1)');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
        datasets: [{
            label: 'Ventas Mensuales',
            data: @json($ventasAnuales),
            borderColor: '#2563EB',
            backgroundColor: gradient,
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#3b82f6',
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { font: { weight: 'bold' } } } },
        animation: { duration: 1200 }
    }
});

/* Microcharts animados */
function createMicroChart(id, data, color) {
    const ctx = document.getElementById(id);
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ene','Feb','Mar','Abr','May','Jun'],
            datasets: [{
                data: data,
                borderColor: color,
                backgroundColor: Chart.helpers.color(color).alpha(0.2).rgbString(),
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: color
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { display: false }, y: { display: false } }
        }
    });
}

createMicroChart('ventasHoyChart', @json($ventasHoyMes), '#10b981');
createMicroChart('productosChart', @json($productosMes), '#3b82f6');
createMicroChart('clientesChart', @json($clientesMes), '#6366f1');
createMicroChart('stockChart', @json($stockMes), '#ef4444');
</script>
@endpush
