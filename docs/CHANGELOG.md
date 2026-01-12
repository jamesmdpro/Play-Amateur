# Release Notes

## [Fase 2 - v2.0.0 (2025-11-27)]

### 🎉 Nuevas Funcionalidades

#### Sistema de Wallet (Cartera Digital)
- Cartera digital para cada usuario con saldo en tiempo real
- Solicitud de recarga mediante comprobante Nequi
- Aprobación/rechazo manual por administrador
- Historial completo de transacciones con auditoría
- Descuento automático al confirmar participación en partidos

#### Confirmación Real de Partidos
- Sistema de estados: `inscrito`, `confirmado`, `cancelado`, `suplente`
- Pago obligatorio para confirmar participación
- Validación de saldo antes de confirmar
- Sistema de cupos limitados por equipo
- Gestión automática de lista de espera

#### Sistema de Sanciones (Tarjeta Naranja)
- Sanción automática por cancelar después de confirmar pago
- Escalado progresivo de sanciones:
  - Primera sanción: 7 días de suspensión
  - Segunda sanción: 15 días de suspensión
  - Tercera sanción: 30 días de suspensión
- Costo de reactivación: $15,000
- Bloqueo automático durante período de sanción
- Historial completo de sanciones

#### Sistema de Reemplazo por Suplentes
- Asignación automática de suplente cuando se cancela un cupo
- Notificación inmediata al suplente promovido
- Gestión de lista de espera por equipo
- Prioridad por orden de inscripción

#### Sistema de Notificaciones
- Notificaciones en tiempo real para usuarios
- Tipos de notificaciones:
  - Recarga aprobada/rechazada
  - Confirmación de partido exitosa
  - Sanción aplicada
  - Asignación como suplente
  - Sanción pagada y cuenta reactivada
- Contador de notificaciones no leídas
- Marcar como leída individual o todas

### 📦 Nuevos Modelos
- `WalletTransaction` - Gestión de transacciones de wallet
- `Sancion` - Sistema de sanciones
- `Notificacion` - Sistema de notificaciones

### 🎮 Nuevos Controladores
- `WalletController` - Gestión de wallet y recargas
- `InscripcionController` - Inscripciones con sistema de pago
- `SancionController` - Gestión de sanciones
- `NotificacionController` - Sistema de notificaciones

### 🗄️ Nuevas Migraciones
- `create_wallet_transactions_table` - Tabla de transacciones
- `create_sanciones_table` - Tabla de sanciones
- `update_inscripciones_table` - Actualización con estados de pago
- `create_notificaciones_table` - Tabla de notificaciones

### 🌐 Nuevos Endpoints API (18)
- Wallet: 5 endpoints
- Inscripciones: 4 endpoints
- Sanciones: 3 endpoints
- Notificaciones: 4 endpoints
- Actualización de rutas en `routes/api.php`

### 🎨 Nuevas Vistas Frontend
- `wallet/index.blade.php` - Gestión de cartera
- `admin/recargas.blade.php` - Administración de recargas
- `sanciones/index.blade.php` - Gestión de sanciones
- `notificaciones/index.blade.php` - Centro de notificaciones
- `partidos/dashboard.blade.php` - Dashboard principal mejorado

### 📚 Documentación
- `docs/API_FASE2.md` - Documentación completa de API
- `docs/README_FASE2.md` - Guía de implementación
- `docs/FASE2_COMPLETADA.md` - Resumen ejecutivo
- `api-tests-fase2.http` - Archivo de pruebas HTTP

### 🔧 Mejoras al Modelo User
- `tieneSancionActiva()` - Verificar sanción activa
- `sancionActiva()` - Obtener sanción vigente
- `tieneSaldo($monto)` - Verificar saldo suficiente
- `descontarSaldo()` - Descontar saldo con registro
- `agregarSaldo()` - Agregar saldo con registro
- Relaciones: `transacciones()`, `sanciones()`, `notificaciones()`

### 🛡️ Validaciones y Seguridad
- Validación de saldo antes de confirmar pago
- Validación de sanción activa antes de inscribirse
- Validación de cupos disponibles por equipo
- Validación de comprobante de pago (imagen, max 5MB)
- Validación de monto mínimo de recarga ($10,000)
- Validación de permisos de administrador
- Validación de propiedad de recursos
- Registro de auditoría en todas las transacciones

---

## [Fase 1 - v1.0.0]

### Funcionalidades Base
- Sistema de autenticación con Laravel Sanctum
- Gestión de usuarios con roles (admin, cancha, arbitro, jugador)
- Perfiles de usuario con foto
- Sistema de partidos
- Inscripciones básicas
- Frontend con Blade templates

---

## [Unreleased](https://github.com/laravel/laravel/compare/v12.0.0...master)

## [v12.0.0 (2025-??-??)](https://github.com/laravel/laravel/compare/v11.0.2...v12.0.0)

Laravel 12 includes a variety of changes to the application skeleton. Please consult the diff to see what's new.
