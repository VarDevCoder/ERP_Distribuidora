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
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            // Cambiar tipos de datos de cantidades
            $table->decimal('cantidad', 14, 3)->change();
            $table->decimal('stock_anterior', 14, 3)->nullable()->change();
            $table->decimal('stock_nuevo', 14, 3)->nullable()->change();

            // Eliminar campo referencia antiguo
            $table->dropColumn('referencia');

            // Agregar nuevos campos de auditoría
            $table->string('referencia_tipo')->after('stock_nuevo'); // 'VENTA' | 'COMPRA'
            $table->unsignedBigInteger('referencia_id')->after('referencia_tipo');

            // Agregar campos de documentos
            $table->string('factura_numero')->nullable()->after('nota_remision_id');
            $table->string('contrafactura_numero')->nullable()->after('factura_numero');
            $table->string('remision_numero')->nullable()->after('contrafactura_numero');

            // Agregar usuario_id
            $table->foreignId('usuario_id')->nullable()->after('observaciones')->constrained('users')->nullOnDelete();

            // Agregar updated_at
            $table->timestamp('updated_at')->nullable()->after('created_at');

            // Agregar índices
            $table->index(['referencia_tipo', 'referencia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropIndex(['referencia_tipo', 'referencia_id']);
            $table->dropForeign(['usuario_id']);

            $table->dropColumn([
                'referencia_tipo',
                'referencia_id',
                'factura_numero',
                'contrafactura_numero',
                'remision_numero',
                'usuario_id',
                'updated_at'
            ]);

            $table->string('referencia')->nullable();
            $table->integer('stock_anterior')->change();
            $table->integer('stock_nuevo')->change();
            $table->decimal('cantidad', 10, 2)->change();
        });
    }
};
