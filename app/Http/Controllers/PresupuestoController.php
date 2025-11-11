<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presupuesto;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class PresupuestoController extends Controller
{
    public function index(Request $request)
    {
        $query = Presupuesto::with('items.producto')->orderBy('fecha', 'desc');

        if ($request->has('tipo') && in_array($request->tipo, ['COMPRA', 'VENTA'])) {
            $query->where('tipo', $request->tipo);
        }

        $presupuestos = $query->paginate(15);
        return view('presupuestos.index', compact('presupuestos'));
    }

    public function create(Request $request)
    {
        $tipo = $request->get('tipo', 'VENTA');
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('presupuestos.create', compact('productos', 'tipo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:COMPRA,VENTA',
            'contacto_nombre' => 'required|string|max:255',
            'contacto_email' => 'nullable|email',
            'contacto_telefono' => 'nullable|string',
            'contacto_empresa' => 'nullable|string',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $presupuesto = Presupuesto::create([
                'tipo' => $validated['tipo'],
                'contacto_nombre' => $validated['contacto_nombre'],
                'contacto_email' => $validated['contacto_email'] ?? null,
                'contacto_telefono' => $validated['contacto_telefono'] ?? null,
                'contacto_empresa' => $validated['contacto_empresa'] ?? null,
                'fecha' => $validated['fecha'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'descuento' => $validated['descuento'] ?? 0,
                'notas' => $validated['notas'] ?? null,
                'estado' => 'BORRADOR',
            ]);

            foreach ($validated['items'] as $index => $itemData) {
                $producto = Producto::find($itemData['producto_id']);
                $presupuesto->items()->create([
                    'producto_id' => $producto->id,
                    'orden' => $index,
                    'descripcion' => $producto->nombre,
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $itemData['precio_unitario'],
                ]);
            }

            $presupuesto->calcularTotales();

            DB::commit();
            return redirect()->route('presupuestos.show', $presupuesto)
                ->with('success', 'Presupuesto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Presupuesto $presupuesto)
    {
        $presupuesto->load('items.producto', 'notaRemision');
        return view('presupuestos.show', compact('presupuesto'));
    }

    public function edit(Presupuesto $presupuesto)
    {
        if ($presupuesto->estado === 'CONVERTIDO') {
            return redirect()->route('presupuestos.show', $presupuesto)
                ->with('error', 'No se puede editar un presupuesto ya convertido');
        }

        $presupuesto->load('items.producto');
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();
        return view('presupuestos.edit', compact('presupuesto', 'productos'));
    }

    public function update(Request $request, Presupuesto $presupuesto)
    {
        if ($presupuesto->estado === 'CONVERTIDO') {
            return redirect()->route('presupuestos.show', $presupuesto)
                ->with('error', 'No se puede editar un presupuesto ya convertido');
        }

        $validated = $request->validate([
            'contacto_nombre' => 'required|string|max:255',
            'contacto_email' => 'nullable|email',
            'contacto_telefono' => 'nullable|string',
            'contacto_empresa' => 'nullable|string',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $presupuesto->update($validated);
            $presupuesto->items()->delete();

            foreach ($validated['items'] as $index => $itemData) {
                $producto = Producto::find($itemData['producto_id']);
                $presupuesto->items()->create([
                    'producto_id' => $producto->id,
                    'orden' => $index,
                    'descripcion' => $producto->nombre,
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $itemData['precio_unitario'],
                ]);
            }

            $presupuesto->calcularTotales();

            DB::commit();
            return redirect()->route('presupuestos.show', $presupuesto)
                ->with('success', 'Presupuesto actualizado');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(Presupuesto $presupuesto)
    {
        if ($presupuesto->estado === 'CONVERTIDO') {
            return back()->with('error', 'No se puede eliminar un presupuesto ya convertido');
        }

        $presupuesto->delete();
        return redirect()->route('presupuestos.index')
            ->with('success', 'Presupuesto eliminado');
    }

    public function aprobar(Presupuesto $presupuesto)
    {
        if (!in_array($presupuesto->estado, ['BORRADOR', 'ENVIADO'])) {
            return back()->with('error', 'El presupuesto no puede ser aprobado');
        }

        $presupuesto->update(['estado' => 'APROBADO']);
        return back()->with('success', 'Presupuesto aprobado exitosamente');
    }
}
