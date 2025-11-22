<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudPresupuesto extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_presupuesto';

    protected $fillable = [
        'numero',
        'pedido_cliente_id',
        'proveedor_id',
        'usuario_id',
        'fecha_solicitud',
        'fecha_limite_respuesta',
        'fecha_respuesta',
        'estado',
        'mensaje_solicitud',
        'respuesta_proveedor',
        'total_cotizado',
        'dias_entrega_estimados',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_limite_respuesta' => 'date',
        'fecha_respuesta' => 'date',
        'total_cotizado' => 'integer',
    ];

    // Estados
    const ESTADO_ENVIADA = 'ENVIADA';
    const ESTADO_VISTA = 'VISTA';
    const ESTADO_COTIZADA = 'COTIZADA';
    const ESTADO_SIN_STOCK = 'SIN_STOCK';
    const ESTADO_ACEPTADA = 'ACEPTADA';
    const ESTADO_RECHAZADA = 'RECHAZADA';
    const ESTADO_VENCIDA = 'VENCIDA';

    /**
     * Generar número automáticamente
     */
    public static function generarNumero(): string
    {
        $año = date('Y');
        $ultimo = static::whereYear('created_at', $año)->count() + 1;
        return sprintf('SP-%s-%04d', $año, $ultimo);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($solicitud) {
            if (empty($solicitud->numero)) {
                $solicitud->numero = static::generarNumero();
            }
        });
    }

    // Relaciones
    public function items(): HasMany
    {
        return $this->hasMany(SolicitudPresupuestoItem::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function pedidoCliente(): BelongsTo
    {
        return $this->belongsTo(PedidoCliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Helpers
    public function puedeSerRespondida(): bool
    {
        return in_array($this->estado, [self::ESTADO_ENVIADA, self::ESTADO_VISTA]);
    }

    public function puedeSerAceptada(): bool
    {
        return $this->estado === self::ESTADO_COTIZADA;
    }

    /**
     * Calcular total de la cotización
     */
    public function calcularTotal(): void
    {
        $this->total_cotizado = (int) $this->items->sum('subtotal_cotizado');
        $this->save();
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_ENVIADA => 'bg-blue-100 text-blue-800',
            self::ESTADO_VISTA => 'bg-yellow-100 text-yellow-800',
            self::ESTADO_COTIZADA => 'bg-indigo-100 text-indigo-800',
            self::ESTADO_SIN_STOCK => 'bg-orange-100 text-orange-800',
            self::ESTADO_ACEPTADA => 'bg-green-100 text-green-800',
            self::ESTADO_RECHAZADA => 'bg-red-100 text-red-800',
            self::ESTADO_VENCIDA => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getEstadoDescripcionAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_ENVIADA => 'Enviada al proveedor',
            self::ESTADO_VISTA => 'Vista por el proveedor',
            self::ESTADO_COTIZADA => 'Cotización recibida',
            self::ESTADO_SIN_STOCK => 'Sin disponibilidad',
            self::ESTADO_ACEPTADA => 'Cotización aceptada',
            self::ESTADO_RECHAZADA => 'Cotización rechazada',
            self::ESTADO_VENCIDA => 'Solicitud vencida',
            default => 'Estado desconocido',
        };
    }
}
