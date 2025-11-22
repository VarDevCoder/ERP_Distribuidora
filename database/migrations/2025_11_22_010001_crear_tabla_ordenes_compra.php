<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * FLUJO ANKOR: Órdenes de Compra a Proveedores
     */
    public function up(): void
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // OC-2025-0001

            // Relación con pedido del cliente (origen)
            $table->foreignId('pedido_cliente_id')->nullable()->constrained('pedidos_cliente')->onDelete('set null');

            // Datos del Proveedor
            $table->string('proveedor_nombre');
            $table->string('proveedor_ruc')->nullable();
            $table->string('proveedor_telefono')->nullable();
            $table->string('proveedor_email')->nullable();
            $table->string('proveedor_direccion')->nullable();

            // Referencia al presupuesto del proveedor
            $table->foreignId('presupuesto_proveedor_id')->nullable()->constrained('presupuestos')->onDelete('set null');

            // Fechas
            $table->date('fecha_orden');
            $table->date('fecha_entrega_esperada')->nullable();
            $table->date('fecha_recepcion')->nullable();

            // Estados del flujo ANKOR
            $table->enum('estado', [
                'BORRADOR',           // En preparación
                'ENVIADA',            // Enviada al proveedor
                'CONFIRMADA',         // Proveedor confirmó
                'EN_TRANSITO',        // Mercadería en camino
                'RECIBIDA_PARCIAL',   // Recepción parcial
                'RECIBIDA_COMPLETA',  // Recepción completa
                'CANCELADA'           // Orden cancelada
            ])->default('BORRADOR');

            // Montos (en Guaraníes - sin decimales)
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('descuento')->default(0);
            $table->bigInteger('total')->default(0);

            // Notas
            $table->text('notas')->nullable();
            $table->text('motivo_cancelacion')->nullable();

            // Auditoría
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });

        // Tabla de items de la orden de compra
        Schema::create('orden_compra_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad_solicitada', 12, 3);
            $table->decimal('cantidad_recibida', 12, 3)->default(0);
            $table->bigInteger('precio_unitario');
            $table->bigInteger('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_compra_items');
        Schema::dropIfExists('ordenes_compra');
    }
};
