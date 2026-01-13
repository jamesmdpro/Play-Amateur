@extends('layouts.cancha')

@section('title', 'Dashboard - Cancha')

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

    .btn-ver-detalle {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-ver-detalle:hover {
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
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1">Partidos Programados</p>
                        <h3 class="stat-value" id="partidos-programados">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card light-green">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1">Partidos en Marcha</p>
                        <h3 class="stat-value" id="partidos-marcha">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card accent">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="stat-label mb-1">Ingresos del Mes</p>
                        <h3 class="stat-value" id="ingresos-mes">$0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card partido-card">
            <div class="card-body p-4">
                <h2 class="section-title">Próximos Partidos</h2>
                <div id="proximos-partidos">
                    <p class="text-center text-muted py-4">Cargando partidos...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function cargarPartidos() {
    try {
        const response = await fetch('/api/partidos', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const partidos = await response.json();
        const misPartidos = partidos.filter(p => p.creador.id === {{ auth()->id() }});
        
        mostrarPartidos(misPartidos);
        actualizarEstadisticas(misPartidos);
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('proximos-partidos').innerHTML = '<p class="text-center text-muted py-4">Error al cargar partidos</p>';
    }
}

function mostrarPartidos(partidos) {
    const container = document.getElementById('proximos-partidos');
    
    if (partidos.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">No hay partidos programados</p>';
        return;
    }
    
    const proximosPartidos = partidos
        .filter(p => p.estado === 'abierto' || p.estado === 'en_curso')
        .slice(0, 5);
    
    if (proximosPartidos.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">No hay partidos próximos</p>';
        return;
    }
    
    container.innerHTML = proximosPartidos.map(partido => `
        <div class="card mb-3 partido-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-1" style="color: #1a5f3f; font-weight: 600;">${partido.nombre}</h5>
                        <p class="text-muted mb-1">
                            <i class="bi bi-calendar3 me-1"></i>
                            ${new Date(partido.fecha_hora).toLocaleString('es-CO', { 
                                dateStyle: 'medium', 
                                timeStyle: 'short' 
                            })}
                        </p>
                        <p class="text-muted mb-0">
                            <i class="bi bi-geo-alt-fill me-1" style="color: #2d8659;"></i>
                            ${partido.ubicacion}
                        </p>
                    </div>
                    <div class="col-md-3 text-center">
                        <p class="mb-0 text-muted small">Inscritos</p>
                        <h4 class="mb-0" style="color: #2d8659; font-weight: 700;">
                            ${partido.inscritos}/${partido.cupos_totales}
                        </h4>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="badge ${getBadgeEstado(partido.estado)} mb-2">
                            ${getEstadoTexto(partido.estado)}
                        </span>
                        <br>
                        <button onclick="verDetallePartido(${partido.id})" class="btn btn-ver-detalle btn-sm">
                            <i class="bi bi-eye"></i> Ver Detalle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function getBadgeEstado(estado) {
    const badges = {
        'abierto': 'bg-success',
        'en_curso': 'bg-primary',
        'cerrado': 'bg-warning',
        'finalizado': 'bg-secondary'
    };
    return badges[estado] || 'bg-secondary';
}

function getEstadoTexto(estado) {
    const textos = {
        'abierto': 'Abierto',
        'en_curso': 'En Marcha',
        'cerrado': 'Cerrado',
        'finalizado': 'Finalizado'
    };
    return textos[estado] || estado;
}

function actualizarEstadisticas(partidos) {
    const programados = partidos.filter(p => p.estado === 'abierto').length;
    const enMarcha = partidos.filter(p => p.estado === 'en_curso').length;
    
    document.getElementById('partidos-programados').textContent = programados;
    document.getElementById('partidos-marcha').textContent = enMarcha;
    
    const ingresosMes = partidos
        .filter(p => {
            const fecha = new Date(p.fecha_hora);
            const ahora = new Date();
            return fecha.getMonth() === ahora.getMonth() && 
                   fecha.getFullYear() === ahora.getFullYear() &&
                   p.estado === 'finalizado';
        })
        .reduce((total, p) => total + (p.costo || 0), 0);
    
    document.getElementById('ingresos-mes').textContent = '$' + ingresosMes.toLocaleString('es-CO');
}

function verDetallePartido(id) {
    window.location.href = `/partidos/${id}`;
}

cargarPartidos();
</script>
@endsection
