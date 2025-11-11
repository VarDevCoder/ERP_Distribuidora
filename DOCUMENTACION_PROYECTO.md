# ANKHOR ERP - Documentación del Sistema

**Fecha de Análisis:** 11 de Noviembre 2025
**Framework:** Laravel 12
**Estado:** En desarrollo activo - Rama `erp-sistema`

---

## Índice

1. [Descripción General](#1-descripción-general)
2. [Stack Tecnológico](#2-stack-tecnológico)
3. [Arquitectura del Sistema](#3-arquitectura-del-sistema)
4. [Módulos Implementados](#4-módulos-implementados)
5. [Flujo de Negocio](#5-flujo-de-negocio)
6. [Modelo de Datos](#6-modelo-de-datos)
7. [Cambios Arquitectónicos Recientes](#7-cambios-arquitectónicos-recientes)
8. [Estado Actual](#8-estado-actual)
9. [Limitaciones y Deuda Técnica](#9-limitaciones-y-deuda-técnica)
10. [Próximos Pasos Recomendados](#10-próximos-pasos-recomendados)

---

## 1. Descripción General

**Ankhor ERP** es un sistema de gestión para distribuidoras que se enfoca en optimizar el flujo de trabajo desde la cotización hasta la gestión de inventario.

### Propósito
Proporcionar una herramienta unificada para:
- Generar presupuestos/cotizaciones (COMPRA y VENTA)
- Gestionar despachos mediante notas de remisión
- Controlar inventario con trazabilidad completa
- Mantener catálogo de productos con precios duales

### Filosofía del Sistema
El sistema elimina la complejidad de entidades separadas (Cliente/Proveedor/Venta/Compra) en favor de un modelo unificado basado en **Presupuestos** que fluyen naturalmente hacia el inventario.

---

## 2. Stack Tecnológico

### Backend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| PHP | 8.2+ | Lenguaje base |
| Laravel | 12.x | Framework MVC |
| SQLite/MySQL | Default/Compatible | Base de datos |
| Eloquent ORM | Built-in | Gestión de modelos |

### Frontend
| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| Blade | Built-in | Motor de plantillas |
| Tailwind CSS | 4.0 | Framework CSS |
| Alpine.js | 3.x | JavaScript reactivo |
| Axios | Latest | Peticiones HTTP |
| Vite | 7.x | Build tool |

### Herramientas de Desarrollo
- **Composer** - Gestor de dependencias PHP
- **npm** - Gestor de paquetes JavaScript
- **Laravel Migrations** - Control de versiones de BD
- **PHPUnit** - Testing unitario

---

## 3. Arquitectura del Sistema

### Patrón Arquitectónico
El sistema sigue el patrón **MVC (Model-View-Controller)** de Laravel con una arquitectura orientada a documentos:

```
PRESUPUESTO (Documento cotización)
    ↓
NOTA DE REMISIÓN (Documento despacho)
    ↓
MOVIMIENTO DE INVENTARIO (Transacción automática)
    ↓
INVENTARIO ACTUALIZADO
```

### Flujo de Estados

#### Presupuesto
```
BORRADOR → ENVIADO → APROBADO → CONVERTIDO
```

#### Nota de Remisión
```
PENDIENTE → APLICADA
```

### Principios de Diseño
1. **Integridad Transaccional** - Uso de DB::transaction() para operaciones críticas
2. **Validación Estricta** - Validación de stock antes de salidas
3. **Auditoría Completa** - Todo movimiento queda registrado
4. **Numeración Automática** - Sistema de códigos secuenciales
5. **Protección de Datos** - Foreign keys con restricciones apropiadas

---

## 4. Módulos Implementados

### 4.1 Autenticación

**Estado:** ✅ Completo

**Archivos:**
- Controller: `app/Http/Controllers/AuthController.php` (76 líneas)
- Modelo: `app/Models/User.php` (48 líneas)
- Vistas: `resources/views/auth/login.blade.php`, `register.blade.php`

**Funcionalidades:**
- ✅ Registro de usuarios con validación de email único
- ✅ Login con email/contraseña
- ✅ Opción "Recordarme"
- ✅ Protección CSRF
- ✅ Hash de contraseñas (bcrypt rounds=12)
- ✅ Cierre de sesión con invalidación

**Rutas:**
```
GET  /login       - Formulario de login
POST /login       - Procesar autenticación
GET  /register    - Formulario de registro
POST /register    - Crear usuario
POST /logout      - Cerrar sesión
```

---

### 4.2 Presupuestos (Core)

**Estado:** ✅ Completo - Módulo Central

**Archivos:**
- Controller: `app/Http/Controllers/PresupuestoController.php` (175 líneas)
- Modelos:
  - `app/Models/Presupuesto.php` (90 líneas)
  - `app/Models/PresupuestoItem.php` (41 líneas)
- Vistas: `resources/views/presupuestos/` (index, create, edit, show)

**Funcionalidades:**
- ✅ Crear presupuestos de COMPRA o VENTA
- ✅ Gestión de contactos integrada (sin entidad separada)
- ✅ Ítems de línea con selección de productos
- ✅ Cálculo automático de:
  - Subtotales por ítem
  - Descuentos
  - IVA (16%)
  - Total general
- ✅ Numeración automática: `PV-YYYY-NNNN` (Venta) / `PC-YYYY-NNNN` (Compra)
- ✅ Flujo de aprobación
- ✅ Protección contra modificación de presupuestos convertidos
- ✅ Eliminación con validación de estado

**Campos del Presupuesto:**
```
- numero (auto)
- tipo (COMPRA/VENTA)
- contacto_nombre, contacto_email, contacto_telefono, contacto_empresa
- fecha, fecha_vencimiento
- subtotal, descuento, impuesto, total
- estado (BORRADOR/ENVIADO/APROBADO/CONVERTIDO)
- notas
- nota_remision_id (FK)
- factura_numero, factura_fecha
- contrafactura_numero, contrafactura_fecha
- remision_numero, remision_fecha
- venta_validada, compra_validada
```

**Rutas:**
```
GET  /presupuestos              - Listar (filtro por tipo)
GET  /presupuestos/create       - Formulario (parámetro tipo)
POST /presupuestos              - Guardar
GET  /presupuestos/{id}         - Ver detalle
GET  /presupuestos/{id}/edit    - Editar
PUT  /presupuestos/{id}         - Actualizar
DELETE /presupuestos/{id}       - Eliminar
POST /presupuestos/{id}/aprobar - Aprobar
```

**Lógica de Negocio:**
1. **Creación:** Validación de campos, generación de número, estado BORRADOR
2. **Edición:** Solo permitida si estado != CONVERTIDO
3. **Aprobación:** Cambia estado a APROBADO, habilita conversión
4. **Conversión:** Al crear Nota de Remisión, estado → CONVERTIDO
5. **Eliminación:** Bloqueada si estado = CONVERTIDO

---

### 4.3 Notas de Remisión (Core)

**Estado:** ✅ Completo - Módulo Central

**Archivos:**
- Controller: `app/Http/Controllers/NotaRemisionController.php` (127 líneas)
- Modelos:
  - `app/Models/NotaRemision.php` (95 líneas) - **Contiene lógica crítica**
  - `app/Models/NotaRemisionItem.php` (30 líneas)
- Vistas: `resources/views/notas_remision/` (index, create, show)

**Funcionalidades:**
- ✅ Conversión desde presupuestos APROBADOS únicamente
- ✅ Asignación automática de tipo:
  - COMPRA → ENTRADA (aumenta stock)
  - VENTA → SALIDA (disminuye stock)
- ✅ Copia automática de ítems del presupuesto
- ✅ Numeración automática: `NE-YYYY-NNNN` (Entrada) / `NS-YYYY-NNNN` (Salida)
- ✅ Aplicación al inventario con transacciones
- ✅ Validación de stock disponible
- ✅ Generación automática de movimientos

**Método Crítico: `aplicarAInventario()`**
```php
public function aplicarAInventario()
{
    DB::transaction(function () {
        foreach ($this->items as $item) {
            $producto = $item->producto;
            $stockAnterior = $producto->stock_actual;

            if ($this->tipo === 'ENTRADA') {
                $producto->stock_actual += $item->cantidad;
            } else {
                if ($producto->stock_actual < $item->cantidad) {
                    throw new \Exception("Stock insuficiente");
                }
                $producto->stock_actual -= $item->cantidad;
            }

            $producto->save();

            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'nota_remision_id' => $this->id,
                'tipo' => $this->tipo,
                'cantidad' => $item->cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $producto->stock_actual,
                'referencia' => "Nota Remisión {$this->numero}"
            ]);
        }

        $this->estado = 'APLICADA';
        $this->save();
    });
}
```

**Rutas:**
```
GET  /notas-remision              - Listar
GET  /notas-remision/create       - Crear desde presupuesto
POST /notas-remision              - Guardar
GET  /notas-remision/{id}         - Ver detalle
POST /notas-remision/{id}/aplicar - Aplicar al inventario
DELETE /notas-remision/{id}       - Eliminar (solo si PENDIENTE)
```

---

### 4.4 Productos

**Estado:** ✅ Completo

**Archivos:**
- Controller: `app/Http/Controllers/ProductoController.php` (85 líneas)
- Modelo: `app/Models/Producto.php` (47 líneas)
- Vistas: `resources/views/productos/` (index, create, edit, show)

**Funcionalidades:**
- ✅ CRUD completo
- ✅ Código automático: `PROD-00001` (5 dígitos)
- ✅ Precios duales (compra/venta)
- ✅ Control de stock:
  - `stock_actual` (decimal)
  - `stock_minimo` (alertas)
- ✅ Unidades de medida (pz, kg, lt, m, etc.)
- ✅ Estado activo/inactivo
- ✅ Protección contra eliminación si tiene referencias

**Campos del Producto:**
```
- codigo (único, auto)
- nombre
- descripcion
- precio_compra (decimal 10,2)
- precio_venta (decimal 10,2)
- stock_actual (decimal 10,2)
- stock_minimo (decimal 10,2)
- unidad_medida
- activo (boolean)
```

**Rutas:**
```
GET  /productos         - Listar (paginado 20)
GET  /productos/create  - Formulario
POST /productos         - Guardar
GET  /productos/{id}    - Ver + historial
GET  /productos/{id}/edit - Editar
PUT  /productos/{id}    - Actualizar
DELETE /productos/{id}  - Eliminar (con validación)
```

---

### 4.5 Inventario

**Estado:** ✅ Completo - Solo Lectura/Analítica

**Archivos:**
- Controller: `app/Http/Controllers/InventarioController.php` (38 líneas)
- Modelo: `app/Models/MovimientoInventario.php` (36 líneas)
- Vistas: `resources/views/inventario/` (index, kardex, movimientos)

**Funcionalidades:**
- ✅ Dashboard con estadísticas:
  - Total de productos
  - Productos con stock bajo
  - Total de movimientos
  - Productos activos
- ✅ Alertas de stock mínimo
- ✅ Kardex por producto (historial completo)
- ✅ Registro global de movimientos
- ✅ Trazabilidad: cada movimiento apunta a su Nota de Remisión

**Campos del Movimiento:**
```
- producto_id (FK)
- nota_remision_id (FK, nullable)
- tipo (ENTRADA/SALIDA/AJUSTE)
- cantidad (decimal)
- stock_anterior (decimal)
- stock_nuevo (decimal)
- referencia (texto)
- observaciones
- usuario_id (opcional, futuro)
```

**Rutas:**
```
GET /inventario                - Dashboard
GET /inventario/movimientos    - Todos los movimientos
GET /inventario/kardex/{id}    - Kardex de producto
```

**Nota Importante:**
- ❌ **NO permite ajustes manuales de inventario**
- ✅ Todo movimiento DEBE provenir de una Nota de Remisión
- ✅ Garantiza auditoría completa

---

## 5. Flujo de Negocio

### Caso de Uso: Venta Completa

#### Paso 1: Crear Presupuesto de Venta
```
Usuario → Presupuestos → Crear → Tipo: VENTA
    ↓
Ingresar contacto:
    - Nombre: "Juan Pérez"
    - Email: "juan@ejemplo.com"
    - Teléfono: "555-1234"
    - Empresa: "Distribuidora ABC"
    ↓
Agregar ítems:
    - Producto: "Tornillo M6" (código PROD-00123)
    - Cantidad: 100 pz
    - Precio: $2.50/pz
    - Subtotal: $250.00
    ↓
    - Producto: "Tuerca M6" (código PROD-00124)
    - Cantidad: 100 pz
    - Precio: $1.50/pz
    - Subtotal: $150.00
    ↓
Descuento: $20.00
    ↓
Sistema calcula:
    - Subtotal: $400.00
    - Descuento: -$20.00
    - Base: $380.00
    - IVA (16%): $60.80
    - TOTAL: $440.80
    ↓
Guardar → Número asignado: PV-2025-0042
Estado: BORRADOR
```

#### Paso 2: Aprobar Presupuesto
```
Usuario → Ver Presupuesto PV-2025-0042 → Aprobar
    ↓
Estado: BORRADOR → APROBADO
    ↓
Botón "Convertir a Nota de Remisión" se activa
```

#### Paso 3: Convertir a Nota de Remisión
```
Usuario → Convertir a Nota de Remisión
    ↓
Sistema crea:
    - Número: NS-2025-0089
    - Tipo: SALIDA (porque presupuesto es VENTA)
    - Copia contacto del presupuesto
    - Copia TODOS los ítems (100 tornillos, 100 tuercas)
    - Estado: PENDIENTE
    ↓
Actualiza presupuesto:
    - Estado: APROBADO → CONVERTIDO
    - nota_remision_id: [ID de NS-2025-0089]
    ↓
Redirect a vista de Nota de Remisión
```

#### Paso 4: Aplicar al Inventario
```
Usuario → Ver Nota NS-2025-0089 → Aplicar al Inventario
    ↓
Sistema ejecuta transacción:

    Ítem 1: Tornillo M6 (PROD-00123)
        - Stock actual: 500 pz
        - Cantidad a descontar: 100 pz
        - Validación: 500 >= 100 ✓
        - Nuevo stock: 400 pz
        - Crear movimiento:
            * tipo: SALIDA
            * cantidad: 100
            * stock_anterior: 500
            * stock_nuevo: 400
            * referencia: "Nota Remisión NS-2025-0089"

    Ítem 2: Tuerca M6 (PROD-00124)
        - Stock actual: 350 pz
        - Cantidad a descontar: 100 pz
        - Validación: 350 >= 100 ✓
        - Nuevo stock: 250 pz
        - Crear movimiento:
            * tipo: SALIDA
            * cantidad: 100
            * stock_anterior: 350
            * stock_nuevo: 250
            * referencia: "Nota Remisión NS-2025-0089"

    Actualizar nota:
        - Estado: PENDIENTE → APLICADA

    Commit transacción ✓
    ↓
Mensaje: "Nota de remisión aplicada al inventario exitosamente"
```

#### Paso 5: Verificar Inventario
```
Usuario → Inventario → Dashboard
    ↓
Ver productos actualizados:
    - PROD-00123: Stock 400 pz (antes 500)
    - PROD-00124: Stock 250 pz (antes 350)
    ↓
Usuario → Inventario → Movimientos
    ↓
Ver registro completo:
    | Fecha | Producto | Tipo | Cantidad | Stock Ant. | Stock Nuevo | Referencia |
    |-------|----------|------|----------|------------|-------------|------------|
    | 11/11 | Tornillo | SALIDA | 100 | 500 | 400 | NS-2025-0089 |
    | 11/11 | Tuerca   | SALIDA | 100 | 350 | 250 | NS-2025-0089 |
```

### Caso de Uso: Compra (Entrada de Inventario)

Mismo flujo pero:
- Tipo de presupuesto: **COMPRA**
- Número: `PC-2025-NNNN`
- Nota de remisión: `NE-2025-NNNN` (ENTRADA)
- Operación: **Stock AUMENTA** en lugar de disminuir

---

## 6. Modelo de Datos

### Diagrama de Relaciones

```
┌─────────────┐
│   USERS     │
│             │
│ - id        │
│ - name      │
│ - email     │
│ - password  │
└─────────────┘

┌──────────────────┐
│   PRODUCTOS      │
│                  │
│ - id             │
│ - codigo         │◄────────┐
│ - nombre         │         │
│ - precio_compra  │         │
│ - precio_venta   │         │
│ - stock_actual   │         │
│ - stock_minimo   │         │
└──────────────────┘         │
         ▲                   │
         │                   │
         │ FK                │ FK
         │                   │
┌─────────────────────┐  ┌──────────────────────┐
│ MOVIMIENTOS_        │  │ PRESUPUESTO_ITEMS    │
│ INVENTARIO          │  │                      │
│                     │  │ - id                 │
│ - id                │  │ - presupuesto_id (FK)│
│ - producto_id (FK)  │  │ - producto_id (FK)   │
│ - nota_remision_id  │  │ - orden              │
│ - tipo              │  │ - descripcion        │
│ - cantidad          │  │ - cantidad           │
│ - stock_anterior    │  │ - precio_unitario    │
│ - stock_nuevo       │  │ - subtotal           │
│ - referencia        │  └──────────────────────┘
└─────────────────────┘           ▲
         ▲                        │
         │                        │ FK
         │ FK                     │
         │                 ┌──────────────────────┐
         │                 │   PRESUPUESTOS       │
         │                 │                      │
         │                 │ - id                 │
         │                 │ - numero             │
         │                 │ - tipo (COMPRA/VENTA)│
         │                 │ - contacto_*         │
         │                 │ - fecha              │
         │                 │ - subtotal           │
         │                 │ - descuento          │
         │                 │ - impuesto           │
         │                 │ - total              │
         │                 │ - estado             │
         │                 │ - nota_remision_id   │
         │                 └──────────────────────┘
         │                          ▲
         │                          │
         │                          │ FK
         │                          │
         │                 ┌──────────────────────┐
         │                 │  NOTAS_REMISION      │
         │                 │                      │
         │                 │ - id                 │
         │                 │ - numero             │
         │                 │ - presupuesto_id (FK)│
         │                 │ - tipo (ENTRADA/SALIDA)
         │                 │ - contacto_nombre    │
         │                 │ - fecha              │
         │                 │ - estado             │
         └─────────────────┤ - observaciones      │
                           └──────────────────────┘
                                    ▲
                                    │
                                    │ FK
                                    │
                           ┌──────────────────────┐
                           │ NOTA_REMISION_ITEMS  │
                           │                      │
                           │ - id                 │
                           │ - nota_remision_id   │
                           │ - producto_id (FK)   │
                           │ - cantidad           │
                           │ - precio_unitario    │
                           └──────────────────────┘
```

### Restricciones de Integridad

| Tabla | Columna | Constraint | Acción |
|-------|---------|-----------|--------|
| presupuesto_items | producto_id | FK | RESTRICT delete |
| presupuesto_items | presupuesto_id | FK | CASCADE delete |
| nota_remision_items | producto_id | FK | RESTRICT delete |
| nota_remision_items | nota_remision_id | FK | CASCADE delete |
| notas_remision | presupuesto_id | FK | RESTRICT delete |
| movimientos_inventario | producto_id | FK | CASCADE delete |
| movimientos_inventario | nota_remision_id | FK | SET NULL delete |

### Índices de Performance

```sql
-- Productos
INDEX idx_productos_codigo ON productos(codigo)
INDEX idx_productos_nombre ON productos(nombre)
INDEX idx_productos_activo ON productos(activo)

-- Presupuestos
INDEX idx_presupuestos_numero ON presupuestos(numero)
INDEX idx_presupuestos_tipo ON presupuestos(tipo)
INDEX idx_presupuestos_estado ON presupuestos(estado)
INDEX idx_presupuestos_nota_remision ON presupuestos(nota_remision_id)

-- Notas Remisión
INDEX idx_notas_numero ON notas_remision(numero)
INDEX idx_notas_presupuesto ON notas_remision(presupuesto_id)
INDEX idx_notas_tipo ON notas_remision(tipo)
INDEX idx_notas_estado ON notas_remision(estado)

-- Movimientos
INDEX idx_movimientos_producto ON movimientos_inventario(producto_id)
INDEX idx_movimientos_nota ON movimientos_inventario(nota_remision_id)
INDEX idx_movimientos_tipo ON movimientos_inventario(tipo)
INDEX idx_movimientos_fecha ON movimientos_inventario(created_at)
INDEX idx_movimientos_composite ON movimientos_inventario(producto_id, created_at)
```

---

## 7. Cambios Arquitectónicos Recientes

### Migración de Arquitectura (Rama: erp-sistema)

#### Entidades ELIMINADAS ❌

**Controllers:**
- `ClienteController.php` → Reemplazado por campos contacto_* en Presupuesto
- `ProveedorController.php` → Reemplazado por campos contacto_* en Presupuesto
- `VentaController.php` → Reemplazado por Presupuesto tipo VENTA
- `CompraController.php` → Reemplazado por Presupuesto tipo COMPRA
- `DashboardController.php` → Dashboard redirige a presupuestos.index

**Models:**
- `Cliente.php` → Sin modelo separado
- `Proveedor.php` → Sin modelo separado
- `Venta.php` → Reemplazado por Presupuesto
- `Compra.php` → Reemplazado por Presupuesto
- `Usuario.php` → Reemplazado por User estándar de Laravel
- `DetalleVenta.php` → Reemplazado por PresupuestoItem
- `DetalleCompra.php` → Reemplazado por PresupuestoItem
- `DetallePresupuesto.php` → Reemplazado por PresupuestoItem

**Migrations:**
- Todas las migraciones con fecha `2025_11_05_*` fueron eliminadas
- Total: 11 migraciones antiguas removidas

**Views:**
- `clientes/*` → Eliminado (3 vistas)
- `proveedores/*` → Eliminado (3 vistas)
- `ventas/*` → Eliminado (3 vistas)
- `compras/*` → Eliminado (3 vistas)
- `dashboard/index.blade.php` → Eliminado
- `inventario/ajuste.blade.php` → Eliminado (ahora solo vía Notas)

#### Entidades AÑADIDAS ✅

**Controllers:**
- `NotaRemisionController.php` - Nuevo módulo central (127 líneas)

**Models:**
- `NotaRemision.php` - Documento de despacho (95 líneas)
- `NotaRemisionItem.php` - Ítems de nota (30 líneas)
- `PresupuestoItem.php` - Ítems de presupuesto refactorizado (41 líneas)

**Migrations (Fecha: 2025_11_11_*):**
1. `012836_create_productos_table.php` - Nueva estructura de productos
2. `012906_create_presupuestos_table.php` - Nueva estructura de presupuestos
3. `012907_create_presupuesto_items_table.php` - Ítems de presupuesto
4. `012908_create_notas_remision_table.php` - Notas de remisión
5. `012909_create_nota_remision_items_table.php` - Ítems de nota
6. `012910_create_movimientos_inventario_table.php` - Movimientos
7. `012911_add_nota_remision_id_to_presupuestos.php` - Link bidireccional
8. `015223_create_sessions_table.php` - Gestión de sesiones
9. `024227_create_users_table.php` - Usuarios Laravel estándar
10. `031142_add_docs_to_presupuestos_table.php` - Campos de documentos
11. `031233_add_doc_refs_to_notas_remision_table.php` - Referencias doc
12. `031320_update_movimientos_inventario_structure.php` - Ajustes estructura
13. `031408_update_productos_stock_to_decimal.php` - Stock a decimal

**Views:**
- `notas_remision/` - 3 nuevas vistas (index, create, show)
- `presupuestos/edit.blade.php` - Nueva vista de edición
- `productos/` - 3 nuevas vistas (create, edit, show)
- `auth/register.blade.php` - Nueva vista de registro
- `components/` - Nuevos componentes reutilizables (data-table, table-row, table-cell)

**Seeders:**
- `AdminUserSeeder.php` - Seeder de usuario admin
- `PresupuestoSeeder.php` - Datos de prueba

#### Archivos MODIFICADOS 🔄

**Controllers:**
- `AuthController.php` - Autenticación mejorada
- `InventarioController.php` - Adaptado a nuevos modelos
- `PresupuestoController.php` - Expandido con transacciones
- `ProductoController.php` - Ajustes menores

**Models:**
- `MovimientoInventario.php` - Refactorizado
- `Presupuesto.php` - Expandido significativamente
- `Producto.php` - Ajustes

**Views:**
- `layouts/app.blade.php` - Navegación actualizada, tema azul corporativo
- `presupuestos/*` - Adaptados al nuevo flujo
- `inventario/*` - Refactorizados
- `productos/index.blade.php` - Actualizaciones de estilo
- `auth/login.blade.php` - Mejoras visuales

**Routes:**
- `web.php` - Reestructurado completamente para nuevos módulos

### Evolución del Diseño Visual (Últimos 5 Commits)

```
0f3399ed - Aplicar fondo tenue a todas las tarjetas del sistema
ab6f21fd - Cambiar fondo de cards a azul marino tenue
46b34ea3 - Igualar color del header con el sidebar
ed8a773b - Cambiar fondo del header principal a azul oscuro
d7e5ce6d - Cambiar header del dashboard a gradiente azul oscuro
```

**Tema Visual Actual:**
- **Colores primarios:** Gradiente azul oscuro (#1e3a8a → #2563eb)
- **Acentos:** Amarillo (#fbbf24) para botones de acción
- **Cards:** Fondo azul marino tenue
- **Navegación:** Header y sidebar con mismo color azul oscuro
- **Animaciones:** Login con formas flotantes y gradientes animados

---

## 8. Estado Actual

### Funcionalidades Completas ✅

#### Autenticación
- [x] Registro de usuarios
- [x] Login/Logout
- [x] Protección de rutas
- [x] Gestión de sesiones
- [x] Validación de formularios

#### Gestión de Presupuestos
- [x] Crear presupuestos COMPRA/VENTA
- [x] Editar presupuestos
- [x] Agregar/editar ítems dinámicamente
- [x] Cálculo automático de totales e IVA
- [x] Flujo de aprobación
- [x] Conversión a Nota de Remisión
- [x] Protección de datos según estado

#### Notas de Remisión
- [x] Conversión desde presupuestos aprobados
- [x] Validación de estado de presupuesto
- [x] Copia automática de ítems
- [x] Aplicación al inventario con transacciones
- [x] Validación de stock
- [x] Generación automática de movimientos
- [x] Protección contra re-aplicación

#### Productos
- [x] CRUD completo
- [x] Generación automática de códigos
- [x] Gestión de precios duales
- [x] Control de stock
- [x] Alertas de stock mínimo
- [x] Estado activo/inactivo
- [x] Protección contra eliminación con referencias

#### Inventario
- [x] Dashboard con estadísticas
- [x] Kardex por producto
- [x] Registro global de movimientos
- [x] Trazabilidad completa
- [x] Cálculo automático de stock
- [x] Auditoría de movimientos

### Estabilidad del Sistema

**Transacciones:** ✅ Completa
Todas las operaciones críticas usan `DB::transaction()`:
- Creación de presupuestos con ítems
- Actualización de presupuestos
- Aplicación de notas al inventario

**Validaciones:** ✅ Robustas
- Validación de stock antes de salidas
- Validación de estados antes de operaciones
- Validación de relaciones antes de eliminaciones
- Validación de formularios con mensajes personalizados

**Auditoría:** ✅ Completa
- Todos los movimientos quedan registrados
- Trazabilidad a documento origen (Nota de Remisión)
- Histórico completo (kardex)
- Stock antes/después en cada movimiento

**Performance:** ✅ Optimizado
- Índices en campos clave
- Eager loading (`with()`) para evitar N+1
- Paginación en listados
- Queries optimizadas

---

## 9. Limitaciones y Deuda Técnica

### Funcionalidades Faltantes ❌

#### Gestión de Usuarios
- [ ] Sistema de roles y permisos (todos los usuarios = admin)
- [ ] Auditoría de quién creó/modificó registros
- [ ] Histórico de acciones por usuario
- [ ] Gestión de perfiles de usuario

#### Reportes y Análisis
- [ ] Dashboard con gráficas
- [ ] Reporte de ventas por período
- [ ] Reporte de compras por período
- [ ] Análisis de rentabilidad
- [ ] Productos más vendidos
- [ ] Rotación de inventario
- [ ] Proyecciones de stock

#### Documentos
- [ ] Exportación a PDF de presupuestos
- [ ] Exportación a PDF de notas de remisión
- [ ] Impresión de documentos
- [ ] Envío de presupuestos por email
- [ ] Adjuntar archivos a presupuestos/notas
- [ ] Generación de facturas

#### Funcionalidades de Negocio
- [ ] Gestión de pagos
- [ ] Cuentas por cobrar/pagar
- [ ] Seguimiento de facturación
- [ ] Múltiples notas de remisión por presupuesto (entregas parciales)
- [ ] Devoluciones
- [ ] Ajustes manuales de inventario (con autorización)
- [ ] Órdenes de compra
- [ ] Catálogo de proveedores
- [ ] Historial de precios
- [ ] Control de costos vs precios de venta

#### Búsqueda y Filtros
- [ ] Búsqueda avanzada en presupuestos
- [ ] Filtros por fecha/rango
- [ ] Búsqueda de productos por categoría
- [ ] Filtros en movimientos de inventario
- [ ] Búsqueda global

#### Exportación de Datos
- [ ] Exportar listados a CSV
- [ ] Exportar listados a Excel
- [ ] Exportar kardex
- [ ] Exportar movimientos

#### Integración
- [ ] API REST
- [ ] Webhooks
- [ ] Integración con sistemas de facturación
- [ ] Integración con sistemas contables

### Deuda Técnica 🔧

#### Testing
- [ ] Tests unitarios de modelos
- [ ] Tests de integración de controllers
- [ ] Tests de flujos completos
- [ ] Tests de validaciones
- [ ] Cobertura de código

#### Seguridad
- [ ] Rate limiting
- [ ] Throttling de requests
- [ ] Logs de seguridad
- [ ] Protección contra inyección SQL (mitigado por Eloquent)
- [ ] Sanitización de inputs (mitigado por Blade)
- [ ] Políticas de contraseñas robustas
- [ ] Autenticación de dos factores

#### Performance
- [ ] Caché de consultas frecuentes
- [ ] Optimización de queries complejas
- [ ] Lazy loading de imágenes (cuando se implementen)
- [ ] Compresión de assets
- [ ] CDN para assets estáticos

#### Código
- [ ] Refactorización de vistas repetitivas
- [ ] Extracción de lógica de negocio a servicios
- [ ] Implementación de Repository Pattern
- [ ] Form Requests para validaciones complejas
- [ ] Eventos y Listeners para acciones post-operación
- [ ] Jobs para procesamiento asíncrono

#### Documentación
- [x] Documentación de arquitectura (este archivo)
- [ ] Documentación de API
- [ ] Manual de usuario
- [ ] Guía de instalación
- [ ] Guía de despliegue
- [ ] Changelog detallado

---

## 10. Próximos Pasos Recomendados

### Prioridad ALTA 🔴

1. **Sistema de Roles y Permisos**
   - Implementar roles: Admin, Vendedor, Almacenista
   - Permisos granulares por módulo
   - Middleware de autorización
   - **Impacto:** Seguridad y control

2. **Exportación a PDF**
   - Librería: DomPDF o Snappy (wkhtmltopdf)
   - Templates de presupuestos
   - Templates de notas de remisión
   - **Impacto:** Presentación profesional

3. **Reportes Básicos**
   - Dashboard con gráficas (Chart.js)
   - Reporte de ventas mensuales
   - Productos con stock bajo
   - Top 10 productos más vendidos
   - **Impacto:** Toma de decisiones

4. **Búsqueda y Filtros**
   - Búsqueda de presupuestos por número/contacto
   - Filtro por rango de fechas
   - Búsqueda de productos
   - **Impacto:** Usabilidad

### Prioridad MEDIA 🟡

5. **Gestión de Pagos**
   - Registro de pagos parciales/completos
   - Estados de cobranza
   - Cuentas por cobrar
   - **Impacto:** Control financiero

6. **Notificaciones por Email**
   - Envío de presupuestos a clientes
   - Alertas de stock bajo
   - Resumen diario de operaciones
   - **Impacto:** Comunicación

7. **Entregas Parciales**
   - Permitir múltiples notas de remisión por presupuesto
   - Tracking de cantidades pendientes
   - Estados de entrega
   - **Impacto:** Flexibilidad operativa

8. **Testing Automatizado**
   - Tests de flujos críticos
   - Tests de validaciones
   - CI/CD básico
   - **Impacto:** Calidad y confiabilidad

### Prioridad BAJA 🟢

9. **API REST**
   - Endpoints para integraciones
   - Autenticación con tokens
   - Documentación con Swagger
   - **Impacto:** Integraciones futuras

10. **Optimizaciones Avanzadas**
    - Implementación de caché
    - Queue para operaciones pesadas
    - Optimización de queries
    - **Impacto:** Performance a escala

11. **Categorías de Productos**
    - Organización por categorías
    - Navegación jerárquica
    - Filtros por categoría
    - **Impacto:** Organización

12. **Historial de Precios**
    - Registro de cambios de precio
    - Gráficas de evolución
    - Análisis de márgenes
    - **Impacto:** Análisis histórico

---

## Estructura de Archivos del Proyecto

```
ERP-Distribuidora/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php (76L)
│   │       ├── Controller.php
│   │       ├── InventarioController.php (38L)
│   │       ├── NotaRemisionController.php (127L) ★
│   │       ├── PresupuestoController.php (175L) ★
│   │       └── ProductoController.php (85L)
│   ├── Models/
│   │   ├── User.php (48L)
│   │   ├── Producto.php (47L)
│   │   ├── Presupuesto.php (90L) ★
│   │   ├── PresupuestoItem.php (41L) ★
│   │   ├── NotaRemision.php (95L) ★
│   │   ├── NotaRemisionItem.php (30L) ★
│   │   └── MovimientoInventario.php (36L) ★
│   └── Providers/
│       └── AppServiceProvider.php
│
├── database/
│   ├── migrations/
│   │   ├── 2025_11_11_012836_create_productos_table.php
│   │   ├── 2025_11_11_012906_create_presupuestos_table.php
│   │   ├── 2025_11_11_012907_create_presupuesto_items_table.php
│   │   ├── 2025_11_11_012908_create_notas_remision_table.php
│   │   ├── 2025_11_11_012909_create_nota_remision_items_table.php
│   │   ├── 2025_11_11_012910_create_movimientos_inventario_table.php
│   │   ├── 2025_11_11_012911_add_nota_remision_id_to_presupuestos.php
│   │   ├── 2025_11_11_015223_create_sessions_table.php
│   │   ├── 2025_11_11_024227_create_users_table.php
│   │   ├── 2025_11_11_031142_add_docs_to_presupuestos_table.php
│   │   ├── 2025_11_11_031233_add_doc_refs_to_notas_remision_table.php
│   │   ├── 2025_11_11_031320_update_movimientos_inventario_structure.php
│   │   └── 2025_11_11_031408_update_productos_stock_to_decimal.php
│   └── seeders/
│       ├── AdminUserSeeder.php
│       ├── DatabaseSeeder.php
│       └── PresupuestoSeeder.php
│
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── components/
│   │   │   ├── data-table.blade.php
│   │   │   ├── table-row.blade.php
│   │   │   └── table-cell.blade.php
│   │   ├── inventario/
│   │   │   ├── index.blade.php
│   │   │   ├── kardex.blade.php
│   │   │   └── movimientos.blade.php
│   │   ├── notas_remision/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   └── show.blade.php
│   │   ├── presupuestos/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   ├── productos/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   └── show.blade.php
│   │   └── layouts/
│   │       └── app.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
│
├── routes/
│   └── web.php
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── session.php
│
├── public/
│   └── index.php
│
├── composer.json
├── package.json
├── .env.example
└── artisan

★ = Archivos críticos del sistema
```

---

## Resumen Ejecutivo

**Ankhor ERP** es un sistema de gestión distribuidor funcional y estable que implementa con éxito un flujo simplificado de presupuesto → despacho → inventario.

### Fortalezas
- ✅ Arquitectura limpia y mantenible
- ✅ Transacciones seguras
- ✅ Auditoría completa
- ✅ Validaciones robustas
- ✅ UI moderna y responsiva

### Oportunidades
- 📊 Reportes y análisis
- 🔒 Roles y permisos
- 📄 Exportación a PDF
- 💰 Gestión financiera
- 📧 Notificaciones

### Estado
- **Código:** Producción-ready para flujo base
- **Funcionalidades:** Core completo, features avanzadas pendientes
- **Performance:** Optimizado para volumen medio
- **Seguridad:** Básica implementada, avanzada pendiente

---

**Última actualización:** 11 de Noviembre 2025
**Versión del documento:** 1.0
**Autor del análisis:** Claude Code
