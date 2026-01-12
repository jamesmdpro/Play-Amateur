@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Notificaciones</h3>
                    <button class="btn btn-sm btn-secondary" onclick="marcarTodasLeidas()">
                        Marcar todas como leídas
                    </button>
                </div>
                <div class="card-body">
                    <div id="notificacionesList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarNotificaciones();
    setInterval(cargarNotificacionesNoLeidas, 30000);
});

async function cargarNotificaciones() {
    try {
        const response = await fetch('/api/notificaciones', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const container = document.getElementById('notificacionesList');
        container.innerHTML = '';
        
        if (data.data.length === 0) {
            container.innerHTML = '<p class="text-muted">No tienes notificaciones</p>';
            return;
        }
        
        data.data.forEach(n => {
            const notif = `
                <div class="alert alert-${n.leida ? 'secondary' : 'info'} d-flex justify-content-between align-items-start">
                    <div>
                        <h5>${n.titulo}</h5>
                        <p class="mb-1">${n.mensaje}</p>
                        <small class="text-muted">${new Date(n.created_at).toLocaleString()}</small>
                    </div>
                    ${!n.leida ? 
                        `<button class="btn btn-sm btn-primary" onclick="marcarLeida(${n.id})">
                            Marcar como leída
                        </button>` : 
                        ''
                    }
                </div>
            `;
            container.innerHTML += notif;
        });
    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
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
        const badge = document.getElementById('notifBadge');
        if (badge && data.count > 0) {
            badge.textContent = data.count;
            badge.classList.remove('d-none');
        }
    } catch (error) {
        console.error('Error al cargar notificaciones no leídas:', error);
    }
}

async function marcarLeida(id) {
    try {
        const response = await fetch(`/api/notificaciones/${id}/marcar-leida`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            cargarNotificaciones();
            cargarNotificacionesNoLeidas();
        }
    } catch (error) {
        console.error('Error al marcar notificación:', error);
    }
}

async function marcarTodasLeidas() {
    try {
        const response = await fetch('/api/notificaciones/marcar-todas-leidas', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            cargarNotificaciones();
            cargarNotificacionesNoLeidas();
        }
    } catch (error) {
        console.error('Error al marcar todas las notificaciones:', error);
    }
}
</script>
@endsection
