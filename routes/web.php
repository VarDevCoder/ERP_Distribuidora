<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\InventarioController;

// Autenticación
Route::get('/', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Productos
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');

// Clientes
Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
Route::get('/clientes/crear', [ClienteController::class, 'create'])->name('clientes.create');
Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
Route::get('/clientes/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{id}', [ClienteController::class, 'update'])->name('clientes.update');
Route::delete('/clientes/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');

// Ventas
Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');

// Presupuestos
Route::get('/presupuestos', [PresupuestoController::class, 'index'])->name('presupuestos.index');
Route::get('/presupuestos/crear', [PresupuestoController::class, 'create'])->name('presupuestos.create');
Route::post('/presupuestos', [PresupuestoController::class, 'store'])->name('presupuestos.store');
Route::get('/presupuestos/{id}', [PresupuestoController::class, 'show'])->name('presupuestos.show');
Route::post('/presupuestos/{id}/estado', [PresupuestoController::class, 'updateEstado'])->name('presupuestos.updateEstado');
Route::get('/presupuestos/{id}/convertir', [PresupuestoController::class, 'convertirVenta'])->name('presupuestos.convertir');

// Proveedores
Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::get('/proveedores/crear', [ProveedorController::class, 'create'])->name('proveedores.create');
Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
Route::get('/proveedores/{id}/editar', [ProveedorController::class, 'edit'])->name('proveedores.edit');
Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

// Compras
Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
Route::get('/compras/crear', [CompraController::class, 'create'])->name('compras.create');
Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
Route::get('/compras/{id}', [CompraController::class, 'show'])->name('compras.show');
Route::post('/compras/{id}/anular', [CompraController::class, 'anular'])->name('compras.anular');

// Inventario
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
Route::get('/inventario/kardex/{id}', [InventarioController::class, 'kardex'])->name('inventario.kardex');
Route::get('/inventario/ajuste', [InventarioController::class, 'ajusteForm'])->name('inventario.ajuste');
Route::post('/inventario/ajuste', [InventarioController::class, 'ajusteStore'])->name('inventario.ajuste.store');
Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
