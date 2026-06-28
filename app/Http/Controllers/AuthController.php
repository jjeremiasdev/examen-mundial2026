<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     * Endpoint: POST /api/auth/register
     */
    public function register(Request $request)
    {
        // Validaciones requeridas por el documento
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Email único
            'password' => 'required|string|min:8', // Mínimo 8 caracteres
            'rol' => 'required|string|in:ADMIN,CONSULTA' // Roles válidos
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Creación del usuario (la contraseña se encripta automáticamente por el cast 'hashed' en el Modelo)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, 
            'rol' => $request->rol,
        ]);

        // Genera el token inmediatamente para el usuario registrado
        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Iniciar sesión y obtener el token JWT.
     * Endpoint: POST /api/auth/login
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Valida las credenciales contra la BDD
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Obtener los datos del usuario autenticado mediante el Token.
     * Endpoint: GET /api/auth/me
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Cerrar sesión (Invalidar el token).
     * Endpoint: POST /api/auth/logout
     */
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Sesion cerrada exitosamente']);
    }

    /**
     * Estructura auxiliar para responder con el token detallado.
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()
        ]);
    }
}