<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventoPartido extends Model
{
    use HasFactory;

    protected $fillable = [
        'partido_id',
        'tipo',
        'jugador_id',
        'minuto',
        'descripcion',
    ];

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function jugador()
    {
        return $this->belongsTo(User::class, 'jugador_id');
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeEnMinuto($query, $minuto)
    {
        return $query->where('minuto', $minuto);
    }
}