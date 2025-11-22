<?php

namespace App\Http\Controllers;

use App\Models\SolicitudPresupuesto;
use App\Models\SolicitudPresupuestoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Portal para usuarios con rol PROVEEDOR
 * Aquí los proveedores ven las solicitudes de presupuesto y responden
 */
class ProveedorPortalController extends Controller
{
    /**
     * Dashboard del proveedor
     */
    public function dashboard()
    {
        $proveedor = Auth::user()->proveedor;

        if (!$proveedor) {
            return view('proveedor.sin-perfil');
        }

        $solicitudesPendientes = $proveedor->solicitudesPresupuesto()
            ->whereIn('estado', SolicitudPresupuesto::getEstadosPendientes())
            ->count();

        $solicitudesRecientes = $proveedor->solicitudesPresupuesto()
            ->with('items.producto')
            ->orderBy('created_at', 'desc')
            ->take(config('ankor.limits.dashboard_recent', 5))
            ->get();

        return view('proveedor.dashboard', compact('proveedor', 'solicitudesPendientes', 'solicitudesRecientes'));
    }

    /**
     * Listar solicitudes de presupuesto
     */
    public function solicitudes(Request $request)
    {
        $proveedor = Auth::user()->proveedor;

        if (!$proveedor) {
            return redirect()->route('proveedor.dashboard');
        }

        $query = $proveedor->solicitudesPresupuesto()
            ->with(['items.producto', 'pedidoCliente'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->paginate(config('ankor.pagination.per_page', 15));

        return view('proveedor.solicitudes.index', compact('solicitudes'));
    }

    /**
     * Ver detalle de una solicitud
     */
    public function verSolicitud(SolicitudPresupuesto $solicitud)
    {
        $proveedor = Auth::user()->proveedor;

        // Verificar que la solicitud pertenece a este proveedor
        if ($solicitud->proveedor_id !== $proveedor->id) {
            abort(403);
        }

        // Marcar como VISTA si estaba en ENVIADA
        if ($solicitud->estado === SolicitudPresupuesto::ESTADO_ENVIADA) {
            $solicitud->update(['estado' => SolicitudPresupuesto::ESTADO_VISTA]);
        }

        $solicitud->load(['items.producto', 'pedidoCliente', 'usuario']);

        return view('proveedor.solicitudes.show', compact('solicitud'));
    }

    /**
     * Formulario para responder la solicitud
     */
    public function formResponder(SolicitudPresupuesto $solicitud)
    {
        $proveedor = Auth::user()->proveedor;

        if ($solicitud->proveedor_id !== $proveedor->id) {
            abort(403);
        }

        if (!$solicitud->puedeSerRespondida()) {
            return back()->with('error', 'Esta solicitud ya no puede ser respondida');
        }

        $solicitud->load('items.producto');

        return view('proveedor.solicitudes.responder', compact('solicitud'));
    }

    /**
     * Enviar cotización
     */
    public function enviarCotizacion(Request $request, SolicitudPresupuesto $solicitud)
    {
        $proveedor = Auth::user()->proveedor;

        if ($solicitud->proveedor_id !== $proveedor->id) {
            abort(403);
        }

        if (!$solicitud->puedeSerRespondida()) {
            return back()->with('error', 'Esta solicitud ya no puede ser respondida');
        }

        $request->validate([
            'respuesta_proveedor' => 'nullable|string|max:1000',
            'dias_entrega_estimados' => 'required|integer|min:1',
            'items' => 'required|array',
            'items.*.tiene_stock' => 'required|boolean',
            'items.*.cantidad_disponible' => 'nullable|numeric|min:0',
            'items.*.precio_unitario_cotizado' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $todosConStock = true;
            $totalCotizado = 0;

            foreach ($request->items as $itemId => $data) {
                $item = SolicitudPresupuestoItem::find($itemId);
                if (!$item || $item->solicitud_presupuesto_id !== $solicitud->id) continue;

                $tieneStock = (bool) $data['tiene_stock'];

                if (!$tieneStock) {
                    $todosConStock = false;
                    $item->update([
                        'tiene_stock' => false,
                        'cantidad_disponible' => 0,
                        'precio_unitario_cotizado' => null,
                        'subtotal_cotizado' => null,
                    ]);
                } else {
                    $cantidad = $data['cantidad_disponible'] ?? 0;
                    $precio = $data['precio_unitario_cotizado'] ?? 0;
                    $subtotal = (int) round($cantidad * $precio);
                    $totalCotizado += $subtotal;

                    $item->update([
                        'tiene_stock' => true,
                        'cantidad_disponible' => $cantidad,
                        'precio_unitario_cotizado' => $precio,
                        'subtotal_cotizado' => $subtotal,
                    ]);
                }
            }

            // Actualizar solicitud
            $solicitud->update([
                'estado' => $todosConStock ? SolicitudPresupuesto::ESTADO_COTIZADA : SolicitudPresupuesto::ESTADO_SIN_STOCK,
                'respuesta_proveedor' => $request->respuesta_proveedor,
                'dias_entrega_estimados' => $request->dias_entrega_estimados,
                'total_cotizado' => $totalCotizado,
                'fecha_respuesta' => now(),
            ]);

            DB::commit();

            $mensaje = $todosConStock
                ? 'Cotización enviada exitosamente'
                : 'Se notificó la falta de disponibilidad';

            return redirect()
                ->route('proveedor.solicitud.ver', $solicitud)
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al enviar cotización: ' . $e->getMessage());
        }
    }

    /**
     * Marcar sin stock (atajo rápido)
     */
    public function marcarSinStock(Request $request, SolicitudPresupuesto $solicitud)
    {
        $proveedor = Auth::user()->proveedor;

        if ($solicitud->proveedor_id !== $proveedor->id) {
            abort(403);
        }

        if (!$solicitud->puedeSerRespondida()) {
            return back()->with('error', 'Esta solicitud ya no puede ser respondida');
        }

        $request->validate([
            'respuesta_proveedor' => 'required|string|max:1000',
        ]);

        // Marcar todos los items sin stock
        foreach ($solicitud->items as $item) {
            $item->update([
                'tiene_stock' => false,
                'cantidad_disponible' => 0,
            ]);
        }

        $solicitud->update([
            'estado' => SolicitudPresupuesto::ESTADO_SIN_STOCK,
            'respuesta_proveedor' => $request->respuesta_proveedor,
            'fecha_respuesta' => now(),
        ]);

        return redirect()
            ->route('proveedor.solicitud.ver', $solicitud)
            ->with('success', 'Se notificó la falta de disponibilidad');
    }

    /**
     * Perfil del proveedor
     */
    public function perfil()
    {
        $proveedor = Auth::user()->proveedor;
        return view('proveedor.perfil', compact('proveedor'));
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil(Request $request)
    {
        $proveedor = Auth::user()->proveedor;

        $request->validate([
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'rubros' => 'nullable|string|max:500',
        ]);

        $proveedor->update($request->only(['telefono', 'direccion', 'ciudad', 'rubros']));

        return back()->with('success', 'Perfil actualizado');
    }
}
