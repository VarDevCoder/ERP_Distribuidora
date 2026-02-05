<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder - Punto de entrada para seeders
 *
 * Uso:
 *   php artisan migrate:fresh --seed    (reset completo + seeders)
 *   php artisan db:seed                 (solo seeders)
 *   php artisan db:seed --class=UserSeeder   (seeder especifico)
 *
 * Credenciales de prueba:
 * -------------------------
 * ADMIN:       admin@ankor.com / admin123
 * ANKOR USER:  usuario@ankor.com / usuario123
 * PROVEEDOR:   proveedor@ferreteria.com / proveedor123
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Iniciando seeders del sistema ANKOR ===');

        // 1. Usuarios con los 3 roles + Proveedores
        $this->call(UserSeeder::class);
        $this->command->info('Usuarios creados');

        // 2. Categorías de productos
        $this->call(CategoriaSeeder::class);
        $this->command->info('Categorías creadas');

        // 3. Productos de prueba (con categorías asignadas)
        $this->call(ProductoSeeder::class);
        $this->command->info('Productos creados');

        // 4. Flujo completo ANKOR (pedidos en todos los estados)
        $this->call(FlujoAnkorSeeder::class);
        $this->command->info('Flujo ANKOR completo creado');

        $this->command->info('=== Seeders completados ===');
        $this->command->newLine();
        $this->command->info('Credenciales de prueba:');
        $this->command->table(
            ['Rol', 'Email', 'Password'],
            [
                ['Administrador', 'admin@ankor.com', 'admin123'],
                ['Usuario ANKOR', 'usuario@ankor.com', 'usuario123'],
                ['Proveedor', 'proveedor@ferreteria.com', 'proveedor123'],
            ]
        );
    }
}
