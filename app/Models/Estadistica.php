<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estadistica extends Model
{
    use HasFactory;

    protected $fillable = [
        'jugador_id',
        'partidos_jugados',
        'goles',
        'tarjetas_amarillas',
        'tarjetas_rojas',
        'posicion_mas_usada',
        'puntualidad_promedio',
        'calificaciones',
    ];

    protected function casts(): array
    {
        return [
            'calificaciones' => 'array',
            'puntualidad_promedio' => 'decimal:2',
        ];
    }

    public function jugador()
    {
        return $this->belongsTo(User::class, 'jugador_id');
    }

    public function agregarPartido()
    {
        $this->partidos_jugados += 1;
        $this->save();
    }

    public function agregarGol($cantidad = 1)
    {
        $this->goles += $cantidad;
        $this->save();
    }

    public function agregarTarjeta($tipo = 'amarilla')
    {
        if ($tipo === 'amarilla') {
            $this->tarjetas_amarillas += 1;
        } elseif ($tipo === 'roja') {
            $this->tarjetas_rojas += 1;
        }
        $this->save();
    }

    public function agregarCalificacion($tipo, $puntuacion)
    {
        $calificaciones = $this->calificaciones ?? [];
        if (!isset($calificaciones[$tipo])) {
            $calificaciones[$tipo] = ['total' => 0, 'cantidad' => 0];
        }
        $calificaciones[$tipo]['total'] += $puntuacion;
        $calificaciones[$tipo]['cantidad'] += 1;
        $this->calificaciones = $calificaciones;
        $this->save();
    }

    public function getPromedioCalificacion($tipo)
    {
        $calificaciones = $this->calificaciones ?? [];
        if (isset($calificaciones[$tipo]) && $calificaciones[$tipo]['cantidad'] > 0) {
            return $calificaciones[$tipo]['total'] / $calificaciones[$tipo]['cantidad'];
        }
        return 0;
    }

    public function actualizarPosicionMasUsada($posicion)
    {
        // Lógica simple: si es la primera vez, asignar; si no, comparar conteos (necesitaría más campos)
        if (!$this->posicion_mas_usada) {
            $this->posicion_mas_usada = $posicion;
        }
        // Para simplificar, solo actualizar si es diferente
        $this->save();
    }
}