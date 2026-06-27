# Sistema de Gestión del Mundial de Fútbol - API REST con JWT

Este proyecto consiste en el desarrollo de un backend completo para la administración de usuarios, selecciones y partidos del Mundial de Fútbol, desarrollado para el examen práctico de **Desarrollo basado en plataformas**

## Características e Implementación
* **Autenticación Segura:** Implementada mediante JSON Web Tokens (JWT) utilizando el estándar `php-open-source-saver/jwt-auth`.
* **Control de Accesos por Roles:** El sistema restringe operaciones de mutación (POST, PUT, DELETE) exclusivamente al rol `ADMIN`, mientras que los usuarios con rol `CONSULTA` solo tienen permisos de lectura (GET), retornando un código `HTTP 403 Forbidden` ante accesos no autorizados.
* **Base de Datos:** Configurada de forma ligera y embebida utilizando **SQLite** y gestionada mediante migraciones nativas de Laravel.
* **Reglas Automatizadas:** Validación de unicidad de emails y selecciones, prohibición de partidos de un equipo contra sí mismo, control de fases/estados del torneo, y cálculo dinámico automatizado de la tabla de posiciones por grupo ordenado bajo criterios de la FIFA.

---

## 🛠️ Requisitos Previos
* PHP 8.2 o superior (Gestionado idealmente mediante **Laravel Herd**).
* Composer instalado globalmente.

---

## 💻 Instrucciones de Instalación y Ejecución

Sigue estos pasos detallados para desplegar el proyecto localmente:

1. **Clonar el proyecto o extraer el código fuente** en el directorio de trabajo de Laravel Herd.

2. **Instalar las dependencias de PHP** mediante Composer abriendo una terminal en la raíz del proyecto:
   ```bash
   composer install
    ```

3. Generar la clave de la aplicación:
    ```bash
    php artisan key:generate
    ```

4. Ejecutar las migraciones para estructurar las tablas en la base de datos SQLite:
    ```bash
    php artisan migrate:fresh
    ```

---

## Pruebas con Postman (Thunder client)

El proyecto incluye una colección de Postman preconfigurada para probar todos los endpoints requeridos:
* **Rutas Públicas:** ```POST /api/auth/register``` y ```POST /api/auth/login```.
* **Rutas Protegidas:** Todo el CRUD de Selecciones, Partidos, Filtrado por Fases y la Tabla de Posiciones por Grupo.

**Importante:** Al realizar el Login o Register, se debe copiar el token generado y configurarlo en Postman bajo la pestaña **Authorization** de tipo **Bearer** Token para poder consumir los endpoints protegidos.
