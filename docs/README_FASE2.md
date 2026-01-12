# FASE 2 - Sistema de Pagos, Confirmaciones y Sanciones

## Resumen

La Fase 2 implementa un sistema completo de gestión de pagos, confirmaciones reales y sanciones para profesionalizar la organización de partidos amateur.

## Características Implementadas

### 1. Sistema de Wallet (Cartera Digital)
- ✅ Saldo digital para cada usuario
- ✅ Solicitud de recarga mediante comprobante Nequi
- ✅ Aprobación/rechazo manual por administrador
- ✅ Historial completo de transacciones
- ✅ Descuento automático al confirmar partidos

### 2. Confirmación Real de Partidos
- ✅ Estados de inscripción: `inscrito`, `confirmado`, `cancelado`, `suplente`
- ✅ Pago obligatorio para confirmar participación
- ✅ Validación de saldo antes de confirmar
- ✅ Sistema de cupos limitados por equipo

### 3. Sistema de Sanciones (Tarjeta Naranja)
- ✅ Sanción automática por cancelar después de confirmar
- ✅ Escalado de sanciones:
  - 1ª sanción: 7 días
  - 2ª sanción: 15 días
  - 3ª sanción: 30 días
- ✅ Costo de reactivación: $15,000
- ✅ Bloqueo automático durante sanción

### 4. Sistema de Suplentes
- ✅ Asignación automática cuando se cancela un cupo
- ✅ Notificación al suplente cuando es promovido a titular
- ✅ Gestión de lista de espera por equipo

### 5. Sistema de Notificaciones
- ✅ Notificaciones en tiempo real
- ✅ Tipos de notificaciones:
  - Recarga aprobada/rechazada
  - Confirmación de partido
  - Sanción aplicada
  - Asignación como suplente
  - Sanción pagada
- ✅ Contador de notificaciones no leídas

## Estructura de Base de Datos

### Nuevas Tablas

#### `wallet_transactions`
- Registro de todas las transacciones de wallet
- Tipos: recarga, pago_partido, sancion, reembolso
- Estados: pendiente, aprobado, rechazado

#### `sanciones`
- Registro de sanciones aplicadas
- Escalado automático según número de sanción
- Control de fechas de inicio y fin

#### `notificaciones`
- Sistema de notificaciones para usuarios
- Tipos personalizados según evento
- Estado de lectura

#### Actualización `inscripciones`
- Nuevos campos: `confirmado_en`, `pago_realizado`
- Estados actualizados

## Endpoints API

### Wallet
- `GET /api/wallet` - Ver saldo y transacciones
- `POST /api/wallet/recarga` - Solicitar recarga
- `POST /api/wallet/recarga/{id}/aprobar` - Aprobar recarga (Admin)
- `POST /api/wallet/recarga/{id}/rechazar` - Rechazar recarga (Admin)
- `GET /api/wallet/recargas-pendientes` - Listar pendientes (Admin)

### Inscripciones
- `GET /api/inscripciones/mis-inscripciones` - Mis inscripciones
- `POST /api/inscripciones/partido/{id}` - Inscribirse
- `POST /api/inscripciones/{id}/confirmar-pago` - Confirmar pago
- `POST /api/inscripciones/{id}/cancelar` - Cancelar inscripción

### Sanciones
- `GET /api/sanciones/mis-sanciones` - Mis sanciones
- `POST /api/sanciones/{id}/pagar` - Pagar reactivación
- `GET /api/sanciones/listado` - Todas las sanciones (Admin)

### Notificaciones
- `GET /api/notificaciones` - Todas las notificaciones
- `GET /api/notificaciones/no-leidas` - No leídas
- `POST /api/notificaciones/{id}/marcar-leida` - Marcar como leída
- `POST /api/notificaciones/marcar-todas-leidas` - Marcar todas

## Vistas Frontend

### Para Jugadores
- `/wallet` - Gestión de cartera y recargas
- `/partidos/dashboard` - Dashboard principal con partidos e inscripciones
- `/sanciones` - Ver y pagar sanciones
- `/notificaciones` - Centro de notificaciones

### Para Administradores
- `/admin/recargas` - Aprobar/rechazar recargas

## Flujo de Usuario

### Flujo Normal
1. Usuario se inscribe a un partido (estado: `inscrito`)
2. Usuario confirma pago (descuenta del saldo, estado: `confirmado`)
3. Usuario juega el partido
4. Sistema registra la participación

### Flujo con Cancelación
1. Usuario se inscribe y confirma pago
2. Usuario cancela después de confirmar
3. Sistema aplica sanción automática
4. Sistema busca suplente disponible
5. Suplente es notificado y promovido a titular
6. Usuario sancionado debe pagar $15,000 para reactivar

### Flujo de Recarga
1. Usuario solicita recarga con comprobante Nequi
2. Administrador revisa comprobante
3. Administrador aprueba o rechaza
4. Usuario recibe notificación
5. Si aprobado, saldo se actualiza automáticamente

## Instalación y Configuración

### 1. Ejecutar Migraciones
```bash
php artisan migrate
```

### 2. Configurar Storage
```bash
php artisan storage:link
```

### 3. Configurar Permisos
Asegurar que la carpeta `storage/app/public/comprobantes` tenga permisos de escritura.

### 4. Configurar Variables de Entorno
```env
FILESYSTEM_DISK=public
```

## Modelos Creados

- `WalletTransaction` - Transacciones de wallet
- `Sancion` - Sanciones de usuarios
- `Notificacion` - Notificaciones del sistema

## Controladores Creados

- `WalletController` - Gestión de wallet y recargas
- `InscripcionController` - Inscripciones con pago
- `SancionController` - Gestión de sanciones
- `NotificacionController` - Sistema de notificaciones

## Métodos Agregados al Modelo User

- `tieneSancionActiva()` - Verifica si tiene sanción activa
- `sancionActiva()` - Obtiene la sanción activa
- `tieneSaldo($monto)` - Verifica si tiene saldo suficiente
- `descontarSaldo($monto, $tipo, $partidoId, $notas)` - Descuenta saldo
- `agregarSaldo($monto, $tipo, $notas)` - Agrega saldo

## Validaciones Implementadas

- ✅ Validación de saldo antes de confirmar pago
- ✅ Validación de sanción activa antes de inscribirse
- ✅ Validación de cupos disponibles por equipo
- ✅ Validación de comprobante de pago (imagen, max 5MB)
- ✅ Validación de monto mínimo de recarga ($10,000)

## Seguridad

- ✅ Autenticación requerida en todos los endpoints
- ✅ Validación de permisos de administrador
- ✅ Validación de propiedad de recursos
- ✅ Almacenamiento seguro de comprobantes
- ✅ Registro de auditoría en transacciones

## Próximos Pasos (Fase 3)

- Integración con pasarelas de pago reales
- Sistema de estadísticas de jugadores
- Ranking y reputación
- Sistema de árbitros y calificaciones
- Reportes y analytics

## Soporte

Para más información, consulta:
- `docs/API_FASE2.md` - Documentación completa de API
- `api-tests-fase2.http` - Ejemplos de pruebas HTTP
