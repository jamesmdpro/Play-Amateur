<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'calificador_id',
        'calificado_id',
        'partido_id',
        'tipo',
        'puntuacion',
        'comentario',
    ];

    protected function casts(): array
    {
        return [
            'puntuacion' => 'integer',
        ];
    }

    public function calificador()
    {
        return $this->belongsTo(User::class, 'calificador_id');
    }

    public function calificado()
    {
        return $this->belongsTo(User::class, 'calificado_id');
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeParaJugador($query, $jugadorId)
    {
        return $query->where('calificado_id', $jugadorId);
    }

    public function scopePorCalificador($query, $calificadorId)
    {
        return $query->where('calificador_id', $calificadorId);
    }
}