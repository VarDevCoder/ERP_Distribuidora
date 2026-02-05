@extends('pdf.layout')

@section('doc-number'){{ $pedido->numero }}@endsection
@section('doc-date')Fecha: {{ $pedido->fecha_pedido->format('d/m/Y') }}@endsection
@section('doc-badge')<span class="badge badge-blue">SOLICITUD DE CLIENTE</span>@endsection

@section('content')
<div class="section">
    <div class="section-title">Datos del Cliente</div>
    <table class="info-grid">
        <tr>
            <td width="15%"><span class="info-label">Cliente:</span></td>
            <td width="35%"><span class="info-value">{{ $pedido->cliente_nombre }}</span></td>
            <td width="15%"><span class="info-label">RUC:</span></td>
            <td width="35%"><span class="info-value">{{ $pedido->cliente_ruc ?? '-' }}</span></td>
        </tr>
        <tr>
            <td><span class="info-label">Teléfono:</span></td>
            <td><span class="info-value">{{ $pedido->cliente_telefono ?? '-' }}</span></td>
            <td><span class="info-label">Email:</span></td>
            <td><span class="info-value">{{ $pedido->cliente_email ?? '-' }}</span></td>
        </tr>
        <tr>
            <td><span class="info-label">Dirección:</span></td>
            <td colspan="3"><span class="info-value">{{ $pedido->cliente_direccion ?? '-' }}</span></td>
        </tr>
        @if($pedido->fecha_entrega_solicitada)
        <tr>
            <td><span class="info-label">Entrega:</span></td>
            <td colspan="3"><span class="info-value">{{ $pedido->fecha_entrega_solicitada->format('d/m/Y') }}</span></td>
        </tr>
        @endif
    </table>
</div>

<div class="section">
    <div class="section-title">Detalle de Productos</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="12%">Código</th>
                <th width="38%">Producto</th>
                <th class="right" width="15%">Cantidad</th>
                <th class="right" width="15%">Precio Unit.</th>
                <th class="right" width="20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->items as $item)
            <tr>
                <td>{{ $item->producto->codigo }}</td>
                <td>{{ $item->producto->nombre }}</td>
                <td class="right">{{ number_format($item->cantidad, 3) }}</td>
                <td class="right">{{ number_format($item->precio_unitario, 0, ',', '.') }} Gs.</td>
                <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }} Gs.</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@php
    $descuentoMonto = $pedido->descuento > 0 ? (int) round($pedido->subtotal * $pedido->descuento / 100) : 0;
@endphp

<div class="clearfix">
    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">{{ number_format($pedido->subtotal, 0, ',', '.') }} Gs.</td>
            </tr>
            @if($pedido->descuento > 0)
            <tr>
                <td class="label">Descuento ({{ $pedido->descuento }}%):</td>
                <td class="value" style="color: #dc2626;">-{{ number_format($descuentoMonto, 0, ',', '.') }} Gs.</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="label">Total:</td>
                <td class="value">{{ number_format($pedido->total, 0, ',', '.') }} Gs.</td>
            </tr>
        </table>
    </div>
</div>

@if($pedido->notas)
<div class="notes" style="margin-top: 80px;">
    <strong>Notas:</strong> {{ $pedido->notas }}
</div>
@endif

<div style="margin-top: 30px;">
    <table class="info-grid">
        <tr>
            <td><span class="info-label">Registrado por:</span></td>
            <td><span class="info-value">{{ $pedido->usuario->name ?? '-' }}</span></td>
            <td><span class="info-label">Estado:</span></td>
            <td><span class="info-value">{{ str_replace('_', ' ', $pedido->estado) }}</span></td>
        </tr>
    </table>
</div>
@endsection
