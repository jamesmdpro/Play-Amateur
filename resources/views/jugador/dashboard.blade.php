@extends('layouts.jugador')

@section('title', 'Dashboard - Jugador')

@section('content')
<style>
    .stat-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s;
        background: #fff;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.15);
    }

    .stat-card.green {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        color: white;
    }

    .stat-card.light-green {
        background: linear-gradient(135deg, #3ba76d 0%, #4ecb8f 100%);
        color: white;
    }

    .stat-card.accent {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        color: white;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: rgba(255,255,255,0.2);
    }

    .stat-card.green .stat-icon,
    .stat-card.light-green .stat-icon,
    .stat-card.accent .stat-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .stat-card.white {
        background: white;
    }

    .stat-card.white .stat-icon {
        background: rgba(26, 95, 63, 0.1);
        color: #1a5f3f;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    .section-title {
        color: #1a5f3f;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #4ecb8f;
    }

    .partido-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
        border-left: 4px solid #3ba76d;
    }

    .partido-card:hover {
        box-shadow: 0 6px 20px rgba(26, 95, 63, 0.15);
        transform: translateX(5px);
    }

    .btn-inscribir {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-inscribir:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(45, 134, 89, 0.4);
        color: white;
    }

    .badge-estado {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
    }

    .table-custom {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }

    .table-custom thead {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        color: white;
    }

    .table-custom tbody tr:hover {
        background: rgba(26, 95, 63, 0.05);
    }
</style>

<div class="row g-4 mb-4">
    <!-- Saldo Card -->
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1">Mi Saldo</p>
                        <h3 class="stat-value">${{ number_format(auth()->user()->wallet ?? 0, 0) }}</h3>
                    </div>
                </div>
                <a href="{{ route('wallet.index') }}" class="btn btn-light btn-sm w-100 mt-3">
                    <i class="bi bi-plus-circle"></i> Recargar Saldo
                </a>
            </div>
        </div>
    </div>

    <!-- Partidos Jugados Card -->
    <div class="col-md-4">
        <div class="stat-card light-green">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-trophy"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1">Partidos Jugados</p>
                        <h3 class="stat-value" id="partidosJugados">0</h3>
                    </div>
                </div>
                <small class="d-block mt-2" style="opacity: 0.9;">Total de partidos completados</small>
            </div>
        </div>
    </div>

    <!-- Partidos Disponibles Card -->
    <div class="col-md-4">
        <div class="stat-card accent">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1">Partidos Disponibles</p>
                        <h3 class="stat-value" id="partidosDisponibles">0</h3>
                    </div>
                </div>
                <small class="d-block mt-2" style="opacity: 0.9;">Partidos para inscribirse</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Tarjetas Card -->
    <div class="col-md-4">
        <div class="stat-card white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon me-3">
                        <i class="bi bi-card-list"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0" style="color: #1a5f3f; font-weight: 600;">Tarjetas</p>
                    </div>
                </div>
                <div class="d-flex justify-content-around">
                    <div class="text-center">
                        <div class="badge bg-warning text-dark fs-4 mb-2" id="tarjetasAmarillas">0</div>
                        <small class="d-block text-muted">Amarillas</small>
                    </div>
                    <div class="text-center">
                        <div class="badge bg-danger fs-4 mb-2" id="tarjetasRojas">0</div>
                        <small class="d-block text-muted">Rojas</small>
                    </div>
                    <div class="text-center">
                        <div class="badge fs-4 mb-2" style="background-color: #ff8c00; color: white;" id="tarjetasNaranjas">0</div>
                        <small class="d-block text-muted">Naranjas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendiente Card 1 -->
    <div class="col-md-4">
        <div class="stat-card white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1" style="color: #1a5f3f; font-weight: 600;">Pendiente</p>
                        <h3 class="stat-value" style="color: #6c757d;">-</h3>
                    </div>
                </div>
                <small class="d-block mt-2 text-muted">Próximamente</small>
            </div>
        </div>
    </div>

    <!-- Pendiente Card 2 -->
    <div class="col-md-4">
        <div class="stat-card white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1" style="color: #1a5f3f; font-weight: 600;">Estadísticas</p>
                        <h3 class="stat-value" style="color: #6c757d;">-</h3>
                    </div>
                </div>
                <small class="d-block mt-2 text-muted">Próximamente</small>
            </div>
        </div>
    </div>
</div>

<!-- Partidos Disponibles Section -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="section-title">
            <i class="bi bi-calendar-check me-2"></i>Partidos Disponibles
        </h4>
        <div id="partidosList" class="row g-3">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mis Inscripciones Section -->
<div class="row">
    <div class="col-12">
        <h4 class="section-title">
            <i class="bi bi-list-check me-2"></i>Mis Inscripciones
        </h4>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Partido</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Ubicación</th>
                                <th class="px-4 py-3">Equipo</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="inscripcionesTable">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="spinner-border text-success" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
                    <div class="stat-icon me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h6 class="stat-label">Pendiente</h6>
                        <h3 class="stat-value">-</h3>
                    </div>
                </div>
                <small class="text-muted">Próximamente</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card stat-card white h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon me-3">
                        <i class="bi bi-three-dots"></i>
                    </div>
                    <div>
                        <h6 class="stat-label">Pendiente</h6>
                        <h3 class="stat-value">-</h3>
                    </div>
                </div>
                <small class="text-muted">Próximamente</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Partidos Disponibles</h5>
            </div>
            <div class="card-body">
                <div id="partidosList" class="row g-3"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Mis Inscripciones</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="inscripcionesTable">
                        <thead>
                            <tr>
                                <th>Partido</th>
                                <th>Fecha</th>
                                <th>Cancha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        cargarEstadisticas();
        cargarPartidosDisponibles();
        cargarMisInscripciones();
    });

    function cargarEstadisticas() {
        fetch('/jugador/estadisticas', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('partidosJugados').textContent = data.data.partidos_jugados || 0;
                document.getElementById('partidosDisponibles').textContent = data.data.partidos_disponibles || 0;
                document.getElementById('tarjetasAmarillas').textContent = data.data.tarjetas_amarillas || 0;
                document.getElementById('tarjetasRojas').textContent = data.data.tarjetas_rojas || 0;
                document.getElementById('tarjetasNaranjas').textContent = data.data.tarjetas_naranjas || 0;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function cargarPartidosDisponibles() {
        fetch('/partidos/disponibles', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('partidosList');
            container.innerHTML = '';

            if (data.data && data.data.length > 0) {
                data.data.forEach(partido => {
                    container.innerHTML += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card partido-card h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0" style="color: #1a5f3f; font-weight: 700;">${partido.nombre}</h5>
                                        <span class="badge" style="background: #4ecb8f;">Abierto</span>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-2 text-muted">
                                            <i class="bi bi-calendar3 me-2" style="color: #2d8659;"></i>${partido.fecha}
                                        </p>
                                        <p class="mb-2 text-muted">
                                            <i class="bi bi-geo-alt-fill me-2" style="color: #2d8659;"></i>${partido.cancha}
                                        </p>
                                        <p class="mb-0">
                                            <i class="bi bi-cash-coin me-2" style="color: #2d8659;"></i>
                                            <strong style="color: #1a5f3f; font-size: 1.1rem;">$${partido.costo}</strong>
                                        </p>
                                        <p class="mb-0">
                                            <i class="bi bi-cash-coin me-2" style="color: #2d8659;"></i>
                                            <strong style="color: #1a5f3f; font-size: 1.1rem;">${partido.costo_por_jugador}</strong>
                                            <small class="text-muted ms-1">por jugador</small>
                                        </p>
                                    </div>
                                    <button class="btn btn-inscribir w-100" onclick="inscribirPartido(${partido.id})">
                                        <i class="bi bi-check-circle me-2"></i>Inscribirse
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-calendar-x" style="font-size: 3rem; color: #6c757d;"></i>
                        <p class="text-muted mt-3">No hay partidos disponibles en este momento</p>
                    </div>
                `;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function cargarMisInscripciones() {
        fetch('/jugador/inscripciones', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('inscripcionesTable');
            tbody.innerHTML = '';

            if (data.data && data.data.length > 0) {
                data.data.forEach(inscripcion => {
                    const estadoColors = {
                        'pendiente': 'warning',
                        'confirmada': 'success',
                        'cancelada': 'danger',
                        'completada': 'info'
                    };
                    const colorClass = estadoColors[inscripcion.estado_color] || 'secondary';

                    tbody.innerHTML += `
                        <tr>
                            <td class="px-4 py-3">
                                <strong style="color: #1a5f3f;">${inscripcion.partido}</strong>
                            </td>
                            <td class="px-4 py-3">${inscripcion.fecha}</td>
                            <td class="px-4 py-3">
                                <i class="bi bi-geo-alt-fill me-1" style="color: #2d8659;"></i>${inscripcion.cancha}
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-${colorClass}">${inscripcion.equipo || 'Sin asignar'}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge-estado bg-${colorClass} text-white">${inscripcion.estado}</span>
                            </td>
                            <td class="px-4 py-3">
                                <button class="btn btn-sm" style="background: #3ba76d; color: white;" onclick="verDetalle(${inscripcion.id})">
                                    <i class="bi bi-eye"></i> Ver
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 2.5rem; color: #6c757d;"></i>
                            <p class="text-muted mt-3">No tienes inscripciones aún</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function inscribirPartido(partidoId) {
        if (!confirm('¿Deseas inscribirte a este partido?')) return;

        fetch(`/partidos/${partidoId}/inscribir`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.message) {
                alert(data.message || 'Inscripción exitosa');
                cargarPartidosDisponibles();
                cargarMisInscripciones();
                cargarEstadisticas();
            } else {
                alert(data.message || 'Error al inscribirse');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la inscripción');
        });
    }

    function verDetalle(inscripcionId) {
        window.location.href = `/jugador/inscripciones/${inscripcionId}`;
    }
</script>
@endpush
