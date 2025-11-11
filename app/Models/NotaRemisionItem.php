<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaRemisionItem extends Model
{
    protected $table = 'nota_remision_items';

    protected $fillable = [
        'nota_remision_id', 'producto_id', 'cantidad', 'precio_unitario'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
    ];

    // Relaciones
    public function notaRemision()
    {
        return $this->belongsTo(NotaRemision::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
