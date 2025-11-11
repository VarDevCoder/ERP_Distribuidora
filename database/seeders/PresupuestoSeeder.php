<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presupuesto;

class PresupuestoSeeder extends Seeder
{
    public function run()
    {
        $presupuesto = Presupuesto::create([
            'cliente_nombre' => 'Empresa Demo SA',
            'cliente_email' => 'contacto@demo.com',
            'cliente_telefono' => '555-1234',
            'cliente_empresa' => 'Demo Corporation',
            'fecha' => now(),
            'fecha_vencimiento' => now()->addDays(30),
            'descuento' => 0,
            'estado' => 'ENVIADO',
            'notas' => 'Presupuesto de prueba para demostración del sistema',
        ]);

        $presupuesto->items()->createMany([
            ['orden' => 0, 'descripcion' => 'Desarrollo de aplicación web completa', 'cantidad' => 1, 'precio_unitario' => 5000],
            ['orden' => 1, 'descripcion' => 'Diseño UI/UX profesional', 'cantidad' => 1, 'precio_unitario' => 2000],
            ['orden' => 2, 'descripcion' => 'Hosting y dominio (anual)', 'cantidad' => 1, 'precio_unitario' => 500],
        ]);

        $presupuesto->calcularTotales();

        // Crear un segundo presupuesto
        $presupuesto2 = Presupuesto::create([
            'cliente_nombre' => 'Juan Pérez',
            'cliente_email' => 'juan@ejemplo.com',
            'cliente_telefono' => '555-5678',
            'fecha' => now()->subDays(5),
            'fecha_vencimiento' => now()->addDays(25),
            'descuento' => 100,
            'estado' => 'BORRADOR',
            'notas' => 'Cliente potencial interesado en servicios de consultoría',
        ]);

        $presupuesto2->items()->createMany([
            ['orden' => 0, 'descripcion' => 'Consultoría tecnológica (10 horas)', 'cantidad' => 10, 'precio_unitario' => 150],
            ['orden' => 1, 'descripcion' => 'Análisis de sistemas', 'cantidad' => 1, 'precio_unitario' => 800],
        ]);

        $presupuesto2->calcularTotales();
    }
}
