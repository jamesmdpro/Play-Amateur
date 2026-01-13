@extends('layouts.cancha')

@section('title', 'Mis Partidos - Cancha')

@section('content')
<style>
    .partido-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
        border-left: 4px solid #3ba76d;
        margin-bottom: 20px;
    }

    .partido-card:hover {
        box-shadow: 0 6px 20px rgba(26, 95, 63, 0.15);
        transform: translateX(5px);
    }

    .filter-tabs {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }

    .filter-btn {
        background: transparent;
        border: none;
        color: #6c757d;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        margin-right: 10px;
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        color: white;
    }

    .btn-ver {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-ver:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(45, 134, 89, 0.4);
        color: white;
    }
</style>

<div class="filter-tabs">
    <button class="filter-btn active" onclick="filtrarPartidos('todos')">Todos</button>
    <button class="filter-btn" onclick="filtrarPartidos('abierto')">Abiertos</button>
    <button class="filter-btn" onclick="filtrarPartidos('en_curso')">En Marcha</button>
    <button class="filter-btn" onclick="filtrarPartidos('finalizado')">Finalizados</button>
</div>

<div id="listaPartidos">
    <p class="text-center text-muted py-4">Cargando partidos...</p>
</div>

<script>
let filtroActual = 'todos';

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
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('listaPartidos').innerHTML = '<p class="text-center text-muted py-4">Error al cargar partidos</p>';
    }
}

function mostrarPartidos(partidos) {
    const container = document.getElementById('listaPartidos');
    
    let partidosFiltrados = partidos;
    if (filtroActual !== 'todos') {
        partidosFiltrados = partidos.filter(p => p.estado === filtroActual);
    }
    
    if (partidosFiltrados.length === 0) {
        container.innerHTML = '<p class="text-center text-muted py-4">No hay partidos</p>';
        return;
    }
    
    container.innerHTML = partidosFiltrados.map(partido => `
        <div class="card partido-card">
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
                        <button onclick="verDetallePartido(${partido.id})" class="btn btn-ver btn-sm">
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

function filtrarPartidos(estado) {
    filtroActual = estado;
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    cargarPartidos();
}

function verDetallePartido(id) {
    window.location.href = `/partidos/${id}`;
}

cargarPartidos();
</script>
@endsection
