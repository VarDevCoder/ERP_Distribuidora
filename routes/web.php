<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\NotaRemisionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\PedidoClienteController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\OrdenEnvioController;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard redirect
    Route::get('/dashboard', function () {
        return redirect()->route('presupuestos.index');
    })->name('dashboard');

    // Presupuestos
    Route::resource('presupuestos', PresupuestoController::class);
    Route::post('presupuestos/{presupuesto}/aprobar', [PresupuestoController::class, 'aprobar'])->name('presupuestos.aprobar');

    // Productos
    Route::resource('productos', ProductoController::class);

    // Notas de Remisión
    Route::resource('notas-remision', NotaRemisionController::class);
    Route::post('notas-remision/{notaRemision}/aplicar', [NotaRemisionController::class, 'aplicar'])->name('notas-remision.aplicar');

    // Inventario
    Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
    Route::get('inventario/kardex/{producto}', [InventarioController::class, 'kardex'])->name('inventario.kardex');

    // Ventas - Documentos
    Route::get('/ventas/{presupuesto}/factura', [VentaController::class, 'mostrarFormularioFactura'])->name('ventas.formulario-factura');
    Route::post('/ventas/{presupuesto}/factura', [VentaController::class, 'registrarFactura'])->name('ventas.registrar-factura');
    Route::post('/ventas/{presupuesto}/contrafactura', [VentaController::class, 'registrarContrafactura'])->name('ventas.registrar-contrafactura');

    // Compras - Documentos
    Route::get('/compras/{presupuesto}/remision', [CompraController::class, 'mostrarFormularioRemision'])->name('compras.formulario-remision');
    Route::post('/compras/{presupuesto}/remision', [CompraController::class, 'registrarRemision'])->name('compras.registrar-remision');
    Route::post('/compras/{presupuesto}/contrafactura', [CompraController::class, 'registrarContrafactura'])->name('compras.registrar-contrafactura');

    // ============================================
    // FLUJO ANKOR - Pedidos de Clientes
    // ============================================
    Route::resource('pedidos-cliente', PedidoClienteController::class);
    Route::post('pedidos-cliente/{pedido}/procesar', [PedidoClienteController::class, 'procesar'])->name('pedidos-cliente.procesar');
    Route::post('pedidos-cliente/{pedido}/cancelar', [PedidoClienteController::class, 'cancelar'])->name('pedidos-cliente.cancelar');
    Route::post('pedidos-cliente/{pedido}/mercaderia-recibida', [PedidoClienteController::class, 'marcarMercaderiaRecibida'])->name('pedidos-cliente.mercaderia-recibida');

    // ============================================
    // FLUJO ANKOR - Órdenes de Compra a Proveedores
    // ============================================
    Route::resource('ordenes-compra', OrdenCompraController::class);
    Route::post('ordenes-compra/{orden}/enviar', [OrdenCompraController::class, 'enviar'])->name('ordenes-compra.enviar');
    Route::post('ordenes-compra/{orden}/confirmar', [OrdenCompraController::class, 'confirmar'])->name('ordenes-compra.confirmar');
    Route::post('ordenes-compra/{orden}/en-transito', [OrdenCompraController::class, 'enTransito'])->name('ordenes-compra.en-transito');
    Route::get('ordenes-compra/{orden}/recepcion', [OrdenCompraController::class, 'formRecepcion'])->name('ordenes-compra.form-recepcion');
    Route::post('ordenes-compra/{orden}/recibir', [OrdenCompraController::class, 'recibirMercaderia'])->name('ordenes-compra.recibir');
    Route::post('ordenes-compra/{orden}/cancelar', [OrdenCompraController::class, 'cancelar'])->name('ordenes-compra.cancelar');

    // ============================================
    // FLUJO ANKOR - Órdenes de Envío a Clientes
    // ============================================
    Route::resource('ordenes-envio', OrdenEnvioController::class)->except(['edit', 'update']);
    Route::post('ordenes-envio/{orden}/lista-despachar', [OrdenEnvioController::class, 'listaDespachar'])->name('ordenes-envio.lista-despachar');
    Route::post('ordenes-envio/{orden}/despachar', [OrdenEnvioController::class, 'despachar'])->name('ordenes-envio.despachar');
    Route::post('ordenes-envio/{orden}/entregar', [OrdenEnvioController::class, 'entregar'])->name('ordenes-envio.entregar');
    Route::post('ordenes-envio/{orden}/devolver', [OrdenEnvioController::class, 'devolver'])->name('ordenes-envio.devolver');
    Route::post('ordenes-envio/{orden}/cancelar', [OrdenEnvioController::class, 'cancelar'])->name('ordenes-envio.cancelar');
});
