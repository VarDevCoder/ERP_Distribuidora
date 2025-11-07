<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'usu_id';

    protected $fillable = [
        'usu_email',
        'usu_pass',
        'usu_rol',
        'usu_nombre',
        'usu_apellido',
        'usu_estado'
    ];

    protected $hidden = ['usu_pass'];
}
