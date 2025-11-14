<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla para guardar las cantidades reales ingresadas al registrar factura/remisión
     */
    public function up(): void
    {
        Schema::create('cantidades_reales_documentos', function (Blueprint $table) {
            $table->id();

            // Referencias
            $table->foreignId('presupuesto_id')
                  ->constrained('presupuestos')
                  ->onDelete('cascade');
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('restrict');

            // Tipo de documento que se registró
            $table->enum('tipo_documento', ['FACTURA', 'REMISION']);

            // Cantidades
            $table->decimal('cantidad_presupuestada', 10, 3); // Lo que se pidió/cotizó
            $table->decimal('cantidad_real', 10, 3);          // Lo que realmente llegó/se envió
            $table->decimal('diferencia', 10, 3);             // cantidad_real - cantidad_presupuestada

            // Motivo de la diferencia (si existe)
            $table->text('motivo_diferencia')->nullable();

            // Auditoría
            $table->foreignId('usuario_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();

            // Índices
            $table->index('presupuesto_id');
            $table->index('producto_id');
            $table->index('tipo_documento');
            $table->index(['presupuesto_id', 'tipo_documento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cantidades_reales_documentos');
    }
};
