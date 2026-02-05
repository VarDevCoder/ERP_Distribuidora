@extends('pdf.layout')

@section('doc-number'){{ $orden->numero }}@endsection
@section('doc-date')Fecha: {{ $orden->fecha_orden->format('d/m/Y') }}@endsection
@section('doc-badge')<span class="badge badge-blue">ORDEN DE COMPRA</span>@endsection

@section('content')
<div class="section">
    <div class="section-title">Datos del Proveedor</div>
    <table class="info-grid">
        <tr>
            <td width="15%"><span class="info-label">Proveedor:</span></td>
            <td width="35%"><span class="info-value">{{ $orden->proveedor_nombre }}</span></td>
            <td width="15%"><span class="info-label">RUC:</span></td>
            <td width="35%"><span class="info-value">{{ $orden->proveedor_ruc ?? '-' }}</span></td>
        </tr>
        <tr>
            <td><span class="info-label">Teléfono:</span></td>
            <td><span class="info-value">{{ $orden->proveedor_telefono ?? '-' }}</span></td>
            <td><span class="info-label">Email:</span></td>
            <td><span class="info-value">{{ $orden->proveedor_email ?? '-' }}</span></td>
        </tr>
        <tr>
            <td><span class="info-label">Dirección:</span></td>
            <td colspan="3"><span class="info-value">{{ $orden->proveedor_direccion ?? '-' }}</span></td>
        </tr>
        @if($orden->fecha_entrega_esperada)
        <tr>
            <td><span class="info-label">Entrega:</span></td>
            <td colspan="3"><span class="info-value">{{ $orden->fecha_entrega_esperada->format('d/m/Y') }}</span></td>
        </tr>
        @endif
    </table>
</div>

@if($orden->pedidoCliente)
<div class="section">
    <div class="section-title">Referencia</div>
    <table class="info-grid">
        <tr>
            <td width="20%"><span class="info-label">Solicitud Cliente:</span></td>
            <td><span class="info-value">{{ $orden->pedidoCliente->numero }} - {{ $orden->pedidoCliente->cliente_nombre }}</span></td>
        </tr>
    </table>
</div>
@endif

<div class="section">
    <div class="section-title">Detalle de Productos</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="10%">Código</th>
                <th width="35%">Producto</th>
                <th class="right" width="13%">Solicitado</th>
                <th class="right" width="13%">Recibido</th>
                <th class="right" width="14%">Precio Unit.</th>
                <th class="right" width="15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->items as $item)
            <tr>
                <td>{{ $item->producto->codigo }}</td>
                <td>{{ $item->producto->nombre }}</td>
                <td class="right">{{ number_format($item->cantidad_solicitada, 3) }}</td>
                <td class="right">{{ number_format($item->cantidad_recibida, 3) }}</td>
                <td class="right">{{ number_format($item->precio_unitario, 0, ',', '.') }} Gs.</td>
                <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }} Gs.</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@php
    $descuentoMonto = $orden->descuento > 0 ? (int) round($orden->subtotal * $orden->descuento / 100) : 0;
@endphp

<div class="clearfix">
    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value">{{ number_format($orden->subtotal, 0, ',', '.') }} Gs.</td>
            </tr>
            @if($orden->descuento > 0)
            <tr>
                <td class="label">Descuento ({{ $orden->descuento }}%):</td>
                <td class="value" style="color: #dc2626;">-{{ number_format($descuentoMonto, 0, ',', '.') }} Gs.</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="label">Total:</td>
                <td class="value">{{ number_format($orden->total, 0, ',', '.') }} Gs.</td>
            </tr>
        </table>
    </div>
</div>

@if($orden->notas)
<div class="notes" style="margin-top: 80px;">
    <strong>Notas:</strong> {{ $orden->notas }}
</div>
@endif
@endsection
