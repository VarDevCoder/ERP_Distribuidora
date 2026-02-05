<?php

namespace App\Http\Controllers;

use App\Models\PedidoCliente;
use App\Models\OrdenCompra;
use App\Models\OrdenEnvio;
use App\Models\SolicitudPresupuesto;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function pedidoCliente(PedidoCliente $pedido)
    {
        $pedido->load(['items.producto', 'usuario']);

        $pdf = Pdf::loadView('pdf.pedido-cliente', compact('pedido'))
            ->setPaper('a4');

        $filename = str_replace(['#', ' '], ['', '_'], $pedido->numero) . '.pdf';
        return $pdf->download($filename);
    }

    public function ordenCompra(OrdenCompra $orden)
    {
        $orden->load(['items.producto', 'usuario', 'pedidoCliente']);

        $pdf = Pdf::loadView('pdf.orden-compra', compact('orden'))
            ->setPaper('a4');

        return $pdf->download("OrdenCompra_{$orden->numero}.pdf");
    }

    public function ordenEnvio(OrdenEnvio $orden)
    {
        $orden->load(['items.producto', 'pedidoCliente', 'usuario']);

        $pdf = Pdf::loadView('pdf.orden-envio', compact('orden'))
            ->setPaper('a4');

        return $pdf->download("OrdenEnvio_{$orden->numero}.pdf");
    }

    public function solicitudPresupuesto(SolicitudPresupuesto $solicitud)
    {
        $solicitud->load(['items.producto', 'proveedor', 'usuario', 'pedidoCliente']);

        $pdf = Pdf::loadView('pdf.solicitud-presupuesto', compact('solicitud'))
            ->setPaper('a4');

        return $pdf->download("Solicitud_{$solicitud->numero}.pdf");
    }
}
