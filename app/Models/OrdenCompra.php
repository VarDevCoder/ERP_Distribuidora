<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compra';

    protected $fillable = [
        'numero',
        'pedido_cliente_id',
        'proveedor_nombre',
        'proveedor_ruc',
        'proveedor_telefono',
        'proveedor_email',
        'proveedor_direccion',
        'presupuesto_proveedor_id',
        'fecha_orden',
        'fecha_entrega_esperada',
        'fecha_recepcion',
        'estado',
        'subtotal',
        'descuento',
        'total',
        'notas',
        'motivo_cancelacion',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_orden' => 'date',
        'fecha_entrega_esperada' => 'date',
        'fecha_recepcion' => 'date',
        'subtotal' => 'integer',
        'descuento' => 'integer',
        'total' => 'integer',
    ];

    // Estados del flujo ANKOR
    const ESTADO_BORRADOR = 'BORRADOR';
    const ESTADO_ENVIADA = 'ENVIADA';
    const ESTADO_CONFIRMADA = 'CONFIRMADA';
    const ESTADO_EN_TRANSITO = 'EN_TRANSITO';
    const ESTADO_RECIBIDA_PARCIAL = 'RECIBIDA_PARCIAL';
    const ESTADO_RECIBIDA_COMPLETA = 'RECIBIDA_COMPLETA';
    const ESTADO_CANCELADA = 'CANCELADA';

    /**
     * Obtener todos los estados con sus etiquetas
     */
    public static function getEstados(): array
    {
        return [
            self::ESTADO_BORRADOR => 'Borrador',
            self::ESTADO_ENVIADA => 'Enviada',
            self::ESTADO_CONFIRMADA => 'Confirmada',
            self::ESTADO_EN_TRANSITO => 'En Tránsito',
            self::ESTADO_RECIBIDA_PARCIAL => 'Recibida Parcial',
            self::ESTADO_RECIBIDA_COMPLETA => 'Recibida Completa',
            self::ESTADO_CANCELADA => 'Cancelada',
        ];
    }

    /**
     * Generar número de orden automáticamente
     */
    public static function generarNumero(): string
    {
        $año = date('Y');
        $ultimo = static::whereYear('created_at', $año)->count() + 1;
        return sprintf('OC-%s-%04d', $año, $ultimo);
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orden) {
            if (empty($orden->numero)) {
                $orden->numero = static::generarNumero();
            }
        });
    }

    /**
     * Calcular totales
     */
    public function calcularTotales(): void
    {
        $this->subtotal = (int) $this->items->sum('subtotal');
        $this->total = $this->subtotal - $this->descuento;
        $this->save();
    }

    /**
     * Verificar si todas las cantidades fueron recibidas
     */
    public function verificarRecepcionCompleta(): bool
    {
        foreach ($this->items as $item) {
            if ($item->cantidad_recibida < $item->cantidad_solicitada) {
                return false;
            }
        }
        return true;
    }

    // Relaciones
    public function items(): HasMany
    {
        return $this->hasMany(OrdenCompraItem::class);
    }

    public function pedidoCliente(): BelongsTo
    {
        return $this->belongsTo(PedidoCliente::class);
    }

    public function presupuestoProveedor(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class, 'presupuesto_proveedor_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Helpers de estado
    public function puedeSerEnviada(): bool
    {
        return $this->estado === self::ESTADO_BORRADOR;
    }

    public function puedeSerConfirmada(): bool
    {
        return $this->estado === self::ESTADO_ENVIADA;
    }

    public function puedeRecibirMercaderia(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_CONFIRMADA,
            self::ESTADO_EN_TRANSITO,
            self::ESTADO_RECIBIDA_PARCIAL
        ]);
    }

    public function puedeSerCancelada(): bool
    {
        return !in_array($this->estado, [
            self::ESTADO_RECIBIDA_COMPLETA,
            self::ESTADO_CANCELADA
        ]);
    }

    /**
     * Obtener el color del badge según estado
     */
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_BORRADOR => 'bg-gray-100 text-gray-800',
            self::ESTADO_ENVIADA => 'bg-blue-100 text-blue-800',
            self::ESTADO_CONFIRMADA => 'bg-indigo-100 text-indigo-800',
            self::ESTADO_EN_TRANSITO => 'bg-yellow-100 text-yellow-800',
            self::ESTADO_RECIBIDA_PARCIAL => 'bg-orange-100 text-orange-800',
            self::ESTADO_RECIBIDA_COMPLETA => 'bg-green-100 text-green-800',
            self::ESTADO_CANCELADA => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Obtener descripción del estado
     */
    public function getEstadoDescripcionAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_BORRADOR => 'Orden en preparación',
            self::ESTADO_ENVIADA => 'Enviada al proveedor',
            self::ESTADO_CONFIRMADA => 'Confirmada por proveedor',
            self::ESTADO_EN_TRANSITO => 'Mercadería en tránsito',
            self::ESTADO_RECIBIDA_PARCIAL => 'Recepción parcial',
            self::ESTADO_RECIBIDA_COMPLETA => 'Recepción completa',
            self::ESTADO_CANCELADA => 'Orden cancelada',
            default => 'Estado desconocido',
        };
    }
}
