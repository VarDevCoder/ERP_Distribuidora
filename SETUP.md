# ANKHOR ERP - Guía de Configuración e Instalación

## Descripción del Proyecto

**ANKHOR ERP** es un sistema de gestión empresarial (ERP) para una distribuidora, desarrollado con Laravel 12 y Tailwind CSS v4. El sistema gestiona el flujo completo desde pedidos de clientes hasta entregas, incluyendo comparación de precios entre proveedores.

### Stack Tecnológico
- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Tailwind CSS v4, Alpine.js, Vite
- **Base de datos:** SQLite (desarrollo) / MySQL/PostgreSQL (producción)
- **Autenticación:** Sistema propio con roles

---

## Instalación Rápida

### Requisitos Previos
- PHP >= 8.2 con extensiones: sqlite3, pdo_sqlite, mbstring, openssl, tokenizer, xml, ctype, json
- Composer >= 2.0
- Node.js >= 18
- npm >= 9

---

## Instalación en Windows (Paso a Paso)

### 1. Instalar PHP 8.2+

**Opción A: XAMPP (Recomendado para principiantes)**
```powershell
# Descargar XAMPP desde https://www.apachefriends.org/
# Incluye PHP, Apache y MySQL
# PHP estará en: C:\xampp\php\php.exe
```

**Opción B: PHP Directo**
```powershell
# Descargar desde https://windows.php.net/download/
# Extraer en C:\php
# Agregar C:\php al PATH del sistema
```

### 2. Instalar Composer (NO usar winget)

```powershell
# Método 1: Instalador oficial (RECOMENDADO)
# Descargar desde: https://getcomposer.org/Composer-Setup.exe
# Ejecutar el instalador y seguir los pasos

# Método 2: Vía PHP (si el instalador falla)
cd C:\Users\TuUsuario
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
# Mover composer.phar a un lugar accesible y crear composer.bat
move composer.phar C:\php\composer.phar
echo @php "%~dp0composer.phar" %* > C:\php\composer.bat
```

### 3. Instalar Node.js

```powershell
# Opción A: Descargar instalador desde https://nodejs.org/
# Elegir versión LTS (18.x o superior)

# Opción B: Via winget (esto SÍ funciona)
winget install OpenJS.NodeJS.LTS
```

### 4. Verificar instalaciones

```powershell
php -v          # Debe mostrar PHP 8.2+
composer -V     # Debe mostrar Composer 2.x
node -v         # Debe mostrar v18+
npm -v          # Debe mostrar 9+
```

### 5. Clonar y configurar el proyecto

```powershell
# Clonar repositorio
git clone https://github.com/VarDevCoder/ERP_Distribuidora.git
cd ERP_Distribuidora

# Instalar dependencias PHP
composer install

# Copiar archivo de entorno
copy .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Crear base de datos SQLite (Windows)
type nul > database\database.sqlite

# Ejecutar migraciones con datos de prueba
php artisan migrate --seed

# Instalar dependencias Node
npm install

# Compilar assets
npm run build

# Iniciar servidor
php artisan serve
```

### 6. Acceder al sistema

Abrir en el navegador: `http://localhost:8000`

**Credenciales de prueba:**
- Admin: `admin@ankhor.com` / `password`
- Usuario: `usuario@ankhor.com` / `password`

---

## Instalación en Linux/Mac

### Pasos de Instalación

```bash
# 1. Clonar repositorio
git clone https://github.com/VarDevCoder/ERP_Distribuidora.git
cd ERP_Distribuidora

# 2. Instalar dependencias PHP
composer install

# 3. Copiar archivo de entorno
cp .env.example .env

# 4. Generar clave de aplicación
php artisan key:generate

# 5. Crear base de datos SQLite
touch database/database.sqlite

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Instalar dependencias Node
npm install

# 8. Compilar assets
npm run build

# 9. Iniciar servidor
php artisan serve
```

---

## Archivo .env Completo

Crear archivo `.env` en la raíz del proyecto con el siguiente contenido:

```env
APP_NAME="Ankhor ERP"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_PY

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Base de datos SQLite (desarrollo)
DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite  # Laravel lo detecta automáticamente

# Alternativa MySQL (producción)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=ankhor_erp
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@ankhor.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

---

## Estructura del Proyecto

### Modelos de Datos

```
User                    → Usuarios del sistema (admin, ankor_user, proveedor)
├── Proveedor           → Perfil de proveedor vinculado a user
│   └── ProveedorProducto → Catálogo de productos del proveedor con precios
├── Cliente             → Clientes de la distribuidora
├── Categoria           → Categorías de productos
└── Producto            → Catálogo maestro de productos
    └── MovimientoInventario → Historial de movimientos de stock

PedidoCliente           → Solicitudes de clientes
├── PedidoClienteItem   → Items del pedido
├── SolicitudPresupuesto → Cotizaciones enviadas a proveedores
│   └── SolicitudPresupuestoItem
├── OrdenCompra         → Órdenes de compra a proveedores
│   └── OrdenCompraItem
└── OrdenEnvio          → Órdenes de envío al cliente
    └── OrdenEnvioItem
```

### Roles de Usuario

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| `admin` | Administrador total | Todo el sistema |
| `ankor_user` | Empleado ANKHOR | Gestión completa excepto config |
| `proveedor` | Usuario proveedor | Solo portal proveedor |

### Usuarios de Prueba (Seeders)

```
Admin:     admin@ankhor.com / password
Usuario:   usuario@ankhor.com / password
Proveedor: proveedor1@test.com / password
```

---

## Módulos del Sistema

### 1. Dashboard (`/dashboard`)
- Métricas generales: pedidos, ventas, compras
- Accesos rápidos a funciones principales

### 2. Pedidos de Cliente (`/pedidos-cliente`)
- CRUD completo de pedidos
- Estados: pendiente → procesando → completado/cancelado
- Generación automática de solicitudes de cotización

### 3. Solicitudes de Presupuesto (`/solicitudes-presupuesto`)
- Envío de cotizaciones a múltiples proveedores
- Recepción y comparación de respuestas
- Selección del mejor precio

### 4. Órdenes de Compra (`/ordenes-compra`)
- Generación desde cotizaciones aceptadas
- Estados: borrador → enviada → confirmada → en_transito → recibida
- Recepción de mercadería con actualización de inventario

### 5. Órdenes de Envío (`/ordenes-envio`)
- Preparación de pedidos para despacho
- Estados: preparando → lista → despachada → entregada
- Actualización automática de inventario

### 6. Inventario (`/inventario`)
- Vista de stock actual
- Kardex por producto
- Historial de movimientos

### 7. Productos (`/productos`)
- Catálogo maestro de productos
- Gestión de categorías
- Unidades de medida configurables

### 8. Proveedores (`/proveedores`)
- CRUD de proveedores
- Toggle activo/inactivo

### 9. Catálogo de Proveedores (`/catalogo-proveedores`)
- Asignación de productos a proveedores
- Gestión de precios por proveedor
- Toggle de disponibilidad

### 10. Clientes (`/clientes`)
- CRUD de clientes
- Historial de pedidos

### 11. Análisis de Proveedores (`/analisis-proveedores`)
- Comparación de precios entre proveedores
- Estadísticas por producto
- Ranking de proveedores

### 12. Portal Proveedor (`/proveedor`)
- Dashboard del proveedor
- Ver y responder solicitudes de cotización
- Gestionar catálogo propio (si habilitado)

---

## Migraciones

Las migraciones crean las siguientes tablas:

```sql
users               -- Usuarios del sistema
proveedores         -- Datos de proveedores
productos           -- Catálogo de productos
categorias          -- Categorías de productos
clientes            -- Clientes
proveedor_productos -- Catálogo por proveedor

pedidos_cliente       -- Pedidos de clientes
pedido_cliente_items  -- Items de pedidos

solicitudes_presupuesto       -- Cotizaciones
solicitud_presupuesto_items   -- Items de cotizaciones

ordenes_compra        -- Órdenes de compra
orden_compra_items    -- Items de OC

ordenes_envio         -- Órdenes de envío
orden_envio_items     -- Items de envío

movimientos_inventario -- Kardex
sessions               -- Sesiones de usuario
cache                  -- Cache de aplicación
jobs                   -- Cola de trabajos
```

---

## Comandos Útiles

```bash
# Desarrollo
npm run dev                    # Vite en modo desarrollo
php artisan serve              # Servidor PHP

# Base de datos
php artisan migrate            # Ejecutar migraciones
php artisan migrate:fresh --seed  # Recrear BD con datos de prueba
php artisan db:seed            # Solo seeders

# Cache
php artisan config:clear       # Limpiar cache de config
php artisan cache:clear        # Limpiar cache general
php artisan view:clear         # Limpiar vistas compiladas

# Producción
npm run build                  # Compilar assets
php artisan config:cache       # Cachear configuración
php artisan route:cache        # Cachear rutas
php artisan view:cache         # Cachear vistas
```

---

## Configuración Adicional

### config/ankor.php

Archivo de configuración centralizada:

```php
return [
    'pagination' => ['per_page' => 15],
    'margen' => ['porcentaje' => 25],  // Margen de ganancia
    'metodos_envio' => [
        'RETIRO_LOCAL' => 'Retiro en Local',
        'DELIVERY_PROPIO' => 'Delivery Propio',
        // ...
    ],
    'unidades_medida' => [
        'unid' => 'Unidad',
        'kg' => 'Kilogramo',
        // ...
    ],
];
```

---

## Estructura de Carpetas CSS

```
resources/css/
├── app.css              # Entry point con imports
├── base/
│   ├── _fonts.css       # Tipografía (Plus Jakarta Sans)
│   └── _reset.css       # Reset y variables CSS
├── components/
│   ├── _badges.css      # Badges de estado
│   ├── _buttons.css     # Botones (primary, secondary, danger)
│   ├── _cards.css       # Tarjetas y paneles
│   ├── _forms.css       # Formularios
│   ├── _panels.css      # Paneles de sección
│   ├── _price.css       # Estilos de precios
│   ├── _stepper.css     # Stepper de progreso
│   ├── _tables.css      # Tablas de datos
│   └── _tooltips.css    # Tooltips
├── layout/
│   ├── _navbar.css      # Navegación
│   └── _page.css        # Layout de página
└── utilities/
    └── _utilities.css   # Animaciones y helpers
```

---

## Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 1 no such table"
```bash
php artisan migrate:fresh --seed
```

### Error: "Vite manifest not found"
```bash
npm run build
```

### Error: "Class not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Permisos en Linux/Mac
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Repositorio

- **GitHub:** https://github.com/VarDevCoder/ERP_Distribuidora
- **Rama principal:** main
- **Rama desarrollo:** claude-code

---

## Notas para IAs

1. **Base de datos:** Por defecto usa SQLite. El archivo se crea en `database/database.sqlite`
2. **Autenticación:** No usa Laravel Breeze/Jetstream, tiene AuthController propio
3. **Frontend:** Tailwind CSS v4 (usa `bg-linear-to-b` en lugar de `bg-gradient-to-b`)
4. **Roles:** Middleware `role:` verifica roles. Admin tiene acceso total automático
5. **Moneda:** Guaraníes paraguayos (Gs.) - sin decimales
6. **Idioma:** Español (Paraguay)
