<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Herramientas', 'descripcion' => 'Herramientas eléctricas y manuales', 'orden' => 1],
            ['nombre' => 'Materiales de Construcción', 'descripcion' => 'Cemento, arena, hierro y ladrillos', 'orden' => 2],
            ['nombre' => 'Electricidad', 'descripcion' => 'Cables, llaves térmicas y componentes eléctricos', 'orden' => 3],
            ['nombre' => 'Pinturas', 'descripcion' => 'Pinturas, esmaltes y accesorios', 'orden' => 4],
        ];

        foreach ($categorias as $cat) {
            Categoria::create($cat);
        }
    }
}
