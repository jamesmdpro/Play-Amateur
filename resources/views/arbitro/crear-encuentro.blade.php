@extends('layouts.arbitro')

@section('title', 'Crear Encuentro - Árbitro')

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }

    .form-card h3 {
        color: #1a5f3f;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #4ecb8f;
    }

    .btn-crear {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        color: white;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-crear:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(45, 134, 89, 0.4);
        color: white;
    }
</style>

<div class="form-card">
    <h3><i class="bi bi-plus-circle me-2"></i>Crear Nuevo Encuentro</h3>
    
    <form id="formCrearPartido">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre del Partido *</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Ciudad *</label>
                <input type="text" class="form-control" name="ciudad" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tipo de Partido</label>
                <select class="form-select" name="tipo_partido">
                    <option value="">Seleccionar...</option>
                    <option value="5vs5">5 vs 5</option>
                    <option value="7vs7">7 vs 7</option>
                    <option value="11vs11">11 vs 11</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Fecha *</label>
                <input type="date" class="form-control" name="fecha" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Hora *</label>
                <input type="time" class="form-control" name="hora" required>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" rows="3"></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Ubicación *</label>
                <input type="text" class="form-control" name="ubicacion" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Cupos Totales *</label>
                <input type="number" class="form-control" name="cupos_totales" min="2" value="12" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Cupos Suplentes</label>
                <input type="number" class="form-control" name="cupos_suplentes" min="0" value="4">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Costo Total *</label>
                <input type="number" class="form-control" name="costo" min="0" value="80000" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Estado Inicial</label>
                <select class="form-select" name="estado_inicial">
                    <option value="abierto" selected>Abierto</option>
                    <option value="cerrado">Cerrado</option>
                    <option value="en_curso">En Curso</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nivel</label>
                <select class="form-select" name="nivel">
                    <option value="">Seleccionar...</option>
                    <option value="principiante">Principiante</option>
                    <option value="intermedio">Intermedio</option>
                    <option value="avanzado">Avanzado</option>
                    <option value="profesional">Profesional</option>
                </select>
            </div>
            <div class="col-12 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="con_arbitro" id="conArbitro" checked disabled>
                    <label class="form-check-label" for="conArbitro">
                        Incluir Árbitro (Yo seré el árbitro de este partido)
                    </label>
                </div>
            </div>
        </div>
        
        <div class="text-end">
            <button type="submit" class="btn btn-crear">
                <i class="bi bi-check-circle me-2"></i>Crear Partido
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('formCrearPartido').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const fecha = formData.get('fecha');
    const hora = formData.get('hora');
    const fecha_hora = `${fecha} ${hora}:00`;
    
    const data = {
        nombre: formData.get('nombre'),
        descripcion: formData.get('descripcion'),
        ciudad: formData.get('ciudad'),
        tipo_partido: formData.get('tipo_partido'),
        fecha_hora: fecha_hora,
        ubicacion: formData.get('ubicacion'),
        cupos_totales: parseInt(formData.get('cupos_totales')),
        cupos_suplentes: parseInt(formData.get('cupos_suplentes')),
        costo: parseFloat(formData.get('costo')),
        estado_inicial: formData.get('estado_inicial'),
        nivel: formData.get('nivel'),
        con_arbitro: true
    };
    
    try {
        const response = await fetch('/api/partidos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Partido creado exitosamente');
            window.location.href = '{{ route("arbitro.partidos") }}';
        } else {
            alert('Error al crear el partido');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al crear el partido');
    }
});
</script>
@endsection
