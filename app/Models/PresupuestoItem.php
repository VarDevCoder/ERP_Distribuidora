<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresupuestoItem extends Model
{
    protected $table = 'presupuesto_items';

    protected $fillable = [
        'presupuesto_id', 'producto_id', 'orden', 'descripcion',
        'cantidad', 'precio_unitario', 'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->subtotal = $item->cantidad * $item->precio_unitario;
        });
    }
}
