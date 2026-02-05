<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PedidoCliente extends Model
{
    use HasFactory;

    protected $table = 'pedidos_cliente';

    protected $fillable = [
        'numero',
        'cliente_id',
        'cliente_nombre',
        'cliente_ruc',
        'cliente_telefono',
        'cliente_email',
        'cliente_direccion',
        'fecha_pedido',
        'fecha_entrega_solicitada',
        'fecha_entrega_estimada',
        'estado',
        'subtotal',
        'descuento',
        'total',
        'notas',
        'motivo_cancelacion',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'fecha_entrega_solicitada' => 'date',
        'fecha_entrega_estimada' => 'date',
        'subtotal' => 'integer',
        'descuento' => 'integer',
        'total' => 'integer',
    ];

    // Estados del flujo ANKOR
    const ESTADO_RECIBIDO = 'RECIBIDO';
    const ESTADO_EN_PROCESO = 'EN_PROCESO';
    const ESTADO_PRESUPUESTADO = 'PRESUPUESTADO';
    const ESTADO_ORDEN_COMPRA = 'ORDEN_COMPRA';
    const ESTADO_MERCADERIA_RECIBIDA = 'MERCADERIA_RECIBIDA';
    const ESTADO_LISTO_ENVIO = 'LISTO_ENVIO';
    const ESTADO_ENVIADO = 'ENVIADO';
    const ESTADO_ENTREGADO = 'ENTREGADO';
    const ESTADO_CANCELADO = 'CANCELADO';

    /**
     * Obtener todos los estados con sus etiquetas para vistas
     */
    public static function getEstados(): array
    {
        return [
            self::ESTADO_RECIBIDO => 'Recibido',
            self::ESTADO_EN_PROCESO => 'En Proceso',
            self::ESTADO_PRESUPUESTADO => 'Presupuestado',
            self::ESTADO_ORDEN_COMPRA => 'Orden Compra',
            self::ESTADO_MERCADERIA_RECIBIDA => 'Mercadería Recibida',
            self::ESTADO_LISTO_ENVIO => 'Listo Envío',
            self::ESTADO_ENVIADO => 'Enviado',
            self::ESTADO_ENTREGADO => 'Entregado',
            self::ESTADO_CANCELADO => 'Cancelado',
        ];
    }

    /**
     * Secuencia de estados para timeline (sin cancelado)
     */
    public static function getEstadosSecuencia(): array
    {
        return [
            self::ESTADO_RECIBIDO,
            self::ESTADO_EN_PROCESO,
            self::ESTADO_PRESUPUESTADO,
            self::ESTADO_ORDEN_COMPRA,
            self::ESTADO_MERCADERIA_RECIBIDA,
            self::ESTADO_LISTO_ENVIO,
            self::ESTADO_ENVIADO,
            self::ESTADO_ENTREGADO,
        ];
    }

    /**
     * Generar número de solicitud automáticamente
     */
    public static function generarNumero(): string
    {
        $ultimo = static::max('id') ?? 0;
        return sprintf('Solicitud #%d', $ultimo + 1);
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pedido) {
            if (empty($pedido->numero)) {
                $pedido->numero = static::generarNumero();
            }
        });
    }

    /**
     * Calcular totales
     */
    public function calcularTotales(): void
    {
        $this->subtotal = (int) $this->items->sum('subtotal');
        $descuentoMonto = (int) round($this->subtotal * $this->descuento / 100);
        $this->total = max(0, $this->subtotal - $descuentoMonto);
        $this->save();
    }

    // Relaciones
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PedidoClienteItem::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function ordenesCompra(): HasMany
    {
        return $this->hasMany(OrdenCompra::class);
    }

    public function ordenEnvio(): HasOne
    {
        return $this->hasOne(OrdenEnvio::class);
    }

    public function solicitudesPresupuesto(): HasMany
    {
        return $this->hasMany(SolicitudPresupuesto::class);
    }

    // Helpers de estado
    public function puedeSerCancelado(): bool
    {
        return !in_array($this->estado, [
            self::ESTADO_ENVIADO,
            self::ESTADO_ENTREGADO,
            self::ESTADO_CANCELADO
        ]);
    }

    public function puedeGenerarOrdenCompra(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_RECIBIDO,
            self::ESTADO_EN_PROCESO,
            self::ESTADO_PRESUPUESTADO
        ]);
    }

    public function puedeGenerarOrdenEnvio(): bool
    {
        return $this->estado === self::ESTADO_MERCADERIA_RECIBIDA;
    }

    /**
     * Obtener el color del badge según estado
     */
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_RECIBIDO => 'bg-blue-100 text-blue-800',
            self::ESTADO_EN_PROCESO => 'bg-yellow-100 text-yellow-800',
            self::ESTADO_PRESUPUESTADO => 'bg-indigo-100 text-indigo-800',
            self::ESTADO_ORDEN_COMPRA => 'bg-purple-100 text-purple-800',
            self::ESTADO_MERCADERIA_RECIBIDA => 'bg-cyan-100 text-cyan-800',
            self::ESTADO_LISTO_ENVIO => 'bg-orange-100 text-orange-800',
            self::ESTADO_ENVIADO => 'bg-teal-100 text-teal-800',
            self::ESTADO_ENTREGADO => 'bg-green-100 text-green-800',
            self::ESTADO_CANCELADO => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Obtener descripción del estado
     */
    public function getEstadoDescripcionAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_RECIBIDO => 'Solicitud recibida del cliente',
            self::ESTADO_EN_PROCESO => 'Procesando - solicitando presupuestos',
            self::ESTADO_PRESUPUESTADO => 'Presupuestos de proveedores recibidos',
            self::ESTADO_ORDEN_COMPRA => 'Orden de compra emitida',
            self::ESTADO_MERCADERIA_RECIBIDA => 'Mercadería recibida del proveedor',
            self::ESTADO_LISTO_ENVIO => 'Listo para enviar al cliente',
            self::ESTADO_ENVIADO => 'Enviado al cliente',
            self::ESTADO_ENTREGADO => 'Entregado al cliente',
            self::ESTADO_CANCELADO => 'Solicitud cancelada',
            default => 'Estado desconocido',
        };
    }
}
