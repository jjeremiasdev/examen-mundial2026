<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seleccion extends Model
{
    use HasFactory;

    // Indica explícitamente el nombre de la tabla porque en la migración se llama 'selecciones'
    protected $table = 'selecciones';

    // Campos que permitirá registrar masivamente (los mismos de la migración)
    protected $fillable = [
        'nombre',
        'continente',
        'grupo',
        'ranking_fifa',
        'entrenador'
    ];

    /**
     * Relación: Una selección puede jugar muchos partidos como LOCAL.
     */
    public function partidosLocal()
    {
        return $this->hasMany(Partido::class, 'seleccion_local_id');
    }

    /**
     * Relación: Una selección puede jugar muchos partidos como VISITANTE.
     */
    public function partidosVisitante()
    {
        return $this->hasMany(Partido::class, 'seleccion_visitante_id');
    }
}