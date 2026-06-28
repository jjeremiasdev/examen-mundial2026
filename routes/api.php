<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SeleccionController;
use App\Http\Controllers\PartidoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Aquí es donde se registran las rutas de la API para la aplicación.
| Todas estas rutas serán cargadas automáticamente bajo el prefijo "/api".
|
*/

// 1. Grupo de rutas para la Autenticación (JWT)
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});

// 2. Ruta específica para filtrar partidos por fase (Debe ir ANTES del recurso para que no se confunda con un ID)
Route::get('partidos/fase/{fase}', [PartidoController::class, 'filtrarPorFase']);

// Ruta, específica para actualizar el resultado de un partido
Route::put('partidos/{id}/resultado', [PartidoController::class, 'update']);

// 3. Ruta específica para la Tabla de Posiciones por grupo
Route::get('grupos/{grupo}/tabla', [PartidoController::class, 'tablaPosiciones']);

// 4. Recursos CRUD completos (Manejan automáticamente GET, POST, PUT, DELETE)
Route::apiResource('selecciones', SeleccionController::class);
Route::apiResource('partidos', PartidoController::class);