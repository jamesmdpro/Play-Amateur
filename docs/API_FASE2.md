# API FASE 2 - Sistema de Pagos, Sanciones y Confirmaciones

## Wallet / Cartera

### GET /api/wallet
Obtener saldo y transacciones del usuario
```json
Response:
{
  "saldo": 50000,
  "transacciones": {
    "data": [...]
  }
}
```

### POST /api/wallet/recarga
Solicitar recarga de saldo
```json
Request (multipart/form-data):
{
  "monto": 50000,
  "comprobante": <file>
}

Response:
{
  "message": "Solicitud de recarga enviada",
  "transaccion": {...}
}
```

### POST /api/wallet/recarga/{id}/aprobar
Aprobar recarga (Admin)
```json
Response:
{
  "message": "Recarga aprobada exitosamente",
  "transaccion": {...}
}
```

### POST /api/wallet/recarga/{id}/rechazar
Rechazar recarga (Admin)
```json
Request:
{
  "notas": "Comprobante no válido"
}

Response:
{
  "message": "Recarga rechazada",
  "transaccion": {...}
}
```

### GET /api/wallet/recargas-pendientes
Listar recargas pendientes (Admin)

---

## Inscripciones

### GET /api/inscripciones/mis-inscripciones
Obtener inscripciones del usuario

### POST /api/inscripciones/partido/{partidoId}
Inscribirse a un partido
```json
Request:
{
  "equipo": "A"
}

Response:
{
  "message": "Te has inscrito exitosamente",
  "inscripcion": {...}
}
```

### POST /api/inscripciones/{inscripcionId}/confirmar-pago
Confirmar pago y asegurar cupo
```json
Response:
{
  "message": "Pago confirmado exitosamente",
  "inscripcion": {...},
  "saldo_restante": 30000
}

Error (saldo insuficiente):
{
  "message": "Saldo insuficiente",
  "saldo_actual": 10000,
  "costo_partido": 20000,
  "faltante": 10000
}
```

### POST /api/inscripciones/{inscripcionId}/cancelar
Cancelar inscripción
```json
Response:
{
  "message": "Inscripción cancelada",
  "sancion": {...} // Si aplica
}
```

---

## Sanciones

### GET /api/sanciones/mis-sanciones
Obtener sanciones del usuario

### POST /api/sanciones/{sancionId}/pagar
Pagar reactivación de cuenta
```json
Response:
{
  "message": "Sanción pagada exitosamente",
  "sancion": {...},
  "saldo_restante": 35000
}

Error:
{
  "message": "Saldo insuficiente",
  "saldo_actual": 10000,
  "monto_requerido": 15000,
  "faltante": 5000
}
```

### GET /api/sanciones/listado
Listar todas las sanciones (Admin)

---

## Notificaciones

### GET /api/notificaciones
Obtener todas las notificaciones

### GET /api/notificaciones/no-leidas
Obtener notificaciones no leídas
```json
Response:
{
  "count": 3,
  "notificaciones": [...]
}
```

### POST /api/notificaciones/{id}/marcar-leida
Marcar notificación como leída

### POST /api/notificaciones/marcar-todas-leidas
Marcar todas las notificaciones como leídas

---

## Estados de Inscripción

- **inscrito**: Usuario inscrito pero no ha pagado
- **confirmado**: Usuario pagó y tiene cupo asegurado
- **cancelado**: Usuario canceló su inscripción
- **suplente**: Usuario en lista de espera

---

## Sistema de Sanciones

### Escalado de Sanciones:
1. **Primera sanción**: 7 días de suspensión
2. **Segunda sanción**: 15 días de suspensión
3. **Tercera sanción**: 30 días de suspensión

### Costo de Reactivación:
- $15,000 para reactivar cuenta después de sanción

### Motivos de Sanción:
- Cancelar después de confirmar pago

---

## Flujo de Confirmación de Partido

1. Usuario se inscribe al partido (estado: `inscrito`)
2. Usuario confirma pago (descuenta del saldo, estado: `confirmado`)
3. Si cancela después de confirmar → recibe sanción automática
4. Si hay suplentes disponibles → se asigna automáticamente
5. Notificaciones enviadas en cada paso

---

## Tipos de Transacciones

- **recarga**: Carga de saldo
- **pago_partido**: Pago de confirmación de partido
- **sancion**: Pago de reactivación por sanción
- **reembolso**: Devolución de saldo

---

## Tipos de Notificaciones

- **recarga_aprobada**: Recarga aprobada por admin
- **recarga_rechazada**: Recarga rechazada por admin
- **confirmacion_partido**: Confirmación exitosa de partido
- **sancion**: Sanción aplicada
- **sancion_pagada**: Sanción pagada y cuenta reactivada
- **asignacion_suplente**: Asignado como titular desde suplente
