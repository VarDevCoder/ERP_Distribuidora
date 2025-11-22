<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * FLUJO ANKOR: Órdenes de Envío a Clientes
     */
    public function up(): void
    {
        Schema::create('ordenes_envio', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // ENV-2025-0001

            // Relación con pedido del cliente
            $table->foreignId('pedido_cliente_id')->constrained('pedidos_cliente')->onDelete('cascade');

            // Datos de entrega
            $table->string('direccion_entrega');
            $table->string('contacto_entrega')->nullable();
            $table->string('telefono_entrega')->nullable();

            // Fechas
            $table->date('fecha_generacion');
            $table->date('fecha_envio')->nullable();
            $table->date('fecha_entrega')->nullable();

            // Estados del flujo ANKOR
            $table->enum('estado', [
                'PREPARANDO',         // Preparando envío
                'LISTO',              // Listo para despachar
                'EN_TRANSITO',        // En camino
                'ENTREGADO',          // Entregado al cliente
                'DEVUELTO',           // Devuelto
                'CANCELADO'           // Cancelado
            ])->default('PREPARANDO');

            // Método de envío
            $table->string('metodo_envio')->nullable();
            $table->string('numero_guia')->nullable();
            $table->string('transportista')->nullable();

            // Notas
            $table->text('notas')->nullable();
            $table->text('observaciones_entrega')->nullable();

            // Auditoría
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
        });

        // Tabla de items del envío
        Schema::create('orden_envio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_envio_id')->constrained('ordenes_envio')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 12, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_envio_items');
        Schema::dropIfExists('ordenes_envio');
    }
};
