@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Mi Saldo</h5>
                            <h2>${{ number_format(auth()->user()->wallet, 0) }}</h2>
                            <a href="/wallet" class="btn btn-light btn-sm">Recargar</a>
                        </div>
                        <div class="col-md-4">
                            <h5>Notificaciones</h5>
                            <h2 id="notifCount">0</h2>
                            <a href="/notificaciones" class="btn btn-light btn-sm">Ver todas</a>
                        </div>
                        <div class="col-md-4">
                            <h5>Estado</h5>
                            <h2 id="estadoUsuario">Activo</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Partidos Disponibles</h3>
                </div>
                <div class="card-body">
                    <div id="partidosList" class="row"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Mis Inscripciones</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="inscripcionesTable">
                            <thead>
                                <tr>
                                    <th>Partido</th>
                                    <th>Fecha</th>
                                    <th>Equipo</th>
                                    <th>Estado</th>
                                    <th>Tipo</th>
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
</div>

<div class="modal fade" id="inscripcionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inscribirse al Partido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="inscripcionForm">
                    <input type="hidden" id="partidoId" name="partido_id">
                    <div class="mb-3">
                        <label class="form-label">Selecciona tu equipo</label>
                        <select class="form-select" name="equipo" required>
                            <option value="">Selecciona...</option>
                            <option value="A">Equipo A</option>
                            <option value="B">Equipo B</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <strong>Importante:</strong> Al inscribirte, deberás confirmar tu pago para asegurar tu cupo.
                    </div>
                    <button type="submit" class="btn btn-primary">Inscribirse</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarPartidos();
    cargarInscripciones();
    verificarEstadoUsuario();
    cargarNotificacionesNoLeidas();
});

async function verificarEstadoUsuario() {
    try {
        const response = await fetch('/api/sanciones/mis-sanciones', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const sancionActiva = data.data.find(s => s.activa && new Date(s.fecha_fin) >= new Date());
        
        if (sancionActiva) {
            document.getElementById('estadoUsuario').innerHTML = 
                '<span class="text-danger">Sancionado</span>';
            document.getElementById('estadoUsuario').parentElement.innerHTML += 
                `<a href="/sanciones" class="btn btn-warning btn-sm mt-2">Ver Sanción</a>`;
        }
    } catch (error) {
        console.error('Error al verificar estado:', error);
    }
}

async function cargarNotificacionesNoLeidas() {
    try {
        const response = await fetch('/api/notificaciones/no-leidas', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        document.getElementById('notifCount').textContent = data.count;
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    }
}

async function cargarPartidos() {
    try {
        const response = await fetch('/api/partidos', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const container = document.getElementById('partidosList');
        container.innerHTML = '';
        
        data.data.forEach(p => {
            const card = `
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">${p.nombre || 'Partido'}</h5>
                            <p class="card-text">
                                <strong>Fecha:</strong> ${new Date(p.fecha).toLocaleDateString()}<br>
                                <strong>Hora:</strong> ${p.hora}<br>
                                <strong>Lugar:</strong> ${p.lugar}<br>
                                <strong>Costo:</strong> $${(p.costo_por_jugador || 20000).toLocaleString()}
                            </p>
                            <button class="btn btn-primary btn-sm" onclick="mostrarInscripcion(${p.id})">
                                Inscribirse
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += card;
        });
    } catch (error) {
        console.error('Error al cargar partidos:', error);
    }
}

async function cargarInscripciones() {
    try {
        const response = await fetch('/api/inscripciones/mis-inscripciones', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#inscripcionesTable tbody');
        tbody.innerHTML = '';
        
        data.data.forEach(i => {
            const row = `
                <tr>
                    <td>${i.partido.nombre || 'Partido'}</td>
                    <td>${new Date(i.partido.fecha).toLocaleDateString()}</td>
                    <td><span class="badge bg-info">Equipo ${i.equipo}</span></td>
                    <td>
                        <span class="badge bg-${
                            i.estado === 'confirmado' ? 'success' : 
                            i.estado === 'cancelado' ? 'danger' : 
                            'warning'
                        }">${i.estado}</span>
                    </td>
                    <td>${i.es_suplente ? 'Suplente' : 'Titular'}</td>
                    <td>
                        ${i.estado === 'inscrito' && !i.pago_realizado ? 
                            `<button class="btn btn-sm btn-success" onclick="confirmarPago(${i.id})">
                                Confirmar Pago
                            </button>` : 
                            ''
                        }
                        ${i.estado === 'inscrito' || i.estado === 'confirmado' ? 
                            `<button class="btn btn-sm btn-danger" onclick="cancelarInscripcion(${i.id})">
                                Cancelar
                            </button>` : 
                            ''
                        }
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error al cargar inscripciones:', error);
    }
}

function mostrarInscripcion(partidoId) {
    document.getElementById('partidoId').value = partidoId;
    new bootstrap.Modal(document.getElementById('inscripcionModal')).show();
}

document.getElementById('inscripcionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const partidoId = document.getElementById('partidoId').value;
    const equipo = this.querySelector('[name="equipo"]').value;
    
    try {
        const response = await fetch(`/api/inscripciones/partido/${partidoId}`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ equipo })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('inscripcionModal')).hide();
            cargarInscripciones();
        } else {
            alert(data.message || 'Error al inscribirse');
        }
    } catch (error) {
        alert('Error de conexión');
    }
});

async function confirmarPago(inscripcionId) {
    if (!confirm('¿Confirmar pago y asegurar tu cupo?')) return;
    
    try {
        const response = await fetch(`/api/inscripciones/${inscripcionId}/confirmar-pago`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert('Pago confirmado exitosamente');
            cargarInscripciones();
            location.reload();
        } else {
            alert(data.message || 'Error al confirmar pago');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

async function cancelarInscripcion(inscripcionId) {
    if (!confirm('¿Estás seguro de cancelar? Si ya pagaste, recibirás una sanción.')) return;
    
    try {
        const response = await fetch(`/api/inscripciones/${inscripcionId}/cancelar`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert(data.message);
            cargarInscripciones();
            if (data.sancion) {
                alert('Has recibido una sanción. Revisa la sección de sanciones.');
            }
        } else {
            alert(data.message || 'Error al cancelar');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>
@endsection
