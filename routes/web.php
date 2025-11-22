<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\PedidoClienteController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\OrdenEnvioController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\SolicitudPresupuestoController;
use App\Http\Controllers\ProveedorPortalController;

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
    // Dashboard redirect según rol
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->esProveedor()) {
            return redirect()->route('proveedor.dashboard');
        }

        // Admin y AnkorUser van a pedidos
        return redirect()->route('pedidos-cliente.index');
    })->name('dashboard');

    // ============================================
    // PORTAL PROVEEDOR (Solo rol: proveedor)
    // ============================================
    Route::prefix('proveedor')
        ->name('proveedor.')
        ->middleware('role:proveedor')
        ->group(function () {
            Route::get('/', [ProveedorPortalController::class, 'dashboard'])->name('dashboard');
            Route::get('/solicitudes', [ProveedorPortalController::class, 'solicitudes'])->name('solicitudes');
            Route::get('/solicitud/{solicitud}', [ProveedorPortalController::class, 'verSolicitud'])->name('solicitud.ver');
            Route::get('/solicitud/{solicitud}/responder', [ProveedorPortalController::class, 'formResponder'])->name('solicitud.responder');
            Route::post('/solicitud/{solicitud}/cotizar', [ProveedorPortalController::class, 'enviarCotizacion'])->name('solicitud.cotizar');
            Route::post('/solicitud/{solicitud}/sin-stock', [ProveedorPortalController::class, 'marcarSinStock'])->name('solicitud.sin-stock');
            Route::get('/perfil', [ProveedorPortalController::class, 'perfil'])->name('perfil');
            Route::put('/perfil', [ProveedorPortalController::class, 'actualizarPerfil'])->name('perfil.actualizar');
        });

    // ============================================
    // FLUJO ANKOR (Solo rol: ankor_user)
    // Admin tiene acceso automático vía middleware
    // ============================================
    Route::middleware('role:ankor_user')->group(function () {

        // GESTIÓN DE PROVEEDORES
        Route::resource('proveedores', ProveedorController::class);
        Route::post('proveedores/{proveedor}/toggle-activo', [ProveedorController::class, 'toggleActivo'])->name('proveedores.toggle-activo');

        // SOLICITUDES DE COTIZACIÓN
        Route::resource('solicitudes-presupuesto', SolicitudPresupuestoController::class)->except(['edit', 'update', 'destroy']);
        Route::post('solicitudes-presupuesto/{solicitud}/aceptar', [SolicitudPresupuestoController::class, 'aceptar'])->name('solicitudes-presupuesto.aceptar');
        Route::post('solicitudes-presupuesto/{solicitud}/rechazar', [SolicitudPresupuestoController::class, 'rechazar'])->name('solicitudes-presupuesto.rechazar');

        // PRODUCTOS E INVENTARIO
        Route::resource('productos', ProductoController::class);
        Route::get('inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
        Route::get('inventario/kardex/{producto}', [InventarioController::class, 'kardex'])->name('inventario.kardex');

        // PEDIDOS DE CLIENTES
        Route::resource('pedidos-cliente', PedidoClienteController::class);
        Route::post('pedidos-cliente/{pedido}/procesar', [PedidoClienteController::class, 'procesar'])->name('pedidos-cliente.procesar');
        Route::post('pedidos-cliente/{pedido}/cancelar', [PedidoClienteController::class, 'cancelar'])->name('pedidos-cliente.cancelar');
        Route::post('pedidos-cliente/{pedido}/mercaderia-recibida', [PedidoClienteController::class, 'marcarMercaderiaRecibida'])->name('pedidos-cliente.mercaderia-recibida');

        // ÓRDENES DE COMPRA
        Route::resource('ordenes-compra', OrdenCompraController::class);
        Route::post('ordenes-compra/{orden}/enviar', [OrdenCompraController::class, 'enviar'])->name('ordenes-compra.enviar');
        Route::post('ordenes-compra/{orden}/confirmar', [OrdenCompraController::class, 'confirmar'])->name('ordenes-compra.confirmar');
        Route::post('ordenes-compra/{orden}/en-transito', [OrdenCompraController::class, 'enTransito'])->name('ordenes-compra.en-transito');
        Route::get('ordenes-compra/{orden}/recepcion', [OrdenCompraController::class, 'formRecepcion'])->name('ordenes-compra.form-recepcion');
        Route::post('ordenes-compra/{orden}/recibir', [OrdenCompraController::class, 'recibirMercaderia'])->name('ordenes-compra.recibir');
        Route::post('ordenes-compra/{orden}/cancelar', [OrdenCompraController::class, 'cancelar'])->name('ordenes-compra.cancelar');

        // ÓRDENES DE ENVÍO
        Route::resource('ordenes-envio', OrdenEnvioController::class)->except(['edit', 'update']);
        Route::post('ordenes-envio/{orden}/lista-despachar', [OrdenEnvioController::class, 'listaDespachar'])->name('ordenes-envio.lista-despachar');
        Route::post('ordenes-envio/{orden}/despachar', [OrdenEnvioController::class, 'despachar'])->name('ordenes-envio.despachar');
        Route::post('ordenes-envio/{orden}/entregar', [OrdenEnvioController::class, 'entregar'])->name('ordenes-envio.entregar');
        Route::post('ordenes-envio/{orden}/devolver', [OrdenEnvioController::class, 'devolver'])->name('ordenes-envio.devolver');
        Route::post('ordenes-envio/{orden}/cancelar', [OrdenEnvioController::class, 'cancelar'])->name('ordenes-envio.cancelar');
    });
});
