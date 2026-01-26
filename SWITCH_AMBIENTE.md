# 🔄 Guía de Cambio de Ambientes - ERP Distribuidora

## 📋 Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Ambientes Disponibles](#ambientes-disponibles)
- [Cómo Cambiar de Ambiente](#cómo-cambiar-de-ambiente)
- [Archivos de Configuración](#archivos-de-configuración)
- [Flujo de Trabajo Completo](#flujo-de-trabajo-completo)
- [Solución de Problemas](#solución-de-problemas)

---

## 📝 Descripción General

Este proyecto cuenta con un sistema flexible para cambiar entre diferentes ambientes de base de datos según tus necesidades de desarrollo o producción.

**¿Por qué es útil?**
- Desarrolla localmente sin depender de conexión a internet
- Cambia rápidamente entre desarrollo y producción
- Mantén configuraciones separadas y organizadas
- Evita conflictos al trabajar en equipo

---

## 🌍 Ambientes Disponibles

### 1. **Local** (SQLite)
```
Base de datos: SQLite
Archivo: database/database.sqlite
Ventajas:
  ✅ No requiere servidor de base de datos
  ✅ Funciona sin internet
  ✅ Perfecto para desarrollo rápido
  ✅ Fácil de resetear
Uso ideal: Desarrollo local, pruebas rápidas
```

### 2. **Supabase** (PostgreSQL)
```
Base de datos: PostgreSQL en la nube
Host: db.pizwuwasqflpshwmlczy.supabase.co
Puerto: 5432
Ventajas:
  ✅ Base de datos compartida con el equipo
  ✅ Backup automático
  ✅ Escalable
  ✅ Funciones avanzadas de PostgreSQL
Uso ideal: Producción, colaboración en equipo
```

---

## 🚀 Cómo Cambiar de Ambiente

### En Windows

#### Opción 1: Usar el Script (Recomendado)

**Cambiar a ambiente LOCAL (SQLite):**
```bash
switch-env.bat local
```

**Cambiar a ambiente SUPABASE (PostgreSQL):**
```bash
switch-env.bat supabase
```

#### Opción 2: Manual

**Para Local:**
```bash
copy /Y .env.local .env
```

**Para Supabase:**
```bash
copy /Y .env.supabase .env
```

### ⚠️ IMPORTANTE
Después de cambiar de ambiente, **DEBES reiniciar el servidor Laravel**:

1. Detén el servidor: `Ctrl+C`
2. Reinicia: `php artisan serve`

---

## 📁 Archivos de Configuración

| Archivo | Descripción | ¿Editar? |
|---------|-------------|----------|
| `.env` | **Archivo activo** - Es el que usa Laravel | ❌ NO editar directamente |
| `.env.local` | Configuración para SQLite local | ✅ SÍ, si necesitas cambiar config local |
| `.env.supabase` | Configuración para Supabase PostgreSQL | ✅ SÍ, si cambia la contraseña de Supabase |
| `switch-env.bat` | Script para cambiar ambientes | ⚙️ Solo si sabes lo que haces |
| `database/database.sqlite` | Archivo de base de datos SQLite | 🗄️ Generado automáticamente |

### ⚡ Regla de Oro
**NUNCA edites el archivo `.env` directamente.**
Siempre edita `.env.local` o `.env.supabase` y luego ejecuta el script de cambio.

---

## 🔄 Flujo de Trabajo Completo

### Escenario 1: Empezar a desarrollar localmente

```bash
# 1. Cambiar a ambiente local
switch-env.bat local

# 2. Ejecutar migraciones (primera vez)
php artisan migrate

# 3. (Opcional) Cargar datos de prueba
php artisan db:seed

# 4. Iniciar servidor
php artisan serve
```

### Escenario 2: Subir cambios a producción

```bash
# 1. Asegúrate de que tus migraciones funcionan en local
php artisan migrate:fresh

# 2. Cambiar a ambiente Supabase
switch-env.bat supabase

# 3. Ejecutar migraciones en producción
php artisan migrate

# 4. Reiniciar servidor
php artisan serve
```

### Escenario 3: Probar algo rápido sin afectar producción

```bash
# 1. Cambiar a local
switch-env.bat local

# 2. Resetear base de datos
php artisan migrate:fresh --seed

# 3. Hacer tus pruebas...

# 4. Volver a producción cuando termines
switch-env.bat supabase
```

---

## 🐛 Solución de Problemas

### Error: "could not translate host name"
**Causa:** El servidor de Supabase no está disponible (mantenimiento, internet, etc.)

**Solución:**
```bash
# Cambiar temporalmente a local
switch-env.bat local
php artisan serve
```

### Error: "database table not found"
**Causa:** Las migraciones no se han ejecutado en el ambiente actual

**Solución:**
```bash
php artisan migrate
```

### Error: "SQLSTATE[HY000] [14] unable to open database file"
**Causa:** El archivo SQLite no existe

**Solución:**
```bash
# Crear el archivo
touch database/database.sqlite

# O en Windows
type nul > database\database.sqlite

# Luego ejecutar migraciones
php artisan migrate
```

### El cambio de ambiente no se refleja
**Causa:** El servidor no se reinició después del cambio

**Solución:**
1. Detén el servidor: `Ctrl+C`
2. Verifica que el `.env` cambió: `type .env` (Windows) o `cat .env` (Linux/Mac)
3. Reinicia: `php artisan serve`

### "¿Cómo sé en qué ambiente estoy?"
**Solución:**
```bash
# Ver la configuración actual
php artisan config:show database

# O revisar el archivo .env
type .env | findstr DB_CONNECTION
```

---

## 📊 Comparación Rápida

| Característica | Local (SQLite) | Supabase (PostgreSQL) |
|----------------|----------------|----------------------|
| Velocidad | ⚡⚡⚡ Muy rápida | ⚡⚡ Rápida (depende de internet) |
| Internet requerido | ❌ No | ✅ Sí |
| Compartir datos | ❌ No | ✅ Sí |
| Backup automático | ❌ No | ✅ Sí |
| Funciones avanzadas | ⚠️ Limitadas | ✅ Completas |
| Ideal para | Desarrollo | Producción |

---

## 🎯 Mejores Prácticas

1. **Desarrolla en local primero**
   Siempre prueba tus cambios en SQLite local antes de subir a Supabase

2. **Commits pequeños**
   Haz commits frecuentes con migraciones que funcionen en ambos ambientes

3. **Documenta cambios de schema**
   Cada vez que modifiques la estructura de la BD, documéntalo

4. **Backup antes de cambios grandes**
   Antes de ejecutar `migrate:fresh` en producción, haz backup

5. **No commitees archivos de ambiente**
   `.env`, `.env.local`, `.env.supabase` no deben subirse a git

---

## 📞 Soporte

Si tienes problemas o preguntas:
1. Revisa la sección [Solución de Problemas](#solución-de-problemas)
2. Verifica los logs de Laravel: `storage/logs/laravel.log`
3. Contacta al equipo de desarrollo

---

**Última actualización:** Enero 2026
**Versión del documento:** 1.0
