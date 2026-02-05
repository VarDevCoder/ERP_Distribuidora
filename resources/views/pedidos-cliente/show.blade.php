@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $pedido->numero }}</h1>
            <p class="page-subtitle">{{ $pedido->estado_descripcion }}</p>
        </div>
        <div class="actions-row">
            <a href="{{ route('pdf.pedido-cliente', $pedido) }}" class="btn-secondary btn-sm" target="_blank">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                PDF
            </a>
            @if($pedido->puedeSerCancelado() && !in_array($pedido->estado, ['ENVIADO', 'ENTREGADO']))
                <a href="{{ route('pedidos-cliente.edit', $pedido) }}" class="btn-primary btn-sm">Editar</a>
            @endif
            <a href="{{ route('pedidos-cliente.index') }}" class="btn-secondary btn-sm">Volver</a>
        </div>
    </div>

    <!-- Flujo de Estado - Modern Stepper -->
    <div class="card mb-6">
        <div class="card-body">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-5">Estado del Flujo</h2>
            <div class="flex items-start justify-between overflow-x-auto pb-2 scrollbar-hide">
                @php
                    $estados = ['RECIBIDO', 'EN_PROCESO', 'PRESUPUESTADO', 'ORDEN_COMPRA', 'MERCADERIA_RECIBIDA', 'LISTO_ENVIO', 'ENVIADO', 'ENTREGADO'];
                    $estadoActualIndex = array_search($pedido->estado, $estados);
                    if ($estadoActualIndex === false) $estadoActualIndex = -1;
                @endphp
                @foreach($estados as $i => $estado)
                    <div class="stepper-step">
                        <div class="flex flex-col items-center">
                            @if($i < $estadoActualIndex)
                                <div class="stepper-indicator stepper-indicator-done">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="stepper-label stepper-label-done">{{ str_replace('_', ' ', $estado) }}</span>
                            @elseif($i == $estadoActualIndex)
                                <div class="stepper-indicator stepper-indicator-current">
                                    {{ $i + 1 }}
                                </div>
                                <span class="stepper-label stepper-label-current">{{ str_replace('_', ' ', $estado) }}</span>
                            @else
                                <div class="stepper-indicator stepper-indicator-pending">
                                    {{ $i + 1 }}
                                </div>
                                <span class="stepper-label stepper-label-pending">{{ str_replace('_', ' ', $estado) }}</span>
                            @endif
                        </div>
                        @if($i < count($estados) - 1)
                            <div class="stepper-line {{ $i < $estadoActualIndex ? 'stepper-line-done' : 'stepper-line-pending' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Acciones segun estado -->
    @if($pedido->estado === 'RECIBIDO')
        <div class="action-panel action-panel-primary mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="font-semibold text-slate-800">Solicitud recibida</p>
                    <p class="text-sm text-slate-600 mt-0.5">Marca la solicitud como en proceso para comenzar a gestionar las compras</p>
                </div>
                <form action="{{ route('pedidos-cliente.procesar', $pedido) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                        Procesar Solicitud
                    </button>
                </form>
            </div>
        </div>
    @elseif(in_array($pedido->estado, ['EN_PROCESO', 'PRESUPUESTADO']))
        <div class="action-panel action-panel-warning mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="font-semibold text-slate-800">En proceso</p>
                    <p class="text-sm text-slate-600 mt-0.5">Solicite cotizaciones a proveedores o cree una orden de compra directa</p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <form action="{{ route('pedidos-cliente.solicitar-todos', $pedido) }}" method="POST"
                          x-data @submit.prevent="confirmSubmit($event, {title: 'Cotizar con todos los proveedores', text: 'Se enviara una solicitud de cotizacion a cada proveedor activo del sistema.', confirmText: 'Si, enviar a todos', icon: 'question'})">
                        @csrf
                        <button type="submit" class="btn-success">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cotizar con Todos
                        </button>
                    </form>
                    <a href="{{ route('solicitudes-presupuesto.create', ['pedido_cliente_id' => $pedido->id]) }}" class="btn-secondary">
                        Cotizar Individual
                    </a>
                    <a href="{{ route('ordenes-compra.create', ['pedido_cliente_id' => $pedido->id]) }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Crear Orden de Compra
                    </a>
                </div>
            </div>
        </div>
    @elseif($pedido->estado === 'MERCADERIA_RECIBIDA')
        <div class="action-panel action-panel-success mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="font-semibold text-slate-800">Mercadería recibida</p>
                    <p class="text-sm text-slate-600 mt-0.5">Crea la orden de envío para el cliente</p>
                </div>
                <a href="{{ route('ordenes-envio.create', ['pedido_cliente_id' => $pedido->id]) }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                    Generar Orden de Envío
                </a>
            </div>
        </div>
    @elseif($pedido->estado === 'ENTREGADO')
        <div class="action-panel action-panel-success mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-semibold text-emerald-800">Solicitud completada y entregada al cliente</p>
            </div>
        </div>
    @elseif($pedido->estado === 'CANCELADO')
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-rose-800">Solicitud Cancelada</p>
                    @if($pedido->motivo_cancelacion)
                        <p class="text-sm text-rose-600 mt-0.5">Motivo: {{ $pedido->motivo_cancelacion }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- COMPARACIÓN AUTOMÁTICA DESDE CATÁLOGO DE PROVEEDORES --}}
    @if(count($comparacionCatalogo) > 0 && $proveedoresEnCatalogo->count() > 0)
        <div class="card mb-6 overflow-hidden">
            <div class="card-header bg-linear-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Comparación de Precios</h2>
                            <p class="text-sm text-slate-500">{{ $proveedoresEnCatalogo->count() }} proveedores con productos en catálogo</p>
                        </div>
                    </div>
                    <a href="{{ route('analisis-proveedores.index') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">
                        Ver Análisis Completo →
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th class="text-left">Producto</th>
                            <th>Cant.</th>
                            @foreach($proveedoresEnCatalogo as $prov)
                                <th>{{ Str::limit($prov->razon_social, 15) }}</th>
                            @endforeach
                            <th class="text-emerald-300!">Mejor</th>
                            <th class="text-sky-300!">Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalsPorProveedor = [];
                            foreach ($proveedoresEnCatalogo as $prov) {
                                $totalsPorProveedor[$prov->id] = 0;
                            }
                        @endphp

                        @foreach($comparacionCatalogo as $productoId => $data)
                            @php
                                $preciosValidos = collect($data['precios'])->pluck('precio')->filter(fn($p) => $p > 0);
                                $mejorPrecio = $preciosValidos->count() > 0 ? $preciosValidos->min() : null;
                                $precioPromedio = $preciosValidos->count() > 0 ? (int) round($preciosValidos->avg()) : null;

                                foreach ($data['precios'] as $provId => $info) {
                                    if ($info['precio'] > 0) {
                                        $totalsPorProveedor[$provId] += ($info['precio'] * $data['cantidad']);
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $data['producto']->nombre }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $data['producto']->codigo }}</div>
                                </td>
                                <td class="price-cell font-medium">{{ number_format($data['cantidad'], 0) }}</td>
                                @foreach($proveedoresEnCatalogo as $prov)
                                    <td class="price-cell">
                                        @if(isset($data['precios'][$prov->id]))
                                            @php
                                                $precio = $data['precios'][$prov->id]['precio'];
                                                $esMejor = $mejorPrecio && $precio == $mejorPrecio;
                                            @endphp
                                            <div class="{{ $esMejor ? 'price-cell-best rounded px-2 py-1 inline-block' : '' }}">
                                                <span class="price-cell-value {{ $esMejor ? 'text-emerald-700!' : '' }}">
                                                    {{ number_format($precio, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            @if($data['precios'][$prov->id]['tiempo_entrega'])
                                                <div class="text-xs text-slate-400 mt-0.5">{{ $data['precios'][$prov->id]['tiempo_entrega'] }} días</div>
                                            @endif
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="price-cell">
                                    @if($mejorPrecio)
                                        <span class="font-bold text-emerald-600">{{ number_format($mejorPrecio, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="price-cell">
                                    @if($precioPromedio)
                                        <span class="font-medium text-sky-600">{{ number_format($precioPromedio, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $totalesPositivos = array_filter($totalsPorProveedor, fn($t) => $t > 0);
                            $mejorTotalGeneral = count($totalesPositivos) > 0 ? min($totalesPositivos) : 0;
                            $peorTotalGeneral = count($totalesPositivos) > 0 ? max($totalesPositivos) : 0;
                            $ahorroTotalGeneral = $peorTotalGeneral - $mejorTotalGeneral;
                        @endphp
                        <tr>
                            <td class="font-bold text-slate-800" colspan="2">TOTAL ESTIMADO</td>
                            @foreach($proveedoresEnCatalogo as $prov)
                                @php
                                    $totalProv = $totalsPorProveedor[$prov->id] ?? 0;
                                    $esMejorTotal = $totalProv > 0 && $totalProv == $mejorTotalGeneral;
                                @endphp
                                <td class="price-cell">
                                    <div class="font-bold text-lg {{ $esMejorTotal ? 'text-emerald-600' : 'text-slate-800' }}">
                                        @if($esMejorTotal)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ number_format($totalProv, 0, ',', '.') }}
                                            </span>
                                        @else
                                            {{ number_format($totalProv, 0, ',', '.') }}
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            <td class="price-cell font-bold text-lg text-emerald-600">
                                {{ number_format($mejorTotalGeneral, 0, ',', '.') }} Gs.
                            </td>
                            <td class="price-cell">
                                @if($ahorroTotalGeneral > 0)
                                    <div class="text-xs text-slate-500">Ahorro posible:</div>
                                    <div class="font-bold text-emerald-600">{{ number_format($ahorroTotalGeneral, 0, ',', '.') }} Gs.</div>
                                @endif
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="font-medium text-slate-600" colspan="2">Crear Orden de Compra</td>
                            @foreach($proveedoresEnCatalogo as $prov)
                                @php
                                    $totalProv = $totalsPorProveedor[$prov->id] ?? 0;
                                    $esMejorTotal = $totalProv > 0 && $totalProv == $mejorTotalGeneral;
                                @endphp
                                <td class="price-cell">
                                    @if($totalProv > 0)
                                        <a href="{{ route('ordenes-compra.create', ['pedido_cliente_id' => $pedido->id, 'proveedor_id' => $prov->id]) }}"
                                           class="{{ $esMejorTotal ? 'btn-success btn-sm' : 'btn-secondary btn-sm' }}">
                                            {{ $esMejorTotal ? 'Comprar' : 'Seleccionar' }}
                                        </a>
                                    @endif
                                </td>
                            @endforeach
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="p-4 bg-sky-50 border-t border-sky-100">
                <p class="text-sm text-sky-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span><strong>Precios del catálogo de proveedores.</strong> Puede crear una orden de compra directamente sin esperar cotizaciones.</span>
                </p>
            </div>
        </div>
    @elseif(count($comparacionCatalogo ?? []) == 0 && in_array($pedido->estado, ['EN_PROCESO', 'PRESUPUESTADO', 'RECIBIDO']))
        {{-- No hay precios en catálogo, mostrar mensaje --}}
        <div class="action-panel action-panel-warning mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Sin precios en catálogo de proveedores</p>
                    <p class="text-sm text-slate-600 mt-0.5">Los proveedores aún no han agregado estos productos a su catálogo. Puede solicitar cotizaciones manualmente.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Cotizaciones pendientes de respuesta (sistema antiguo) --}}
    @php
        $cotizacionesPendientes = $pedido->solicitudesPresupuesto->whereIn('estado', ['ENVIADA', 'VISTA']);
    @endphp
    @if($cotizacionesPendientes->count() > 0)
        <div class="card mb-6">
            <div class="card-body">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-700">{{ $cotizacionesPendientes->count() }} cotización(es) manual(es) pendiente(s)</p>
                        <p class="text-sm text-slate-500 mt-0.5">Enviadas a proveedores para respuesta personalizada.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Dashboard Comparativo de Cotizaciones --}}
    @if($cotizacionesCotizadas->count() > 1)
        <div class="card mb-6 overflow-hidden">
            <div class="card-header bg-linear-to-r from-slate-50 to-indigo-50 border-b border-indigo-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Comparación de Cotizaciones</h2>
                        <p class="text-sm text-slate-500">{{ $cotizacionesCotizadas->count() }} proveedores cotizaron</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th class="text-left">Producto</th>
                            <th>Cant.</th>
                            @foreach($cotizacionesCotizadas as $cot)
                                <th>
                                    {{ $cot->proveedor->razon_social ?? 'Proveedor' }}
                                    <div class="text-[10px] font-normal text-slate-400 mt-0.5">{{ $cot->dias_entrega_estimados ?? '-' }} días entrega</div>
                                </th>
                            @endforeach
                            <th class="text-emerald-300!">Mejor</th>
                            <th class="text-emerald-300!">Ahorro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $productosMap = [];
                            $totalsPorProveedor = [];
                            foreach ($cotizacionesCotizadas as $cot) {
                                $totalsPorProveedor[$cot->id] = 0;
                            }
                            foreach ($cotizacionesCotizadas as $cot) {
                                foreach ($cot->items as $item) {
                                    $pid = $item->producto_id;
                                    if (!isset($productosMap[$pid])) {
                                        $productosMap[$pid] = [
                                            'nombre' => $item->producto->nombre,
                                            'codigo' => $item->producto->codigo,
                                            'cantidad' => $item->cantidad_solicitada,
                                            'cotizaciones' => [],
                                        ];
                                    }
                                    $productosMap[$pid]['cotizaciones'][$cot->id] = $item;
                                    if ($item->tiene_stock && $item->precio_unitario_cotizado > 0) {
                                        $totalsPorProveedor[$cot->id] += ($item->precio_unitario_cotizado * $item->cantidad_solicitada);
                                    }
                                }
                            }
                        @endphp

                        @foreach($productosMap as $pid => $data)
                            @php
                                $preciosValidos = collect($data['cotizaciones'])
                                    ->filter(fn($i) => $i->tiene_stock && $i->precio_unitario_cotizado > 0)
                                    ->pluck('precio_unitario_cotizado');
                                $mejorPrecio = $preciosValidos->count() > 0 ? $preciosValidos->min() : null;
                                $peorPrecio = $preciosValidos->count() > 0 ? $preciosValidos->max() : null;
                                $ahorroPorUnidad = ($mejorPrecio && $peorPrecio) ? ($peorPrecio - $mejorPrecio) : 0;
                                $ahorroTotal = $ahorroPorUnidad * $data['cantidad'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $data['nombre'] }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $data['codigo'] }}</div>
                                </td>
                                <td class="price-cell font-medium">{{ number_format($data['cantidad'], 0) }}</td>
                                @foreach($cotizacionesCotizadas as $cot)
                                    <td class="price-cell">
                                        @if(isset($data['cotizaciones'][$cot->id]))
                                            @php $item = $data['cotizaciones'][$cot->id]; @endphp
                                            @if($item->tiene_stock)
                                                @php $esMejor = $mejorPrecio && $item->precio_unitario_cotizado == $mejorPrecio; @endphp
                                                <div class="{{ $esMejor ? 'price-cell-best rounded px-2 py-1 inline-block' : '' }}">
                                                    <span class="price-cell-value {{ $esMejor ? 'text-emerald-700!' : '' }}">
                                                        {{ number_format($item->precio_unitario_cotizado, 0, ',', '.') }} Gs.
                                                    </span>
                                                </div>
                                                <div class="text-xs text-slate-400 mt-0.5">Disp: {{ number_format($item->cantidad_disponible, 0) }}</div>
                                            @else
                                                <span class="badge badge-danger">Sin stock</span>
                                            @endif
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="price-cell">
                                    @if($mejorPrecio)
                                        <span class="font-bold text-emerald-600">{{ number_format($mejorPrecio, 0, ',', '.') }} Gs.</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="price-cell">
                                    @if($ahorroTotal > 0)
                                        <span class="font-semibold text-emerald-600">{{ number_format($ahorroTotal, 0, ',', '.') }} Gs.</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $totalesPositivos = array_filter($totalsPorProveedor, fn($t) => $t > 0);
                            $mejorTotalGeneral = count($totalesPositivos) > 0 ? min($totalesPositivos) : 0;
                            $peorTotalGeneral = count($totalesPositivos) > 0 ? max($totalesPositivos) : 0;
                            $ahorroTotalGeneral = $peorTotalGeneral - $mejorTotalGeneral;
                        @endphp
                        <tr>
                            <td class="font-bold text-slate-800" colspan="2">TOTAL</td>
                            @foreach($cotizacionesCotizadas as $cot)
                                @php
                                    $totalProv = $totalsPorProveedor[$cot->id] ?? 0;
                                    $esMejorTotal = $totalProv > 0 && $totalProv == $mejorTotalGeneral;
                                @endphp
                                <td class="price-cell">
                                    <div class="font-bold text-lg {{ $esMejorTotal ? 'text-emerald-600' : 'text-slate-800' }}">
                                        @if($esMejorTotal)
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ number_format($totalProv, 0, ',', '.') }} Gs.
                                            </span>
                                        @else
                                            {{ number_format($totalProv, 0, ',', '.') }} Gs.
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            <td class="price-cell font-bold text-lg text-emerald-600">
                                {{ number_format($mejorTotalGeneral, 0, ',', '.') }} Gs.
                            </td>
                            <td class="price-cell">
                                @if($ahorroTotalGeneral > 0)
                                    <div class="font-bold text-lg text-emerald-600">{{ number_format($ahorroTotalGeneral, 0, ',', '.') }} Gs.</div>
                                    <div class="text-xs text-slate-500">{{ round(($ahorroTotalGeneral / $peorTotalGeneral) * 100, 1) }}% ahorro</div>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="bg-white">
                            <td class="font-medium text-slate-600" colspan="2">Acción</td>
                            @foreach($cotizacionesCotizadas as $cot)
                                @php
                                    $totalProv = $totalsPorProveedor[$cot->id] ?? 0;
                                    $esMejorTotal = $totalProv > 0 && $totalProv == $mejorTotalGeneral;
                                @endphp
                                <td class="price-cell">
                                    <form action="{{ route('solicitudes-presupuesto.aceptar', $cot) }}" method="POST"
                                          x-data @submit.prevent="confirmSubmit($event, {title: 'Aceptar cotización de {{ $cot->proveedor->razon_social }}', text: 'Se creará una orden de compra automáticamente.', confirmText: 'Aceptar'})">
                                        @csrf
                                        <button type="submit" class="{{ $esMejorTotal ? 'btn-success btn-sm' : 'btn-secondary btn-sm' }}">
                                            {{ $esMejorTotal ? 'Aceptar' : 'Seleccionar' }}
                                        </button>
                                    </form>
                                </td>
                            @endforeach
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @elseif($cotizacionesCotizadas->count() == 1)
        {{-- Solo una cotización - mostrar simple --}}
        @php $unicaCot = $cotizacionesCotizadas->first(); @endphp
        <div class="card mb-6">
            <div class="card-body">
                <h2 class="text-lg font-bold text-slate-900 mb-2">Cotización Recibida</h2>
                <p class="text-sm text-slate-600 mb-4">
                    {{ $unicaCot->proveedor->razon_social }} — Total:
                    <strong class="text-slate-900">{{ number_format($unicaCot->total_cotizado, 0, ',', '.') }} Gs.</strong>
                    <span class="text-slate-400">({{ $unicaCot->dias_entrega_estimados ?? '-' }} días entrega)</span>
                </p>
                <form action="{{ route('solicitudes-presupuesto.aceptar', $unicaCot) }}" method="POST"
                      x-data @submit.prevent="confirmSubmit($event, {title: 'Aceptar cotización', text: 'Se creará una orden de compra automáticamente.', confirmText: 'Aceptar'})">
                    @csrf
                    <button type="submit" class="btn-success">Aceptar Cotización</button>
                </form>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos del Cliente -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-bold text-slate-900">Datos del Cliente</h2>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">Nombre</label>
                            <p class="font-semibold text-slate-900 mt-1">{{ $pedido->cliente_nombre }}</p>
                        </div>
                        @if($pedido->cliente_ruc)
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">RUC</label>
                                <p class="font-semibold text-slate-900 mt-1">{{ $pedido->cliente_ruc }}</p>
                            </div>
                        @endif
                        @if($pedido->cliente_telefono)
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">Teléfono</label>
                                <p class="font-semibold text-slate-900 mt-1">{{ $pedido->cliente_telefono }}</p>
                            </div>
                        @endif
                        @if($pedido->cliente_email)
                            <div>
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">Email</label>
                                <p class="font-semibold text-slate-900 mt-1">{{ $pedido->cliente_email }}</p>
                            </div>
                        @endif
                        @if($pedido->cliente_direccion)
                            <div class="col-span-2">
                                <label class="text-xs font-medium text-slate-500 uppercase tracking-wider">Dirección</label>
                                <p class="font-semibold text-slate-900 mt-1">{{ $pedido->cliente_direccion }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="card">
                <div class="card-header">
                    <h2 class="text-lg font-bold text-slate-900">Productos Solicitados</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-container border-0 shadow-none rounded-none">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-right">Cantidad</th>
                                    <th class="text-right">Precio Unit.</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedido->items as $item)
                                    <tr>
                                        <td>
                                            <div class="font-medium text-slate-900">{{ $item->producto->nombre }}</div>
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $item->producto->codigo }}</div>
                                        </td>
                                        <td class="text-right tabular-nums">{{ $item->cantidad }}</td>
                                        <td class="text-right tabular-nums">{{ number_format($item->precio_unitario, 0, ',', '.') }} Gs.</td>
                                        <td class="text-right tabular-nums font-semibold">{{ number_format($item->subtotal, 0, ',', '.') }} Gs.</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Solicitudes de Cotización -->
            @if($pedido->solicitudesPresupuesto->count() > 0)
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h2 class="text-lg font-bold text-slate-900">Cotizaciones a Proveedores</h2>
                        @if(in_array($pedido->estado, ['EN_PROCESO', 'PRESUPUESTADO']))
                            <a href="{{ route('solicitudes-presupuesto.create', ['pedido_cliente_id' => $pedido->id]) }}" class="btn-secondary btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Nueva Cotización
                            </a>
                        @endif
                    </div>
                    <div class="card-body space-y-3">
                        @foreach($pedido->solicitudesPresupuesto as $sol)
                            <div class="p-4 rounded-xl border {{ $sol->estado === 'COTIZADA' ? 'border-indigo-200 bg-indigo-50/50' : 'border-slate-200' }} transition-all hover:border-slate-300">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('solicitudes-presupuesto.show', $sol) }}"
                                           class="font-semibold text-sky-600 hover:text-sky-700">
                                            {{ $sol->numero }}
                                        </a>
                                        <span class="text-sm text-slate-500">
                                            {{ $sol->proveedor->razon_social ?? '-' }}
                                        </span>
                                        @if($sol->total_cotizado)
                                            <span class="text-sm font-semibold text-slate-900">
                                                {{ number_format($sol->total_cotizado, 0, ',', '.') }} Gs.
                                            </span>
                                        @endif
                                        @if($sol->dias_entrega_estimados)
                                            <span class="text-xs text-slate-400">
                                                ({{ $sol->dias_entrega_estimados }} días)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="badge {{ $sol->estado_color }}">
                                            {{ str_replace('_', ' ', $sol->estado) }}
                                        </span>
                                        @if($sol->puedeSerAceptada())
                                            <form action="{{ route('solicitudes-presupuesto.aceptar', $sol) }}" method="POST"
                                                  x-data @submit.prevent="confirmSubmit($event, {title: 'Aceptar cotización', text: 'Se creará una orden de compra automáticamente.', confirmText: 'Aceptar'})">
                                                @csrf
                                                <button type="submit" class="btn-success btn-xs">
                                                    Aceptar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Órdenes de Compra Relacionadas -->
            @if($pedido->ordenesCompra->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-bold text-slate-900">Órdenes de Compra</h2>
                    </div>
                    <div class="card-body space-y-3">
                        @foreach($pedido->ordenesCompra as $oc)
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-all">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('ordenes-compra.show', $oc) }}" class="font-semibold text-sky-600 hover:text-sky-700">
                                            {{ $oc->numero }}
                                        </a>
                                        <span class="text-sm text-slate-500">{{ $oc->proveedor_nombre }}</span>
                                        <span class="text-sm font-semibold text-slate-900">{{ number_format($oc->total, 0, ',', '.') }} Gs.</span>
                                    </div>
                                    <span class="badge {{ $oc->estado_color }}">{{ $oc->estado }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Orden de Envío -->
            @if($pedido->ordenEnvio)
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-lg font-bold text-slate-900">Orden de Envío</h2>
                    </div>
                    <div class="card-body">
                        <div class="p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-all">
                            <div class="flex justify-between items-center">
                                <a href="{{ route('ordenes-envio.show', $pedido->ordenEnvio) }}" class="font-semibold text-sky-600 hover:text-sky-700">
                                    {{ $pedido->ordenEnvio->numero }}
                                </a>
                                <span class="badge {{ $pedido->ordenEnvio->estado_color }}">
                                    {{ $pedido->ordenEnvio->estado }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="card sticky top-20">
                <div class="card-header">
                    <h2 class="text-lg font-bold text-slate-900">Resumen</h2>
                </div>
                <div class="card-body">
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-500">Estado</span>
                            <span class="badge {{ $pedido->estado_color }}">
                                {{ str_replace('_', ' ', $pedido->estado) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-500">Fecha Solicitud</span>
                            <span class="font-semibold text-slate-900">{{ $pedido->fecha_pedido->format('d/m/Y') }}</span>
                        </div>
                        @if($pedido->fecha_entrega_solicitada)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Entrega Solicitada</span>
                                <span class="font-semibold text-slate-900">{{ $pedido->fecha_entrega_solicitada->format('d/m/Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-500">Subtotal</span>
                            <span class="tabular-nums">{{ number_format($pedido->subtotal, 0, ',', '.') }} Gs.</span>
                        </div>
                        @if($pedido->descuento > 0)
                            @php $descMontoShow = (int) round($pedido->subtotal * $pedido->descuento / 100); @endphp
                            <div class="flex justify-between items-center text-rose-600">
                                <span class="text-sm">Descuento ({{ $pedido->descuento }}%)</span>
                                <span class="tabular-nums">-{{ number_format($descMontoShow, 0, ',', '.') }} Gs.</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center text-lg font-bold border-t border-slate-100 pt-3">
                            <span>Total</span>
                            <span class="text-sky-600 tabular-nums">{{ number_format($pedido->total, 0, ',', '.') }} Gs.</span>
                        </div>
                    </div>

                    @if($pedido->puedeSerCancelado())
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <form action="{{ route('pedidos-cliente.cancelar', $pedido) }}" method="POST"
                                  x-data @submit.prevent="confirmSubmit($event, {title: 'Cancelar solicitud', text: 'Esta acción no se puede deshacer.', confirmText: 'Sí, cancelar', icon: 'warning'})">
                                @csrf
                                <input type="text" name="motivo_cancelacion" placeholder="Motivo de cancelación..."
                                       class="form-input mb-3" required>
                                <button type="submit" class="btn-danger w-full">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Cancelar Solicitud
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
