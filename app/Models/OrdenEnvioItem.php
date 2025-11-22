<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenEnvioItem extends Model
{
    use HasFactory;

    protected $table = 'orden_envio_items';

    protected $fillable = [
        'orden_envio_id',
        'producto_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
    ];

    // Relaciones
    public function ordenEnvio(): BelongsTo
    {
        return $this->belongsTo(OrdenEnvio::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
