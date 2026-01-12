# API Documentation - Play Amateur

## Base URL
```
http://localhost:8000/api
```

## Autenticación

Todos los endpoints protegidos requieren el header:
```
Authorization: Bearer {token}
```

---

## Endpoints de Autenticación

### Registro
**POST** `/register`

**Body:**
```json
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

**Response:**
```json
{
  "message": "Usuario registrado exitosamente",
  "user": {...},
  "access_token": "token...",
  "token_type": "Bearer"
}
```

### Login
**POST** `/login`

**Body:**
```json
{
  "email": "admin@test.com",
  "password": "password"
}
```

### Logout
**POST** `/logout` (Requiere autenticación)

### Usuario Actual
**GET** `/me` (Requiere autenticación)

---

## Endpoints de Usuarios

### Ver Usuario
**GET** `/users/{id}` (Requiere autenticación)

### Actualizar Perfil
**PUT** `/profile` (Requiere autenticación)

**Body:**
```json
{
  "name": "Nuevo Nombre",
  "posicion": "ataque",
  "nivel": 8,
  "ciudad": "Córdoba"
}
```

### Subir Foto
**POST** `/profile/foto` (Requiere autenticación)

**Body:** (multipart/form-data)
```
foto: [archivo de imagen]
```

### Actualizar Wallet
**POST** `/wallet/update` (Requiere autenticación)

**Body:**
```json
{
  "user_id": 1,
  "monto": 100,
  "operacion": "agregar"
}
```

---

## Endpoints de Partidos

### Listar Partidos
**GET** `/partidos` (Requiere autenticación)

**Response:**
```json
[
  {
    "id": 1,
    "nombre": "Partido Amistoso - Sábado",
    "descripcion": "...",
    "fecha_hora": "2025-11-29 18:00:00",
    "ubicacion": "Cancha Central",
    "cupos_totales": 14,
    "cupos_suplentes": 4,
    "cupos_disponibles": 14,
    "cupos_suplentes_disponibles": 4,
    "costo": 150.00,
    "estado": "abierto",
    "creador": {...},
    "inscritos": 0
  }
]
```

### Crear Partido
**POST** `/partidos` (Requiere autenticación - Admin/Cancha)

**Body:**
```json
{
  "nombre": "Partido Nocturno",
  "descripcion": "Partido bajo las luces",
  "fecha_hora": "2025-12-01 21:00:00",
  "ubicacion": "Cancha Sintética",
  "cupos_totales": 10,
  "cupos_suplentes": 2,
  "costo": 120.00
}
```

### Ver Partido
**GET** `/partidos/{id}` (Requiere autenticación)

**Response:**
```json
{
  "partido": {...},
  "cupos_disponibles": 14,
  "cupos_suplentes_disponibles": 4,
  "equipo1": [...],
  "equipo2": [...],
  "suplentes": [...]
}
```

### Actualizar Partido
**PUT** `/partidos/{id}` (Requiere autenticación - Admin/Cancha/Creador)

**Body:**
```json
{
  "nombre": "Nuevo Nombre",
  "estado": "cerrado"
}
```

### Eliminar Partido
**DELETE** `/partidos/{id}` (Requiere autenticación - Admin/Creador)

### Inscribirse a Partido
**POST** `/partidos/{id}/inscribirse` (Requiere autenticación - Jugador)

**Response:**
```json
{
  "message": "Inscrito exitosamente",
  "inscripcion": {...}
}
```

### Generar Equipos
**POST** `/partidos/{id}/generar-equipos` (Requiere autenticación - Admin/Cancha/Creador)

**Response:**
```json
{
  "message": "Equipos generados exitosamente",
  "partido": {...}
}
```

---

## Usuarios de Prueba

| Email | Password | Rol |
|-------|----------|-----|
| admin@test.com | password | admin |
| cancha@test.com | password | cancha |
| arbitro@test.com | password | arbitro |
| jugador1@test.com | password | jugador |
| jugador2@test.com | password | jugador |
| ... | ... | ... |
| jugador15@test.com | password | jugador |

---

## Roles y Permisos

- **admin**: Puede crear partidos, generar equipos, actualizar wallets
- **cancha**: Puede crear partidos, generar equipos
- **arbitro**: Puede ver partidos
- **jugador**: Puede inscribirse a partidos, actualizar su perfil

---

## Estados de Partido

- `abierto`: Acepta inscripciones
- `cerrado`: No acepta más inscripciones
- `en_curso`: Partido en progreso
- `finalizado`: Partido terminado

---

## Posiciones de Jugador

- `arquero`
- `defensa`
- `medio`
- `ataque`
