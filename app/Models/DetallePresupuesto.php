<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePresupuesto extends Model
{
    protected $table = 'detalle_presupuesto';
    protected $primaryKey = 'det_pre_id';

    protected $fillable = [
        'pre_id',
        'pro_id',
        'det_pre_cantidad',
        'det_pre_precio_unitario',
        'det_pre_subtotal'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'pro_id', 'pro_id');
    }

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class, 'pre_id', 'pre_id');
    }
}
