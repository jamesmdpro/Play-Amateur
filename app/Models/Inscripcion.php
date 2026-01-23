<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'partido_id',
        'jugador_id',
        'es_suplente',
        'equipo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'es_suplente' => 'boolean',
        ];
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'jugador_id');
    }
}
