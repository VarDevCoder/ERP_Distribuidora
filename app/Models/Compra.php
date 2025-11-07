<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compra';
    protected $primaryKey = 'com_id';

    protected $fillable = [
        'com_numero',
        'prov_id',
        'usu_id',
        'com_fecha',
        'com_factura',
        'com_subtotal',
        'com_descuento',
        'com_total',
        'com_estado',
        'com_observaciones'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'prov_id', 'prov_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usu_id', 'usu_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'com_id', 'com_id');
    }
}
