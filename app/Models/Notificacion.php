<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'leida' => 'boolean',
            'data' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function notificarNuevoPartido($partido)
    {
        if ($partido->con_arbitro) {
            $arbitros = User::where('rol', 'arbitro')->get();

            foreach ($arbitros as $arbitro) {
                self::create([
                    'user_id' => $arbitro->id,
                    'tipo' => 'partido_requiere_arbitro',
                    'titulo' => 'Nuevo partido requiere árbitro',
                    'mensaje' => "El partido '{$partido->nombre}' necesita un árbitro. Fecha: {$partido->fecha_hora->format('d/m/Y H:i')}",
                    'data' => [
                        'partido_id' => $partido->id,
                        'partido_nombre' => $partido->nombre,
                        'fecha_hora' => $partido->fecha_hora,
                        'ubicacion' => $partido->ubicacion,
                    ],
                ]);
            }
        }

        $jugadores = User::where('rol', 'jugador')->get();
        
        foreach ($jugadores as $jugador) {
            self::create([
                'user_id' => $jugador->id,
                'tipo' => 'partido_disponible',
                'titulo' => 'Nuevo partido disponible',
                'mensaje' => "Hay un nuevo partido '{$partido->nombre}' disponible. Fecha: {$partido->fecha_hora->format('d/m/Y H:i')}",
                'data' => [
                    'partido_id' => $partido->id,
                    'partido_nombre' => $partido->nombre,
                    'fecha_hora' => $partido->fecha_hora,
                    'ubicacion' => $partido->ubicacion,
                    'cupos_disponibles' => $partido->cuposDisponibles(),
                ],
            ]);
        }
    }
}
