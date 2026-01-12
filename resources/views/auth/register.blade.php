@extends('layouts.app')

@section('title', 'Registro')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Crear Cuenta</h1>
            <p>Regístrate para comenzar</p>
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

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Nombre Completo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    placeholder="Tu nombre completo"
                    required
                    autofocus
                >
            </div>

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
                >
            </div>

            <div class="form-group">
                <label for="rol">Tipo de Usuario</label>
                <select id="rol" name="rol" class="form-control" required>
                    <option value="">Selecciona tu rol</option>
                    <option value="jugador" {{ old('rol') == 'jugador' ? 'selected' : '' }}>Jugador</option>
                    <option value="cancha" {{ old('rol') == 'cancha' ? 'selected' : '' }}>Cancha</option>
                    <option value="arbitro" {{ old('rol') == 'arbitro' ? 'selected' : '' }}>Árbitro</option>
                </select>
            </div>

            <div class="form-group" id="genero-group" style="display: none;">
                <label for="genero">Género</label>
                <select id="genero" name="genero" class="form-control">
                    <option value="">Selecciona tu género</option>
                    <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>

            <div class="form-group" id="posicion-group" style="display: none;">
                <label for="posicion">Posición (Solo Jugadores)</label>
                <select id="posicion" name="posicion" class="form-control">
                    <option value="">Selecciona tu posición</option>
                    <option value="portero" {{ old('posicion') == 'portero' ? 'selected' : '' }}>Portero</option>
                    <option value="defensa" {{ old('posicion') == 'defensa' ? 'selected' : '' }}>Defensa</option>
                    <option value="mediocampista" {{ old('posicion') == 'mediocampista' ? 'selected' : '' }}>Mediocampista</option>
                    <option value="delantero" {{ old('posicion') == 'delantero' ? 'selected' : '' }}>Delantero</option>
                </select>
            </div>

            <div class="form-group" id="nivel-group" style="display: none;">
                <label for="nivel">Nivel (Solo Jugadores)</label>
                <select id="nivel" name="nivel" class="form-control">
                    <option value="">Selecciona tu nivel</option>
                    <option value="principiante" {{ old('nivel') == 'principiante' ? 'selected' : '' }}>Principiante</option>
                    <option value="intermedio" {{ old('nivel') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                    <option value="avanzado" {{ old('nivel') == 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                </select>
            </div>

            <div class="form-group">
                <label for="ciudad">Ciudad</label>
                <input
                    type="text"
                    id="ciudad"
                    name="ciudad"
                    class="form-control"
                    value="{{ old('ciudad') }}"
                    placeholder="Tu ciudad"
                    required
                >
            </div>

            <div class="form-group">
                <label for="foto">Foto de Perfil</label>
                <input
                    type="file"
                    id="foto"
                    name="foto"
                    class="form-control"
                    accept="image/*"
                    style="padding: 8px;"
                    required
                >
                <small style="color: rgba(255, 255, 255, 0.7); font-size: 0.85rem; display: block; margin-top: 5px;">
                    Formatos: JPG, PNG, GIF (Máx. 2MB)
                </small>
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

            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="••••••••"
                    required
                >
            </div>

            <button type="submit" class="btn btn-secondary">
                Registrarse
            </button>
        </form>

        <div class="auth-links">
            <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a></p>
        </div>
    </div>
</div>

<script>
    document.getElementById('rol').addEventListener('change', function() {
        const posicionGroup = document.getElementById('posicion-group');
        const nivelGroup = document.getElementById('nivel-group');
        const generoGroup = document.getElementById('genero-group');
        const posicionInput = document.getElementById('posicion');
        const nivelInput = document.getElementById('nivel');
        const generoInput = document.getElementById('genero');

        if (this.value === 'jugador' || this.value === 'arbitro') {
            generoGroup.style.display = 'block';
            generoInput.required = true;

            if (this.value === 'jugador') {
                posicionGroup.style.display = 'block';
                nivelGroup.style.display = 'block';
                posicionInput.required = true;
                nivelInput.required = true;
            } else {
                posicionGroup.style.display = 'none';
                nivelGroup.style.display = 'none';
                posicionInput.required = false;
                nivelInput.required = false;
                posicionInput.value = '';
                nivelInput.value = '';
            }
        } else {
            generoGroup.style.display = 'none';
            posicionGroup.style.display = 'none';
            nivelGroup.style.display = 'none';
            generoInput.required = false;
            posicionInput.required = false;
            nivelInput.required = false;
            generoInput.value = '';
            posicionInput.value = '';
            nivelInput.value = '';
        }
    });

    const currentRol = document.getElementById('rol').value;
    if (currentRol === 'jugador') {
        document.getElementById('posicion-group').style.display = 'block';
        document.getElementById('nivel-group').style.display = 'block';
        document.getElementById('genero-group').style.display = 'block';
    } else if (currentRol === 'arbitro') {
        document.getElementById('genero-group').style.display = 'block';
    }
</script>
@endsection
