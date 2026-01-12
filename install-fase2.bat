@echo off
echo ==========================================
echo   INSTALACION FASE 2 - Play Amateur
echo ==========================================
echo.

echo 1. Ejecutando migraciones...
php artisan migrate

echo.
echo 2. Configurando storage...
php artisan storage:link

echo.
echo 3. Limpiando cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo.
echo ==========================================
echo   INSTALACION COMPLETADA
echo ==========================================
echo.
echo Proximos pasos:
echo 1. Ejecutar: php artisan serve
echo 2. Acceder a: http://localhost:8000
echo 3. Revisar documentacion en: docs/README_FASE2.md
echo.
echo Endpoints disponibles:
echo - Wallet: /api/wallet
echo - Inscripciones: /api/inscripciones
echo - Sanciones: /api/sanciones
echo - Notificaciones: /api/notificaciones
echo.
pause
