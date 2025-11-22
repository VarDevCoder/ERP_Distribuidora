<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenEnvio extends Model
{
    use HasFactory;

    protected $table = 'ordenes_envio';

    protected $fillable = [
        'numero',
        'pedido_cliente_id',
        'direccion_entrega',
        'contacto_entrega',
        'telefono_entrega',
        'fecha_generacion',
        'fecha_envio',
        'fecha_entrega',
        'estado',
        'metodo_envio',
        'numero_guia',
        'transportista',
        'notas',
        'observaciones_entrega',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_generacion' => 'date',
        'fecha_envio' => 'date',
        'fecha_entrega' => 'date',
    ];

    // Estados del flujo ANKOR
    const ESTADO_PREPARANDO = 'PREPARANDO';
    const ESTADO_LISTO = 'LISTO';
    const ESTADO_EN_TRANSITO = 'EN_TRANSITO';
    const ESTADO_ENTREGADO = 'ENTREGADO';
    const ESTADO_DEVUELTO = 'DEVUELTO';
    const ESTADO_CANCELADO = 'CANCELADO';

    /**
     * Obtener todos los estados con sus etiquetas
     */
    public static function getEstados(): array
    {
        return [
            self::ESTADO_PREPARANDO => 'Preparando',
            self::ESTADO_LISTO => 'Listo',
            self::ESTADO_EN_TRANSITO => 'En Tránsito',
            self::ESTADO_ENTREGADO => 'Entregado',
            self::ESTADO_DEVUELTO => 'Devuelto',
            self::ESTADO_CANCELADO => 'Cancelado',
        ];
    }

    /**
     * Obtener métodos de envío desde config
     */
    public static function getMetodosEnvio(): array
    {
        return config('ankor.metodos_envio', []);
    }

    /**
     * Generar número de orden de envío automáticamente
     */
    public static function generarNumero(): string
    {
        $año = date('Y');
        $ultimo = static::whereYear('created_at', $año)->count() + 1;
        return sprintf('ENV-%s-%04d', $año, $ultimo);
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

    // Relaciones
    public function items(): HasMany
    {
        return $this->hasMany(OrdenEnvioItem::class);
    }

    public function pedidoCliente(): BelongsTo
    {
        return $this->belongsTo(PedidoCliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Helpers de estado
    public function puedeSerDespachada(): bool
    {
        return $this->estado === self::ESTADO_LISTO;
    }

    public function puedeSerEntregada(): bool
    {
        return $this->estado === self::ESTADO_EN_TRANSITO;
    }

    public function puedeSerCancelada(): bool
    {
        return !in_array($this->estado, [
            self::ESTADO_ENTREGADO,
            self::ESTADO_CANCELADO
        ]);
    }

    /**
     * Obtener el color del badge según estado
     */
    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            self::ESTADO_PREPARANDO => 'bg-yellow-100 text-yellow-800',
            self::ESTADO_LISTO => 'bg-blue-100 text-blue-800',
            self::ESTADO_EN_TRANSITO => 'bg-indigo-100 text-indigo-800',
            self::ESTADO_ENTREGADO => 'bg-green-100 text-green-800',
            self::ESTADO_DEVUELTO => 'bg-orange-100 text-orange-800',
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
            self::ESTADO_PREPARANDO => 'Preparando envío',
            self::ESTADO_LISTO => 'Listo para despachar',
            self::ESTADO_EN_TRANSITO => 'En tránsito',
            self::ESTADO_ENTREGADO => 'Entregado al cliente',
            self::ESTADO_DEVUELTO => 'Devuelto',
            self::ESTADO_CANCELADO => 'Cancelado',
            default => 'Estado desconocido',
        };
    }
}
