<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    use HasFactory;

    protected $fillable = [
        'partido_id',
        'arbitro_id',
        'marcador_equipo1',
        'marcador_equipo2',
        'estado',
        'notas',
        'validaciones_equipo1',
        'validaciones_equipo2',
    ];

    protected function casts(): array
    {
        return [
            'validaciones_equipo1' => 'array',
            'validaciones_equipo2' => 'array',
        ];
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function arbitro()
    {
        return $this->belongsTo(User::class, 'arbitro_id');
    }

    public function eventos()
    {
        return $this->hasMany(EventoPartido::class);
    }

    public function validarPorJugador($jugadorId, $equipo, $aceptar = true, $notas = null)
    {
        if ($equipo == 1) {
            $validaciones = $this->validaciones_equipo1 ?? [];
            $validaciones[$jugadorId] = ['aceptado' => $aceptar, 'notas' => $notas, 'fecha' => now()];
            $this->validaciones_equipo1 = $validaciones;
        } elseif ($equipo == 2) {
            $validaciones = $this->validaciones_equipo2 ?? [];
            $validaciones[$jugadorId] = ['aceptado' => $aceptar, 'notas' => $notas, 'fecha' => now()];
            $this->validaciones_equipo2 = $validaciones;
        }

        $this->checkValidacionCompleta();
        $this->save();
    }

    private function checkValidacionCompleta()
    {
        $validaciones1 = $this->validaciones_equipo1 ?? [];
        $validaciones2 = $this->validaciones_equipo2 ?? [];

        $aceptados1 = collect($validaciones1)->where('aceptado', true)->count();
        $aceptados2 = collect($validaciones2)->where('aceptado', true)->count();

        if ($aceptados1 >= 2 && $aceptados2 >= 2) {
            $this->estado = 'validado';
        } elseif (collect($validaciones1)->where('aceptado', false)->isNotEmpty() ||
                  collect($validaciones2)->where('aceptado', false)->isNotEmpty()) {
            $this->estado = 'rechazado';
        }
    }
}