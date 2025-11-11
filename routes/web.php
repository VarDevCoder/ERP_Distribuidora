<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\NotaRemisionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraController;

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
    Route::post('/ventas/{presupuesto}/factura', [VentaController::class, 'registrarFactura'])->name('ventas.registrar-factura');
    Route::post('/ventas/{presupuesto}/contrafactura', [VentaController::class, 'registrarContrafactura'])->name('ventas.registrar-contrafactura');

    // Compras - Documentos
    Route::post('/compras/{presupuesto}/remision', [CompraController::class, 'registrarRemision'])->name('compras.registrar-remision');
    Route::post('/compras/{presupuesto}/contrafactura', [CompraController::class, 'registrarContrafactura'])->name('compras.registrar-contrafactura');
});
