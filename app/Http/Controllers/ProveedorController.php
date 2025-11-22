<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Gestión de proveedores por parte de ANKOR
 */
class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::with('user')
            ->orderBy('razon_social');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('razon_social', 'like', "%{$buscar}%")
                  ->orWhere('ruc', 'like', "%{$buscar}%");
            });
        }

        $proveedores = $query->paginate(config('ankor.pagination.per_page', 15));

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => 'required|string|max:50|unique:proveedores,ruc',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'rubros' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Crear usuario con rol proveedor
            $user = User::create([
                'name' => $request->razon_social,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => User::ROL_PROVEEDOR,
                'activo' => true,
            ]);

            // Crear perfil de proveedor
            $proveedor = Proveedor::create([
                'user_id' => $user->id,
                'razon_social' => $request->razon_social,
                'ruc' => $request->ruc,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'ciudad' => $request->ciudad,
                'rubros' => $request->rubros,
            ]);

            DB::commit();
            return redirect()
                ->route('proveedores.show', $proveedor)
                ->with('success', 'Proveedor creado exitosamente. Credenciales: ' . $request->email);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear proveedor: ' . $e->getMessage());
        }
    }

    public function show(Proveedor $proveedore)
    {
        $proveedor = $proveedore->load(['user', 'solicitudesPresupuesto' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedore)
    {
        return view('proveedores.edit', ['proveedor' => $proveedore]);
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $proveedor = $proveedore;

        $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => 'required|string|max:50|unique:proveedores,ruc,' . $proveedor->id,
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string|max:500',
            'ciudad' => 'nullable|string|max:100',
            'rubros' => 'nullable|string|max:500',
        ]);

        $proveedor->update($request->only([
            'razon_social', 'ruc', 'telefono', 'direccion', 'ciudad', 'rubros'
        ]));

        // Actualizar nombre del usuario
        $proveedor->user->update(['name' => $request->razon_social]);

        return redirect()
            ->route('proveedores.show', $proveedor)
            ->with('success', 'Proveedor actualizado');
    }

    public function destroy(Proveedor $proveedore)
    {
        $proveedor = $proveedore;
        $nombre = $proveedor->razon_social;

        // El usuario se elimina en cascada
        $proveedor->user->delete();

        return redirect()
            ->route('proveedores.index')
            ->with('success', "Proveedor {$nombre} eliminado");
    }

    /**
     * Activar/desactivar proveedor
     */
    public function toggleActivo(Proveedor $proveedor)
    {
        $proveedor->user->update(['activo' => !$proveedor->user->activo]);

        $estado = $proveedor->user->activo ? 'activado' : 'desactivado';
        return back()->with('success', "Proveedor {$estado}");
    }
}
