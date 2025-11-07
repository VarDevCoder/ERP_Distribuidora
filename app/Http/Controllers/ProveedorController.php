<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $query = Proveedor::query();

        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('prov_nombre', 'ILIKE', "%$buscar%")
                  ->orWhere('prov_ruc', 'ILIKE', "%$buscar%")
                  ->orWhere('prov_ciudad', 'ILIKE', "%$buscar%");
            });
        }

        $proveedores = $query->orderBy('prov_nombre')->paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        if (!session('usuario')) return redirect()->route('login');
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        if (!session('usuario')) return redirect()->route('login');

        $request->validate([
            'prov_nombre' => 'required|max:150',
            'prov_ruc' => 'required|unique:proveedor,prov_ruc|max:20',
            'prov_telefono' => 'nullable|max:20',
            'prov_email' => 'nullable|email|max:100',
            'prov_direccion' => 'nullable|max:200',
            'prov_ciudad' => 'nullable|max:100',
            'prov_contacto' => 'nullable|max:100',
            'prov_estado' => 'required|in:ACTIVO,INACTIVO'
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    public function edit($id)
    {
        if (!session('usuario')) return redirect()->route('login');
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $proveedor = Proveedor::findOrFail($id);

        $request->validate([
            'prov_nombre' => 'required|max:150',
            'prov_ruc' => 'required|max:20|unique:proveedor,prov_ruc,' . $id . ',prov_id',
            'prov_telefono' => 'nullable|max:20',
            'prov_email' => 'nullable|email|max:100',
            'prov_direccion' => 'nullable|max:200',
            'prov_ciudad' => 'nullable|max:100',
            'prov_contacto' => 'nullable|max:100',
            'prov_estado' => 'required|in:ACTIVO,INACTIVO'
        ]);

        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy($id)
    {
        if (!session('usuario')) return redirect()->route('login');

        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente');
    }
}
