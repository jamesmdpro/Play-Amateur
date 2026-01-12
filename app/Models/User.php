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
        'telefono',
        'fecha_nacimiento',
        'direccion',
        'documento',
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

    public function transacciones()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function sanciones()
    {
        return $this->hasMany(Sancion::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function tieneSancionActiva()
    {
        return $this->sanciones()
            ->where('activa', true)
            ->where('fecha_fin', '>=', now())
            ->exists();
    }

    public function sancionActiva()
    {
        return $this->sanciones()
            ->where('activa', true)
            ->where('fecha_fin', '>=', now())
            ->first();
    }

    public function tieneSaldo($monto)
    {
        return $this->wallet >= $monto;
    }

    public function descontarSaldo($monto, $tipo, $partidoId = null, $notas = null)
    {
        $saldoAnterior = $this->wallet;
        $this->wallet -= $monto;
        $this->save();

        return $this->transacciones()->create([
            'tipo' => $tipo,
            'monto' => -$monto,
            'saldo_anterior' => $saldoAnterior,
            'saldo_nuevo' => $this->wallet,
            'estado' => 'aprobado',
            'partido_id' => $partidoId,
            'notas' => $notas,
        ]);
    }

    public function agregarSaldo($monto, $tipo, $notas = null)
    {
        $saldoAnterior = $this->wallet;
        $this->wallet += $monto;
        $this->save();

        return $this->transacciones()->create([
            'tipo' => $tipo,
            'monto' => $monto,
            'saldo_anterior' => $saldoAnterior,
            'saldo_nuevo' => $this->wallet,
            'estado' => 'aprobado',
            'notas' => $notas,
        ]);
    }
}