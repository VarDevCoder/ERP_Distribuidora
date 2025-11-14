# Mejoras Implementadas - ERP Distribuidora

## Resumen de Cambios

Se implementaron mejoras críticas en tres áreas principales:
1. **Adaptación a Guaraníes (moneda paraguaya)**
2. **Cantidades reales en facturas/remisiones**
3. **Trazabilidad mejorada de inventario**

---

## 1. Guaraníes (Sin Decimales)

### Cambios Realizados:
- ✅ Migraci\u00f3n para convertir columnas `decimal` a `bigInteger`
- ✅ Actualización de modelos para usar casts `integer`
- ✅ Cálculos sin IVA (por ahora)
- ✅ Redondeo automático en cálculos

### Archivos Modificados:
- `database/migrations/2025_11_14_014036_convertir_moneda_a_guaranies.php`
- `app/Models/Producto.php` → `precio_compra`, `precio_venta` ahora son `integer`
- `app/Models/Presupuesto.php` → `subtotal`, `descuento`, `total` ahora son `integer`
- `app/Models/PresupuestoItem.php` → `precio_unitario`, `subtotal` ahora son `integer`

### Ejecutar Migración:
```bash
php artisan migrate
```

---

## 2. Cantidades Reales en Documentos

### Problema Resuelto:
Antes: Al registrar factura o remisión, el sistema usaba automáticamente las cantidades del presupuesto.

Ahora: Puedes ingresar las cantidades **reales** que llegaron/se enviaron.

### Flujo Actualizado:

#### COMPRAS:
1. Crear Presupuesto COMPRA → Aprobar
2. **Registrar Remisión** → Ingresar cantidades reales + motivos de diferencia
3. Registrar Contrafactura → Se aplica al inventario con cantidades reales

#### VENTAS:
1. Crear Presupuesto VENTA → Aprobar
2. **Registrar Factura** → Ingresar cantidades reales + motivos de diferencia
3. Registrar Contrafactura → Se aplica al inventario con cantidades reales

### Nuevas Rutas:
```php
// Ver formulario para registrar factura con cantidades reales
GET  /ventas/{presupuesto}/factura

// Ver formulario para registrar remisión con cantidades reales
GET  /compras/{presupuesto}/remision
```

### Nueva Tabla: `cantidades_reales_documentos`
Almacena las cantidades reales ingresadas al registrar factura/remisión:
- `cantidad_presupuestada`: Lo que se pidió originalmente
- `cantidad_real`: Lo que realmente llegó/se envió
- `diferencia`: Cálculo automático
- `motivo_diferencia`: Explicación de la diferencia
- `usuario_id`: Quién registró
- `timestamps`: Cuándo se registró

---

## 3. Trazabilidad de Inventario

### Mejoras Implementadas:

#### a) Registro de Diferencias
Cada movimiento de inventario ahora registra:
- ✅ Cantidad presupuestada
- ✅ Cantidad real aplicada
- ✅ Diferencia
- ✅ Motivo de la diferencia

#### b) Hash de Verificación
Cada movimiento genera un **hash SHA-256** para prevenir alteraciones:
```php
$movimiento->verificarIntegridad(); // true si no fue alterado
```

#### c) Auditoría Completa
Cada movimiento registra:
- Usuario que lo creó (`usuario_id`)
- Timestamp exacto (`created_at`)
- Stock anterior y nuevo
- Todos los números de documentos (factura, contrafactura, remisión)

### Campos Nuevos en `movimientos_inventario`:
- `cantidad_presupuestada`
- `diferencia`
- `motivo_diferencia`
- `hash_verificacion`

---

## Archivos Creados

### Migraciones:
1. `database/migrations/2025_11_14_014036_convertir_moneda_a_guaranies.php`
2. `database/migrations/2025_11_14_014040_crear_tabla_cantidades_reales_documentos.php`
3. `database/migrations/2025_11_14_014044_agregar_diferencias_a_movimientos_inventario.php`

### Modelos:
- `app/Models/CantidadRealDocumento.php` (nuevo)

### Servicios Actualizados:
- `app/Services/CompraService.php`
- `app/Services/VentaService.php`
- `app/Services/InventoryService.php`

### Controllers Actualizados:
- `app/Http/Controllers/CompraController.php`
- `app/Http/Controllers/VentaController.php`

### Tests Creados:
- `tests/Unit/Unit/Models/MovimientoInventarioTest.php`
- `tests/Unit/Unit/Models/PresupuestoTest.php`

---

## Cómo Usar las Nuevas Funcionalidades

### Ejemplo: Registrar Compra con Cantidades Reales

1. **Crear Presupuesto de Compra PC-2025-0001:**
   - Producto A: 100 unidades
   - Producto B: 50 unidades

2. **Aprobar el Presupuesto**

3. **Registrar Remisión:**
   - Ir a `/compras/{id}/remision`
   - Ingresar número de remisión del proveedor
   - **Ajustar cantidades reales:**
     - Producto A: 95 unidades (faltaron 5)
     - Motivo: "Faltante en envío del proveedor"
     - Producto B: 52 unidades (sobraron 2)
     - Motivo: "Envío adicional de cortesía"
   - Guardar

4. **Registrar Contrafactura:**
   - Se aplica al inventario usando las cantidades reales (95 y 52)
   - Se registran las diferencias en `movimientos_inventario`

### Verificar Integridad de Datos:

```php
use App\Models\MovimientoInventario;

// Verificar que un movimiento no fue alterado
$movimiento = MovimientoInventario::find(1);
$integro = $movimiento->verificarIntegridad();

if (!$integro) {
    // ¡Alerta! Los datos fueron modificados después de su creación
}
```

---

## Próximos Pasos

### Vistas Pendientes:
Necesitas crear las vistas Blade para los formularios:
- `resources/views/ventas/registrar_factura.blade.php`
- `resources/views/compras/registrar_remision.blade.php`

### Ejemplo de Vista (Factura):
```blade
<form method="POST" action="{{ route('ventas.registrar-factura', $presupuesto) }}">
    @csrf
    <input name="factura_numero" required>

    @foreach($presupuesto->productos as $item)
        <div>
            <span>{{ $item->producto->nombre }}</span>
            <span>Presupuestado: {{ $item->cantidad }}</span>
            <input
                name="cantidades[{{ $loop->index }}][producto_id]"
                value="{{ $item->producto_id }}"
                type="hidden">
            <input
                name="cantidades[{{ $loop->index }}][cantidad]"
                value="{{ $item->cantidad }}"
                step="0.01"
                required>
            <textarea
                name="cantidades[{{ $loop->index }}][motivo]"
                placeholder="Motivo si hay diferencia"></textarea>
        </div>
    @endforeach

    <button type="submit">Registrar Factura</button>
</form>
```

### Tests de Integración Recomendados:
```bash
# Estos tests requieren configuración de base de datos
tests/Feature/PresupuestoWorkflowTest.php
tests/Feature/NotaRemisionInventoryTest.php
tests/Feature/InventoryMovementTest.php
```

---

## Validaciones de Seguridad

### ✅ Prevención de Corrupción de Datos:
- Transacciones DB en todos los movimientos críticos
- Row-level locking (`lockForUpdate()`) en productos
- Hash de verificación en movimientos
- Validación de stock suficiente antes de SALIDA

### ✅ Trazabilidad Completa:
- Usuario, timestamp, diferencias, motivos
- Hash SHA-256 para detectar alteraciones
- Referencias cruzadas entre documentos

### ✅ Rollback Automático:
- Si falla cualquier operación en InventoryService, se hace rollback completo
- No hay riesgo de stock inconsistente

---

## Comandos Útiles

### Ejecutar Migraciones:
```bash
php artisan migrate
```

### Ejecutar Tests:
```bash
php artisan test
```

### Ver Movimientos con Diferencias:
```php
$movimientosConDiferencias = MovimientoInventario::whereNotNull('diferencia')->get();
```

### Generar Reporte de Diferencias:
```php
$faltantes = MovimientoInventario::where('diferencia', '<', 0)->get();
$sobrantes = MovimientoInventario::where('diferencia', '>', 0)->get();
```

---

## Notas Importantes

1. **IVA Deshabilitado:** Por ahora los cálculos no incluyen IVA. Se puede agregar después.

2. **Guaraníes:** Todos los precios y totales son números enteros (sin decimales).

3. **Cantidades:** Pueden tener decimales (ej: 2.5 kg), pero los montos son enteros.

4. **Seguridad:** Nunca ejecutes `php artisan migrate:fresh` en producción sin backup.

5. **Backups:** Los movimientos de inventario son **inmutables** (tienen hash). Haz backups regulares.

---

Implementado por: Claude
Fecha: 2025-11-14
