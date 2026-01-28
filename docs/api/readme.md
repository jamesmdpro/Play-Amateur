# Documentación de API - Play Amateur

Esta carpeta contiene la documentación completa de la API RESTful del sistema Play Amateur.

## Archivos

- **[API.md](API.md)** - Documentación completa de la API, incluyendo diferenciación entre implementaciones web y API, endpoints disponibles, códigos de error, y plan de migración.

## Estado Actual

El sistema opera actualmente en un **modelo híbrido**:

### ✅ Completamente Implementado
- Autenticación (login/register)
- Wallet (cartera digital)
- Partidos (CRUD completo)
- Inscripciones y confirmaciones
- Sanciones automáticas
- Notificaciones push

### ⚠️ Parcialmente Implementado
- Usuarios (perfil básico)
- Estadísticas (básicas)
- Ratings (solo creación)

### ❌ Solo Web (Pendiente API)
- Resultados de partidos
- Gestión específica de árbitro
- Estadísticas avanzadas
- Sistema completo de ratings

## Uso

### Para Desarrolladores Frontend
- Consulta [API.md](API.md) para integrar con la API
- Usa los endpoints documentados para consumir datos
- Implementa manejo de errores según códigos especificados

### Para Migración Completa
- Identifica funcionalidades solo web que necesitan API
- Sigue el plan de migración en [API.md](API.md)
- Implementa endpoints faltantes manteniendo consistencia

### Para Testing
- Usa `api-tests-fase1.http` y `api-tests-fase2.http` en la raíz del proyecto
- Extension REST Client en VS Code recomendada

## Próximos Pasos

1. **Implementar APIs faltantes** para resultados y árbitro
2. **Expandir estadísticas** por API
3. **Completar ratings** con consultas y gestión
4. **Implementar versionado** formal de API
5. **Agregar documentación OpenAPI**

Para más detalles, consulta [API.md](API.md).