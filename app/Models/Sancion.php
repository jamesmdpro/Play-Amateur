<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Sancion extends Model
{
    use HasFactory;

    protected $table = 'sanciones';

    protected $fillable = [
        'user_id',
        'partido_id',
        'numero_sancion',
        'dias_suspension',
        'fecha_inicio',
        'fecha_fin',
        'monto_reactivacion',
        'pagada',
        'activa',
        'motivo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'monto_reactivacion' => 'decimal:2',
        'pagada' => 'boolean',
        'activa' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function estaVigente()
    {
        return $this->activa && Carbon::now()->lte($this->fecha_fin);
    }

    public static function calcularDiasSuspension($numeroSancion)
    {
        return match($numeroSancion) {
            1 => 7,
            2 => 15,
            3 => 30,
            default => 30,
        };
    }
}
