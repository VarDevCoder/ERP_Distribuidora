<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuesto', function (Blueprint $table) {
            $table->id('pre_id');
            $table->string('pre_numero', 50)->unique();
            $table->foreignId('cli_id')->constrained('cliente', 'cli_id');
            $table->foreignId('usu_id')->constrained('usuario', 'usu_id');
            $table->date('pre_fecha');
            $table->date('pre_fecha_vencimiento');
            $table->decimal('pre_subtotal', 10, 2);
            $table->decimal('pre_descuento', 10, 2)->default(0);
            $table->decimal('pre_total', 10, 2);
            $table->string('pre_estado', 20)->default('PENDIENTE'); // PENDIENTE, APROBADO, RECHAZADO, CONVERTIDO
            $table->text('pre_observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuesto');
    }
};
