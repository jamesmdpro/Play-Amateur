@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Mi Cartera</h3>
                    <h2 class="text-success">${{ number_format(auth()->user()->wallet, 0) }}</h2>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#recargaModal">
                        <i class="fas fa-plus"></i> Solicitar Recarga
                    </button>

                    <h4 class="mt-4">Historial de Transacciones</h4>
                    <div class="table-responsive">
                        <table class="table table-striped" id="transaccionesTable">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Saldo Anterior</th>
                                    <th>Saldo Nuevo</th>
                                    <th>Estado</th>
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

<div class="modal fade" id="recargaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Solicitar Recarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="recargaForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Monto</label>
                        <input type="number" class="form-control" name="monto" min="10000" required>
                        <small class="text-muted">Monto mínimo: $10,000</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comprobante de Pago (Nequi)</label>
                        <input type="file" class="form-control" name="comprobante" accept="image/*" required>
                        <small class="text-muted">Sube una captura de tu comprobante de Nequi</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarTransacciones();

    document.getElementById('recargaForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch('/api/wallet/recarga', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token')
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                alert('Solicitud enviada exitosamente. Espera la aprobación del administrador.');
                bootstrap.Modal.getInstance(document.getElementById('recargaModal')).hide();
                this.reset();
                cargarTransacciones();
            } else {
                alert(data.message || 'Error al enviar solicitud');
            }
        } catch (error) {
            alert('Error de conexión');
        }
    });
});

async function cargarTransacciones() {
    try {
        const response = await fetch('/api/wallet', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        });
        
        const data = await response.json();
        const tbody = document.querySelector('#transaccionesTable tbody');
        tbody.innerHTML = '';
        
        data.transacciones.data.forEach(t => {
            const row = `
                <tr>
                    <td>${new Date(t.created_at).toLocaleDateString()}</td>
                    <td><span class="badge bg-info">${t.tipo}</span></td>
                    <td class="${t.monto >= 0 ? 'text-success' : 'text-danger'}">
                        $${Math.abs(t.monto).toLocaleString()}
                    </td>
                    <td>$${t.saldo_anterior.toLocaleString()}</td>
                    <td>$${t.saldo_nuevo.toLocaleString()}</td>
                    <td><span class="badge bg-${t.estado === 'aprobado' ? 'success' : t.estado === 'rechazado' ? 'danger' : 'warning'}">${t.estado}</span></td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error al cargar transacciones:', error);
    }
}
</script>
@endsection
