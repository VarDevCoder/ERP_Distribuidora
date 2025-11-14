<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega campos para rastrear diferencias entre cantidades presupuestadas y reales
     */
    public function up(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // Cantidad presupuestada vs cantidad real
            $table->decimal('cantidad_presupuestada', 10, 3)->nullable()->after('cantidad');
            $table->decimal('diferencia', 10, 3)->nullable()->after('cantidad_presupuestada');
            $table->text('motivo_diferencia')->nullable()->after('diferencia');

            // Hash para integridad de datos (previene alteraciones)
            $table->string('hash_verificacion', 64)->nullable()->after('observaciones');

            // Índice para búsqueda de movimientos con diferencias
            $table->index('diferencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropColumn([
                'cantidad_presupuestada',
                'diferencia',
                'motivo_diferencia',
                'hash_verificacion'
            ]);
        });
    }
};
