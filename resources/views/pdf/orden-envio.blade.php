@extends('pdf.layout')

@section('doc-number'){{ $orden->numero }}@endsection
@section('doc-date')Fecha: {{ $orden->fecha_generacion->format('d/m/Y') }}@endsection
@section('doc-badge')<span class="badge badge-green">NOTA DE REMISIÓN</span>@endsection

@section('content')
<div class="section">
    <div class="section-title">Datos de Entrega</div>
    <table class="info-grid">
        <tr>
            <td width="15%"><span class="info-label">Dirección:</span></td>
            <td colspan="3"><span class="info-value">{{ $orden->direccion_entrega }}</span></td>
        </tr>
        <tr>
            <td><span class="info-label">Contacto:</span></td>
            <td width="35%"><span class="info-value">{{ $orden->contacto_entrega ?? '-' }}</span></td>
            <td width="15%"><span class="info-label">Teléfono:</span></td>
            <td width="35%"><span class="info-value">{{ $orden->telefono_entrega ?? '-' }}</span></td>
        </tr>
        <tr>
            <td><span class="info-label">Método:</span></td>
            <td><span class="info-value">{{ $orden->metodo_envio ?? '-' }}</span></td>
            <td><span class="info-label">Transportista:</span></td>
            <td><span class="info-value">{{ $orden->transportista ?? '-' }}</span></td>
        </tr>
        @if($orden->numero_guia)
        <tr>
            <td><span class="info-label">N° Guía:</span></td>
            <td colspan="3"><span class="info-value">{{ $orden->numero_guia }}</span></td>
        </tr>
        @endif
    </table>
</div>

@if($orden->pedidoCliente)
<div class="section">
    <div class="section-title">Cliente</div>
    <table class="info-grid">
        <tr>
            <td width="15%"><span class="info-label">Solicitud:</span></td>
            <td width="35%"><span class="info-value">{{ $orden->pedidoCliente->numero }}</span></td>
            <td width="15%"><span class="info-label">Cliente:</span></td>
            <td width="35%"><span class="info-value">{{ $orden->pedidoCliente->cliente_nombre }}</span></td>
        </tr>
        @if($orden->pedidoCliente->cliente_ruc)
        <tr>
            <td><span class="info-label">RUC:</span></td>
            <td colspan="3"><span class="info-value">{{ $orden->pedidoCliente->cliente_ruc }}</span></td>
        </tr>
        @endif
    </table>
</div>
@endif

<div class="section">
    <div class="section-title">Productos Enviados</div>
    <table class="items-table">
        <thead>
            <tr>
                <th width="15%">Código</th>
                <th width="55%">Producto</th>
                <th class="right" width="30%">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orden->items as $item)
            <tr>
                <td>{{ $item->producto->codigo }}</td>
                <td>{{ $item->producto->nombre }}</td>
                <td class="right">{{ number_format($item->cantidad, 3) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($orden->notas)
<div class="notes">
    <strong>Notas:</strong> {{ $orden->notas }}
</div>
@endif

<div style="margin-top: 60px;">
    <table width="100%">
        <tr>
            <td width="45%" style="text-align: center; border-top: 1px solid #9ca3af; padding-top: 8px;">
                <span class="info-label">Entregado por</span>
            </td>
            <td width="10%"></td>
            <td width="45%" style="text-align: center; border-top: 1px solid #9ca3af; padding-top: 8px;">
                <span class="info-label">Recibido por</span>
            </td>
        </tr>
    </table>
</div>
@endsection
