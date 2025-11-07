<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimiento_inventario';
    protected $primaryKey = 'mov_id';

    protected $fillable = [
        'pro_id',
        'usu_id',
        'mov_tipo',
        'mov_motivo',
        'mov_cantidad',
        'mov_stock_anterior',
        'mov_stock_nuevo',
        'mov_costo',
        'mov_referencia',
        'mov_referencia_id',
        'mov_observaciones',
        'mov_fecha'
    ];

    protected $casts = [
        'mov_fecha' => 'datetime'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'pro_id', 'pro_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usu_id', 'usu_id');
    }
}
