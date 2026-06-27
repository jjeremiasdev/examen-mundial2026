<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; //  1. Importar la interfaz de JWT

class User extends Authenticatable implements JWTSubject //  2. Implementar la interfaz
{
    use HasFactory, Notifiable;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol', // 3. Asegurar que el rol ('ADMIN' o 'CONSULTA') sea asignable 
    ];

    /**
     * Los atributos que deben ocultarse para los arrays (como en las respuestas JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser casteados (convertidos de tipo).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel encripta automáticamente la contraseña aquí [cite: 14]
    ];

    /**
     * 4. Método requerido por JWTSubject.
     * Retorna el identificador clave del usuario (el ID).
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 5. Método requerido por JWTSubject.
     * Permite pasar datos personalizados dentro del token JWT si lo deseamos.
     */
    public function getJWTCustomClaims()
    {
        return [
            'rol' => $this->rol // Guarda el rol en el token para validar permisos fácilmente luego 
        ];
    }
}