# ⚽ Play Amateur - Sistema de Gestión de Partidos de Fútbol

Sistema completo para organizar partidos de fútbol amateur con gestión de jugadores, equipos automáticos, pagos y más.

## 🚀 Estado del Proyecto

**✅ FASE 1 COMPLETADA** - MVP Base funcional

## 📋 Características Implementadas

### Autenticación y Usuarios
- ✅ Registro y login con Laravel Sanctum
- ✅ Sistema de roles (jugador, árbitro, cancha, admin)
- ✅ Perfil de usuario con posición, nivel y ciudad
- ✅ Subida de foto de perfil
- ✅ Wallet básica

### Gestión de Partidos
- ✅ Crear, editar y eliminar partidos
- ✅ Lista de partidos disponibles
- ✅ Inscripción de jugadores
- ✅ Control de cupos titulares y suplentes
- ✅ Estados de partido (abierto, cerrado, en curso, finalizado)

### Equipos Automáticos
- ✅ Generación automática de equipos balanceados
- ✅ División por posiciones (arquero, defensa, medio, ataque)
- ✅ Balanceo por nivel de habilidad

## 🛠️ Tecnologías

- **Backend:** Laravel 11
- **Base de datos:** MySQL
- **Autenticación:** Laravel Sanctum
- **API:** RESTful

## 📦 Instalación

### Requisitos
- PHP 8.2+
- Composer
- MySQL
- Node.js (para frontend)

### Pasos

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd temp-laravel
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos**
Editar `.env` con tus credenciales de MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=play_amateur
DB_USERNAME=root
DB_PASSWORD=
```

5. **Ejecutar migraciones y seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Iniciar servidor**
```bash
php artisan serve
```

El servidor estará disponible en: `http://localhost:8000`

## 📚 Documentación

- **[API_DOCS.md](API_DOCS.md)** - Documentación completa de endpoints
- **[FASE_1_COMPLETADO.md](FASE_1_COMPLETADO.md)** - Resumen de la Fase 1
- **[api-tests.http](api-tests.http)** - Archivo de pruebas HTTP

## 🧪 Usuarios de Prueba

Todos los usuarios tienen la contraseña: `password`

| Email | Rol | Descripción |
|-------|-----|-------------|
| admin@test.com | admin | Administrador del sistema |
| cancha@test.com | cancha | Dueño de cancha |
| arbitro@test.com | arbitro | Árbitro |
| jugador1@test.com | jugador | Jugador (Arquero) |
| jugador2@test.com | jugador | Jugador (Defensa) |
| ... | ... | ... |
| jugador15@test.com | jugador | Jugador (Defensa) |

## 🔑 Endpoints Principales

### Autenticación
```
POST   /api/register       - Registrar usuario
POST   /api/login          - Iniciar sesión
POST   /api/logout         - Cerrar sesión
GET    /api/me             - Usuario actual
```

### Partidos
```
GET    /api/partidos                      - Listar partidos
POST   /api/partidos                      - Crear partido
GET    /api/partidos/{id}                 - Ver partido
PUT    /api/partidos/{id}                 - Actualizar partido
DELETE /api/partidos/{id}                 - Eliminar partido
POST   /api/partidos/{id}/inscribirse     - Inscribirse
POST   /api/partidos/{id}/generar-equipos - Generar equipos
```

### Usuarios
```
GET    /api/users/{id}      - Ver usuario
PUT    /api/profile         - Actualizar perfil
POST   /api/profile/foto    - Subir foto
POST   /api/wallet/update   - Actualizar wallet
```

## 🧪 Probar la API

### Ejemplo: Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password"}'
```

### Ejemplo: Listar Partidos
```bash
curl -X GET http://localhost:8000/api/partidos \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 📁 Estructura del Proyecto

```
temp-laravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── UserController.php
│   │       └── PartidoController.php
│   └── Models/
│       ├── User.php
│       ├── Partido.php
│       └── Inscripcion.php
├── database/
│   ├── migrations/
│   │   ├── 2025_11_26_040000_create_partidos_table.php
│   │   └── 2025_11_26_040001_create_inscripciones_table.php
│   └── seeders/
│       ├── UserSeeder.php
│       └── PartidoSeeder.php
├── routes/
│   └── api.php
├── API_DOCS.md
├── FASE_1_COMPLETADO.md
└── README.md
```

## 🎯 Roadmap

### ✅ Fase 1 - MVP Base (COMPLETADO)
- Backend y arquitectura básica
- Funciones para jugadores
- Gestión de partidos
- Equipos automáticos simples
- Suplentes MVP

### 🔄 Fase 2 - Pagos y Wallet (Próximo)
- Integración con Mercado Pago
- Sistema de pagos automáticos
- Historial de transacciones
- Reembolsos

### 📅 Fase 3 - Funcionalidades Avanzadas
- Sistema de DT (Director Técnico)
- Estadísticas de jugadores
- Sistema de valoración
- Notificaciones

### 🎨 Fase 4 - Frontend Completo
- Aplicación web React/Vue
- Aplicación móvil (opcional)
- Dashboard de administración

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT.

## 👥 Autores

- Tu Nombre - Desarrollo inicial

## 🙏 Agradecimientos

- Laravel Framework
- Comunidad de fútbol amateur
- Todos los contribuidores

---

**¿Preguntas?** Abre un issue en el repositorio.

**¿Listo para la Fase 2?** Revisa [FASE_1_COMPLETADO.md](FASE_1_COMPLETADO.md) para más detalles.
