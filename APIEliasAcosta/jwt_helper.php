<?php
// Habilitar CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Si es una solicitud de preflight, terminamos aquí.
    http_response_code(200);
    exit();
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'C:\laragon\www\APIEliasAcosta\vendor\autoload.php';

class JWT_Helper {
    private static $key = 'secreto'; // Clave secreta para el JWT (cámbiala por una segura)

    public static function encode($payload) {
        return JWT::encode($payload, self::$key, 'HS256');
    }

    public static function decode($jwt) {
        return JWT::decode($jwt, new Key(self::$key, 'HS256')); // Usa `Key` para definir el algoritmo
    }
}
?>

