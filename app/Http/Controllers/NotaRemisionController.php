<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotaRemision;
use App\Models\Presupuesto;
use Illuminate\Support\Facades\DB;

class NotaRemisionController extends Controller
{
    public function index()
    {
        $notas = NotaRemision::with('presupuesto')->orderBy('fecha', 'desc')->paginate(15);
        return view('notas_remision.index', compact('notas'));
    }

    public function create(Request $request)
    {
        $presupuestoId = $request->get('presupuesto_id');

        if ($presupuestoId) {
            $presupuesto = Presupuesto::with('items.producto')->findOrFail($presupuestoId);

            if (!$presupuesto->puedeConvertirANotaRemision()) {
                return redirect()->route('presupuestos.show', $presupuesto)
                    ->with('error', 'El presupuesto debe estar en estado APROBADO para convertirlo');
            }

            return view('notas_remision.create', compact('presupuesto'));
        }

        return redirect()->route('presupuestos.index')
            ->with('error', 'Debe seleccionar un presupuesto aprobado');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'presupuesto_id' => 'required|exists:presupuestos,id',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $presupuesto = Presupuesto::with('items')->findOrFail($validated['presupuesto_id']);

            if (!$presupuesto->puedeConvertirANotaRemision()) {
                throw new \Exception('El presupuesto no puede ser convertido');
            }

            // Crear nota de remisión
            $nota = NotaRemision::create([
                'presupuesto_id' => $presupuesto->id,
                'tipo' => $presupuesto->tipo === 'COMPRA' ? 'ENTRADA' : 'SALIDA',
                'contacto_nombre' => $presupuesto->contacto_nombre,
                'contacto_empresa' => $presupuesto->contacto_empresa,
                'fecha' => $validated['fecha'],
                'estado' => 'PENDIENTE',
                'observaciones' => $validated['observaciones'],
            ]);

            // Copiar items del presupuesto
            foreach ($presupuesto->items as $item) {
                $nota->items()->create([
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                ]);
            }

            // Vincular nota al presupuesto
            $presupuesto->update(['nota_remision_id' => $nota->id]);

            DB::commit();

            return redirect()->route('notas-remision.show', $nota)
                ->with('success', 'Nota de remisión creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(NotaRemision $notaRemision)
    {
        $notaRemision->load('presupuesto', 'items.producto', 'movimientos');
        return view('notas_remision.show', compact('notaRemision'));
    }

    public function aplicar(NotaRemision $notaRemision)
    {
        try {
            $notaRemision->aplicarAInventario();

            return redirect()->route('notas-remision.show', $notaRemision)
                ->with('success', 'Nota de remisión aplicada al inventario exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(NotaRemision $notaRemision)
    {
        if ($notaRemision->estado === 'APLICADA') {
            return back()->with('error', 'No se puede eliminar una nota de remisión ya aplicada');
        }

        DB::transaction(function () use ($notaRemision) {
            // Desvincular del presupuesto
            if ($notaRemision->presupuesto) {
                $notaRemision->presupuesto->update([
                    'nota_remision_id' => null,
                    'estado' => 'APROBADO'
                ]);
            }

            $notaRemision->delete();
        });

        return redirect()->route('notas-remision.index')
            ->with('success', 'Nota de remisión eliminada');
    }
}
