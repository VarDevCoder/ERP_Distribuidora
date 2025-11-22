<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenCompraItem extends Model
{
    use HasFactory;

    protected $table = 'orden_compra_items';

    protected $fillable = [
        'orden_compra_id',
        'producto_id',
        'cantidad_solicitada',
        'cantidad_recibida',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:3',
        'cantidad_recibida' => 'decimal:3',
        'precio_unitario' => 'integer',
        'subtotal' => 'integer',
    ];

    /**
     * Boot del modelo - calcular subtotal automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->subtotal = (int) round($item->cantidad_solicitada * $item->precio_unitario);
        });

        static::updating(function ($item) {
            $item->subtotal = (int) round($item->cantidad_solicitada * $item->precio_unitario);
        });

        // Recalcular totales de la orden después de guardar
        static::saved(function ($item) {
            $item->ordenCompra->calcularTotales();
        });

        static::deleted(function ($item) {
            $item->ordenCompra->calcularTotales();
        });
    }

    // Relaciones
    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Cantidad pendiente por recibir
     */
    public function getCantidadPendienteAttribute(): float
    {
        return $this->cantidad_solicitada - $this->cantidad_recibida;
    }

    /**
     * Verificar si está completamente recibido
     */
    public function estaCompletoAttribute(): bool
    {
        return $this->cantidad_recibida >= $this->cantidad_solicitada;
    }

    /**
     * Porcentaje de recepción
     */
    public function getPorcentajeRecibidoAttribute(): float
    {
        if ($this->cantidad_solicitada == 0) return 0;
        return round(($this->cantidad_recibida / $this->cantidad_solicitada) * 100, 2);
    }
}
