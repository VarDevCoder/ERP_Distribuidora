<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PedidoClienteItem extends Model
{
    use HasFactory;

    protected $table = 'pedido_cliente_items';

    protected $fillable = [
        'pedido_cliente_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
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
            $item->subtotal = (int) round($item->cantidad * $item->precio_unitario);
        });

        static::updating(function ($item) {
            $item->subtotal = (int) round($item->cantidad * $item->precio_unitario);
        });

        // Recalcular totales del pedido después de guardar
        static::saved(function ($item) {
            $item->pedidoCliente->calcularTotales();
        });

        static::deleted(function ($item) {
            $item->pedidoCliente->calcularTotales();
        });
    }

    // Relaciones
    public function pedidoCliente(): BelongsTo
    {
        return $this->belongsTo(PedidoCliente::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
