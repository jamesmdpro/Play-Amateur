@extends('layouts.jugador')

@section('title', 'Crear Encuentro - Jugador')

@section('content')
<style>
    .create-header {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.2);
    }

    .form-card {
        background: white;
        border-radius: 15px;
        padding: 35px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 25px;
        border-bottom: 2px solid #f0f7f4;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section h5 {
        color: #1a5f3f;
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-label {
        font-weight: 600;
        color: #1a5f3f;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border: 2px solid #e0f2e9;
        border-radius: 10px;
        padding: 12px 15px;
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3ba76d;
        box-shadow: 0 0 0 0.2rem rgba(59, 167, 109, 0.25);
    }

    .btn-create {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 15px 40px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-create:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(45, 134, 89, 0.4);
        color: white;
    }

    .btn-cancel {
        background: #6c757d;
        border: none;
        color: white;
        padding: 15px 40px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .info-box {
        background: linear-gradient(135deg, #e8f5e9 0%, #f0f7f4 100%);
        border-left: 4px solid #3ba76d;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .info-box i {
        color: #2d8659;
        font-size: 1.2rem;
    }
</style>

<div class="create-header">
    <h2><i class="bi bi-plus-circle-fill me-3"></i>Crear Nuevo Encuentro</h2>
    <p class="mb-0">Organiza un partido y permite que otros jugadores se unan</p>
</div>

<div class="form-card">
    <div class="info-box">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Importante:</strong> Completa todos los campos para crear tu encuentro. Los jugadores podrán inscribirse una vez publicado.
    </div>

    <form id="createPartidoForm" method="POST" action="{{ route('jugador.partidos.store') }}">
        @csrf
        
        <div class="form-section">
            <h5><i class="bi bi-calendar-event"></i>Información del Partido</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre del Partido *</label>
                    <input type="text" class="form-control" name="nombre" placeholder="Ej: Partido Amistoso Sábado" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo de Partido *</label>
                    <select class="form-select" name="tipo" required>
                        <option value="">Seleccionar...</option>
                        <option value="amistoso">Amistoso</option>
                        <option value="competitivo">Competitivo</option>
                        <option value="torneo">Torneo</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha del Partido *</label>
                    <input type="date" class="form-control" name="fecha" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Hora de Inicio *</label>
                    <input type="time" class="form-control" name="hora" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" rows="3" placeholder="Describe el partido, nivel requerido, etc."></textarea>
            </div>
        </div>

        <div class="form-section">
            <h5><i class="bi bi-geo-alt-fill"></i>Ubicación</h5>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Dirección de la Cancha *</label>
                    <input type="text" class="form-control" name="ubicacion" placeholder="Ej: Calle 123, Barrio Centro" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ciudad *</label>
                    <input type="text" class="form-control" name="ciudad" placeholder="Ej: Bogotá" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5><i class="bi bi-people-fill"></i>Configuración de Jugadores</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jugadores por Equipo *</label>
                    <select class="form-select" name="jugadores_por_equipo" required>
                        <option value="">Seleccionar...</option>
                        <option value="5">5 vs 5</option>
                        <option value="6">6 vs 6</option>
                        <option value="7">7 vs 7</option>
                        <option value="8">8 vs 8</option>
                        <option value="11">11 vs 11</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Máximo de Jugadores *</label>
                    <input type="number" class="form-control" name="max_jugadores" min="10" max="22" placeholder="Ej: 14" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Costo por Jugador *</label>
                    <input type="number" class="form-control" name="costo" min="0" placeholder="Ej: 15000" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h5><i class="bi bi-gear-fill"></i>Configuración Adicional</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado Inicial</label>
                    <select class="form-select" name="estado">
                        <option value="abierto">Abierto (Inscripciones activas)</option>
                        <option value="pendiente">Pendiente (No visible aún)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nivel Requerido</label>
                    <select class="form-select" name="nivel">
                        <option value="todos">Todos los niveles</option>
                        <option value="principiante">Principiante</option>
                        <option value="intermedio">Intermedio</option>
                        <option value="avanzado">Avanzado</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <button type="button" class="btn btn-cancel" onclick="window.location.href='{{ route('jugador.dashboard') }}'">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
            </div>
            <div class="col-md-6 mb-3">
                <button type="submit" class="btn btn-create">
                    <i class="bi bi-check-circle me-2"></i>Crear Encuentro
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('createPartidoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('¡Encuentro creado exitosamente!');
                window.location.href = '{{ route("jugador.partidos") }}';
            } else {
                alert('Error al crear el encuentro: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al crear el encuentro. Por favor intenta nuevamente.');
        });
    });

    const fechaInput = document.querySelector('input[name="fecha"]');
    const today = new Date().toISOString().split('T')[0];
    fechaInput.min = today;
</script>
@endpush
@endsection
