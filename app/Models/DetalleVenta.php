<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_venta';
    protected $primaryKey = 'det_id';

    protected $fillable = [
        'ven_id',
        'pro_id',
        'det_cantidad',
        'det_precio_unitario',
        'det_subtotal'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'pro_id', 'pro_id');
    }
}
