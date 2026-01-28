# 📋 Comandos Artisan - Play Amateur

Este documento describe los comandos Artisan más importantes para el mantenimiento y operación del sistema Play Amateur.

## 🎯 Comando Principal: Actualización de Estados de Partidos

### `php artisan partidos:update-states`

**Descripción:** Actualiza automáticamente los estados de los partidos según el horario programado.

**Funcionalidad:**
- **Inicia partidos:** Cambia partidos de `programado` a `en_curso` cuando llega su hora de inicio
- **Finaliza partidos:** Cambia partidos de `en_curso` a `finalizado` 1 hora después del inicio
- **Ejecución automática:** Diseñado para ejecutarse periódicamente via cron job

**Uso recomendado:**
```bash
# Ejecutar manualmente
php artisan partidos:update-states

# Configurar en cron para ejecución automática cada minuto
* * * * * cd /path-to-your-project && php artisan partidos:update-states
```

**Ejemplo de salida:**
```
Actualizando estados de partidos...
Partido 1 iniciado: Partido Amistoso - Sábado
Partido 3 finalizado: Torneo Primavera
Estados de partidos actualizados correctamente.
```

---

## 🗄️ Comandos de Base de Datos

### `php artisan migrate`

**Descripción:** Ejecuta todas las migraciones pendientes de base de datos.

**Uso:**
```bash
php artisan migrate
```

### `php artisan migrate:status`

**Descripción:** Muestra el estado de todas las migraciones.

**Uso:**
```bash
php artisan migrate:status
```

### `php artisan migrate:rollback`

**Descripción:** Revierte la última migración ejecutada.

**Uso:**
```bash
php artisan migrate:rollback
```

### `php artisan migrate:fresh`

**Descripción:** Elimina todas las tablas y vuelve a ejecutar todas las migraciones.

**Uso:**
```bash
php artisan migrate:fresh
```

---

## 🚀 Comandos de Servidor y Cache

### `php artisan serve`

**Descripción:** Inicia el servidor de desarrollo de Laravel.

**Uso:**
```bash
php artisan serve
php artisan serve --host=0.0.0.0 --port=8000
```

### `php artisan config:clear`

**Descripción:** Limpia la caché de configuración.

**Uso:**
```bash
php artisan config:clear
```

### `php artisan cache:clear`

**Descripción:** Limpia la caché de aplicación.

**Uso:**
```bash
php artisan cache:clear
```

### `php artisan route:clear`

**Descripción:** Limpia la caché de rutas.

**Uso:**
```bash
php artisan route:clear
```

### `php artisan view:clear`

**Descripción:** Limpia la caché de vistas compiladas.

**Uso:**
```bash
php artisan view:clear
```

### `php artisan optimize:clear`

**Descripción:** Limpia todas las cachés (config, cache, route, view, compiled).

**Uso:**
```bash
php artisan optimize:clear
```

---

## 🔐 Comandos de Autenticación

### `php artisan sanctum:prune-expired`

**Descripción:** Elimina tokens de autenticación expirados.

**Parámetros:**
- `--hours=24`: Número de horas para considerar tokens como expirados (por defecto 24)

**Uso:**
```bash
php artisan sanctum:prune-expired
php artisan sanctum:prune-expired --hours=168  # 7 días
```

---

## 📊 Comandos de Programación (Schedule)

### `php artisan schedule:list`

**Descripción:** Lista todas las tareas programadas.

**Uso:**
```bash
php artisan schedule:list
```

### `php artisan schedule:run`

**Descripción:** Ejecuta todas las tareas programadas que están pendientes.

**Uso:**
```bash
php artisan schedule:run
```

### `php artisan schedule:work`

**Descripción:** Inicia un worker que ejecuta las tareas programadas automáticamente.

**Uso:**
```bash
php artisan schedule:work
```

---

## 📋 Comandos de Rutas

### `php artisan route:list`

**Descripción:** Lista todas las rutas registradas en la aplicación.

**Uso:**
```bash
php artisan route:list
php artisan route:list --name=auth  # Filtrar por nombre
php artisan route:list --path=api   # Filtrar por path
```

### `php artisan route:cache`

**Descripción:** Crea un archivo de caché de rutas para mejorar el rendimiento.

**Uso:**
```bash
php artisan route:cache
```

### `php artisan route:clear`

**Descripción:** Elimina el archivo de caché de rutas.

**Uso:**
```bash
php artisan route:clear
```

---

## 📦 Comandos de Storage

### `php artisan storage:link`

**Descripción:** Crea un enlace simbólico desde `public/storage` a `storage/app/public`.

**Uso:**
```bash
php artisan storage:link
```

---

## 🔧 Comandos de Desarrollo

### `php artisan make:model`

**Descripción:** Crea una nueva clase de modelo Eloquent.

**Uso:**
```bash
php artisan make:model User
php artisan make:model Post -m  # Con migración
php artisan make:model Comment -c -r  # Con controlador y rutas
```

### `php artisan make:controller`

**Descripción:** Crea un nuevo controlador.

**Uso:**
```bash
php artisan make:controller UserController
php artisan make:controller API/UserController --api
```

### `php artisan make:migration`

**Descripción:** Crea una nueva migración de base de datos.

**Uso:**
```bash
php artisan make:migration create_users_table
php artisan make:migration add_email_to_users_table --table=users
```

### `php artisan make:command`

**Descripción:** Crea un nuevo comando Artisan.

**Uso:**
```bash
php artisan make:command UpdatePartidoStates
```

---

## 📈 Comandos de Testing

### `php artisan test`

**Descripción:** Ejecuta todos los tests de la aplicación.

**Uso:**
```bash
php artisan test
php artisan test --filter=UserTest
php artisan test --coverage
```

### `php artisan make:test`

**Descripción:** Crea una nueva clase de test.

**Uso:**
```bash
php artisan make:test UserTest
php artisan make:test UserTest --feature
```

---

## 🎯 Flujo de Comandos Recomendado

### Para desarrollo diario:
```bash
# Limpiar cachés
php artisan optimize:clear

# Verificar estado de migraciones
php artisan migrate:status

# Iniciar servidor
php artisan serve
```

### Para despliegue en producción:
```bash
# Ejecutar migraciones
php artisan migrate

# Crear enlace de storage
php artisan storage:link

# Cachear configuración y rutas
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Para mantenimiento:
```bash
# Actualizar estados de partidos (automático)
php artisan partidos:update-states

# Limpiar tokens expirados
php artisan sanctum:prune-expired

# Verificar rutas
php artisan route:list
```

---

## ⚠️ Notas Importantes

- **Comando crítico:** `partidos:update-states` debe ejecutarse periódicamente (cada minuto) para mantener los estados de partidos actualizados
- **Configuración de cron:** Asegúrate de configurar el cron job para el comando de partidos en producción
- **Permisos:** Algunos comandos requieren permisos de escritura en directorios específicos
- **Entorno:** Algunos comandos se comportan diferente en desarrollo vs producción

---

**Última actualización:** Diciembre 2024
**Versión del sistema:** 2.0