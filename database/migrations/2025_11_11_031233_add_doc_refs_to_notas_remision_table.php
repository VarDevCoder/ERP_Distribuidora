<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notas_remision', function (Blueprint $table) {
            $table->string('factura_numero')->nullable();
            $table->string('contrafactura_numero')->nullable();
            $table->string('remision_numero')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notas_remision', function (Blueprint $table) {
            $table->dropColumn(['factura_numero', 'contrafactura_numero', 'remision_numero']);
        });
    }
};
