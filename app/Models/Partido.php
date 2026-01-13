<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_hora',
        'ubicacion',
        'cupos_totales',
        'cupos_suplentes',
        'costo',
        'con_arbitro',
        'costo_por_jugador',
        'estado',
        'creador_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'costo' => 'decimal:2',
            'costo_por_jugador' => 'decimal:2',
            'con_arbitro' => 'boolean',
        ];
    }

    public function calcularCostoPorJugador()
    {
        $costoBase = $this->costo;

        if ($this->con_arbitro) {
            $costoBase += 100000;
        }

        $totalJugadores = $this->cupos_totales + $this->cupos_suplentes;

        if ($totalJugadores > 0) {
            $this->costo_por_jugador = $costoBase / $totalJugadores;
            $this->save();
        }

        return $this->costo_por_jugador;
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function jugadores()
    {
        return $this->belongsToMany(User::class, 'inscripciones', 'partido_id', 'jugador_id')
            ->withPivot('es_suplente', 'equipo', 'estado')
            ->withTimestamps();
    }

    public function jugadoresTitulares()
    {
        return $this->jugadores()->wherePivot('es_suplente', false);
    }

    public function jugadoresSuplentes()
    {
        return $this->jugadores()->wherePivot('es_suplente', true);
    }

    public function cuposDisponibles()
    {
        $inscritos = $this->inscripciones()->where('es_suplente', false)->count();
        return $this->cupos_totales - $inscritos;
    }

    public function cuposSuplentesDisponibles()
    {
        $suplentes = $this->inscripciones()->where('es_suplente', true)->count();
        return $this->cupos_suplentes - $suplentes;
    }

    public function generarEquipos()
    {
        $jugadores = $this->jugadoresTitulares()->get();
        
        if ($jugadores->count() < 2) {
            return false;
        }

        $arqueros = $jugadores->where('posicion', 'arquero');
        $defensas = $jugadores->where('posicion', 'defensa');
        $medios = $jugadores->where('posicion', 'medio');
        $atacantes = $jugadores->where('posicion', 'ataque');

        $equipo1 = collect();
        $equipo2 = collect();

        foreach (['arquero' => $arqueros, 'defensa' => $defensas, 'medio' => $medios, 'ataque' => $atacantes] as $posicion => $grupo) {
            $grupo = $grupo->shuffle();
            $mitad = ceil($grupo->count() / 2);
            
            $equipo1 = $equipo1->merge($grupo->take($mitad));
            $equipo2 = $equipo2->merge($grupo->skip($mitad));
        }

        foreach ($equipo1 as $jugador) {
            Inscripcion::where('partido_id', $this->id)
                ->where('jugador_id', $jugador->id)
                ->update(['equipo' => 1]);
        }

        foreach ($equipo2 as $jugador) {
            Inscripcion::where('partido_id', $this->id)
                ->where('jugador_id', $jugador->id)
                ->update(['equipo' => 2]);
        }

        return true;
    }
}
