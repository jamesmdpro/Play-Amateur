<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\SancionController;
use App\Http\Controllers\NotificacionController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::post('/profile/foto', [UserController::class, 'uploadFoto']);

    Route::get('/partidos', [PartidoController::class, 'index']);
    Route::post('/partidos', [PartidoController::class, 'store']);
    Route::get('/partidos/{id}', [PartidoController::class, 'show']);
    Route::put('/partidos/{id}', [PartidoController::class, 'update']);
    Route::delete('/partidos/{id}', [PartidoController::class, 'destroy']);
    Route::post('/partidos/{id}/generar-equipos', [PartidoController::class, 'generarEquipos']);
    Route::get('/partidos/disponibles', [PartidoController::class, 'partidosDisponibles']);
    Route::get('/partidos/requieren-arbitro', [PartidoController::class, 'partidosRequierenArbitro']);
    Route::post('/partidos/{id}/aplicar-arbitro', [PartidoController::class, 'aplicarArbitro']);
    Route::post('/partidos/{id}/inscribirse', [PartidoController::class, 'inscribirse']);
    Route::get('/partidos/mis-partidos', [PartidoController::class, 'misPartidos']);

    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index']);
        Route::post('/recarga', [WalletController::class, 'solicitarRecarga']);
        Route::post('/recarga/{id}/aprobar', [WalletController::class, 'aprobarRecarga']);
        Route::post('/recarga/{id}/rechazar', [WalletController::class, 'rechazarRecarga']);
        Route::get('/recargas-pendientes', [WalletController::class, 'recargasPendientes']);
    });

    Route::prefix('inscripciones')->group(function () {
        Route::get('/mis-inscripciones', [InscripcionController::class, 'misInscripciones']);
        Route::post('/partido/{partidoId}', [InscripcionController::class, 'inscribirse']);
        Route::post('/{inscripcionId}/confirmar-pago', [InscripcionController::class, 'confirmarPago']);
        Route::post('/{inscripcionId}/cancelar', [InscripcionController::class, 'cancelarInscripcion']);
    });

    Route::prefix('sanciones')->group(function () {
        Route::get('/mis-sanciones', [SancionController::class, 'misSanciones']);
        Route::post('/{sancionId}/pagar', [SancionController::class, 'pagarReactivacion']);
        Route::get('/listado', [SancionController::class, 'listadoSanciones']);
    });

    Route::prefix('notificaciones')->group(function () {
        Route::get('/', [NotificacionController::class, 'index']);
        Route::get('/no-leidas', [NotificacionController::class, 'noLeidas']);
        Route::post('/{id}/marcar-leida', [NotificacionController::class, 'marcarComoLeida']);
        Route::post('/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas']);
    });

    Route::post('/ratings', [App\Http\Controllers\RatingController::class, 'store']);

    Route::prefix('jugador')->group(function () {
        Route::get('/estadisticas', [UserController::class, 'estadisticasJugador']);
        Route::get('/inscripciones', [InscripcionController::class, 'misInscripciones']);
        Route::put('/perfil', [UserController::class, 'update']);
        Route::post('/perfil/foto', [UserController::class, 'uploadFoto']);
        Route::put('/perfil/password', [UserController::class, 'updatePassword']);

        Route::get('/partidos/creados', [PartidoController::class, 'partidosCreados']);
        Route::get('/partidos/enMarcha', [PartidoController::class, 'partidosEnMarcha']);
        Route::get('/partidos/finalizados', [PartidoController::class, 'partidosFinalizados']);
        Route::post('/partidos/{id}/cancelar', [PartidoController::class, 'cancelarPartido']);
    });

});
