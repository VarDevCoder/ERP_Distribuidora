@extends('pdf.layout')

@section('doc-number'){{ $solicitud->numero }}@endsection
@section('doc-date')Fecha: {{ $solicitud->fecha_solicitud->format('d/m/Y') }}@endsection
@section('doc-badge')<span class="badge badge-yellow">SOLICITUD DE PRESUPUESTO</span>@endsection

@section('content')
<div class="section">
    <div class="section-title">Proveedor</div>
    <table class="info-grid">
        <tr>
            <td width="15%"><span class="info-label">Proveedor:</span></td>
            <td width="35%"><span class="info-value">{{ $solicitud->proveedor->razon_social }}</span></td>
            <td width="15%"><span class="info-label">RUC:</span></td>
            <td width="35%"><span class="info-value">{{ $solicitud->proveedor->ruc }}</span></td>
        </tr>
        @if($solicitud->fecha_limite_respuesta)
        <tr>
            <td><span class="info-label">Resp. hasta:</span></td>
            <td colspan="3"><span class="info-value">{{ $solicitud->fecha_limite_respuesta->format('d/m/Y') }}</span></td>
        </tr>
        @endif
    </table>
</div>

@if($solicitud->pedidoCliente)
<div class="section">
    <div class="section-title">Referencia</div>
    <table class="info-grid">
        <tr>
            <td width="20%"><span class="info-label">Solicitud Cliente:</span></td>
            <td><span class="info-value">{{ $solicitud->pedidoCliente->numero }} - {{ $solicitud->pedidoCliente->cliente_nombre }}</span></td>
        </tr>
    </table>
</div>
@endif

<div class="section">
    <div class="section-title">Productos Solicitados</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="15%">Código</th>
                <th width="55%">Producto</th>
                <th class="right" width="30%">Cantidad Solicitada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($solicitud->items as $item)
            <tr>
                <td>{{ $item->producto->codigo }}</td>
                <td>{{ $item->producto->nombre }}</td>
                <td class="right">{{ number_format($item->cantidad_solicitada, 3) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($solicitud->mensaje_solicitud)
<div class="notes">
    <strong>Mensaje:</strong> {{ $solicitud->mensaje_solicitud }}
</div>
@endif
@endsection
