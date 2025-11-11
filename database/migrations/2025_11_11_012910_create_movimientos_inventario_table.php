<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('restrict');
            $table->foreignId('nota_remision_id')
                  ->nullable()
                  ->constrained('notas_remision')
                  ->onDelete('set null');

            $table->enum('tipo', ['ENTRADA', 'SALIDA', 'AJUSTE']);
            $table->decimal('cantidad', 10, 2);
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');

            $table->string('referencia')->nullable(); // Ej: "Nota Remisión NR-2025-0001"
            $table->text('observaciones')->nullable();

            $table->timestamp('created_at');

            // Índices
            $table->index('producto_id');
            $table->index('nota_remision_id');
            $table->index('tipo');
            $table->index('created_at');
            $table->index(['producto_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
