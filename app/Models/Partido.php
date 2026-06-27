<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    // Campos asignables mapeados desde la migración de partidos
    protected $fillable = [
        'seleccion_local_id',
        'seleccion_visitante_id',
        'fecha',
        'estadio',
        'fase',
        'goles_local',
        'goles_visitante',
        'estado'
    ];

    /**
     * Relación inversa: El partido pertenece a una selección LOCAL.
     */
    public function seleccionLocal()
    {
        return $this->belongsTo(Seleccion::class, 'seleccion_local_id');
    }

    /**
     * Relación inversa: El partido pertenece a una selección VISITANTE.
     */
    public function seleccionVisitante()
    {
        return $this->belongsTo(Seleccion::class, 'seleccion_visitante_id');
    }
}