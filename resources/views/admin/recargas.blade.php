@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Recargas Pendientes</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="recargasTable">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Monto</th>
                                    <th>Comprobante</th>
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

<div class="modal fade" id="comprobanteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comprobante de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="comprobanteImg" src="" class="img-fluid" alt="Comprobante">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarRecargas();
});

async function cargarRecargas() {
    try {
        const response = await fetch('/api/wallet/recargas-pendientes', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#recargasTable tbody');
        tbody.innerHTML = '';
        
        data.data.forEach(r => {
            const row = `
                <tr>
                    <td>${new Date(r.created_at).toLocaleDateString()}</td>
                    <td>${r.user.name}</td>
                    <td class="text-success">$${r.monto.toLocaleString()}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="verComprobante('${r.comprobante}')">
                            Ver Comprobante
                        </button>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="aprobarRecarga(${r.id})">
                            Aprobar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="rechazarRecarga(${r.id})">
                            Rechazar
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error al cargar recargas:', error);
    }
}

function verComprobante(path) {
    document.getElementById('comprobanteImg').src = '/storage/' + path;
    new bootstrap.Modal(document.getElementById('comprobanteModal')).show();
}

async function aprobarRecarga(id) {
    if (!confirm('¿Aprobar esta recarga?')) return;
    
    try {
        const response = await fetch(`/api/wallet/recarga/${id}/aprobar`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            alert('Recarga aprobada exitosamente');
            cargarRecargas();
        } else {
            alert('Error al aprobar recarga');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

async function rechazarRecarga(id) {
    const notas = prompt('Motivo del rechazo:');
    if (!notas) return;
    
    try {
        const response = await fetch(`/api/wallet/recarga/${id}/rechazar`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ notas })
        });
        
        if (response.ok) {
            alert('Recarga rechazada');
            cargarRecargas();
        } else {
            alert('Error al rechazar recarga');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>
@endsection
