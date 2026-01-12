@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>¡Bienvenido!</h1>
            <p>Has iniciado sesión correctamente</p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p style="font-size: 1.2rem; margin-bottom: 20px;">
                Hola, <strong>{{ Auth::user()->name }}</strong>
            </p>
            <p style="color: rgba(255, 255, 255, 0.8);">
                {{ Auth::user()->email }}
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary">
                Cerrar Sesión
            </button>
        </form>
    </div>
</div>
@endsection
