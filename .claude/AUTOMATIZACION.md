# Guía de Automatización con Claude Code

## ⚠️ Aclaración Importante

**Claude Code** (lo que estás usando ahora) es **diferente** de VS Code Insiders.

- **Claude Code:** CLI de Anthropic que ejecutas desde la terminal
- **VS Code Insiders:** Editor de código de Microsoft con extensiones de AI

El setting `"chat.experimental.tools.terminal.autoReplyToPrompts": true` es específico de **VS Code Insiders** y su integración con GitHub Copilot/Chat, **NO aplica a Claude Code**.

---

## Capacidades de Automatización de Claude Code

Aunque no puedo configurar VS Code, Claude Code tiene potentes capacidades de automatización:

### 1. Agentes Especializados (Task Tool)

Claude Code puede lanzar **agentes autónomos** que trabajan de forma independiente:

#### Tipos de Agentes

**a) general-purpose**
- Para tareas complejas de múltiples pasos
- Búsqueda de código
- Investigación profunda
- Ejecución de tareas complejas

**b) Explore**
- Especializado en explorar codebases
- Búsqueda rápida de archivos y patrones
- Análisis de código
- Niveles: "quick", "medium", "very thorough"

**c) Plan**
- Planificación de tareas
- Diseño de arquitectura
- Estrategias de implementación

### 2. Ejemplo de Uso: Automatización de Desarrollo

```markdown
# Puedes pedirme que ejecute múltiples agentes en paralelo:

Usuario: "Necesito implementar un sistema de roles.
Lanza 3 agentes en paralelo:
1. Uno que explore cómo funcionan los roles en Laravel
2. Otro que diseñe la migración de base de datos
3. Otro que busque ejemplos de middleware de autorización"

Claude Code ejecutará los 3 agentes simultáneamente y te traerá resultados.
```

### 3. Ejecución Paralela de Tareas

Claude Code puede:
- Leer múltiples archivos simultáneamente
- Ejecutar búsquedas en paralelo
- Lanzar múltiples comandos bash
- Procesar múltiples operaciones

### 4. Automatización con TodoWrite

Claude Code gestiona automáticamente:
- Lista de tareas pendientes
- Progreso en tiempo real
- Marcado de completadas
- Nuevas tareas descubiertas durante implementación

---

## Cómo Maximizar la Automatización

### 1. Sé Específico y Detallado

❌ **Malo:**
"Mejora el sistema"

✅ **Bueno:**
"Implementa un sistema de roles con Admin, Vendedor y Almacenista.
Incluye:
- Migración de tabla roles y role_user
- Middleware para proteger rutas
- Seeder con roles base
- Actualizar AuthController
- Agregar blade directives @role
Trabaja en paralelo cuando sea posible."

### 2. Permite Trabajo Autónomo

❌ **Malo:**
"¿Qué necesito para implementar PDF?"

✅ **Bueno:**
"Implementa exportación a PDF de presupuestos usando DomPDF.
Investiga, instala dependencias, crea el servicio,
actualiza el controller, crea la vista PDF y prueba.
Hazlo todo automáticamente."

### 3. Usa Agentes para Investigación

❌ **Malo:**
"¿Cómo funciona Laravel Sanctum?"

✅ **Bueno:**
"Lanza un agente que investigue Laravel Sanctum,
analice si es compatible con nuestro proyecto,
y prepare un plan de implementación para API REST."

### 4. Solicita Múltiples Tareas en Paralelo

✅ **Bueno:**
"Implementa estas 5 features en paralelo usando agentes:
1. Sistema de roles
2. Exportación a PDF
3. Envío de emails
4. Búsqueda avanzada
5. Dashboard con gráficas"

---

## Limitaciones de Automatización

### Lo que Claude Code PUEDE hacer:

✅ Ejecutar comandos bash automáticamente
✅ Leer/escribir/editar múltiples archivos
✅ Instalar dependencias (composer, npm)
✅ Ejecutar migraciones y seeders
✅ Crear commits y PRs
✅ Buscar en toda la codebase
✅ Ejecutar tests
✅ Lanzar servidores en background
✅ Analizar errores y corregirlos
✅ Refactorizar código

### Lo que Claude Code NO PUEDE hacer:

❌ Configurar VS Code
❌ Instalar extensiones de VS Code
❌ Modificar settings de VS Code
❌ Interactuar con la UI de tu editor
❌ Acceder a configuraciones fuera del proyecto

---

## Configuraciones que SÍ Puedes Hacer

### 1. Hooks de Claude Code

Puedes configurar hooks en `.claude/settings.local.json`:

```json
{
  "hooks": {
    "pre-tool-call": "echo 'Ejecutando herramienta...'",
    "post-tool-call": "echo 'Herramienta ejecutada'"
  }
}
```

### 2. Slash Commands Personalizados

Crea comandos en `.claude/commands/`:

**Ejemplo: `.claude/commands/migrate-fresh.md`**
```markdown
Ejecuta php artisan migrate:fresh --seed y muestra el resultado
```

Luego úsalo con: `/migrate-fresh`

### 3. Configuración de Aprobaciones

En `.claude/settings.local.json`:

```json
{
  "approvals": {
    "bash": {
      "patterns": [
        "rm -rf *",
        "git push --force"
      ]
    }
  }
}
```

---

## Plan de Automatización para tu Proyecto

### Fase 1: Automatización Inmediata (Hoy)

1. **Sistema de Roles**
   ```
   Prompt: "Implementa sistema de roles (Admin, Vendedor, Almacenista).
   Usa múltiples agentes en paralelo para:
   - Diseñar migraciones
   - Crear middleware
   - Actualizar vistas
   - Crear seeders
   - Documentar cambios"
   ```

2. **Exportación a PDF**
   ```
   Prompt: "Implementa exportación a PDF de presupuestos y notas.
   Instala DomPDF, crea vistas, actualiza controllers,
   agrega botones de descarga. Hazlo todo en una sesión."
   ```

3. **Sistema de Búsqueda**
   ```
   Prompt: "Implementa búsqueda avanzada en presupuestos.
   Usa Scout o búsqueda SQL nativa. Implementa filtros
   por fecha, contacto, estado. Trabaja autónomamente."
   ```

### Fase 2: Automatización con Testing (Esta semana)

4. **Dashboard con Gráficas**
   ```
   Prompt: "Implementa dashboard con Chart.js.
   Incluye: ventas mensuales, top productos, stock bajo.
   Crea controllers, vistas, tests automáticos."
   ```

5. **Sistema de Emails**
   ```
   Prompt: "Configura Laravel Mail para enviar presupuestos.
   Usa Mailtrap para testing. Crea templates bonitos,
   configura queues, implementa botón de envío."
   ```

### Fase 3: Automatización Avanzada (Próxima semana)

6. **API REST Completa**
   ```
   Prompt: "Implementa API REST con Laravel Sanctum.
   Incluye todos los endpoints, autenticación con tokens,
   documentación con Swagger, tests de API."
   ```

7. **Testing Automatizado**
   ```
   Prompt: "Crea suite completa de tests:
   - Unit tests para modelos
   - Feature tests para controllers
   - Tests de integración de flujos completos
   Configura CI/CD con GitHub Actions."
   ```

---

## Ejemplo de Sesión Automatizada

### Comando a Claude Code:

```
Necesito implementar 3 features críticas en paralelo.
Usa agentes y trabaja autónomamente:

FEATURE 1: Sistema de Roles
- Migración: roles (name, description), role_user (role_id, user_id)
- Modelo Role con relación User
- Middleware CheckRole
- Seeder con Admin, Vendedor, Almacenista
- Actualizar rutas con middleware
- Blade directive @role()

FEATURE 2: Exportación a PDF
- Composer require dompdf
- Servicio PDFService
- Vistas: presupuesto-pdf.blade.php, nota-pdf.blade.php
- Métodos en controllers: downloadPDF()
- Botones en vistas index y show
- Testing con PDF generado

FEATURE 3: Búsqueda Avanzada
- Form de búsqueda en presupuestos/index
- Filtros: número, contacto, fecha_desde, fecha_hasta, estado, tipo
- Query builder con whereHas
- Paginación manteniendo filtros
- Botón "Limpiar filtros"

Cuando termines cada feature:
1. Ejecuta migraciones
2. Prueba manualmente
3. Muestra resultado
4. Documenta cambios

Trabaja sin preguntarme a cada paso. Si hay decisiones de diseño,
usa las mejores prácticas de Laravel. Si encuentras errores, corrígelos.

¡Adelante!
```

### Lo que Claude Code Hará:

1. ✅ Crear todo list con 3 features principales
2. ✅ Dividir cada feature en sub-tareas
3. ✅ Comenzar con Feature 1
4. ✅ Crear migraciones, modelos, middleware
5. ✅ Ejecutar `composer install` si es necesario
6. ✅ Ejecutar `php artisan migrate`
7. ✅ Crear seeders y ejecutarlos
8. ✅ Actualizar vistas y rutas
9. ✅ Pasar a Feature 2
10. ✅ Instalar DomPDF
11. ✅ Crear servicio y vistas PDF
12. ✅ Actualizar controllers
13. ✅ Pasar a Feature 3
14. ✅ Implementar búsqueda
15. ✅ Probar todo
16. ✅ Reportar resultados

**Todo en una sola sesión, sin necesidad de tu intervención.**

---

## Consejos Pro

### 1. Deja Trabajar a Claude Code Durante la Noche

```
Prompt: "Tengo que dormir 8 horas. Mientras duermo,
implementa estas 10 features usando el plan del archivo
DOCUMENTACION_PROYECTO.md sección 'Próximos Pasos'.

Prioriza en este orden:
1. Roles y permisos
2. PDF exports
3. Email notifications
4. Dashboard con gráficas
5. Búsqueda avanzada
6. Gestión de pagos
7. Entregas parciales
8. Testing
9. API REST
10. Optimizaciones

Si encuentras errores, debuggea y corrige.
Si necesitas decisiones de diseño, usa mejores prácticas.
Documenta todo en CHANGELOG.md.

Cuando despierte quiero ver el progreso en git log."
```

### 2. Usa Agentes para Code Review

```
Prompt: "Lanza un agente que revise todo el código del proyecto,
identifique code smells, patrones anti-Laravel, y vulnerabilidades.
Genera reporte y sugiere refactorizaciones."
```

### 3. Automatiza Refactoring

```
Prompt: "Refactoriza el proyecto completo:
- Extrae lógica de controllers a servicios
- Implementa Repository Pattern
- Usa Form Requests para validaciones
- Crea Events y Listeners
- Optimiza queries N+1
Hazlo módulo por módulo automáticamente."
```

### 4. Debugging Automático

```
Prompt: "Ejecuta php artisan serve en background.
Navega a todas las rutas del sistema.
Si encuentras errores, debuggea y corrige automáticamente.
Reporta cuando todo funcione."
```

---

## Comparación: Claude Code vs VS Code Insiders

| Feature | Claude Code | VS Code Insiders |
|---------|-------------|------------------|
| **Entorno** | CLI/Terminal | Editor GUI |
| **Agentes autónomos** | ✅ Sí (Task tool) | ❌ No |
| **Ejecución paralela** | ✅ Múltiples tools | ⚠️ Limitado |
| **Acceso a filesystem** | ✅ Total | ✅ Total |
| **Git operations** | ✅ Commits, PRs | ✅ Commits |
| **Bash commands** | ✅ Ilimitado | ⚠️ Via terminal |
| **Background tasks** | ✅ Sí | ❌ No |
| **Code review** | ✅ Agentes | ⚠️ Manual |
| **Auto-fix errors** | ✅ Autónomo | ⚠️ Sugerencias |
| **Terminal prompts** | ✅ Manual | ✅ Auto (con setting) |
| **Mejor para** | Desarrollo full-stack | Edición de código |

---

## Configuración Recomendada para Máxima Productividad

### En `.claude/settings.local.json`:

```json
{
  "model": "claude-sonnet-4-5",
  "hooks": {
    "user-prompt-submit-hook": "git status --short"
  },
  "autoApprove": {
    "bash": [
      "php artisan:*",
      "composer install",
      "npm install",
      "npm run build",
      "git add:*",
      "git commit:*"
    ]
  }
}
```

### Slash Commands Útiles:

Crea en `.claude/commands/`:

**`dev-setup.md`:**
```
Ejecuta composer install, npm install, copia .env.example a .env,
genera app key, ejecuta migraciones, seeders, y npm run build
```

**`fresh-start.md`:**
```
Ejecuta php artisan migrate:fresh --seed y muestra el resultado
```

**`run-tests.md`:**
```
Ejecuta php artisan test y analiza los resultados. Si hay fallos, corrígelos.
```

**`deploy-check.md`:**
```
Verifica que no haya errores de sintaxis, ejecuta tests,
optimiza autoload, cachea configs, verifica .env.production
```

---

## Conclusión

**Claude Code NO puede configurar VS Code**, pero tiene capacidades de automatización mucho más potentes:

✅ **Agentes autónomos** que trabajan en paralelo
✅ **Ejecución de múltiples tareas** sin supervisión
✅ **Auto-corrección de errores**
✅ **Implementación completa de features**
✅ **Testing y deployment automatizado**

Para maximizar productividad:
1. Da instrucciones detalladas pero deja que Claude trabaje autónomamente
2. Usa agentes Task para exploración e investigación
3. Solicita trabajo en paralelo cuando sea posible
4. Confía en que Claude corrija errores sin preguntarte
5. Deja tareas complejas ejecutándose mientras haces otras cosas

**Recomendación:** Usa Claude Code para desarrollo y automatización,
y VS Code Insiders para edición manual cuando lo prefieras.

---

**Última actualización:** 11 de Noviembre 2025
**Versión:** 1.0
