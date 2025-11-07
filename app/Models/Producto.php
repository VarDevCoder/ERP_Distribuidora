<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'pro_id';

    protected $fillable = [
        'pro_codigo',
        'pro_nombre',
        'pro_categoria',
        'pro_descripcion',
        'pro_precio_compra',
        'pro_precio_venta',
        'pro_stock',
        'pro_stock_minimo',
        'pro_unidad_medida'
    ];
}
