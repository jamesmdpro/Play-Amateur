@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Mis Sanciones</h3>
                </div>
                <div class="card-body">
                    <div id="sancionActiva" class="alert alert-warning d-none">
                        <h5>Tienes una sanción activa</h5>
                        <p id="sancionInfo"></p>
                        <button class="btn btn-primary" id="btnPagarSancion">Pagar Reactivación ($15,000)</button>
                    </div>

                    <h4 class="mt-4">Historial de Sanciones</h4>
                    <div class="table-responsive">
                        <table class="table table-striped" id="sancionesTable">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Partido</th>
                                    <th>Número</th>
                                    <th>Días</th>
                                    <th>Fecha Fin</th>
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarSanciones();
});

async function cargarSanciones() {
    try {
        const response = await fetch('/api/sanciones/mis-sanciones', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#sancionesTable tbody');
        tbody.innerHTML = '';
        
        let sancionActiva = null;
        
        data.data.forEach(s => {
            if (s.activa && new Date(s.fecha_fin) >= new Date()) {
                sancionActiva = s;
            }
            
            const row = `
                <tr>
                    <td>${new Date(s.created_at).toLocaleDateString()}</td>
                    <td>${s.partido ? s.partido.nombre : 'N/A'}</td>
                    <td><span class="badge bg-warning">${s.numero_sancion}ª</span></td>
                    <td>${s.dias_suspension} días</td>
                    <td>${new Date(s.fecha_fin).toLocaleDateString()}</td>
                    <td>
                        <span class="badge bg-${s.pagada ? 'success' : s.activa ? 'danger' : 'secondary'}">
                            ${s.pagada ? 'Pagada' : s.activa ? 'Activa' : 'Vencida'}
                        </span>
                    </td>
                    <td>
                        ${!s.pagada && s.activa ? 
                            `<button class="btn btn-sm btn-primary" onclick="pagarSancion(${s.id})">Pagar</button>` : 
                            '-'
                        }
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
        
        if (sancionActiva) {
            document.getElementById('sancionActiva').classList.remove('d-none');
            document.getElementById('sancionInfo').innerHTML = `
                Sanción ${sancionActiva.numero_sancion}ª - ${sancionActiva.dias_suspension} días<br>
                Válida hasta: ${new Date(sancionActiva.fecha_fin).toLocaleDateString()}<br>
                Motivo: ${sancionActiva.motivo}
            `;
            document.getElementById('btnPagarSancion').onclick = () => pagarSancion(sancionActiva.id);
        }
    } catch (error) {
        console.error('Error al cargar sanciones:', error);
    }
}

async function pagarSancion(id) {
    if (!confirm('¿Pagar $15,000 para reactivar tu cuenta?')) return;
    
    try {
        const response = await fetch(`/api/sanciones/${id}/pagar`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            alert('Sanción pagada exitosamente. Tu cuenta ha sido reactivada.');
            cargarSanciones();
            location.reload();
        } else {
            alert(data.message || 'Error al pagar sanción');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>
@endsection
