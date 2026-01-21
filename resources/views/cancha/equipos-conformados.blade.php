@extends('layouts.cancha')

@section('title', 'Equipos Conformados')

@section('content')
<style>
    .partido-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }

    .partido-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #4ecb8f;
    }

    .partido-titulo {
        color: #1a5f3f;
        font-weight: 700;
        font-size: 1.3rem;
        margin: 0;
    }

    .equipos-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .equipo-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 20px;
    }

    .equipo-titulo {
        color: #1a5f3f;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .jugador-item {
        background: white;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .jugador-nombre {
        font-weight: 600;
        color: #2d8659;
    }

    .jugador-posicion {
        background: #4ecb8f;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .suplentes-section {
        background: #fff3cd;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .arbitro-section {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .btn-generar {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-generar:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(45, 134, 89, 0.4);
        color: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 10px;
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d8659;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>

<div id="partidosContainer">
    <div class="text-center py-5">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
</div>

<script>
async function cargarPartidos() {
    try {
        const response = await fetch('/api/partidos/mis-partidos', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        if (response.ok) {
            const partidos = await response.json();
            mostrarPartidos(partidos);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function mostrarPartidos(partidos) {
    const container = document.getElementById('partidosContainer');
    
    if (partidos.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No has creado partidos aún</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = partidos.map(partido => `
        <div class="partido-card">
            <div class="partido-header">
                <h3 class="partido-titulo">${partido.nombre}</h3>
                <button class="btn btn-generar" onclick="generarEquipos(${partido.id})">
                    <i class="bi bi-shuffle me-2"></i>Generar Equipos
                </button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">${partido.inscritos_count || 0}</div>
                    <div class="stat-label">Inscritos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${partido.cupos_disponibles || 0}</div>
                    <div class="stat-label">Cupos Disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${new Date(partido.fecha_hora).toLocaleDateString('es-CO')}</div>
                    <div class="stat-label">Fecha</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">${new Date(partido.fecha_hora).toLocaleTimeString('es-CO', {hour: '2-digit', minute: '2-digit'})}</div>
                    <div class="stat-label">Hora</div>
                </div>
            </div>

            ${partido.equipos_generados ? `
                <div class="equipos-container">
                    <div class="equipo-card">
                        <div class="equipo-titulo">
                            <i class="bi bi-shield-fill" style="color: #dc3545;"></i>
                            Equipo 1
                        </div>
                        ${partido.equipo1 && partido.equipo1.length > 0 ? 
                            partido.equipo1.map(jugador => `
                                <div class="jugador-item">
                                    <span class="jugador-nombre">${jugador.name}</span>
                                    <span class="jugador-posicion">${jugador.posicion || 'Sin posición'}</span>
                                </div>
                            `).join('') : 
                            '<p class="text-muted">Sin jugadores asignados</p>'
                        }
                    </div>

                    <div class="equipo-card">
                        <div class="equipo-titulo">
                            <i class="bi bi-shield-fill" style="color: #0d6efd;"></i>
                            Equipo 2
                        </div>
                        ${partido.equipo2 && partido.equipo2.length > 0 ? 
                            partido.equipo2.map(jugador => `
                                <div class="jugador-item">
                                    <span class="jugador-nombre">${jugador.name}</span>
                                    <span class="jugador-posicion">${jugador.posicion || 'Sin posición'}</span>
                                </div>
                            `).join('') : 
                            '<p class="text-muted">Sin jugadores asignados</p>'
                        }
                    </div>
                </div>

                ${partido.suplentes && partido.suplentes.length > 0 ? `
                    <div class="suplentes-section">
                        <h5><i class="bi bi-people me-2"></i>Suplentes</h5>
                        ${partido.suplentes.map(jugador => `
                            <div class="jugador-item">
                                <span class="jugador-nombre">${jugador.name}</span>
                                <span class="jugador-posicion">${jugador.posicion || 'Sin posición'}</span>
                            </div>
                        `).join('')}
                    </div>
                ` : ''}

                ${partido.con_arbitro ? `
                    <div class="arbitro-section">
                        <h5><i class="bi bi-whistle me-2"></i>Árbitro</h5>
                        <p class="mb-0">${partido.arbitro ? partido.arbitro.name : 'Pendiente de asignación'}</p>
                    </div>
                ` : ''}
            ` : `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Los equipos aún no han sido generados. Haz clic en "Generar Equipos" cuando tengas suficientes jugadores inscritos.
                </div>
            `}
        </div>
    `).join('');
}

async function generarEquipos(partidoId) {
    if (!confirm('¿Deseas generar los equipos para este partido?')) return;
    
    try {
        const response = await fetch(`/api/partidos/${partidoId}/generar-equipos`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            alert('Equipos generados exitosamente');
            cargarPartidos();
        } else {
            const data = await response.json();
            alert(data.message || 'Error al generar equipos');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al generar equipos');
    }
}

cargarPartidos();
</script>
@endsection
