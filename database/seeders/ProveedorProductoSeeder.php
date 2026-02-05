<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\ProveedorProducto;
use Illuminate\Database\Seeder;

class ProveedorProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar datos existentes
        ProveedorProducto::truncate();

        $proveedores = Proveedor::all();
        $productos = Producto::all();

        if ($proveedores->isEmpty() || $productos->isEmpty()) {
            $this->command->warn('No hay proveedores o productos. Ejecuta primero los seeders correspondientes.');
            return;
        }

        // Cada proveedor tendrá diferentes precios para los productos
        // Simula variaciones de precio entre 80% y 120% del precio_compra del producto
        foreach ($proveedores as $proveedor) {
            // Cada proveedor tiene entre 60% y 100% de los productos
            $productosDelProveedor = $productos->random(rand((int) ceil($productos->count() * 0.6), $productos->count()));

            foreach ($productosDelProveedor as $producto) {
                // Variación de precio: entre -15% y +20% del precio de compra
                $variacion = rand(-15, 20) / 100;
                $precioProveedor = (int) round($producto->precio_compra * (1 + $variacion));

                // Tiempo de entrega varía según proveedor
                $tiempoEntrega = rand(1, 7);

                ProveedorProducto::create([
                    'proveedor_id' => $proveedor->id,
                    'producto_id' => $producto->id,
                    'codigo_proveedor' => strtoupper(substr($proveedor->razon_social, 0, 3)) . '-' . $producto->id . rand(100, 999),
                    'nombre_proveedor' => null, // Usará el nombre del producto de ANKOR
                    'precio' => $precioProveedor,
                    'disponible' => rand(1, 10) > 1, // 90% disponibles
                    'tiempo_entrega_dias' => $tiempoEntrega,
                    'notas' => null,
                ]);
            }
        }

        $total = ProveedorProducto::count();
        $this->command->info("Se crearon {$total} registros en proveedor_productos.");
    }
}
