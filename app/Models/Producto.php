<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'categoria_id', 'codigo', 'nombre', 'descripcion', 'precio_compra', 'precio_venta',
        'stock_actual', 'stock_minimo', 'unidad_medida', 'activo'
    ];

    protected $casts = [
        'precio_compra' => 'integer',
        'precio_venta' => 'integer',
        'stock_actual' => 'decimal:3',
        'stock_minimo' => 'decimal:3',
        'activo' => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function proveedorProductos()
    {
        return $this->hasMany(ProveedorProducto::class);
    }

    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_actual', '<', 'stock_minimo');
    }

    public function calcularPrecioVenta(): int
    {
        $margen = config('ankor.margen.porcentaje', 25);
        return (int) round($this->precio_compra * (1 + $margen / 100));
    }

    public function actualizarPrecios(int $nuevoPrecioCompra): void
    {
        $this->precio_compra = $nuevoPrecioCompra;
        $this->precio_venta = $this->calcularPrecioVenta();
        $this->save();
    }

    public function getMargenAttribute(): float
    {
        if ($this->precio_compra <= 0) return 0;
        return round(($this->precio_venta - $this->precio_compra) / $this->precio_compra * 100, 1);
    }

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
