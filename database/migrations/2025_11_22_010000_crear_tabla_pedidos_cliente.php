<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * FLUJO ANKOR: Pedidos de Clientes (paso inicial del flujo)
     */
    public function up(): void
    {
        Schema::create('pedidos_cliente', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // PED-2025-0001

            // Datos del Cliente
            $table->string('cliente_nombre');
            $table->string('cliente_ruc')->nullable();
            $table->string('cliente_telefono')->nullable();
            $table->string('cliente_email')->nullable();
            $table->string('cliente_direccion')->nullable();

            // Fechas
            $table->date('fecha_pedido');
            $table->date('fecha_entrega_solicitada')->nullable();
            $table->date('fecha_entrega_estimada')->nullable();

            // Estados del flujo ANKOR
            $table->enum('estado', [
                'RECIBIDO',           // Pedido recibido de cliente
                'EN_PROCESO',         // Procesando - generando presupuestos a proveedores
                'PRESUPUESTADO',      // Presupuestos de proveedores recibidos
                'ORDEN_COMPRA',       // Orden de compra emitida a proveedor
                'MERCADERIA_RECIBIDA',// Mercadería recibida del proveedor
                'LISTO_ENVIO',        // Listo para enviar al cliente
                'ENVIADO',            // Enviado al cliente
                'ENTREGADO',          // Entregado al cliente
                'CANCELADO'           // Pedido cancelado
            ])->default('RECIBIDO');

            // Montos (en Guaraníes - sin decimales)
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('descuento')->default(0);
            $table->bigInteger('total')->default(0);

            // Notas y observaciones
            $table->text('notas')->nullable();
            $table->text('motivo_cancelacion')->nullable();

            // Auditoría
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });

        // Tabla de items del pedido
        Schema::create('pedido_cliente_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_cliente_id')->constrained('pedidos_cliente')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 12, 3);
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
        Schema::dropIfExists('pedido_cliente_items');
        Schema::dropIfExists('pedidos_cliente');
    }
};
