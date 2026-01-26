# Sistema de Cambio de Ambientes

Este proyecto tiene configurado un sistema para cambiar fácilmente entre diferentes ambientes de base de datos.

## Ambientes Disponibles

### 1. **Local** (SQLite)
- Base de datos: SQLite local
- Archivo: `database/database.sqlite`
- Ideal para: Desarrollo local, pruebas rápidas

### 2. **Supabase** (PostgreSQL)
- Base de datos: PostgreSQL en Supabase
- Host: `db.pizwuwasqflpshwmlczy.supabase.co`
- Ideal para: Producción, desarrollo con equipo

## Cómo Cambiar de Ambiente

### Windows

```bash
# Cambiar a ambiente LOCAL (SQLite)
switch-env.bat local

# Cambiar a ambiente SUPABASE (PostgreSQL)
switch-env.bat supabase
```

### Después de cambiar de ambiente

Si cambias a un ambiente por primera vez o la base de datos está vacía, ejecuta las migraciones:

```bash
php artisan migrate
```

Si necesitas datos de prueba:

```bash
php artisan db:seed
```

## Archivos de Configuración

- **`.env`** - Archivo activo (NO editar directamente)
- **`.env.local`** - Configuración para SQLite local
- **`.env.supabase`** - Configuración para Supabase PostgreSQL
- **`switch-env.bat`** - Script para cambiar ambientes

## Notas Importantes

1. **NO edites** el archivo `.env` directamente
2. Edita `.env.local` o `.env.supabase` según necesites
3. Usa `switch-env.bat` para aplicar los cambios
4. El servidor Laravel debe reiniciarse después de cambiar de ambiente

## Ejemplo de Flujo de Trabajo

```bash
# 1. Cambiar a ambiente local
switch-env.bat local

# 2. Ejecutar migraciones
php artisan migrate

# 3. Reiniciar servidor (si está corriendo)
# Ctrl+C para detener
php artisan serve

# 4. Cuando Supabase esté disponible, cambiar
switch-env.bat supabase

# 5. Reiniciar servidor nuevamente
```
