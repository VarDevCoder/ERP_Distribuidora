<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50)->unique();
            $table->enum('tipo', ['COMPRA', 'VENTA'])->default('VENTA');

            // Datos del contacto (cliente o proveedor)
            $table->string('contacto_nombre');
            $table->string('contacto_email')->nullable();
            $table->string('contacto_telefono', 50)->nullable();
            $table->string('contacto_empresa')->nullable();

            // Fechas
            $table->date('fecha');
            $table->date('fecha_vencimiento');

            // Montos
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Estado del presupuesto
            $table->enum('estado', ['BORRADOR', 'ENVIADO', 'APROBADO', 'RECHAZADO', 'CONVERTIDO'])
                  ->default('BORRADOR');

            $table->text('notas')->nullable();
            $table->timestamps();

            // Índices
            $table->index('numero');
            $table->index('tipo');
            $table->index('estado');
            $table->index('fecha');
            $table->index(['tipo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
