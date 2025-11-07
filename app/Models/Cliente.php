<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'cli_id';

    protected $fillable = [
        'cli_nombre',
        'cli_apellido',
        'cli_ci',
        'cli_telefono',
        'cli_direccion',
        'cli_email',
        'cli_tipo'
    ];
}
