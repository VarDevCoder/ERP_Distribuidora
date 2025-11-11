<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Presupuesto;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear productos de ejemplo
        $laptop = Producto::create([
            'nombre' => 'Laptop Dell Inspiron',
            'descripcion' => 'Laptop 15.6", Intel i5, 8GB RAM, 256GB SSD',
            'precio_compra' => 8000.00,
            'precio_venta' => 12000.00,
            'stock_actual' => 10,
            'stock_minimo' => 2,
            'unidad_medida' => 'pz'
        ]);

        $mouse = Producto::create([
            'nombre' => 'Mouse Logitech',
            'descripcion' => 'Mouse inalámbrico M170',
            'precio_compra' => 150.00,
            'precio_venta' => 250.00,
            'stock_actual' => 50,
            'stock_minimo' => 10,
            'unidad_medida' => 'pz'
        ]);

        $teclado = Producto::create([
            'nombre' => 'Teclado mecánico',
            'descripcion' => 'Teclado RGB switches azul',
            'precio_compra' => 500.00,
            'precio_venta' => 800.00,
            'stock_actual' => 15,
            'stock_minimo' => 5,
            'unidad_medida' => 'pz'
        ]);

        $monitor = Producto::create([
            'nombre' => 'Monitor Samsung 24"',
            'descripcion' => 'Monitor Full HD 24 pulgadas',
            'precio_compra' => 1500.00,
            'precio_venta' => 2200.00,
            'stock_actual' => 8,
            'stock_minimo' => 3,
            'unidad_medida' => 'pz'
        ]);

        // Crear presupuesto de VENTA
        $presupuestoVenta = Presupuesto::create([
            'tipo' => 'VENTA',
            'contacto_nombre' => 'Juan Pérez',
            'contacto_email' => 'juan@ejemplo.com',
            'contacto_telefono' => '555-1234',
            'contacto_empresa' => 'Empresa Demo SA',
            'fecha' => now(),
            'fecha_vencimiento' => now()->addDays(30),
            'estado' => 'ENVIADO',
            'notas' => 'Cliente solicita factura a nombre de la empresa',
        ]);

        $presupuestoVenta->items()->create([
            'producto_id' => $laptop->id,
            'orden' => 0,
            'descripcion' => 'Laptop Dell Inspiron 15.6"',
            'cantidad' => 2,
            'precio_unitario' => 12000.00,
        ]);

        $presupuestoVenta->items()->create([
            'producto_id' => $mouse->id,
            'orden' => 1,
            'descripcion' => 'Mouse Logitech M170',
            'cantidad' => 2,
            'precio_unitario' => 250.00,
        ]);

        $presupuestoVenta->calcularTotales();

        // Crear presupuesto de COMPRA
        $presupuestoCompra = Presupuesto::create([
            'tipo' => 'COMPRA',
            'contacto_nombre' => 'Proveedor TechMex',
            'contacto_email' => 'ventas@techmex.com',
            'contacto_telefono' => '555-9999',
            'contacto_empresa' => 'TechMex Distribuidores SA de CV',
            'fecha' => now(),
            'fecha_vencimiento' => now()->addDays(15),
            'estado' => 'BORRADOR',
            'notas' => 'Solicitud de compra para reabastecimiento',
        ]);

        $presupuestoCompra->items()->create([
            'producto_id' => $teclado->id,
            'orden' => 0,
            'descripcion' => 'Teclado mecánico RGB',
            'cantidad' => 20,
            'precio_unitario' => 500.00,
        ]);

        $presupuestoCompra->items()->create([
            'producto_id' => $monitor->id,
            'orden' => 1,
            'descripcion' => 'Monitor Samsung 24" Full HD',
            'cantidad' => 10,
            'precio_unitario' => 1500.00,
        ]);

        $presupuestoCompra->calcularTotales();

        // Crear otro presupuesto de VENTA aprobado para demo
        $presupuestoAprobado = Presupuesto::create([
            'tipo' => 'VENTA',
            'contacto_nombre' => 'María García',
            'contacto_email' => 'maria@startup.com',
            'contacto_empresa' => 'Startup Innovadora',
            'fecha' => now()->subDays(3),
            'fecha_vencimiento' => now()->addDays(27),
            'estado' => 'APROBADO',
            'descuento' => 500.00,
            'notas' => 'Cliente aprobó presupuesto. Listo para convertir a nota de remisión.',
        ]);

        $presupuestoAprobado->items()->create([
            'producto_id' => $laptop->id,
            'orden' => 0,
            'descripcion' => 'Laptop Dell Inspiron 15.6"',
            'cantidad' => 5,
            'precio_unitario' => 12000.00,
        ]);

        $presupuestoAprobado->items()->create([
            'producto_id' => $teclado->id,
            'orden' => 1,
            'descripcion' => 'Teclado mecánico RGB',
            'cantidad' => 5,
            'precio_unitario' => 800.00,
        ]);

        $presupuestoAprobado->items()->create([
            'producto_id' => $mouse->id,
            'orden' => 2,
            'descripcion' => 'Mouse Logitech M170',
            'cantidad' => 5,
            'precio_unitario' => 250.00,
        ]);

        $presupuestoAprobado->calcularTotales();
    }
}
