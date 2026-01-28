@extends('layouts.jugador')

@section('title', 'Estadísticas - Jugador')

@section('content')
<style>
    .stat-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s;
        background: #fff;
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.08);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(26, 95, 63, 0.15);
    }

    .stat-card.green {
        background: linear-gradient(135deg, #1a5f3f 0%, #2d8659 100%);
        color: white;
    }

    .stat-card.light-green {
        background: linear-gradient(135deg, #3ba76d 0%, #4ecb8f 100%);
        color: white;
    }

    .stat-card.accent {
        background: linear-gradient(135deg, #2d8659 0%, #3ba76d 100%);
        color: white;
    }

    .stat-card.yellow {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }

    .stat-card.red {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: rgba(255,255,255,0.2);
    }

    .stat-card.green .stat-icon,
    .stat-card.light-green .stat-icon,
    .stat-card.accent .stat-icon,
    .stat-card.yellow .stat-icon,
    .stat-card.red .stat-icon {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .stat-value {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    .section-title {
        color: #1a5f3f;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #4ecb8f;
    }

    .rating-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s;
        background: white;
        box-shadow: 0 2px 10px rgba(26, 95, 63, 0.08);
    }

    .rating-card:hover {
        box-shadow: 0 4px 15px rgba(26, 95, 63, 0.15);
    }

    .position-badge {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }

    .puntualidad-card {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .star-rating {
        color: #fbbf24;
    }

    .star-rating.empty {
        color: rgba(255,255,255,0.3);
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="section-title">
                <i class="bi bi-bar-chart-line-fill me-2"></i>
                Estadísticas de {{ $jugador->name }}
            </h1>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card green">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <p class="stat-value">{{ $estadistica->partidos_jugados }}</p>
                            <p class="stat-label">Partidos Jugados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card light-green">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <div>
                            <p class="stat-value">{{ $estadistica->goles }}</p>
                            <p class="stat-label">Goles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card yellow">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="bi bi-card-text"></i>
                        </div>
                        <div>
                            <p class="stat-value">{{ $estadistica->tarjetas_amarillas }}</p>
                            <p class="stat-label">Tarjetas Amarillas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-card red">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3">
                            <i class="bi bi-x-octagon-fill"></i>
                        </div>
                        <div>
                            <p class="stat-value">{{ $estadistica->tarjetas_rojas }}</p>
                            <p class="stat-label">Tarjetas Rojas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h2 class="section-title">
                <i class="bi bi-star-fill me-2"></i>
                Calificaciones
            </h2>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($promedios as $tipo => $promedio)
            <div class="col-md-6 col-lg-4">
                <div class="rating-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="color: #1a5f3f; font-weight: 600;">
                                {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                            </h5>
                            <span class="badge" style="background: linear-gradient(135deg, #3ba76d 0%, #4ecb8f 100%); font-size: 1rem; padding: 8px 15px;">
                                {{ number_format($promedio, 1) }}
                            </span>
                        </div>
                        <div class="d-flex">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill" style="font-size: 1.2rem; margin-right: 4px; color: {{ $i <= round($promedio) ? '#fbbf24' : '#e5e7eb' }};"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($estadistica->posicion_mas_usada || $estadistica->puntualidad_promedio > 0)
        <div class="row g-4">
            @if($estadistica->posicion_mas_usada)
                <div class="col-md-6">
                    <div class="stat-card" style="background: white;">
                        <div class="card-body p-4">
                            <h5 class="mb-3" style="color: #1a5f3f; font-weight: 600;">
                                <i class="bi bi-geo-alt-fill me-2"></i>
                                Posición Más Usada
                            </h5>
                            <div class="position-badge">
                                <i class="bi bi-person-badge-fill"></i>
                                {{ ucfirst($estadistica->posicion_mas_usada) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($estadistica->puntualidad_promedio > 0)
                <div class="col-md-6">
                    <div class="puntualidad-card">
                        <h5 class="mb-3" style="font-weight: 600;">
                            <i class="bi bi-clock-fill me-2"></i>
                            Puntualidad Promedio
                        </h5>
                        <div class="d-flex align-items-center">
                            <div class="stat-value me-3">{{ number_format($estadistica->puntualidad_promedio, 1) }}</div>
                            <div class="d-flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill" style="font-size: 1.5rem; margin-right: 4px; color: {{ $i <= round($estadistica->puntualidad_promedio) ? '#fbbf24' : 'rgba(255,255,255,0.3)' }};"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
