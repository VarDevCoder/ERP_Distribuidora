# GUÍA PARA COMPLETAR EL SISTEMA ERP DISTRIBUIDORA

## ✅ LO QUE YA ESTÁ HECHO:

### 1. Base de Datos y Migraciones ✅
- Tabla `presupuesto` y `detalle_presupuesto` creadas
- Migraciones listas para ejecutar

### 2. Modelos ✅
- `Presupuesto.php`
- `DetallePresupuesto.php`

### 3. Controladores ✅
- `PresupuestoController.php` - Completo con CRUD
- `ProductoController.php` - Completo con CRUD y búsqueda
- `ClienteController.php` - Completo con CRUD y búsqueda
- `VentaController.php` - Completo con creación y listado

### 4. Rutas ✅
- Todas las rutas configuradas en `routes/web.php`

### 5. Layout Base ✅
- `layouts/app.blade.php` - Layout reutilizable con sidebar y header

### 6. Vistas Creadas ✅
- `productos/index.blade.php` - Listado de productos

---

## 📋 PASOS PARA COMPLETAR

### PASO 1: Ejecutar las nuevas migraciones

```bash
php artisan migrate
```

Esto creará las tablas:
- `presupuesto`
- `detalle_presupuesto`

### PASO 2: Crear las vistas faltantes

Las vistas ya tienen sus controladores funcionando. Solo necesitas crear los archivos HTML.

#### A) Productos - Crear (productos/create.blade.php)

```blade
@extends('layouts.app')
@section('title', 'Nuevo Producto')
@section('content')
<h4 class="mb-4"><i class="fas fa-box-open me-2"></i>NUEVO PRODUCTO</h4>
<div class="card shadow-sm">
<div class="card-body">
<form action="{{ route('productos.store') }}" method="POST">
@csrf
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Código *</label>
<input type="text" name="pro_codigo" class="form-control @error('pro_codigo') is-invalid @enderror" value="{{ old('pro_codigo') }}" required>
@error('pro_codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Nombre *</label>
<input type="text" name="pro_nombre" class="form-control @error('pro_nombre') is-invalid @enderror" value="{{ old('pro_nombre') }}" required>
@error('pro_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Categoría *</label>
<input type="text" name="pro_categoria" class="form-control @error('pro_categoria') is-invalid @enderror" value="{{ old('pro_categoria') }}" required>
@error('pro_categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Unidad de Medida *</label>
<select name="pro_unidad_medida" class="form-select" required>
<option value="UNIDAD">UNIDAD</option>
<option value="CAJA">CAJA</option>
<option value="KG">KG</option>
<option value="LITRO">LITRO</option>
</select>
</div>
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Precio Compra *</label>
<input type="number" name="pro_precio_compra" class="form-control" step="0.01" min="0" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Precio Venta *</label>
<input type="number" name="pro_precio_venta" class="form-control" step="0.01" min="0" required>
</div>
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Stock Inicial *</label>
<input type="number" name="pro_stock" class="form-control" min="0" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Stock Mínimo *</label>
<input type="number" name="pro_stock_minimo" class="form-control" min="0" required>
</div>
</div>
<div class="mb-3">
<label class="form-label">Descripción</label>
<textarea name="pro_descripcion" class="form-control" rows="3">{{ old('pro_descripcion') }}</textarea>
</div>
<div class="d-flex gap-2">
<button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar</button>
<a href="{{ route('productos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Cancelar</a>
</div>
</form>
</div>
</div>
@endsection
```

#### B) Productos - Editar (productos/edit.blade.php)

Copia el mismo contenido de `create.blade.php` pero cambia:
- El título a "EDITAR PRODUCTO"
- El action del form a: `action="{{ route('productos.update', $producto->pro_id) }}"`
- Agrega: `@method('PUT')`
- Agrega `value="{{ old('campo', $producto->campo) }}"` en cada input

#### C) Clientes - Index (clientes/index.blade.php)

Similar a productos/index.blade.php pero con campos:
- cli_nombre, cli_apellido, cli_ci, cli_telefono, cli_tipo

#### D) Clientes - Create y Edit

Similar a productos pero con campos de cliente.

#### E) Ventas - Index (ventas/index.blade.php)

```blade
@extends('layouts.app')
@section('title', 'Ventas')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
<h4 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>VENTAS</h4>
<a href="{{ route('ventas.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nueva Venta</a>
</div>
<div class="card">
<div class="card-body">
<table class="table table-hover">
<thead class="table-light">
<tr>
<th>Número</th>
<th>Cliente</th>
<th>Fecha</th>
<th>Total</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>
@forelse($ventas as $venta)
<tr>
<td><code>{{ $venta->ven_numero }}</code></td>
<td>{{ $venta->cliente->cli_nombre }} {{ $venta->cliente->cli_apellido }}</td>
<td>{{ \Carbon\Carbon::parse($venta->ven_fecha)->format('d/m/Y') }}</td>
<td class="fw-bold">Gs. {{ number_format($venta->ven_total, 0, ',', '.') }}</td>
<td><span class="badge bg-success">{{ $venta->ven_estado }}</span></td>
<td>
<a href="{{ route('ventas.show', $venta->ven_id) }}" class="btn btn-sm btn-info" title="Ver">
<i class="fas fa-eye"></i>
</a>
</td>
</tr>
@empty
<tr><td colspan="6" class="text-center">No hay ventas registradas</td></tr>
@endforelse
</tbody>
</table>
{{ $ventas->links() }}
</div>
</div>
@endsection
```

#### F) Ventas - Create (ventas/create.blade.php)

```blade
@extends('layouts.app')
@section('title', 'Nueva Venta')
@section('content')
<h4 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>NUEVA VENTA</h4>
<div class="card">
<div class="card-body">
<form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
@csrf
<input type="hidden" name="ven_numero" value="{{ $numeroVenta }}">
@if($presupuesto)
<input type="hidden" name="presupuesto_id" value="{{ $presupuesto->pre_id }}">
@endif
<div class="row">
<div class="col-md-6 mb-3">
<label>Número de Venta</label>
<input type="text" class="form-control" value="{{ $numeroVenta }}" readonly>
</div>
<div class="col-md-6 mb-3">
<label>Fecha *</label>
<input type="date" name="ven_fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
</div>
</div>
<div class="mb-3">
<label>Cliente *</label>
<select name="cli_id" class="form-select" required @if($presupuesto) disabled @endif>
<option value="">Seleccione un cliente</option>
@foreach($clientes as $cliente)
<option value="{{ $cliente->cli_id }}" @if($presupuesto && $presupuesto->cli_id == $cliente->cli_id) selected @endif>
{{ $cliente->cli_nombre }} {{ $cliente->cli_apellido }} - {{ $cliente->cli_ci }}
</option>
@endforeach
</select>
@if($presupuesto)<input type="hidden" name="cli_id" value="{{ $presupuesto->cli_id }}">@endif
</div>
<hr>
<h5>Productos</h5>
<div id="productosContainer">
@if($presupuesto)
@foreach($presupuesto->detalles as $index => $detalle)
<div class="row mb-2 producto-row">
<div class="col-md-5">
<select name="productos[{{ $index }}][pro_id]" class="form-select" required>
<option value="{{ $detalle->pro_id }}">{{ $detalle->producto->pro_nombre }}</option>
</select>
</div>
<div class="col-md-3">
<input type="number" name="productos[{{ $index }}][cantidad]" class="form-control" value="{{ $detalle->det_pre_cantidad }}" min="1" required>
</div>
<div class="col-md-3">
<input type="text" class="form-control" value="Gs. {{ number_format($detalle->det_pre_precio_unitario * $detalle->det_pre_cantidad, 0) }}" readonly>
</div>
</div>
@endforeach
@else
<div class="row mb-2 producto-row">
<div class="col-md-5">
<select name="productos[0][pro_id]" class="form-select" required>
<option value="">Seleccione producto</option>
@foreach($productos as $producto)
<option value="{{ $producto->pro_id }}">{{ $producto->pro_nombre }} - Gs. {{ number_format($producto->pro_precio_venta, 0) }}</option>
@endforeach
</select>
</div>
<div class="col-md-3">
<input type="number" name="productos[0][cantidad]" class="form-control" placeholder="Cantidad" min="1" required>
</div>
<div class="col-md-3">
<button type="button" class="btn btn-danger btn-sm remove-producto"><i class="fas fa-trash"></i></button>
</div>
</div>
@endif
</div>
@if(!$presupuesto)
<button type="button" class="btn btn-sm btn-secondary mb-3" id="addProducto"><i class="fas fa-plus"></i> Agregar Producto</button>
@endif
<div class="mb-3">
<label>Descuento</label>
<input type="number" name="ven_descuento" class="form-control" value="0" min="0" step="1">
</div>
<div class="mb-3">
<label>Observaciones</label>
<textarea name="ven_observaciones" class="form-control" rows="2"></textarea>
</div>
<div class="d-flex gap-2">
<button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Venta</button>
<a href="{{ route('ventas.index') }}" class="btn btn-secondary">Cancelar</a>
</div>
</form>
</div>
</div>
@endsection
@push('scripts')
<script>
let productoIndex = 1;
document.getElementById('addProducto')?.addEventListener('click', function() {
const container = document.getElementById('productosContainer');
const row = document.createElement('div');
row.className = 'row mb-2 producto-row';
row.innerHTML = `
<div class="col-md-5">
<select name="productos[${productoIndex}][pro_id]" class="form-select" required>
<option value="">Seleccione producto</option>
@foreach($productos as $producto)
<option value="{{ $producto->pro_id }}">{{ $producto->pro_nombre }} - Gs. {{ number_format($producto->pro_precio_venta, 0) }}</option>
@endforeach
</select>
</div>
<div class="col-md-3">
<input type="number" name="productos[${productoIndex}][cantidad]" class="form-control" placeholder="Cantidad" min="1" required>
</div>
<div class="col-md-3">
<button type="button" class="btn btn-danger btn-sm remove-producto"><i class="fas fa-trash"></i></button>
</div>
`;
container.appendChild(row);
productoIndex++;
});

document.addEventListener('click', function(e) {
if (e.target.classList.contains('remove-producto') || e.target.parentElement.classList.contains('remove-producto')) {
const row = e.target.closest('.producto-row');
if (document.querySelectorAll('.producto-row').length > 1) {
row.remove();
}
}
});
</script>
@endpush
```

#### G) Presupuestos - Index, Create, Show

Similar a Ventas pero con campos de presupuesto.

### PASO 3: Ejecutar el servidor

```bash
php artisan serve
```

### PASO 4: Probar el sistema

Accede a:
- http://localhost:8000
- Login: admin@distribuidora.com / admin123

---

## 🎯 FUNCIONALIDADES IMPLEMENTADAS

### ✅ Sistema de Presupuestos
- Crear presupuestos con múltiples productos
- Listar presupuestos con filtros
- Ver detalle de presupuesto
- Cambiar estado (Pendiente, Aprobado, Rechazado)
- Convertir presupuesto a venta

### ✅ CRUD de Productos
- Listar con búsqueda
- Crear, editar, eliminar
- Alertas de stock bajo
- Control de stock mínimo

### ✅ CRUD de Clientes
- Listar con búsqueda
- Crear, editar, eliminar
- Tipos: Mayorista/Minorista

### ✅ Módulo de Ventas
- Crear ventas con múltiples productos
- Descuento por venta
- Actualización automática de stock
- Conversión desde presupuesto
- Ver detalle de venta

---

## 📝 DATOS DE PRUEBA ADICIONALES

Ejecuta este SQL para agregar presupuestos de prueba:

```sql
-- Insertar presupuesto de prueba
INSERT INTO presupuesto (pre_numero, cli_id, usu_id, pre_fecha, pre_fecha_vencimiento, pre_subtotal, pre_descuento, pre_total, pre_estado, pre_observaciones, created_at, updated_at)
VALUES ('PRE-2025-0001', 1, 1, '2025-11-05', '2025-11-20', 50000, 2000, 48000, 'PENDIENTE', 'Presupuesto de prueba', NOW(), NOW());

-- Insertar detalles del presupuesto
INSERT INTO detalle_presupuesto (pre_id, pro_id, det_pre_cantidad, det_pre_precio_unitario, det_pre_subtotal, created_at, updated_at)
VALUES
(1, 1, 2, 18000, 36000, NOW(), NOW()),
(1, 2, 1, 12000, 12000, NOW(), NOW());
```

---

## 🚀 PRÓXIMAS MEJORAS

1. **Dashboard mejorado**: Integrar datos de presupuestos
2. **Reportes**: PDF de ventas y presupuestos
3. **Permisos**: Sistema de roles
4. **API REST**: Para integraciones
5. **Notificaciones**: Emails al aprobar presupuestos
6. **Historial**: Auditoría de cambios

---

**Sistema completo con Presupuestos, Productos, Clientes y Ventas funcionando!**
