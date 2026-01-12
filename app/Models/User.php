<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'genero',
        'posicion',
        'nivel',
        'ciudad',
        'foto',
        'wallet',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet' => 'decimal:2',
        ];
    }

    public function partidosCreados()
    {
        return $this->hasMany(Partido::class, 'creador_id');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'jugador_id');
    }

    public function partidos()
    {
        return $this->belongsToMany(Partido::class, 'inscripciones', 'jugador_id', 'partido_id')
            ->withPivot('es_suplente', 'equipo', 'estado')
            ->withTimestamps();
    }

    public function isAdmin()
    {
        return $this->rol === 'admin';
    }

    public function isCancha()
    {
        return $this->rol === 'cancha';
    }

    public function isArbitro()
    {
        return $this->rol === 'arbitro';
    }

    public function isJugador()
    {
        return $this->rol === 'jugador';
    }
}
