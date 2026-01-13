@extends('layouts.jugador')

@section('title', 'Mis Partidos - Jugador')

@section('content')
<style>
    .partidos-header {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.2);
    }

    .tabs-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
        margin-bottom: 30px;
    }

    .nav-tabs {
        border-bottom: 3px solid #e0f2e9;
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 600;
        border: none;
        padding: 12px 25px;
        transition: all 0.3s;
    }

    .nav-tabs .nav-link:hover {
        color: #2d8659;
        border-color: transparent;
    }

    .nav-tabs .nav-link.active {
        color: #1a5f3f;
        background: transparent;
        border-bottom: 3px solid #3ba76d;
        margin-bottom: -3px;
    }

    .partido-item {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
        border-left: 5px solid #3ba76d;
        transition: all 0.3s;
    }

    .partido-item:hover {
        transform: translateX(5px);
        box-shadow: 0 6px 20px rgba(26, 95, 63, 0.15);
    }

    .partido-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
    }

    .partido-title {
        color: #1a5f3f;
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 5px;
    }

    .partido-info {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
    }

    .info-item i {
        color: #3ba76d;
        font-size: 1.1rem;
    }

    .badge-estado {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .badge-abierto {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .badge-en-marcha {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
    }

    .badge-finalizado {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }

    .badge-cancelado {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .partido-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
    }

    .btn-ver {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        color: white;
    }

    .btn-ver:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(45, 134, 89, 0.4);
        color: white;
    }

    .btn-editar {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .btn-editar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
        color: white;
    }

    .btn-cancelar {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .btn-cancelar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        color: #e0f2e9;
        margin-bottom: 20px;
    }

    .jugadores-count {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #e8f5e9;
        padding: 5px 12px;
        border-radius: 15px;
        color: #2d8659;
        font-weight: 600;
    }
</style>

<div class="partidos-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-trophy-fill me-3"></i>Mis Partidos</h2>
            <p class="mb-0">Gestiona tus partidos creados y en curso</p>
        </div>
        <a href="{{ route('jugador.crear-encuentro') }}" class="btn btn-light btn-lg">
            <i class="bi bi-plus-circle me-2"></i>Crear Nuevo
        </a>
    </div>
</div>

<div class="tabs-container">
    <ul class="nav nav-tabs" id="partidosTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="creados-tab" data-bs-toggle="tab" data-bs-target="#creados" type="button">
                <i class="bi bi-calendar-plus me-2"></i>Partidos Creados
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="en-marcha-tab" data-bs-toggle="tab" data-bs-target="#en-marcha" type="button">
                <i class="bi bi-play-circle me-2"></i>En Marcha
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="finalizados-tab" data-bs-toggle="tab" data-bs-target="#finalizados" type="button">
                <i class="bi bi-check-circle me-2"></i>Finalizados
            </button>
        </li>
    </ul>

    <div class="tab-content mt-4" id="partidosTabContent">
        <div class="tab-pane fade show active" id="creados" role="tabpanel">
            <div id="partidosCreados">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="en-marcha" role="tabpanel">
            <div id="partidosEnMarcha">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="finalizados" role="tabpanel">
            <div id="partidosFinalizados">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function cargarPartidos(tipo) {
        const container = document.getElementById(`partidos${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
        
        fetch(`/api/jugador/partidos/${tipo}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.partidos.length > 0) {
                    container.innerHTML = data.partidos.map(partido => `
                        <div class="partido-item">
                            <div class="partido-header">
                                <div>
                                    <h4 class="partido-title">${partido.nombre}</h4>
                                    <span class="badge-estado badge-${partido.estado}">${partido.estado_texto}</span>
                                </div>
                                <div class="jugadores-count">
                                    <i class="bi bi-people-fill"></i>
                                    ${partido.inscritos}/${partido.max_jugadores}
                                </div>
                            </div>
                            
                            <div class="partido-info">
                                <div class="info-item">
                                    <i class="bi bi-calendar3"></i>
                                    <span>${partido.fecha_formateada}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-clock"></i>
                                    <span>${partido.hora}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>${partido.ubicacion}</span>
                                </div>
                                <div class="info-item">
                                    <i class="bi bi-cash"></i>
                                    <span>$${partido.costo}</span>
                                </div>
                            </div>
                            
                            <div class="partido-actions">
                                <button class="btn btn-action btn-ver" onclick="verPartido(${partido.id})">
                                    <i class="bi bi-eye me-1"></i>Ver Detalles
                                </button>
                                ${partido.puede_editar ? `
                                    <button class="btn btn-action btn-editar" onclick="editarPartido(${partido.id})">
                                        <i class="bi bi-pencil me-1"></i>Editar
                                    </button>
                                    <button class="btn btn-action btn-cancelar" onclick="cancelarPartido(${partido.id})">
                                        <i class="bi bi-x-circle me-1"></i>Cancelar
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h4>No hay partidos ${tipo === 'creados' ? 'creados' : tipo === 'enMarcha' ? 'en marcha' : 'finalizados'}</h4>
                            <p>Cuando ${tipo === 'creados' ? 'crees' : 'tengas'} partidos, aparecerán aquí</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle"></i>
                        <h4>Error al cargar partidos</h4>
                        <p>Por favor intenta nuevamente</p>
                    </div>
                `;
            });
    }

    function verPartido(id) {
        window.location.href = `/jugador/partidos/${id}`;
    }

    function editarPartido(id) {
        window.location.href = `/jugador/partidos/${id}/editar`;
    }

    function cancelarPartido(id) {
        if (confirm('¿Estás seguro de cancelar este partido?')) {
            fetch(`/api/jugador/partidos/${id}/cancelar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Partido cancelado exitosamente');
                    cargarPartidos('creados');
                } else {
                    alert('Error al cancelar el partido');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cancelar el partido');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        cargarPartidos('creados');
        
        document.getElementById('en-marcha-tab').addEventListener('click', function() {
            cargarPartidos('enMarcha');
        });
        
        document.getElementById('finalizados-tab').addEventListener('click', function() {
            cargarPartidos('finalizados');
        });
    });
</script>
@endpush
@endsection
