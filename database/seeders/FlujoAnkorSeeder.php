<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\PedidoCliente;
use App\Models\PedidoClienteItem;
use App\Models\SolicitudPresupuesto;
use App\Models\SolicitudPresupuestoItem;
use App\Models\OrdenCompra;
use App\Models\OrdenCompraItem;
use App\Models\OrdenEnvio;
use App\Models\OrdenEnvioItem;

/**
 * Seeder del flujo completo ANKOR
 *
 * Crea pedidos en diferentes estados para probar:
 * 1. Pedido RECIBIDO (nuevo, sin procesar)
 * 2. Pedido EN_PROCESO con solicitudes de presupuesto enviadas
 * 3. Pedido PRESUPUESTADO con cotizaciones recibidas
 * 4. Pedido ORDEN_COMPRA con OC generada
 * 5. Pedido MERCADERIA_RECIBIDA listo para envio
 * 6. Pedido ENVIADO en transito
 * 7. Pedido ENTREGADO completado
 */
class FlujoAnkorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@ankor.com')->first();
        $ankorUser = User::where('email', 'usuario@ankor.com')->first();
        $proveedor1 = Proveedor::where('razon_social', 'like', '%Martillo%')->first();
        $proveedor2 = Proveedor::where('razon_social', 'like', '%TechParts%')->first();

        $productos = Producto::all();

        if (!$admin || !$ankorUser || !$proveedor1 || $productos->isEmpty()) {
            $this->command->warn('Ejecuta primero UserSeeder y ProductoSeeder');
            return;
        }

        // =====================================================
        // 1. PEDIDO RECIBIDO - Nuevo, sin procesar
        // =====================================================
        $pedido1 = PedidoCliente::create([
            'cliente_nombre' => 'Constructora ABC S.A.',
            'cliente_ruc' => '80111222-3',
            'cliente_telefono' => '021-777-8888',
            'cliente_email' => 'compras@constructoraabc.com',
            'cliente_direccion' => 'Av. Eusebio Ayala 5500, Asuncion',
            'fecha_pedido' => now(),
            'fecha_entrega_solicitada' => now()->addDays(15),
            'estado' => PedidoCliente::ESTADO_RECIBIDO,
            'notas' => 'Cliente nuevo, primer pedido',
            'usuario_id' => $ankorUser->id,
        ]);

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido1->id,
            'producto_id' => $productos->where('codigo', 'MAT-001')->first()->id,
            'cantidad' => 50,
            'precio_unitario' => 72000,
        ]);

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido1->id,
            'producto_id' => $productos->where('codigo', 'MAT-003')->first()->id,
            'cantidad' => 100,
            'precio_unitario' => 45000,
        ]);

        // =====================================================
        // 2. PEDIDO EN_PROCESO - Con solicitudes enviadas
        // =====================================================
        $pedido2 = PedidoCliente::create([
            'cliente_nombre' => 'Ferreteria Don Pedro',
            'cliente_ruc' => '80333444-5',
            'cliente_telefono' => '0981-555-666',
            'cliente_email' => 'donpedro@gmail.com',
            'cliente_direccion' => 'Mcal. Estigarribia 1200, Luque',
            'fecha_pedido' => now()->subDays(2),
            'fecha_entrega_solicitada' => now()->addDays(10),
            'estado' => PedidoCliente::ESTADO_EN_PROCESO,
            'notas' => 'Pedido urgente',
            'usuario_id' => $ankorUser->id,
        ]);

        $prod_taladro = $productos->where('codigo', 'HERR-001')->first();
        $prod_amoladora = $productos->where('codigo', 'HERR-002')->first();

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido2->id,
            'producto_id' => $prod_taladro->id,
            'cantidad' => 10,
            'precio_unitario' => $prod_taladro->precio_venta,
        ]);

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido2->id,
            'producto_id' => $prod_amoladora->id,
            'cantidad' => 15,
            'precio_unitario' => $prod_amoladora->precio_venta,
        ]);

        // Crear solicitud de presupuesto ENVIADA
        $solicitud1 = SolicitudPresupuesto::create([
            'pedido_cliente_id' => $pedido2->id,
            'proveedor_id' => $proveedor1->id,
            'usuario_id' => $admin->id,
            'fecha_solicitud' => now()->subDays(1),
            'fecha_limite_respuesta' => now()->addDays(3),
            'estado' => SolicitudPresupuesto::ESTADO_ENVIADA,
            'mensaje_solicitud' => 'Solicitud urgente de herramientas para cliente mayorista',
        ]);

        SolicitudPresupuestoItem::create([
            'solicitud_presupuesto_id' => $solicitud1->id,
            'producto_id' => $prod_taladro->id,
            'cantidad_solicitada' => 10,
        ]);

        SolicitudPresupuestoItem::create([
            'solicitud_presupuesto_id' => $solicitud1->id,
            'producto_id' => $prod_amoladora->id,
            'cantidad_solicitada' => 15,
        ]);

        // =====================================================
        // 3. PEDIDO PRESUPUESTADO - Con cotizacion recibida
        // =====================================================
        $pedido3 = PedidoCliente::create([
            'cliente_nombre' => 'Electricidad Total',
            'cliente_ruc' => '80555666-7',
            'cliente_telefono' => '021-300-4000',
            'cliente_email' => 'compras@electricidadtotal.com',
            'cliente_direccion' => 'Av. Artigas 800, Fernando de la Mora',
            'fecha_pedido' => now()->subDays(5),
            'fecha_entrega_solicitada' => now()->addDays(7),
            'estado' => PedidoCliente::ESTADO_PRESUPUESTADO,
            'notas' => 'Cliente frecuente, buen historial de pago',
            'usuario_id' => $ankorUser->id,
        ]);

        $prod_cable = $productos->where('codigo', 'ELEC-001')->first();
        $prod_termica = $productos->where('codigo', 'ELEC-002')->first();

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido3->id,
            'producto_id' => $prod_cable->id,
            'cantidad' => 20,
            'precio_unitario' => $prod_cable->precio_venta,
        ]);

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido3->id,
            'producto_id' => $prod_termica->id,
            'cantidad' => 30,
            'precio_unitario' => $prod_termica->precio_venta,
        ]);

        // Solicitud COTIZADA
        $solicitud2 = SolicitudPresupuesto::create([
            'pedido_cliente_id' => $pedido3->id,
            'proveedor_id' => $proveedor2 ? $proveedor2->id : $proveedor1->id,
            'usuario_id' => $admin->id,
            'fecha_solicitud' => now()->subDays(4),
            'fecha_limite_respuesta' => now()->addDays(1),
            'fecha_respuesta' => now()->subDays(2),
            'estado' => SolicitudPresupuesto::ESTADO_COTIZADA,
            'mensaje_solicitud' => 'Cotizacion para materiales electricos',
            'respuesta_proveedor' => 'Tenemos stock disponible. Entrega en 5 dias habiles.',
            'total_cotizado' => 5250000,
            'dias_entrega_estimados' => 5,
        ]);

        SolicitudPresupuestoItem::create([
            'solicitud_presupuesto_id' => $solicitud2->id,
            'producto_id' => $prod_cable->id,
            'cantidad_solicitada' => 20,
            'tiene_stock' => true,
            'cantidad_disponible' => 20,
            'precio_unitario_cotizado' => 175000,
            'subtotal_cotizado' => 3500000,
        ]);

        SolicitudPresupuestoItem::create([
            'solicitud_presupuesto_id' => $solicitud2->id,
            'producto_id' => $prod_termica->id,
            'cantidad_solicitada' => 30,
            'tiene_stock' => true,
            'cantidad_disponible' => 30,
            'precio_unitario_cotizado' => 58000,
            'subtotal_cotizado' => 1750000,
        ]);

        $solicitud2->calcularTotal();

        // =====================================================
        // 4. PEDIDO ORDEN_COMPRA - OC generada
        // =====================================================
        $pedido4 = PedidoCliente::create([
            'cliente_nombre' => 'Pintureria Los Colores',
            'cliente_ruc' => '80777888-9',
            'cliente_telefono' => '0971-222-333',
            'cliente_email' => 'loscolores@gmail.com',
            'cliente_direccion' => 'Calle Palma 500, San Lorenzo',
            'fecha_pedido' => now()->subDays(7),
            'fecha_entrega_solicitada' => now()->addDays(5),
            'fecha_entrega_estimada' => now()->addDays(6),
            'estado' => PedidoCliente::ESTADO_ORDEN_COMPRA,
            'notas' => 'Pedido de pintura para obra grande',
            'usuario_id' => $admin->id,
        ]);

        $prod_latex = $productos->where('codigo', 'PINT-001')->first();
        $prod_esmalte = $productos->where('codigo', 'PINT-002')->first();

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido4->id,
            'producto_id' => $prod_latex->id,
            'cantidad' => 15,
            'precio_unitario' => $prod_latex->precio_venta,
        ]);

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido4->id,
            'producto_id' => $prod_esmalte->id,
            'cantidad' => 20,
            'precio_unitario' => $prod_esmalte->precio_venta,
        ]);

        // Crear Orden de Compra
        $ordenCompra1 = OrdenCompra::create([
            'pedido_cliente_id' => $pedido4->id,
            'proveedor_nombre' => $proveedor1->razon_social,
            'proveedor_ruc' => $proveedor1->ruc,
            'proveedor_telefono' => $proveedor1->telefono,
            'proveedor_email' => $proveedor1->user->email,
            'proveedor_direccion' => $proveedor1->direccion,
            'fecha_orden' => now()->subDays(3),
            'fecha_entrega_esperada' => now()->addDays(2),
            'estado' => OrdenCompra::ESTADO_CONFIRMADA,
            'notas' => 'Proveedor confirmo disponibilidad',
            'usuario_id' => $admin->id,
        ]);

        OrdenCompraItem::create([
            'orden_compra_id' => $ordenCompra1->id,
            'producto_id' => $prod_latex->id,
            'cantidad_solicitada' => 15,
            'cantidad_recibida' => 0,
            'precio_unitario' => 320000,
        ]);

        OrdenCompraItem::create([
            'orden_compra_id' => $ordenCompra1->id,
            'producto_id' => $prod_esmalte->id,
            'cantidad_solicitada' => 20,
            'cantidad_recibida' => 0,
            'precio_unitario' => 95000,
        ]);

        // =====================================================
        // 5. PEDIDO MERCADERIA_RECIBIDA - Listo para envio
        // =====================================================
        $pedido5 = PedidoCliente::create([
            'cliente_nombre' => 'Inmobiliaria del Sur',
            'cliente_ruc' => '80999000-1',
            'cliente_telefono' => '021-600-7000',
            'cliente_email' => 'proyectos@inmobsur.com',
            'cliente_direccion' => 'Av. Santa Teresa 3000, Encarnacion',
            'fecha_pedido' => now()->subDays(10),
            'fecha_entrega_solicitada' => now()->addDays(2),
            'fecha_entrega_estimada' => now()->addDays(2),
            'estado' => PedidoCliente::ESTADO_MERCADERIA_RECIBIDA,
            'notas' => 'Mercaderia lista en deposito',
            'usuario_id' => $ankorUser->id,
        ]);

        $prod_destornilladores = $productos->where('codigo', 'HERR-003')->first();

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido5->id,
            'producto_id' => $prod_destornilladores->id,
            'cantidad' => 25,
            'precio_unitario' => $prod_destornilladores->precio_venta,
        ]);

        // OC completada
        $ordenCompra2 = OrdenCompra::create([
            'pedido_cliente_id' => $pedido5->id,
            'proveedor_nombre' => $proveedor1->razon_social,
            'proveedor_ruc' => $proveedor1->ruc,
            'proveedor_telefono' => $proveedor1->telefono,
            'proveedor_email' => $proveedor1->user->email,
            'proveedor_direccion' => $proveedor1->direccion,
            'fecha_orden' => now()->subDays(8),
            'fecha_entrega_esperada' => now()->subDays(2),
            'fecha_recepcion' => now()->subDays(1),
            'estado' => OrdenCompra::ESTADO_RECIBIDA_COMPLETA,
            'notas' => 'Mercaderia recibida completa',
            'usuario_id' => $admin->id,
        ]);

        OrdenCompraItem::create([
            'orden_compra_id' => $ordenCompra2->id,
            'producto_id' => $prod_destornilladores->id,
            'cantidad_solicitada' => 25,
            'cantidad_recibida' => 25,
            'precio_unitario' => 45000,
        ]);

        // =====================================================
        // 6. PEDIDO ENVIADO - En transito
        // =====================================================
        $pedido6 = PedidoCliente::create([
            'cliente_nombre' => 'Cooperativa Multiactiva',
            'cliente_ruc' => '80123456-0',
            'cliente_telefono' => '0983-111-222',
            'cliente_email' => 'compras@coopma.org',
            'cliente_direccion' => 'Ruta 2 Km 45, Caacupe',
            'fecha_pedido' => now()->subDays(12),
            'fecha_entrega_solicitada' => now(),
            'fecha_entrega_estimada' => now(),
            'estado' => PedidoCliente::ESTADO_ENVIADO,
            'notas' => 'Enviado via transporte propio',
            'usuario_id' => $ankorUser->id,
        ]);

        $prod_arena = $productos->where('codigo', 'MAT-002')->first();

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido6->id,
            'producto_id' => $prod_arena->id,
            'cantidad' => 5,
            'precio_unitario' => $prod_arena->precio_venta,
        ]);

        // Orden de envio EN_TRANSITO
        $ordenEnvio1 = OrdenEnvio::create([
            'pedido_cliente_id' => $pedido6->id,
            'direccion_entrega' => $pedido6->cliente_direccion,
            'contacto_entrega' => 'Sr. Martinez',
            'telefono_entrega' => $pedido6->cliente_telefono,
            'fecha_generacion' => now()->subDays(1),
            'fecha_envio' => now(),
            'estado' => OrdenEnvio::ESTADO_EN_TRANSITO,
            'metodo_envio' => 'Transporte propio',
            'numero_guia' => 'TP-2025-001',
            'transportista' => 'Camion Empresa',
            'notas' => 'Salida 8:00 AM',
            'usuario_id' => $admin->id,
        ]);

        OrdenEnvioItem::create([
            'orden_envio_id' => $ordenEnvio1->id,
            'producto_id' => $prod_arena->id,
            'cantidad' => 5,
        ]);

        // =====================================================
        // 7. PEDIDO ENTREGADO - Completado
        // =====================================================
        $pedido7 = PedidoCliente::create([
            'cliente_nombre' => 'Hospital Regional',
            'cliente_ruc' => '80654321-9',
            'cliente_telefono' => '021-400-5000',
            'cliente_email' => 'mantenimiento@hospital.gov',
            'cliente_direccion' => 'Av. Venezuela 1000, Asuncion',
            'fecha_pedido' => now()->subDays(20),
            'fecha_entrega_solicitada' => now()->subDays(5),
            'fecha_entrega_estimada' => now()->subDays(5),
            'estado' => PedidoCliente::ESTADO_ENTREGADO,
            'notas' => 'Entrega exitosa - Cliente satisfecho',
            'usuario_id' => $admin->id,
        ]);

        $prod_sierra = $productos->where('codigo', 'HERR-004')->first();

        PedidoClienteItem::create([
            'pedido_cliente_id' => $pedido7->id,
            'producto_id' => $prod_sierra->id,
            'cantidad' => 2,
            'precio_unitario' => $prod_sierra->precio_venta,
        ]);

        // Orden de envio ENTREGADO
        $ordenEnvio2 = OrdenEnvio::create([
            'pedido_cliente_id' => $pedido7->id,
            'direccion_entrega' => $pedido7->cliente_direccion,
            'contacto_entrega' => 'Ing. Gonzalez - Mantenimiento',
            'telefono_entrega' => $pedido7->cliente_telefono,
            'fecha_generacion' => now()->subDays(7),
            'fecha_envio' => now()->subDays(6),
            'fecha_entrega' => now()->subDays(5),
            'estado' => OrdenEnvio::ESTADO_ENTREGADO,
            'metodo_envio' => 'Delivery express',
            'numero_guia' => 'EXP-2025-055',
            'transportista' => 'Delivery Express PY',
            'notas' => 'Entregado sin novedad',
            'observaciones_entrega' => 'Recibido por Ing. Gonzalez. Firma digital.',
            'usuario_id' => $admin->id,
        ]);

        OrdenEnvioItem::create([
            'orden_envio_id' => $ordenEnvio2->id,
            'producto_id' => $prod_sierra->id,
            'cantidad' => 2,
        ]);

        $this->command->info('Flujo ANKOR creado: 7 pedidos en diferentes estados');
    }
}
