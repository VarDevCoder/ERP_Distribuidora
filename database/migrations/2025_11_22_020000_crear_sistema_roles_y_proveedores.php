<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agregar sistema de roles a usuarios
     * ROLES: admin, colaborador, proveedor
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['admin', 'colaborador', 'proveedor'])->default('colaborador')->after('email');
            $table->boolean('activo')->default(true)->after('rol');
        });

        // Tabla de proveedores (datos comerciales vinculados a usuario)
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('razon_social');
            $table->string('ruc')->unique();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->text('rubros')->nullable(); // Qué productos maneja
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        // Solicitudes de presupuesto (ANKOR pide cotización a proveedor)
        Schema::create('solicitudes_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // SP-2025-0001

            // Relaciones
            $table->foreignId('pedido_cliente_id')->nullable()->constrained('pedidos_cliente')->onDelete('set null');
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users'); // Quien solicita

            // Fechas
            $table->date('fecha_solicitud');
            $table->date('fecha_limite_respuesta')->nullable();
            $table->date('fecha_respuesta')->nullable();

            // Estados del flujo
            $table->enum('estado', [
                'ENVIADA',              // Enviada al proveedor
                'VISTA',                // Proveedor la vió
                'COTIZADA',             // Proveedor envió cotización
                'SIN_STOCK',            // Proveedor no tiene mercadería
                'ACEPTADA',             // ANKOR aceptó la cotización
                'RECHAZADA',            // ANKOR rechazó la cotización
                'VENCIDA'               // Pasó fecha límite sin respuesta
            ])->default('ENVIADA');

            // Respuesta del proveedor
            $table->text('mensaje_solicitud')->nullable();
            $table->text('respuesta_proveedor')->nullable();
            $table->bigInteger('total_cotizado')->nullable();
            $table->integer('dias_entrega_estimados')->nullable();

            $table->timestamps();
        });

        // Items de la solicitud
        Schema::create('solicitud_presupuesto_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_presupuesto_id')->constrained('solicitudes_presupuesto')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad_solicitada', 12, 3);

            // Respuesta del proveedor
            $table->boolean('tiene_stock')->nullable();
            $table->decimal('cantidad_disponible', 12, 3)->nullable();
            $table->bigInteger('precio_unitario_cotizado')->nullable();
            $table->bigInteger('subtotal_cotizado')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_presupuesto_items');
        Schema::dropIfExists('solicitudes_presupuesto');
        Schema::dropIfExists('proveedores');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'activo']);
        });
    }
};
