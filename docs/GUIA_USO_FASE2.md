# Guía de Uso Rápido - Fase 2

## Escenarios de Prueba

### Escenario 1: Usuario Nuevo - Primera Recarga

**Paso 1: Registrarse**
```bash
POST /api/register
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "rol": "jugador",
  "genero": "masculino",
  "posicion": "delantero",
  "nivel": "intermedio",
  "ciudad": "Bogotá"
}
```

**Paso 2: Solicitar Recarga**
```bash
POST /api/wallet/recarga
Authorization: Bearer {token}
Content-Type: multipart/form-data

monto: 50000
comprobante: [archivo de imagen]
```

**Paso 3: Admin Aprueba Recarga**
```bash
POST /api/wallet/recarga/1/aprobar
Authorization: Bearer {admin_token}
```

**Paso 4: Verificar Saldo**
```bash
GET /api/wallet
Authorization: Bearer {token}

Response:
{
  "saldo": 50000,
  "transacciones": [...]
}
```

---

### Escenario 2: Inscripción y Confirmación de Partido

**Paso 1: Ver Partidos Disponibles**
```bash
GET /api/partidos
Authorization: Bearer {token}
```

**Paso 2: Inscribirse al Partido**
```bash
POST /api/inscripciones/partido/1
Authorization: Bearer {token}
{
  "equipo": "A"
}

Response:
{
  "message": "Te has inscrito exitosamente",
  "inscripcion": {
    "id": 1,
    "estado": "inscrito",
    "es_suplente": false
  }
}
```

**Paso 3: Confirmar Pago**
```bash
POST /api/inscripciones/1/confirmar-pago
Authorization: Bearer {token}

Response:
{
  "message": "Pago confirmado exitosamente",
  "inscripcion": {
    "estado": "confirmado",
    "pago_realizado": true
  },
  "saldo_restante": 30000
}
```

**Paso 4: Ver Mis Inscripciones**
```bash
GET /api/inscripciones/mis-inscripciones
Authorization: Bearer {token}
```

---

### Escenario 3: Cancelación con Sanción

**Paso 1: Cancelar Inscripción (después de confirmar)**
```bash
POST /api/inscripciones/1/cancelar
Authorization: Bearer {token}

Response:
{
  "message": "Inscripción cancelada",
  "sancion": {
    "id": 1,
    "numero_sancion": 1,
    "dias_suspension": 7,
    "fecha_fin": "2025-12-04",
    "monto_reactivacion": 15000,
    "activa": true
  }
}
```

**Paso 2: Ver Mis Sanciones**
```bash
GET /api/sanciones/mis-sanciones
Authorization: Bearer {token}
```

**Paso 3: Intentar Inscribirse (Bloqueado)**
```bash
POST /api/inscripciones/partido/2
Authorization: Bearer {token}

Response (403):
{
  "message": "Tienes una sanción activa hasta 04/12/2025",
  "sancion": {...}
}
```

**Paso 4: Pagar Reactivación**
```bash
POST /api/sanciones/1/pagar
Authorization: Bearer {token}

Response:
{
  "message": "Sanción pagada exitosamente. Tu cuenta ha sido reactivada.",
  "saldo_restante": 15000
}
```

---

### Escenario 4: Sistema de Suplentes

**Paso 1: Inscribirse como Suplente (cupos llenos)**
```bash
POST /api/inscripciones/partido/1
Authorization: Bearer {token}
{
  "equipo": "A"
}

Response:
{
  "message": "Te has inscrito como suplente",
  "inscripcion": {
    "es_suplente": true,
    "estado": "inscrito"
  }
}
```

**Paso 2: Titular Cancela**
```bash
# Otro usuario cancela su inscripción confirmada
POST /api/inscripciones/2/cancelar
Authorization: Bearer {otro_token}
```

**Paso 3: Suplente Recibe Notificación**
```bash
GET /api/notificaciones/no-leidas
Authorization: Bearer {token}

Response:
{
  "count": 1,
  "notificaciones": [
    {
      "tipo": "asignacion_suplente",
      "titulo": "Asignado como Titular",
      "mensaje": "Has sido asignado como titular en el partido..."
    }
  ]
}
```

**Paso 4: Confirmar Pago como Nuevo Titular**
```bash
POST /api/inscripciones/1/confirmar-pago
Authorization: Bearer {token}
```

---

### Escenario 5: Administración de Recargas

**Paso 1: Ver Recargas Pendientes (Admin)**
```bash
GET /api/wallet/recargas-pendientes
Authorization: Bearer {admin_token}

Response:
{
  "data": [
    {
      "id": 1,
      "user": {
        "name": "Juan Pérez"
      },
      "monto": 50000,
      "comprobante": "comprobantes/abc123.jpg",
      "estado": "pendiente"
    }
  ]
}
```

**Paso 2: Aprobar Recarga**
```bash
POST /api/wallet/recarga/1/aprobar
Authorization: Bearer {admin_token}
```

**Paso 3: Rechazar Recarga**
```bash
POST /api/wallet/recarga/2/rechazar
Authorization: Bearer {admin_token}
{
  "notas": "Comprobante no válido o ilegible"
}
```

---

### Escenario 6: Gestión de Notificaciones

**Paso 1: Ver Todas las Notificaciones**
```bash
GET /api/notificaciones
Authorization: Bearer {token}
```

**Paso 2: Ver No Leídas**
```bash
GET /api/notificaciones/no-leidas
Authorization: Bearer {token}
```

**Paso 3: Marcar Como Leída**
```bash
POST /api/notificaciones/1/marcar-leida
Authorization: Bearer {token}
```

**Paso 4: Marcar Todas Como Leídas**
```bash
POST /api/notificaciones/marcar-todas-leidas
Authorization: Bearer {token}
```

---

## Casos de Error Comunes

### Error: Saldo Insuficiente
```bash
POST /api/inscripciones/1/confirmar-pago

Response (400):
{
  "message": "Saldo insuficiente. Tu saldo actual es: $10,000",
  "saldo_actual": 10000,
  "costo_partido": 20000,
  "faltante": 10000
}
```

**Solución:** Solicitar recarga de saldo

### Error: Sanción Activa
```bash
POST /api/inscripciones/partido/1

Response (403):
{
  "message": "Tienes una sanción activa hasta 04/12/2025",
  "sancion": {...}
}
```

**Solución:** Pagar reactivación de $15,000

### Error: Ya Inscrito
```bash
POST /api/inscripciones/partido/1

Response (400):
{
  "message": "Ya estás inscrito en este partido"
}
```

**Solución:** Verificar inscripciones existentes

---

## Flujo Completo Recomendado

1. **Registro** → Crear cuenta
2. **Recarga** → Solicitar recarga con comprobante
3. **Esperar Aprobación** → Admin aprueba recarga
4. **Inscripción** → Inscribirse a partido
5. **Confirmación** → Confirmar pago (descuenta saldo)
6. **Jugar** → Participar en el partido
7. **Repetir** → Volver al paso 4

---

## Notas Importantes

- **Monto mínimo de recarga:** $10,000
- **Costo típico por partido:** $20,000
- **Costo de reactivación:** $15,000
- **Sanciones:** 7, 15 o 30 días según número
- **Comprobante:** Imagen, máximo 5MB
- **Estados de inscripción:** inscrito → confirmado → cancelado
- **Suplentes:** Asignados automáticamente por orden de inscripción

---

## Comandos Útiles

```bash
# Ver saldo actual
GET /api/wallet

# Ver mis inscripciones
GET /api/inscripciones/mis-inscripciones

# Ver mis sanciones
GET /api/sanciones/mis-sanciones

# Ver notificaciones no leídas
GET /api/notificaciones/no-leidas

# Ver mi perfil
GET /api/me
```
