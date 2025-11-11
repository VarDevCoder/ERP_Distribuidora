<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'precio_compra', 'precio_venta',
        'stock_actual', 'stock_minimo', 'unidad_medida', 'activo'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function presupuestoItems()
    {
        return $this->hasMany(PresupuestoItem::class);
    }

    // Auto-generar código si no existe
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($producto) {
            if (!$producto->codigo) {
                $ultimo = Producto::max('id') ?? 0;
                $producto->codigo = 'PROD-' . str_pad($ultimo + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
