<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/home', function () {
    return view('home');
})->middleware('auth')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard')->middleware('role:admin');

    Route::get('/admin/recargas', function () {
        return view('admin.recargas');
    })->name('admin.recargas')->middleware('role:admin');

    Route::get('/cancha/dashboard', function () {
        return view('cancha.dashboard');
    })->name('cancha.dashboard')->middleware('role:cancha');

    Route::get('/arbitro/dashboard', function () {
        return view('arbitro.dashboard');
    })->name('arbitro.dashboard')->middleware('role:arbitro');

    Route::get('/jugador/dashboard', function () {
        return view('jugador.dashboard');
    })->name('jugador.dashboard')->middleware('role:jugador');

    Route::get('/jugador/perfil', function () {
        return view('jugador.perfil');
    })->name('jugador.perfil')->middleware('role:jugador');

    Route::prefix('jugador')->middleware('role:jugador')->group(function () {
        Route::get('/estadisticas', [App\Http\Controllers\UserController::class, 'estadisticasJugador'])->name('jugador.estadisticas');
        Route::get('/inscripciones', [App\Http\Controllers\InscripcionController::class, 'misInscripciones'])->name('jugador.inscripciones');
        Route::put('/perfil/actualizar', [App\Http\Controllers\UserController::class, 'update'])->name('jugador.perfil.update');
        Route::post('/perfil/foto', [App\Http\Controllers\UserController::class, 'uploadFoto'])->name('jugador.perfil.foto');
        Route::put('/perfil/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('jugador.perfil.password');
    });

    Route::get('/partidos/disponibles', [App\Http\Controllers\PartidoController::class, 'disponibles'])->name('partidos.disponibles');
    Route::post('/partidos/{id}/inscribir', [App\Http\Controllers\InscripcionController::class, 'inscribirse'])->name('partidos.inscribir');

    Route::get('/wallet', function () {
        return view('wallet.index');
    })->name('wallet.index');

    Route::get('/sanciones', function () {
        return view('sanciones.index');
    })->name('sanciones.index');

    Route::get('/notificaciones', function () {
        return view('notificaciones.index');
    })->name('notificaciones.index');
});
