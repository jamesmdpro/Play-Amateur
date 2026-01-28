# Arquitectura del Sistema - Play Amateur

## Introducción

Play Amateur es una plataforma web diseñada para la organización y gestión de partidos de fútbol amateur. El sistema permite a usuarios con diferentes roles (jugadores, canchas, árbitros y administradores) interactuar de manera eficiente, incorporando un sistema de pagos digitales, confirmaciones reales y sanciones para asegurar el compromiso y profesionalismo en los encuentros.

La arquitectura se basa en Laravel (PHP) para el backend, con una API RESTful que soporta autenticación mediante tokens (Sanctum), y vistas Blade para el frontend. La base de datos utiliza MySQL con migraciones para mantener la integridad y versionado del esquema.

## Arquitectura General

### Patrón Arquitectónico
- **MVC (Model-View-Controller)**: Separación clara entre lógica de negocio, presentación y control.
- **API RESTful**: Endpoints stateless con autenticación por token.
- **Repository Pattern**: Para abstracción de acceso a datos (implícito en Eloquent ORM).

### Tecnologías Principales
- **Backend**: Laravel 10.x, PHP 8.1+
- **Base de Datos**: MySQL 8.0+
- **Autenticación**: Laravel Sanctum
- **Frontend**: Blade Templates, Bootstrap 5, JavaScript vanilla
- **Almacenamiento**: Disco local (storage/app/public) para archivos
- **Servidor**: Compatible con Apache/Nginx

### Roles del Sistema
1. **Jugador**: Usuario final que se inscribe y paga por partidos
2. **Cancha**: Gestiona partidos en sus instalaciones
3. **Árbitro**: Participa como juez en partidos
4. **Admin**: Gestiona usuarios, aprueba recargas y supervisa el sistema

## Diagrama de Flujo Principal

```
┌─────────────────────────────────────────────────────────────────┐
│                         USUARIO JUGADOR                          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      1. REGISTRO / LOGIN                         │
│                    (AuthController)                              │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    2. SOLICITAR RECARGA                          │
│                    (WalletController)                            │
│  • Subir comprobante Nequi                                       │
│  • Estado: PENDIENTE                                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    3. ADMIN APRUEBA                              │
│                    (WalletController)                            │
│  • Revisa comprobante                                            │
│  • Aprueba/Rechaza                                               │
│  • Actualiza saldo                                               │
│  • Envía notificación                                            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                  4. INSCRIBIRSE A PARTIDO                        │
│                  (InscripcionController)                         │
│  • Verifica sanción activa                                       │
│  • Verifica cupos disponibles                                    │
│  • Estado: INSCRITO                                              │
│  • Tipo: TITULAR o SUPLENTE                                      │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   5. CONFIRMAR PAGO                              │
│                  (InscripcionController)                         │
│  • Verifica saldo suficiente                                     │
│  • Descuenta del wallet                                          │
│  • Estado: CONFIRMADO                                            │
│  • Envía notificación                                            │
└─────────────────────────────────────────────────────────────────┘
                    ┌─────────┴─────────┐
                    │                   │
                    ▼                   ▼
        ┌───────────────────┐  ┌───────────────────┐
        │   6A. JUGAR       │  │  6B. CANCELAR     │
        │   PARTIDO         │  │  (con sanción)    │
        └───────────────────┘  └───────────────────┘
                                    │
                                    ▼
                        ┌───────────────────────────────┐
                        │   7. SANCIÓN AUTOMÁTICA       │
                        │   (SancionController)         │
                        │  • Escalado: 7/15/30 días     │
                        │  • Bloqueo de cuenta          │
                        │  • Buscar suplente            │
                        │  • Notificar suplente         │
                        └───────────────────────────────┘
                                    │
                                    ▼
                        ┌───────────────────────────────┐
                        │   8. PAGAR REACTIVACIÓN       │
                        │   (SancionController)         │
                        │  • Costo: $15,000             │
                        │  • Desbloquear cuenta         │
                        │  • Envía notificación         │
                        └───────────────────────────────┘
```

## Estructura de Base de Datos

### Diagrama Entidad-Relación

```
┌─────────────────┐
│     USERS       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ password        │
│ rol             │
│ wallet          │ ◄── SALDO ACTUAL
│ posicion        │
│ nivel           │
│ ciudad          │
│ foto            │
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────────┐
│  WALLET_TRANSACTIONS    │
├─────────────────────────┤
│ id (PK)                 │
│ user_id (FK)            │
│ tipo                    │ ◄── recarga, pago_partido, sancion, reembolso
│ monto                   │
│ saldo_anterior          │
│ saldo_nuevo             │
│ comprobante             │
│ estado                  │ ◄── pendiente, aprobado, rechazado
│ partido_id (FK)         │
│ aprobado_por (FK)       │
│ notas                   │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────┐
│    PARTIDOS     │
├─────────────────┤
│ id (PK)         │
│ nombre          │
│ descripcion     │
│ fecha_hora      │
│ ubicacion       │
│ costo           │
│ cupos_totales   │
│ cupos_suplentes │
│ estado          │ ◄── abierto, cerrado, en_curso, finalizado
│ creador_id (FK) │
│ arbitro_id (FK) │
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────────────┐
│    INSCRIPCIONES        │
├─────────────────────────┤
│ id (PK)                 │
│ partido_id (FK)         │
│ jugador_id (FK)         │
│ equipo                  │ ◄── A, B
│ es_suplente             │
│ estado                  │ ◄── inscrito, confirmado, cancelado
│ pago_realizado          │
│ confirmado_en           │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────────────┐
│      SANCIONES          │
├─────────────────────────┤
│ id (PK)                 │
│ user_id (FK)            │
│ partido_id (FK)         │
│ numero_sancion          │ ◄── 1, 2, 3
│ dias_suspension         │ ◄── 7, 15, 30
│ fecha_inicio            │
│ fecha_fin               │
│ monto_reactivacion      │ ◄── 15000
│ pagada                  │
│ activa                  │
│ created_at              │
│ updated_at              │
└─────────────────────────┘

┌─────────────────────────┐
│    NOTIFICACIONES       │
├─────────────────────────┤
│ id (PK)                 │
│ user_id (FK)            │
│ tipo                    │
│ titulo                  │
│ mensaje                 │
│ leida                   │
│ data (JSON)             │
│ created_at              │
│ updated_at              │
└─────────────────────────┘
```

### Migraciones Principales
- `users`: Tabla base de usuarios con campos extendidos
- `partidos`: Gestión de encuentros deportivos
- `inscripciones`: Relación usuario-partido con estados
- `wallet_transactions`: Historial financiero completo
- `sanciones`: Sistema de penalizaciones
- `notificaciones`: Comunicación interna

## Modelos (Eloquent ORM)

### User
- **Relaciones**: 
  - `inscripciones()`: HasMany
  - `sanciones()`: HasMany
  - `notificaciones()`: HasMany
  - `walletTransactions()`: HasMany
  - `partidosCreados()`: HasMany
- **Atributos calculados**: `saldo` (accesor)
- **Scopes**: `activos()`, `conSancionActiva()`

### Partido
- **Relaciones**: 
  - `creador()`: BelongsTo
  - `arbitro()`: BelongsTo
  - `inscripciones()`: HasMany
  - `inscritos()`: HasManyThrough
- **Atributos calculados**: `cupos_disponibles`, `cupos_suplentes_disponibles`
- **Scopes**: `abiertos()`, `disponibles()`

### Inscripcion
- **Relaciones**: 
  - `jugador()`: BelongsTo
  - `partido()`: BelongsTo
- **Estados**: inscrito → confirmado → cancelado

### WalletTransaction
- **Relaciones**: 
  - `usuario()`: BelongsTo
  - `aprobador()`: BelongsTo
  - `partido()`: BelongsTo
- **Tipos**: recarga, pago_partido, sancion, reembolso

### Sancion
- **Relaciones**: 
  - `usuario()`: BelongsTo
  - `partido()`: BelongsTo
- **Lógica**: Escalado automático, bloqueo de cuenta

### Notificacion
- **Relaciones**: 
  - `usuario()`: BelongsTo
- **Tipos**: recarga_aprobada, sancion, confirmacion_partido, etc.

## Controladores

### AuthController
- `register()`: Registro con validación de rol
- `login()`: Autenticación y token generation
- `logout()`: Invalidación de token
- `me()`: Información del usuario autenticado

### WalletController
- `index()`: Saldo y transacciones del usuario
- `solicitarRecarga()`: Upload de comprobante
- `aprobarRecarga()`: Admin approval con actualización de saldo
- `rechazarRecarga()`: Admin rejection con notificación
- `recargasPendientes()`: Lista para admin

### InscripcionController
- `misInscripciones()`: Historial del usuario
- `inscribirse()`: Validación y creación de inscripción
- `confirmarPago()`: Verificación de saldo y descuento
- `cancelarInscripcion()`: Lógica de cancelación con sanción

### SancionController
- `misSanciones()`: Sanciones activas del usuario
- `pagar()`: Pago de reactivación
- `listado()`: Vista admin de todas las sanciones

### NotificacionController
- `index()`: Todas las notificaciones
- `noLeidas()`: Contador de no leídas
- `marcarLeida()`: Marcar individual
- `marcarTodasLeidas()`: Marcar todas

### PartidoController
- `index()`: Lista con filtros
- `store()`: Creación con validación de permisos
- `show()`: Detalle con equipos generados
- `update()`: Modificación por creador/admin
- `generarEquipos()`: Algoritmo de balanceo
- `inscribirse()`: Endpoint legacy (mover a InscripcionController)

## Endpoints API

### Autenticación
```
POST /api/register
POST /api/login
POST /api/logout
GET  /api/me
```

### Wallet
```
GET  /api/wallet
POST /api/wallet/recarga
POST /api/wallet/recarga/{id}/aprobar
POST /api/wallet/recarga/{id}/rechazar
GET  /api/wallet/recargas-pendientes
```

### Inscripciones
```
GET  /api/inscripciones/mis-inscripciones
POST /api/inscripciones/partido/{id}
POST /api/inscripciones/{id}/confirmar-pago
POST /api/inscripciones/{id}/cancelar
```

### Sanciones
```
GET  /api/sanciones/mis-sanciones
POST /api/sanciones/{id}/pagar
GET  /api/sanciones/listado
```

### Notificaciones
```
GET  /api/notificaciones
GET  /api/notificaciones/no-leidas
POST /api/notificaciones/{id}/marcar-leida
POST /api/notificaciones/marcar-todas-leidas
```

### Partidos
```
GET  /api/partidos
POST /api/partidos
GET  /api/partidos/{id}
PUT  /api/partidos/{id}
DELETE /api/partidos/{id}
POST /api/partidos/{id}/generar-equipos
GET  /api/partidos/disponibles
GET  /api/partidos/requieren-arbitro
POST /api/partidos/{id}/aplicar-arbitro
POST /api/partidos/{id}/inscribirse
GET  /api/partidos/mis-partidos
```

## Estados y Transiciones

### Estados de Inscripción
```
    INSCRITO
       │
       ▼
   CONFIRMADO ──────► CANCELADO
       │                  │
       │                  ▼
       │            SANCIÓN
       │
       ▼
    JUGADO
```

### Estados de Transacción
```
   PENDIENTE
       │
       ├──► APROBADO ──► Saldo actualizado
       │
       └──► RECHAZADO ──► Notificación enviada
```

### Estados de Sanción
```
    ACTIVA
       │
       ├──► PAGADA ──► Cuenta reactivada
       │
       └──► VENCIDA ──► Cuenta reactivada automáticamente
```

### Estados de Partido
```
   ABIERTO ───► CERRADO ───► EN_CURSO ───► FINALIZADO
```

## Reglas de Negocio

### Wallet
- **Monto mínimo de recarga**: $10,000
- **Comprobante obligatorio**: Imagen (max 5MB)
- **Aprobación manual**: Solo administradores
- **Auditoría completa**: Registro de todas las transacciones

### Inscripciones
- **Verificación de sanción**: Usuario con sanción activa no puede inscribirse
- **Validación de saldo**: Confirmación requiere saldo suficiente
- **Cupos limitados**: Máximo 7 por equipo (configurable)
- **Suplentes automáticos**: Asignación por orden de inscripción

### Sanciones
- **Escalado automático**: 1ª=7d, 2ª=15d, 3ª=30d
- **Costo fijo**: $15,000 para reactivación
- **Bloqueo total**: Durante período de sanción
- **Motivo único**: Cancelación post-confirmación

### Suplentes
- **Asignación por orden**: Primero en inscribirse
- **Notificación inmediata**: Al ser promovido
- **Confirmación requerida**: Debe pagar para asegurar cupo

## Seguridad

### Autenticación y Autorización
- **Laravel Sanctum**: Tokens stateless para API
- **Middleware auth:sanctum**: Protección de rutas
- **Validación de permisos**: Métodos `isAdmin()`, `isJugador()`, etc.
- **Propiedad de recursos**: `user_id === auth()->id()`

### Validación de Datos
- **Request Classes**: Validación estructurada
- **Reglas de negocio**: Implementadas en controladores
- **Sanitización**: Eloquent previene inyección SQL

### Almacenamiento Seguro
- **Archivos**: `storage/app/public` con symlinks
- **Nombres únicos**: UUID para evitar colisiones
- **Validación de tipos**: Solo imágenes permitidas

### Auditoría
- **Transacciones**: Registro completo en `wallet_transactions`
- **Cambios de estado**: Timestamps automáticos
- **Logs**: Laravel logging para debugging

## Notificaciones

### Tipos de Notificación
- `recarga_aprobada`: Recarga aprobada por admin
- `recarga_rechazada`: Recarga rechazada por admin
- `confirmacion_partido`: Confirmación exitosa de partido
- `sancion`: Sanción aplicada por cancelación
- `sancion_pagada`: Sanción pagada y cuenta reactivada
- `asignacion_suplente`: Asignado como titular desde suplente

### Sistema de Estados
- **Leída/No leída**: Tracking individual
- **Contador global**: Para UI
- **Data JSON**: Información contextual por tipo

## Métricas y KPIs

### Indicadores Clave
- **Tasa de confirmación**: `confirmados / inscritos`
- **Tasa de cancelación**: `cancelados / confirmados`
- **Tasa de sanciones**: `sanciones / total_jugadores`
- **Saldo promedio**: `AVG(wallet)`
- **Recargas pendientes**: `COUNT(estado='pendiente')`
- **Notificaciones no leídas**: `COUNT(leida=false)`

## Escalabilidad y Mejoras Futuras

### Próximas Funcionalidades
- **Integración con pasarelas de pago**: PSE, Nequi API
- **Sistema de estadísticas**: Dashboard con analytics
- **Ranking y reputación**: Sistema de puntuación
- **Torneos**: Competiciones estructuradas
- **Notificaciones push**: Tiempo real
- **Chat entre jugadores**: Comunicación integrada

### Optimizaciones Técnicas
- **Cache**: Redis para consultas frecuentes
- **Queue**: Jobs para notificaciones y procesamiento
- **API Versioning**: Para evolución backward-compatible
- **Microservicios**: Separación de concerns si escala

---

**Última actualización**: Diciembre 2024  
**Versión**: 2.0  
**Estado**: ✅ Implementado y operativo