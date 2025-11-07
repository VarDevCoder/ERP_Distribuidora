<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $query = Cliente::query();

        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('cli_nombre', 'ILIKE', "%$buscar%")
                  ->orWhere('cli_apellido', 'ILIKE', "%$buscar%")
                  ->orWhere('cli_ci', 'ILIKE', "%$buscar%")
                  ->orWhere('cli_email', 'ILIKE', "%$buscar%");
            });
        }

        $clientes = $query->orderBy('cli_nombre')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        if (!session('usuario')) return redirect()->route('login');
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $request->validate([
            'cli_nombre' => 'required|max:100',
            'cli_apellido' => 'required|max:100',
            'cli_ci' => 'required|unique:cliente,cli_ci|max:20',
            'cli_telefono' => 'nullable|max:20',
            'cli_direccion' => 'nullable|max:150',
            'cli_email' => 'nullable|email|max:100',
            'cli_tipo' => 'required|in:MINORISTA,MAYORISTA'
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    public function edit($id)
    {
        if (!session('usuario')) return redirect()->route('login');
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $cliente = Cliente::findOrFail($id);

        $request->validate([
            'cli_nombre' => 'required|max:100',
            'cli_apellido' => 'required|max:100',
            'cli_ci' => 'required|max:20|unique:cliente,cli_ci,' . $id . ',cli_id',
            'cli_telefono' => 'nullable|max:20',
            'cli_direccion' => 'nullable|max:150',
            'cli_email' => 'nullable|email|max:100',
            'cli_tipo' => 'required|in:MINORISTA,MAYORISTA'
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }
}
