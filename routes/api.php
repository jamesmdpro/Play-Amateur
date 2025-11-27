<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PartidoController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::post('/profile/foto', [UserController::class, 'uploadFoto']);
    Route::post('/wallet/update', [UserController::class, 'updateWallet']);
    
    Route::get('/partidos', [PartidoController::class, 'index']);
    Route::post('/partidos', [PartidoController::class, 'store']);
    Route::get('/partidos/{id}', [PartidoController::class, 'show']);
    Route::put('/partidos/{id}', [PartidoController::class, 'update']);
    Route::delete('/partidos/{id}', [PartidoController::class, 'destroy']);
    Route::post('/partidos/{id}/inscribirse', [PartidoController::class, 'inscribirse']);
    Route::post('/partidos/{id}/generar-equipos', [PartidoController::class, 'generarEquipos']);
});
