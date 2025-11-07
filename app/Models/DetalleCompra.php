<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compra';
    protected $primaryKey = 'det_com_id';

    protected $fillable = [
        'com_id',
        'pro_id',
        'det_com_cantidad',
        'det_com_precio_unitario',
        'det_com_subtotal'
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'com_id', 'com_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'pro_id', 'pro_id');
    }
}
