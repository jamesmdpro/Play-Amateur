@extends('layouts.cancha')

@section('title', 'Mi Perfil - Cancha')

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
</style>

<div class="profile-header">
    <div class="row align-items-center">
        <div class="col-md-2 text-center">
            <div class="profile-avatar d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #4ecb8f 0%, #3ba76d 100%);">
                <i class="bi bi-building" style="font-size: 3rem;"></i>
            </div>
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
            <h5><i class="bi bi-building me-2"></i>Información de la Cancha</h5>
            <div class="info-row">
                <span class="info-label">Nombre:</span>
                <span class="info-value">{{ auth()->user()->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ auth()->user()->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Teléfono:</span>
                <span class="info-value">{{ auth()->user()->telefono ?? 'No registrado' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Dirección:</span>
                <span class="info-value">{{ auth()->user()->direccion ?? 'No registrada' }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="profile-card">
            <h5><i class="bi bi-bar-chart me-2"></i>Estadísticas de la Cancha</h5>
            <div class="info-row">
                <span class="info-label">Partidos Realizados:</span>
                <span class="info-value" id="partidosRealizados">0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Partidos Activos:</span>
                <span class="info-value" id="partidosActivos">0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ingresos Totales:</span>
                <span class="info-value" id="ingresosTotal">$0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Calificación:</span>
                <span class="info-value">
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star-fill text-warning"></i>
                    <i class="bi bi-star-half text-warning"></i>
                    <span class="ms-2">4.5</span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="profile-card">
            <h5><i class="bi bi-building me-2"></i>Información de la Cancha</h5>
            <div class="info-row">
                <span class="info-label">Nombre de la Cancha:</span>
                <span class="info-value">{{ auth()->user()->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Dirección:</span>
                <span class="info-value">{{ auth()->user()->direccion ?? 'No registrada' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Teléfono de Contacto:</span>
                <span class="info-value">{{ auth()->user()->telefono ?? 'No registrado' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Horario de Atención:</span>
                <span class="info-value">{{ auth()->user()->horario ?? 'No especificado' }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="profile-card">
            <h5><i class="bi bi-bar-chart me-2"></i>Estadísticas de la Cancha</h5>
            <div class="info-row">
                <span class="info-label">Partidos Realizados:</span>
                <span class="info-value" id="partidosRealizados">0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ingresos Totales:</span>
                <span class="info-value" id="ingresosTotales">$0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Partidos Activos:</span>
                <span class="info-value" id="partidosActivos">0</span>
            </div>
            <div class="info-row">
                <span class="info-label">Calificación Promedio:</span>
                <span class="info-value" id="calificacion">5.0 ⭐</span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%); color: white;">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar Perfil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de la Cancha</label>
                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" value="{{ auth()->user()->phone ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion" value="{{ auth()->user()->direccion ?? '' }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3">{{ auth()->user()->descripcion ?? '' }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-edit" onclick="guardarPerfil()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
async function cargarEstadisticas() {
    try {
        const response = await fetch('/api/partidos', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const partidos = await response.json();
        const misPartidos = partidos.filter(p => p.creador.id === {{ auth()->id() }});
        
        document.getElementById('partidosRealizados').textContent = misPartidos.filter(p => p.estado === 'finalizado').length;
        document.getElementById('partidosActivos').textContent = misPartidos.filter(p => p.estado === 'abierto' || p.estado === 'en_curso').length;
        
        const ingresos = misPartidos
            .filter(p => p.estado === 'finalizado')
            .reduce((total, p) => total + (p.costo || 0), 0);
        
        document.getElementById('ingresosTotales').textContent = '$' + ingresos.toLocaleString('es-CO');
    } catch (error) {
        console.error('Error:', error);
    }
}

async function guardarPerfil() {
    const form = document.getElementById('editProfileForm');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/jugador/perfil/actualizar', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        if (response.ok) {
            alert('Perfil actualizado exitosamente');
            location.reload();
        } else {
            alert('Error al actualizar el perfil');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar el perfil');
    }
}

cargarEstadisticas();
</script>
@endsection
