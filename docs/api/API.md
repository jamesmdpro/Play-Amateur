# API Documentation - Play Amateur

## Introducción

Esta documentación describe la API RESTful del sistema Play Amateur. Actualmente, el sistema opera en un modelo híbrido donde algunas funcionalidades están disponibles tanto en **vistas web** (navegación tradicional con Blade templates) como en **endpoints API** (consumo programático con JSON).

## Arquitectura API

### Base URL
```
http://localhost:8000/api
```

### Autenticación
Todos los endpoints protegidos requieren el header de autorización:
```
Authorization: Bearer {token}
```

### Formato de Respuesta
- **Éxito**: `200 OK` con datos JSON
- **Error**: `4xx/5xx` con estructura de error consistente
```json
{
  "message": "Descripción del error",
  "errors": {...} // Detalles específicos
}
```

### Versionado
La API actual es v1 (implícita). Para futuras versiones se usará `/api/v2/`.

---

## Estado Actual: Web vs API

### 📊 Matriz de Implementación

| Módulo | Web ✅ | API ✅ | Estado | Notas |
|--------|--------|--------|--------|-------|
| **Autenticación** | ✅ | ✅ | Completo | Login/register en ambos |
| **Usuarios** | ✅ | ⚠️ Parcial | Perfil básico | Falta gestión completa |
| **Wallet** | ✅ | ✅ | Completo | Sistema de pagos implementado |
| **Partidos** | ✅ | ✅ | Completo | CRUD completo |
| **Inscripciones** | ✅ | ✅ | Completo | Sistema de confirmación |
| **Sanciones** | ✅ | ✅ | Completo | Escalado automático |
| **Notificaciones** | ✅ | ✅ | Completo | Push y gestión |
| **Estadísticas** | ✅ | ⚠️ Parcial | Básico | Falta analytics avanzado |
| **Resultados** | ✅ | ❌ | Pendiente | Solo vistas, no API |
| **Ratings** | ✅ | ⚠️ Parcial | Básico | Solo creación |
| **Árbitro** | ✅ | ❌ | Pendiente | Funciones específicas |

### 🔄 Funcionalidades Híbridas

Las siguientes funcionalidades están implementadas tanto en web como en API:

#### Wallet (Cartera Digital)
- **Web**: `/wallet` - Vista completa de saldo y transacciones
- **API**: `/api/wallet/*` - Gestión programática
- **Estado**: ✅ Sincronizado

#### Sanciones
- **Web**: `/sanciones` - Vista de sanciones activas y pago
- **API**: `/api/sanciones/*` - Gestión de penalizaciones
- **Estado**: ✅ Sincronizado

#### Notificaciones
- **Web**: `/notificaciones` - Centro de notificaciones
- **API**: `/api/notificaciones/*` - Gestión de mensajes
- **Estado**: ✅ Sincronizado

#### Inscripciones
- **Web**: Integrado en dashboards
- **API**: `/api/inscripciones/*` - Gestión de participaciones
- **Estado**: ✅ Sincronizado

---

## Endpoints API Completos

### Autenticación

#### Registro
```http
POST /api/register
Content-Type: application/json

{
  "name": "Juan Pérez",
  "email": "juan@test.com",
  "password": "password",
  "password_confirmation": "password",
  "rol": "jugador",
  "posicion": "medio",
  "nivel": 7,
  "ciudad": "Buenos Aires"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "juan@test.com",
  "password": "password"
}
```

**Response:**
```json
{
  "user": {...},
  "access_token": "token...",
  "token_type": "Bearer"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Usuario Actual
```http
GET /api/me
Authorization: Bearer {token}
```

### Usuarios

#### Ver Perfil
```http
GET /api/users/{id}
Authorization: Bearer {token}
```

#### Actualizar Perfil
```http
PUT /api/profile
Authorization: Bearer {token}

{
  "name": "Nuevo Nombre",
  "posicion": "ataque",
  "nivel": 8,
  "ciudad": "Córdoba"
}
```

#### Subir Foto de Perfil
```http
POST /api/profile/foto
Authorization: Bearer {token}
Content-Type: multipart/form-data

foto: [archivo]
```

#### Estadísticas de Jugador
```http
GET /api/jugador/estadisticas
Authorization: Bearer {token}
```

### Wallet (Cartera)

#### Ver Saldo y Transacciones
```http
GET /api/wallet
Authorization: Bearer {token}
```

**Response:**
```json
{
  "saldo": 50000,
  "transacciones": {
    "data": [...]
  }
}
```

#### Solicitar Recarga
```http
POST /api/wallet/recarga
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "monto": 50000,
  "comprobante": [archivo]
}
```

#### Aprobar Recarga (Admin)
```http
POST /api/wallet/recarga/{id}/aprobar
Authorization: Bearer {token}
```

#### Rechazar Recarga (Admin)
```http
POST /api/wallet/recarga/{id}/rechazar
Authorization: Bearer {token}

{
  "notas": "Comprobante inválido"
}
```

#### Recargas Pendientes (Admin)
```http
GET /api/wallet/recargas-pendientes
Authorization: Bearer {token}
```

### Partidos

#### Listar Partidos
```http
GET /api/partidos
Authorization: Bearer {token}
```

**Parámetros:**
- `estado`: abierto, cerrado, en_curso, finalizado
- `page`: paginación

#### Crear Partido
```http
POST /api/partidos
Authorization: Bearer {token}

{
  "nombre": "Partido Amistoso",
  "descripcion": "Partido recreativo",
  "fecha_hora": "2024-12-25 18:00:00",
  "ubicacion": "Cancha Central",
  "cupos_totales": 14,
  "cupos_suplentes": 4,
  "costo": 20000
}
```

#### Ver Partido
```http
GET /api/partidos/{id}
Authorization: Bearer {token}
```

#### Actualizar Partido
```http
PUT /api/partidos/{id}
Authorization: Bearer {token}

{
  "estado": "cerrado"
}
```

#### Eliminar Partido
```http
DELETE /api/partidos/{id}
Authorization: Bearer {token}
```

#### Generar Equipos
```http
POST /api/partidos/{id}/generar-equipos
Authorization: Bearer {token}
```

#### Partidos Disponibles
```http
GET /api/partidos/disponibles
Authorization: Bearer {token}
```

#### Partidos que Requieren Árbitro
```http
GET /api/partidos/requieren-arbitro
Authorization: Bearer {token}
```

#### Aplicar como Árbitro
```http
POST /api/partidos/{id}/aplicar-arbitro
Authorization: Bearer {token}
```

#### Inscribirse a Partido
```http
POST /api/partidos/{id}/inscribirse
Authorization: Bearer {token}

{
  "equipo": "A"
}
```

#### Mis Partidos
```http
GET /api/partidos/mis-partidos
Authorization: Bearer {token}
```

### Inscripciones

#### Mis Inscripciones
```http
GET /api/inscripciones/mis-inscripciones
Authorization: Bearer {token}
```

#### Inscribirse a Partido
```http
POST /api/inscripciones/partido/{partidoId}
Authorization: Bearer {token}

{
  "equipo": "A"
}
```

#### Confirmar Pago
```http
POST /api/inscripciones/{inscripcionId}/confirmar-pago
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Pago confirmado exitosamente",
  "inscripcion": {...},
  "saldo_restante": 30000
}
```

#### Cancelar Inscripción
```http
POST /api/inscripciones/{inscripcionId}/cancelar
Authorization: Bearer {token}
```

### Sanciones

#### Mis Sanciones
```http
GET /api/sanciones/mis-sanciones
Authorization: Bearer {token}
```

#### Pagar Reactivación
```http
POST /api/sanciones/{sancionId}/pagar
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Sanción pagada exitosamente",
  "sancion": {...},
  "saldo_restante": 35000
}
```

#### Listado de Sanciones (Admin)
```http
GET /api/sanciones/listado
Authorization: Bearer {token}
```

### Notificaciones

#### Todas las Notificaciones
```http
GET /api/notificaciones
Authorization: Bearer {token}
```

#### Notificaciones No Leídas
```http
GET /api/notificaciones/no-leidas
Authorization: Bearer {token}
```

**Response:**
```json
{
  "count": 3,
  "notificaciones": [...]
}
```

#### Marcar como Leída
```http
POST /api/notificaciones/{id}/marcar-leida
Authorization: Bearer {token}
```

#### Marcar Todas como Leídas
```http
POST /api/notificaciones/marcar-todas-leidas
Authorization: Bearer {token}
```

### Ratings

#### Crear Rating
```http
POST /api/ratings
Authorization: Bearer {token}

{
  "partido_id": 1,
  "rated_user_id": 2,
  "puntuacion": 8,
  "comentario": "Buen jugador"
}
```

---

## Funcionalidades Solo Web (Pendientes de API)

### ⚠️ Resultados de Partidos
- **Web**: `/partidos/{id}/resultado` - Vista de resultados
- **API**: ❌ No implementado
- **Estado**: Pendiente migración

### ⚠️ Estadísticas Avanzadas
- **Web**: `/estadisticas/{id}` - Vista detallada
- **API**: Parcial (`/api/jugador/estadisticas`)
- **Estado**: Necesita expansión

### ⚠️ Gestión de Árbitro
- **Web**: `/arbitro/*` - Funciones completas
- **API**: ❌ No implementado
- **Estado**: Pendiente migración

### ⚠️ Ratings y Comentarios
- **Web**: `/partidos/{id}/ratings` - Sistema completo
- **API**: Solo creación (`POST /api/ratings`)
- **Estado**: Necesita consulta y gestión

---

## Migración a API Completa

### Estrategia Recomendada

1. **Fase 1**: Completar APIs críticas
   - Resultados de partidos
   - Gestión de árbitro
   - Estadísticas avanzadas

2. **Fase 2**: Optimización
   - Paginación consistente
   - Filtros avanzados
   - Caché de respuestas

3. **Fase 3**: Versionado
   - Implementar `/api/v2/`
   - Deprecar endpoints legacy

### Beneficios de la Migración
- **Separación clara**: Frontend/Backend independientes
- **Escalabilidad**: Múltiples clientes (web, móvil, etc.)
- **Mantenibilidad**: API como contrato claro
- **Testing**: Endpoints testeables unitariamente

---

## Códigos de Error

### Autenticación
- `401 Unauthorized`: Token inválido o expirado
- `403 Forbidden`: Permisos insuficientes

### Validación
- `422 Unprocessable Entity`: Datos inválidos
```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Recursos
- `404 Not Found`: Recurso no existe
- `409 Conflict`: Conflicto de estado (ej: partido ya cerrado)

### Servidor
- `500 Internal Server Error`: Error del servidor

---

## Rate Limiting

La API implementa rate limiting básico:
- **Autenticado**: 60 requests/minuto
- **No autenticado**: 30 requests/minuto

Headers incluidos en respuesta:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

---

## Testing

### Archivo de Pruebas
- `api-tests-fase1.http`: Pruebas básicas
- `api-tests-fase2.http`: Pruebas de wallet y sanciones

### Ejecución
```bash
# Usar extension REST Client en VS Code
# O herramientas como Postman, Insomnia
```

---

## Próximas Implementaciones

- [ ] API completa para resultados
- [ ] API para gestión de árbitro
- [ ] Estadísticas avanzadas por API
- [ ] Sistema de ratings completo
- [ ] Webhooks para integraciones
- [ ] API versioning formal
- [ ] Documentación OpenAPI/Swagger

---

**Última actualización**: Diciembre 2024  
**Versión API**: 1.0  
**Estado**: Híbrido (Web + API)