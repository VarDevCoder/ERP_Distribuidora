<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_remision', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->foreignId('presupuesto_id')
                  ->constrained('presupuestos')
                  ->onDelete('restrict');

            $table->enum('tipo', ['ENTRADA', 'SALIDA']); // ENTRADA=Compra, SALIDA=Venta

            // Datos del contacto
            $table->string('contacto_nombre');
            $table->string('contacto_empresa')->nullable();

            $table->date('fecha');
            $table->enum('estado', ['PENDIENTE', 'APLICADA'])->default('PENDIENTE');

            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índices
            $table->index('numero');
            $table->index('presupuesto_id');
            $table->index('tipo');
            $table->index('estado');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_remision');
    }
};
