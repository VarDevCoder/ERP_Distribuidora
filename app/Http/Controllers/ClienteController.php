<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Búsqueda
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('ruc', 'LIKE', "%{$busqueda}%")
                  ->orWhere('email', 'LIKE', "%{$busqueda}%")
                  ->orWhere('telefono', 'LIKE', "%{$busqueda}%");
            });
        }

        // Filtro por estado
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        // Filtro por ciudad
        if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->ciudad);
        }

        $clientes = $query->latest()->paginate(config('ankor.pagination.per_page', 15));

        // Obtener ciudades únicas para filtro
        $ciudades = Cliente::whereNotNull('ciudad')
                          ->distinct()
                          ->pluck('ciudad')
                          ->sort()
                          ->values();

        return view('clientes.index', compact('clientes', 'ciudades'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:255|unique:clientes,ruc',
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'ruc.unique' => 'Ya existe un cliente con este RUC.',
            'email.email' => 'El email no tiene un formato válido.',
            'email.unique' => 'Ya existe un cliente con este email.',
        ]);

        $validated['activo'] = $request->has('activo');

        try {
            $cliente = Cliente::create($validated);

            Log::info('Cliente creado', ['cliente_id' => $cliente->id, 'nombre' => $cliente->nombre]);

            return redirect()
                ->route('clientes.show', $cliente)
                ->with('success', 'Cliente creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear cliente', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al crear el cliente.']);
        }
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['pedidos' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:255|unique:clientes,ruc,' . $cliente->id,
            'telefono' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'ruc.unique' => 'Ya existe otro cliente con este RUC.',
            'email.email' => 'El email no tiene un formato válido.',
            'email.unique' => 'Ya existe otro cliente con este email.',
        ]);

        $validated['activo'] = $request->has('activo');

        try {
            $cliente->update($validated);

            Log::info('Cliente actualizado', ['cliente_id' => $cliente->id, 'nombre' => $cliente->nombre]);

            return redirect()
                ->route('clientes.show', $cliente)
                ->with('success', 'Cliente actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar cliente', ['error' => $e->getMessage(), 'cliente_id' => $cliente->id]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al actualizar el cliente.']);
        }
    }

    public function destroy(Cliente $cliente)
    {
        try {
            // Verificar si el cliente tiene pedidos
            if ($cliente->pedidos()->count() > 0) {
                return back()->withErrors([
                    'error' => 'No se puede eliminar el cliente porque tiene pedidos asociados. Desactívelo en su lugar.'
                ]);
            }

            $nombre = $cliente->nombre;
            $cliente->delete();

            Log::info('Cliente eliminado', ['cliente_nombre' => $nombre]);

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar cliente', ['error' => $e->getMessage(), 'cliente_id' => $cliente->id]);

            return back()->withErrors([
                'error' => 'Ocurrió un error al eliminar el cliente.'
            ]);
        }
    }

    public function toggleActivo(Cliente $cliente)
    {
        try {
            $cliente->update(['activo' => !$cliente->activo]);

            $estado = $cliente->activo ? 'activado' : 'desactivado';
            Log::info('Cliente ' . $estado, ['cliente_id' => $cliente->id]);

            return back()->with('success', "Cliente {$estado} exitosamente.");
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del cliente', ['error' => $e->getMessage(), 'cliente_id' => $cliente->id]);

            return back()->withErrors([
                'error' => 'Ocurrió un error al cambiar el estado del cliente.'
            ]);
        }
    }
}
