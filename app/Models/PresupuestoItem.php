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
        'cantidad' => 'decimal:2',        // Puede ser decimal (ej: 2.5 kg)
        'precio_unitario' => 'integer',   // Guaraníes (sin decimales)
        'subtotal' => 'integer',          // Guaraníes (sin decimales)
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
            // Calcular subtotal y redondear (Guaraníes)
            $item->subtotal = round($item->cantidad * $item->precio_unitario);
        });
    }
}
