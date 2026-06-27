<?php

namespace App\Http\Controllers;

use App\Models\Seleccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class SeleccionController extends BaseController
{
    public function __construct()
    {
        // 1. Obliga a que estén autenticados con JWT para cualquier endpoint de selecciones
        // 2. Aplica el middleware de roles (rol.auth) para proteger POST, PUT y DELETE
        $this->middleware('auth:api');
        $this->middleware('rol.auth')->only(['store', 'update', 'destroy']);
    }

    /**
     * Listar todas las selecciones.
     * Endpoint: GET /api/selecciones
     */
    public function index()
    {
        $selecciones = Seleccion::all();
        return response()->json($selecciones, 200);
    }

    /**
     * Obtener una selección específica por ID.
     * Endpoint: GET /api/selecciones/{id}
     */
    public function show($id)
    {
        $seleccion = Seleccion::find($id);

        if (!$seleccion) {
            return response()->json(['error' => 'Selección no encontrada'], 404);
        }

        return response()->json($seleccion, 200);
    }

    /**
     * Registrar una nueva selección (Solo ADMIN).
     * Endpoint: POST /api/selecciones
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:selecciones,nombre', // Validación: Único
            'continente' => 'required|string|max:255',
            'grupo' => 'required|string|max:2',
            'ranking_fifa' => 'required|integer|min:1', // Validación: Numérico positivo
            'entrenador' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $seleccion = Seleccion::create($request->all());

        return response()->json([
            'message' => 'Selección registrada exitosamente',
            'data' => $seleccion
        ], 201);
    }

    /**
     * Actualizar una selección existente (Solo ADMIN).
     * Endpoint: PUT /api/selecciones/{id}
     */
    public function update(Request $request, $id)
    {
        $seleccion = Seleccion::find($id);

        if (!$seleccion) {
            return response()->json(['error' => 'Selección no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:selecciones,nombre,' . $id, // Ignora el registro actual para permitir edición
            'continente' => 'required|string|max:255',
            'grupo' => 'required|string|max:2',
            'ranking_fifa' => 'required|integer|min:1',
            'entrenador' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $seleccion->update($request->all());

        return response()->json([
            'message' => 'Selección actualizada exitosamente',
            'data' => $seleccion
        ], 200);
    }

    /**
     * Eliminar una selección (Solo ADMIN).
     * Endpoint: DELETE /api/selecciones/{id}
     */
    public function destroy($id)
    {
        $seleccion = Seleccion::find($id);

        if (!$seleccion) {
            return response()->json(['error' => 'Selección no encontrada'], 404);
        }

        $seleccion->delete();

        return response()->json([
            'message' => 'Selección eliminada exitosamente'
        ], 200);
    }
}