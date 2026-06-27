<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SeleccionController;

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

// 2. Rutas del CRUD de Selecciones (Protegidas internamente por Auth y Roles)
Route::apiResource('selecciones', SeleccionController::class);