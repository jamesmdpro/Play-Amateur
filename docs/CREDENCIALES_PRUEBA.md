# Credenciales de Prueba - Fase 2

## Usuarios de Prueba

### Administrador
- **Email:** admin@test.com
- **Password:** password
- **Rol:** admin
- **Redirección:** `/admin/dashboard`

### Cancha
- **Email:** cancha@test.com
- **Password:** password
- **Rol:** cancha
- **Redirección:** `/cancha/dashboard`

### Árbitro
- **Email:** arbitro@test.com
- **Password:** password
- **Rol:** arbitro
- **Redirección:** `/arbitro/dashboard`

### Jugadores
- **Email:** jugador1@test.com hasta jugador15@test.com
- **Password:** password
- **Rol:** jugador
- **Redirección:** `/jugador/dashboard` (vista de partidos)

## URLs de Acceso

- **Login:** http://localhost:8000/login
- **Dashboard Admin:** http://localhost:8000/admin/dashboard
- **Gestión de Recargas:** http://localhost:8000/admin/recargas
- **Dashboard Jugador:** http://localhost:8000/jugador/dashboard
- **Wallet:** http://localhost:8000/wallet
- **Sanciones:** http://localhost:8000/sanciones
- **Notificaciones:** http://localhost:8000/notificaciones

## Flujo de Prueba

1. **Login como Admin:**
   - Ir a http://localhost:8000/login
   - Ingresar: admin@test.com / password
   - Serás redirigido a `/admin/dashboard`

2. **Login como Jugador:**
   - Ir a http://localhost:8000/login
   - Ingresar: jugador1@test.com / password
   - Serás redirigido a `/jugador/dashboard`

3. **Probar Wallet:**
   - Login como jugador
   - Ir a http://localhost:8000/wallet
   - Solicitar recarga con comprobante

4. **Aprobar Recarga (Admin):**
   - Login como admin
   - Ir a http://localhost:8000/admin/recargas
   - Aprobar/rechazar recargas pendientes

## Notas Importantes

- Todos los usuarios tienen la contraseña: **password**
- Los jugadores tienen saldo inicial entre $100 y $500
- El admin tiene saldo inicial de $1000
- El árbitro tiene saldo inicial de $500
- La cancha tiene saldo inicial de $0

## Solución de Problemas

Si no puedes iniciar sesión:
1. Verifica que las migraciones estén ejecutadas: `php artisan migrate`
2. Verifica que los usuarios existan: `php artisan db:seed --class=UserSeeder`
3. Limpia la caché: `php artisan cache:clear`
4. Limpia las sesiones: `php artisan session:clear`
