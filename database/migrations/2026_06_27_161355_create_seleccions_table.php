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
        Schema::create('selecciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Regla: Nombre único
            $table->string('continente');
            $table->string('grupo');
            $table->integer('ranking_fifa'); // Regla: Numérico
            $table->string('entrenador');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seleccions');
    }
};
