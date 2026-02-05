<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos_cliente', function (Blueprint $table) {
            // Agregar campo cliente_id con foreign key
            $table->foreignId('cliente_id')->nullable()->after('numero')->constrained('clientes')->onDelete('restrict');

            // Hacer los campos individuales nullable para permitir migración gradual
            $table->string('cliente_nombre')->nullable()->change();
            $table->string('cliente_ruc')->nullable()->change();
            $table->string('cliente_telefono')->nullable()->change();
            $table->string('cliente_email')->nullable()->change();
            $table->text('cliente_direccion')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos_cliente', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');

            // Revertir los cambios de nullable
            $table->string('cliente_nombre')->nullable(false)->change();
        });
    }
};
