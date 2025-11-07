<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $table = 'presupuesto';
    protected $primaryKey = 'pre_id';

    protected $fillable = [
        'pre_numero',
        'cli_id',
        'usu_id',
        'pre_fecha',
        'pre_fecha_vencimiento',
        'pre_subtotal',
        'pre_descuento',
        'pre_total',
        'pre_estado',
        'pre_observaciones'
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
        return $this->hasMany(DetallePresupuesto::class, 'pre_id', 'pre_id');
    }
}
