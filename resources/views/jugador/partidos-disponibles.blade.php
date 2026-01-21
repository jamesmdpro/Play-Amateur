@extends('layouts.jugador')

@section('title', 'Partidos Disponibles')

@section('content')
<style>
    .partido-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
        transition: all 0.3s;
    }

    .partido-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.15);
    }

    .partido-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 2px solid #4ecb8f;
    }

    .partido-titulo {
        color: #1a5f3f;
        font-weight: 700;
        font-size: 1.3rem;
        margin: 0;
    }

    .badge-cupos {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
    }

    .partido-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-item i {
        color: #2d8659;
        font-size: 1.2rem;
    }

    .btn-inscribirse {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-inscribirse:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(45, 134, 89, 0.4);
        color: white;
    }

    .btn-inscribirse:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }
</style>

<div class="filter-section">
    <h4 class="mb-3"><i class="bi bi-funnel me-2"></i>Filtros</h4>
    <div class="row">
        <div class="col-md-4">
            <input type="text" class="form-control" id="filtroNombre" placeholder="Buscar por nombre...">
        </div>
        <div class="col-md-4">
            <input type="date" class="form-control" id="filtroFecha">
        </div>
        <div class="col-md-4">
            <select class="form-select" id="filtroEstado">
                <option value="">Todos los estados</option>
                <option value="abierto">Abierto</option>
                <option value="cerrado">Cerrado</option>
            </select>
        </div>
    </div>
</div>

<div id="partidosContainer">
    <div class="text-center py-5">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
</div>

<script>
let partidos = [];

async function cargarPartidos() {
    try {
        const response = await fetch('/api/partidos/disponibles', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        if (response.ok) {
            partidos = await response.json();
            mostrarPartidos(partidos);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function mostrarPartidos(partidosFiltrados) {
    const container = document.getElementById('partidosContainer');
    
    if (partidosFiltrados.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No hay partidos disponibles</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = partidosFiltrados.map(partido => `
        <div class="partido-card">
            <div class="partido-header">
                <h3 class="partido-titulo">${partido.nombre}</h3>
                <span class="badge-cupos">
                    <i class="bi bi-people-fill me-1"></i>
                    ${partido.cupos_disponibles}/${partido.cupos_totales} cupos
                </span>
            </div>
            
            <div class="partido-info">
                <div class="info-item">
                    <i class="bi bi-calendar-event"></i>
                    <span>${new Date(partido.fecha_hora).toLocaleDateString('es-CO')}</span>
                </div>
                <div class="info-item">
                    <i class="bi bi-clock"></i>
                    <span>${new Date(partido.fecha_hora).toLocaleTimeString('es-CO', {hour: '2-digit', minute: '2-digit'})}</span>
                </div>
                <div class="info-item">
                    <i class="bi bi-geo-alt"></i>
                    <span>${partido.ubicacion}</span>
                </div>
                <div class="info-item">
                    <i class="bi bi-cash"></i>
                    <span>$${new Intl.NumberFormat('es-CO').format(partido.costo_por_jugador)}</span>
                </div>
                ${partido.con_arbitro ? '<div class="info-item"><i class="bi bi-whistle"></i><span>Con árbitro</span></div>' : ''}
            </div>
            
            ${partido.descripcion ? `<p class="text-muted mb-3">${partido.descripcion}</p>` : ''}
            
            <button class="btn btn-inscribirse" 
                    onclick="inscribirse(${partido.id})"
                    ${partido.cupos_disponibles === 0 ? 'disabled' : ''}>
                <i class="bi bi-check-circle me-2"></i>
                ${partido.cupos_disponibles === 0 ? 'Sin cupos disponibles' : 'Inscribirse'}
            </button>
        </div>
    `).join('');
}

async function inscribirse(partidoId) {
    if (!confirm('¿Deseas inscribirte a este partido?')) return;
    
    try {
        const response = await fetch(`/api/partidos/${partidoId}/inscribirse`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            alert('Inscripción exitosa');
            cargarPartidos();
        } else {
            const data = await response.json();
            alert(data.message || 'Error al inscribirse');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al inscribirse');
    }
}

document.getElementById('filtroNombre').addEventListener('input', filtrarPartidos);
document.getElementById('filtroFecha').addEventListener('change', filtrarPartidos);
document.getElementById('filtroEstado').addEventListener('change', filtrarPartidos);

function filtrarPartidos() {
    const nombre = document.getElementById('filtroNombre').value.toLowerCase();
    const fecha = document.getElementById('filtroFecha').value;
    const estado = document.getElementById('filtroEstado').value;
    
    const filtrados = partidos.filter(partido => {
        const cumpleNombre = !nombre || partido.nombre.toLowerCase().includes(nombre);
        const cumpleFecha = !fecha || new Date(partido.fecha_hora).toISOString().split('T')[0] === fecha;
        const cumpleEstado = !estado || partido.estado === estado;
        
        return cumpleNombre && cumpleFecha && cumpleEstado;
    });
    
    mostrarPartidos(filtrados);
}

cargarPartidos();
</script>
@endsection
