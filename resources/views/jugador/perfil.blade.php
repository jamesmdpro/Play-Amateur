@extends('layouts.jugador')

@section('title', 'Mi Perfil - Jugador')

@section('content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        border-radius: 20px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.2);
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        object-fit: cover;
    }

    .profile-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
        margin-bottom: 20px;
    }

    .profile-card h5 {
        color: #1a5f3f;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #4ecb8f;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #f0f7f4;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #1a5f3f;
    }

    .info-value {
        color: #6c757d;
    }

    .btn-edit {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(45, 134, 89, 0.4);
        color: white;
    }

    .stat-badge {
        background: linear-gradient(135deg, #3ba76d 0%, #4ecb8f 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        margin: 5px;
    }
</style>

<div class="profile-header">
    <div class="row align-items-center">
        <div class="col-md-2 text-center">
            <img src="{{ auth()->user()->avatar ?? asset('images/players/jugador.jpeg') }}" alt="Avatar" class="profile-avatar">
        </div>
        <div class="col-md-7">
            <h2 class="mb-2">{{ auth()->user()->name }}</h2>
            <p class="mb-1"><i class="bi bi-envelope me-2"></i>{{ auth()->user()->email }}</p>
            <p class="mb-0"><i class="bi bi-calendar me-2"></i>Miembro desde {{ auth()->user()->created_at->format('d/m/Y') }}</p>
        </div>
        <div class="col-md-3 text-end">
            <button class="btn btn-edit" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil-square me-2"></i>Editar Perfil
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="profile-card">
            <h5><i class="bi bi-person-badge me-2"></i>Información Personal</h5>
            <div class="info-row">
                <span class="info-label">Nombre Completo:</span>
                <span class="info-value">{{ auth()->user()->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ auth()->user()->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Teléfono:</span>
                <span class="info-value">{{ auth()->user()->phone ?? 'No registrado' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Nacimiento:</span>
                <span class="info-value">{{ auth()->user()->birth_date ?? 'No registrado' }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="profile-card">
            <h5><i class="bi bi-trophy me-2"></i>Estadísticas</h5>
            <div class="info-row">
                <span class="info-label">Partidos Jugados:</span>
                <span class="info-value" id="partidosJugados">0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Saldo Actual:</span>
                <span class="info-value">${{ number_format(auth()->user()->wallet ?? 0, 0) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tarjetas Amarillas:</span>
                <span class="info-value" id="tarjetasAmarillas">0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tarjetas Rojas:</span>
                <span class="info-value" id="tarjetasRojas">0</span>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="profile-card">
            <h5><i class="bi bi-gear me-2"></i>Configuración de Cuenta</h5>
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-outline-primary w-100 mb-3">
                        <i class="bi bi-key me-2"></i>Cambiar Contraseña
                    </button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-outline-success w-100 mb-3">
                        <i class="bi bi-bell me-2"></i>Notificaciones
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%); color: white;">
                <h5 class="modal-title">Editar Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="phone" value="{{ auth()->user()->phone ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="birth_date" value="{{ auth()->user()->birth_date ?? '' }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-edit" onclick="saveProfile()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function saveProfile() {
        alert('Funcionalidad de guardar perfil - Próximamente');
    }

    fetch('/api/jugador/estadisticas')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('partidosJugados').textContent = data.partidos_jugados || 0;
                document.getElementById('tarjetasAmarillas').textContent = data.tarjetas_amarillas || 0;
                document.getElementById('tarjetasRojas').textContent = data.tarjetas_rojas || 0;
            }
        })
        .catch(error => console.error('Error:', error));
</script>
@endpush
@endsection
