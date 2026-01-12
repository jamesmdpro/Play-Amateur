@extends('layouts.jugador')

@section('title', 'Mi Perfil')

@section('content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.2);
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        object-fit: cover;
    }

    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .profile-card .card-header {
        background: linear-gradient(135deg, #3ba76d 0%, #4ecb8f 100%);
        color: white;
        padding: 20px;
        border: none;
        font-weight: 600;
    }

    .profile-card .card-body {
        padding: 25px;
    }

    .form-label {
        color: #1a5f3f;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-control {
        border: 2px solid #e0f2e9;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #3ba76d;
        box-shadow: 0 0 0 0.2rem rgba(59, 167, 109, 0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(45, 134, 89, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .upload-btn {
        background: white;
        color: #2d8659;
        border: 2px solid #2d8659;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .upload-btn:hover {
        background: #2d8659;
        color: white;
    }
</style>

<!-- Profile Header -->
<div class="profile-header">
    <div class="row align-items-center">
        <div class="col-auto">
            <img src="{{ auth()->user()->foto ?? asset('images/default-avatar.svg') }}"
                 alt="Foto de perfil"
                 class="profile-avatar"
                 id="previewFoto">
        </div>
        <div class="col">
            <h2 class="mb-2">{{ auth()->user()->name }}</h2>
            <p class="mb-1"><i class="bi bi-envelope me-2"></i>{{ auth()->user()->email }}</p>
            <p class="mb-0"><i class="bi bi-person-badge me-2"></i>{{ ucfirst(auth()->user()->role) }}</p>
        </div>
        <div class="col-auto">
            <form id="formFoto" enctype="multipart/form-data">
                @csrf
                <input type="file" name="foto" id="inputFoto" class="d-none" accept="image/*">
                <button type="button" class="upload-btn" onclick="document.getElementById('inputFoto').click()">
                    <i class="bi bi-camera-fill me-2"></i>Cambiar Foto
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">

    <div class="col-lg-8">
        <div class="profile-card">
            <div class="card-header">
                <i class="bi bi-person-lines-fill me-2"></i>Información Personal
            </div>
            <div class="card-body">
                <form id="formPerfil">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-person me-2"></i>Nombre Completo</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-envelope me-2"></i>Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-telephone me-2"></i>Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ auth()->user()->telefono ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-calendar me-2"></i>Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" value="{{ auth()->user()->fecha_nacimiento ?? '' }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-geo-alt me-2"></i>Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="{{ auth()->user()->direccion ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-card-text me-2"></i>Documento de Identidad</label>
                        <input type="text" name="documento" class="form-control" value="{{ auth()->user()->documento ?? '' }}">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="location.reload()">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="profile-card">
            <div class="card-header">
                <i class="bi bi-shield-lock me-2"></i>Cambiar Contraseña
            </div>
            <div class="card-body">
                <form id="formPassword">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock me-2"></i>Contraseña Actual</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock-fill me-2"></i>Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-check-circle me-2"></i>Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key me-2"></i>Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="profile-card">
            <div class="card-body">
                <h6 class="mb-3" style="color: #1a5f3f; font-weight: 700;">
                    <i class="bi bi-info-circle me-2"></i>Información Adicional
                </h6>
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">Rol</small>
                    <span class="badge" style="background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%); font-size: 0.9rem;">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">Miembro desde</small>
                    <strong style="color: #1a5f3f;">{{ auth()->user()->created_at->format('d/m/Y') }}</strong>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">Estado</small>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Activo
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block mb-1">Saldo Actual</small>
                    <h4 style="color: #1a5f3f; font-weight: 700;">
                        ${{ number_format(auth()->user()->wallet ?? 0, 0) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('inputFoto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewFoto').src = e.target.result;
            };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('foto', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('/jugador/perfil/foto', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Foto actualizada correctamente');
                    location.reload();
                } else if (data.errors) {
                    const errorMessages = Object.values(data.errors).flat().join('\n');
                    alert('Errores de validación:\n' + errorMessages);
                } else {
                    alert(data.message || 'Error al actualizar la foto');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al subir la foto');
            });
        }
    });

    document.getElementById('formPerfil').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        fetch('/jugador/perfil/actualizar', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: JSON.stringify({...data, _method: 'PUT'})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Perfil actualizado correctamente');
                location.reload();
            } else if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('\n');
                alert('Errores de validación:\n' + errorMessages);
            } else {
                alert(data.message || 'Error al actualizar el perfil');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el perfil');
        });
    });

    document.getElementById('formPassword').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        if (data.password !== data.password_confirmation) {
            alert('Las contraseñas no coinciden');
            return;
        }

        fetch('/jugador/perfil/password', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin',
            body: JSON.stringify({...data, _method: 'PUT'})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Contraseña actualizada correctamente');
                this.reset();
            } else if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('\n');
                alert('Errores de validación:\n' + errorMessages);
            } else {
                alert(data.message || 'Error al actualizar la contraseña');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar la contraseña');
        });
    });
</script>
@endpush
