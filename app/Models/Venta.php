<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';
    protected $primaryKey = 'ven_id';

    protected $fillable = [
        'ven_numero',
        'cli_id',
        'usu_id',
        'ven_fecha',
        'ven_subtotal',
        'ven_descuento',
        'ven_total',
        'ven_estado',
        'ven_observaciones'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cli_id', 'cli_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usu_id', 'usu_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'ven_id', 'ven_id');
    }
}
