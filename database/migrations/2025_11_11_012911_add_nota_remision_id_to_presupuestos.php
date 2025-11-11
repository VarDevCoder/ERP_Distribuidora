<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            // Primero agregar la columna
            if (!Schema::hasColumn('presupuestos', 'nota_remision_id')) {
                $table->foreignId('nota_remision_id')
                      ->nullable()
                      ->after('estado')
                      ->constrained('notas_remision')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->dropForeign(['nota_remision_id']);
            $table->dropColumn('nota_remision_id');
        });
    }
};
