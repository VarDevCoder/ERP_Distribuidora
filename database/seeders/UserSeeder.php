<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Proveedor;

/**
 * Seeder para usuarios de prueba con los 3 roles del sistema
 *
 * Credenciales:
 * - Admin:      admin@ankor.com / admin123
 * - AnkorUser:  usuario@ankor.com / usuario123
 * - Proveedor:  proveedor@ferreteria.com / proveedor123
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ADMINISTRADOR - Acceso total
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@ankor.com',
            'password' => Hash::make('admin123'),
            'rol' => User::ROL_ADMIN,
            'activo' => true,
        ]);

        // 2. USUARIO ANKOR - Solo flujo ANKOR
        User::create([
            'name' => 'Juan Operador',
            'email' => 'usuario@ankor.com',
            'password' => Hash::make('usuario123'),
            'rol' => User::ROL_ANKOR_USER,
            'activo' => true,
        ]);

        // Crear usuario adicional ANKOR
        User::create([
            'name' => 'Maria Vendedora',
            'email' => 'maria@ankor.com',
            'password' => Hash::make('maria123'),
            'rol' => User::ROL_ANKOR_USER,
            'activo' => true,
        ]);

        // 3. PROVEEDORES - Solo portal proveedor
        $userProveedor1 = User::create([
            'name' => 'Ferreteria El Martillo',
            'email' => 'proveedor@ferreteria.com',
            'password' => Hash::make('proveedor123'),
            'rol' => User::ROL_PROVEEDOR,
            'activo' => true,
        ]);

        // Crear ficha de proveedor asociada
        Proveedor::create([
            'user_id' => $userProveedor1->id,
            'razon_social' => 'Ferreteria El Martillo S.A.',
            'ruc' => '80012345-6',
            'telefono' => '021-555-1234',
            'direccion' => 'Av. Mariscal Lopez 1500',
            'ciudad' => 'Asuncion',
            'rubros' => 'Ferreteria, Herramientas, Materiales de construccion',
            'notas' => 'Proveedor principal de herramientas',
        ]);

        // Proveedor 2
        $userProveedor2 = User::create([
            'name' => 'TechParts Paraguay',
            'email' => 'ventas@techparts.com',
            'password' => Hash::make('tech123'),
            'rol' => User::ROL_PROVEEDOR,
            'activo' => true,
        ]);

        Proveedor::create([
            'user_id' => $userProveedor2->id,
            'razon_social' => 'TechParts Paraguay S.R.L.',
            'ruc' => '80098765-4',
            'telefono' => '021-600-9999',
            'direccion' => 'Av. Espana 2000',
            'ciudad' => 'Asuncion',
            'rubros' => 'Electronica, Computacion, Accesorios',
            'notas' => 'Especialistas en repuestos electronicos',
        ]);

        // Proveedor 3
        $userProveedor3 = User::create([
            'name' => 'Distribuidora Industrial',
            'email' => 'contacto@distindustrial.com',
            'password' => Hash::make('dist123'),
            'rol' => User::ROL_PROVEEDOR,
            'activo' => true,
        ]);

        Proveedor::create([
            'user_id' => $userProveedor3->id,
            'razon_social' => 'Distribuidora Industrial del Este',
            'ruc' => '80055555-1',
            'telefono' => '061-500-5000',
            'direccion' => 'Ruta 7 Km 15',
            'ciudad' => 'Ciudad del Este',
            'rubros' => 'Insumos industriales, Maquinaria, Equipos',
            'notas' => 'Gran variedad de productos industriales',
        ]);
    }
}
