@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Bienvenido</h1>
            <p>Inicia sesión en tu cuenta</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    value="{{ old('email') }}" 
                    placeholder="tu@email.com"
                    required 
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="••••••••"
                    required
                >
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Recordarme</label>
            </div>

            <button type="submit" class="btn btn-primary">
                Iniciar Sesión
            </button>
        </form>

        <div class="auth-links">
            <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
        </div>
    </div>
</div>
@endsection
