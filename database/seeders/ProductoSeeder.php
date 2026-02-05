<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;

/**
 * Seeder para productos de prueba
 * Precios en Guaranies (sin decimales)
 */
class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Herramientas
            [
                'codigo' => 'HERR-001',
                'nombre' => 'Taladro Percutor 800W',
                'descripcion' => 'Taladro percutor profesional con maletin',
                'precio_compra' => 450000,
                'precio_venta' => 650000,
                'stock_actual' => 15,
                'stock_minimo' => 5,
                'unidad_medida' => 'unid',
            ],
            [
                'codigo' => 'HERR-002',
                'nombre' => 'Amoladora Angular 4 1/2"',
                'descripcion' => 'Amoladora 750W con disco incluido',
                'precio_compra' => 280000,
                'precio_venta' => 420000,
                'stock_actual' => 20,
                'stock_minimo' => 8,
                'unidad_medida' => 'unid',
            ],
            [
                'codigo' => 'HERR-003',
                'nombre' => 'Set Destornilladores x12',
                'descripcion' => 'Juego de destornilladores punta plana y phillips',
                'precio_compra' => 45000,
                'precio_venta' => 75000,
                'stock_actual' => 50,
                'stock_minimo' => 15,
                'unidad_medida' => 'set',
            ],
            // Materiales
            [
                'codigo' => 'MAT-001',
                'nombre' => 'Cemento Portland 50kg',
                'descripcion' => 'Bolsa de cemento portland tipo I',
                'precio_compra' => 55000,
                'precio_venta' => 72000,
                'stock_actual' => 100,
                'stock_minimo' => 30,
                'unidad_medida' => 'bolsa',
            ],
            [
                'codigo' => 'MAT-002',
                'nombre' => 'Arena Lavada m3',
                'descripcion' => 'Arena fina lavada para construccion',
                'precio_compra' => 180000,
                'precio_venta' => 250000,
                'stock_actual' => 25,
                'stock_minimo' => 10,
                'unidad_medida' => 'm3',
            ],
            [
                'codigo' => 'MAT-003',
                'nombre' => 'Hierro 8mm x 12m',
                'descripcion' => 'Barra de hierro corrugado 8mm',
                'precio_compra' => 32000,
                'precio_venta' => 45000,
                'stock_actual' => 200,
                'stock_minimo' => 50,
                'unidad_medida' => 'barra',
            ],
            // Electricidad
            [
                'codigo' => 'ELEC-001',
                'nombre' => 'Cable THW 2.5mm Rollo 100m',
                'descripcion' => 'Cable electrico THW 2.5mm rojo/negro',
                'precio_compra' => 180000,
                'precio_venta' => 260000,
                'stock_actual' => 30,
                'stock_minimo' => 10,
                'unidad_medida' => 'rollo',
            ],
            [
                'codigo' => 'ELEC-002',
                'nombre' => 'Llave Termica 2x20A',
                'descripcion' => 'Interruptor termomagnetico bipolar',
                'precio_compra' => 35000,
                'precio_venta' => 55000,
                'stock_actual' => 40,
                'stock_minimo' => 15,
                'unidad_medida' => 'unid',
            ],
            // Pinturas
            [
                'codigo' => 'PINT-001',
                'nombre' => 'Pintura Latex 20L Blanco',
                'descripcion' => 'Pintura latex interior/exterior',
                'precio_compra' => 320000,
                'precio_venta' => 450000,
                'stock_actual' => 25,
                'stock_minimo' => 8,
                'unidad_medida' => 'balde',
            ],
            [
                'codigo' => 'PINT-002',
                'nombre' => 'Esmalte Sintetico 4L',
                'descripcion' => 'Esmalte sintetico brillante varios colores',
                'precio_compra' => 95000,
                'precio_venta' => 145000,
                'stock_actual' => 35,
                'stock_minimo' => 12,
                'unidad_medida' => 'galon',
            ],
            // Productos con stock bajo para pruebas
            [
                'codigo' => 'HERR-004',
                'nombre' => 'Sierra Circular 7 1/4"',
                'descripcion' => 'Sierra circular 1200W profesional',
                'precio_compra' => 520000,
                'precio_venta' => 780000,
                'stock_actual' => 3,
                'stock_minimo' => 5,
                'unidad_medida' => 'unid',
            ],
            [
                'codigo' => 'MAT-004',
                'nombre' => 'Ladrillo Hueco 12x18x33',
                'descripcion' => 'Ladrillo hueco ceramico',
                'precio_compra' => 1800,
                'precio_venta' => 2500,
                'stock_actual' => 800,
                'stock_minimo' => 1000,
                'unidad_medida' => 'unid',
            ],
        ];

        $categoriaMap = Categoria::pluck('id', 'nombre');

        $prefixMap = [
            'HERR' => $categoriaMap['Herramientas'] ?? null,
            'MAT'  => $categoriaMap['Materiales de Construcción'] ?? null,
            'ELEC' => $categoriaMap['Electricidad'] ?? null,
            'PINT' => $categoriaMap['Pinturas'] ?? null,
        ];

        foreach ($productos as $producto) {
            $prefix = explode('-', $producto['codigo'])[0] ?? '';
            $producto['categoria_id'] = $prefixMap[$prefix] ?? null;
            Producto::create($producto);
        }
    }
}
