@echo off
REM Script para cambiar entre ambientes de desarrollo

if "%1"=="" (
    echo Uso: switch-env.bat [local^|supabase]
    echo.
    echo Ambientes disponibles:
    echo   local     - Base de datos SQLite local
    echo   supabase  - Base de datos PostgreSQL en Supabase
    exit /b 1
)

if "%1"=="local" (
    echo Cambiando a ambiente LOCAL (SQLite)...
    copy /Y .env.local .env
    echo Ambiente cambiado a LOCAL
    echo.
    echo Ejecuta: php artisan migrate (si es necesario^)
    exit /b 0
)

if "%1"=="supabase" (
    echo Cambiando a ambiente SUPABASE (PostgreSQL)...
    copy /Y .env.supabase .env
    echo Ambiente cambiado a SUPABASE
    echo.
    echo Recuerda que la base de datos debe estar accesible
    exit /b 0
)

echo Ambiente no reconocido: %1
echo Usa: local o supabase
exit /b 1
