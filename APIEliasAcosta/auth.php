<?php

require_once 'conexionDB.php';
require __DIR__ . '/vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new Conexion();
$key = $_ENV['SECRET_KEY']; 
$auth = new Authentication($key); // Pasar la clave secreta al constructor



class Authentication {
    private $claveSecreta;

    public function __construct($claveSecreta) {
        $this->claveSecreta = $claveSecreta; // Asigna la clave secreta correctamente
    }

    /**
     * Función para generar el JWT.
     *
     * @param array $user Datos del usuario (deberías validar las credenciales antes).
     * @return string|null JWT generado o null si la autenticación falla.
     */
    public function generateToken($idPersona, $rol) {
        // Aquí deberías validar las credenciales del usuario
        // Por ejemplo, podrías hacer una consulta a la base de datos para verificar el usuario
        // Suponiendo que tienes un usuario válido, puedes proceder a generar el token

        // Ejemplo de datos de usuario (esto debería ser el resultado de una consulta)
        
            // Generar el payload para el JWT
            $payload = [
                "iat" => time(),
                "exp" => time() + (60 * 60), // 1 hora de validez
                "data" => [
                    "id" => $idPersona,
                    "role" => $rol
                ]
            ];

            // Generar el token JWT
            return JWT::encode($payload, $this->claveSecreta, 'HS256');
        }
        
    /**
     * Función para autenticar el JWT.
     *
     * @param string $tokenUser JWT a verificar.
     * @return object|null Datos decodificados o null si falla la autenticación.
     */
    public function authenticateToken($tokenUser) {
        try {
            // Decodificar el JWT
            $decoded = JWT::decode($tokenUser, new Key($this->claveSecreta, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            return null; // Si falla la autenticación, retorna null
        }
    }
}
?>
