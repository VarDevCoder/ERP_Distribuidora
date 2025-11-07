<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'prov_id';

    protected $fillable = [
        'prov_nombre',
        'prov_ruc',
        'prov_telefono',
        'prov_email',
        'prov_direccion',
        'prov_ciudad',
        'prov_contacto',
        'prov_estado',
        'prov_observaciones'
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'prov_id', 'prov_id');
    }
}
