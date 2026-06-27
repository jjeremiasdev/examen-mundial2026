<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            // Claves foráneas hacia la tabla selecciones
            $table->foreignId('seleccion_local_id')->constrained('selecciones')->onDelete('cascade');
            $table->foreignId('seleccion_visitante_id')->constrained('selecciones')->onDelete('cascade');

            $table->dateTime('fecha');
            $table->string('estadio');

            // Regla: Fases válidas
            $table->enum('fase', ['GRUPOS', 'OCTAVOS', 'CUARTOS', 'SEMIFINAL', 'FINAL']);

            // Regla: Goles no pueden ser negativos (unsigned)
            $table->unsignedInteger('goles_local')->default(0);
            $table->unsignedInteger('goles_visitante')->default(0);

            // Regla: Estados válidos
            $table->enum('estado', ['PROGRAMADO', 'EN_JUEGO', 'FINALIZADO'])->default('PROGRAMADO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
