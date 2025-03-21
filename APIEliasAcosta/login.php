<?php
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    http_response_code(200);
    exit;
}

// Incluir archivos necesarios
include 'ConexionDB.php';      // Incluye la conexión a la base de datos
require 'auth.php';        // Archivo de autenticación JWT
require 'C:\laragon\www\APIEliasAcosta\vendor\autoload.php'; // Autoload de Composer para JWT

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Cargar variables de entorno desde .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Inicialización de la conexión y autenticación
$pdo = new Conexion(); 
$auth = new Authentication($key);

$ruta = "/APIEliasAcosta/login.php";  // Ruta base para la autenticación

// Verificar si el método HTTP es POST para iniciar sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Controlar las rutas de la API
    if ($_SERVER['REQUEST_URI'] == "$ruta/auth") {
        authenticar($auth); // Autenticación del token
    } else {
        loginUser($pdo, $auth); // Iniciar sesión de usuario
    }
} else {
    // Si es una solicitud OPTIONS (preflight), responder con 200 OK
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        header("HTTP/1.1 200 OK");
        exit;
    }

    // Si el método no es POST ni OPTIONS, responder con un error 405 (Método no permitido)
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("message" => "Método no permitido. Use POST"));
}

// Función para el inicio de sesión de usuario
function loginUser($pdo, $auth)
{
    // Obtener los datos de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        try {
            // Verificar si el usuario existe
            $stmt = $pdo->prepare("SELECT idUser, username, password, Role 
                                   FROM user 
                                   WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                // Verificar la contraseña

                error_log("Role obtenido: " . $user['Role'], 3, "./logs.log");
                if (password_verify($password, $user['password'])) {
                    // Generar token JWT
                    $token = $auth->generateToken($user['idUser'], $user['Role']);

                    // Retornar el token al cliente
                    header("HTTP/1.1 200 OK");
                    echo json_encode(array(
                        "Token" => $token
                    ));
                } else {
                    // Si las credenciales son incorrectas
                    header("HTTP/1.1 401 Unauthorized");
                    echo json_encode(array("message" => "Credenciales incorrectas"));
                    exit;
                }
            } else {
                // Si el usuario no existe
                header("HTTP/1.1 401 Unauthorized");
                echo json_encode(array("message" => "Credenciales incorrectas"));
                exit;
            }
        } catch (PDOException $e) {
            // Error al ejecutar la consulta
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("message" => "Error al iniciar sesión: " . $e->getMessage()));
            exit;
        }
    } else {
        // Si no se proporcionan las credenciales necesarias
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("message" => "Debe proporcionar nombre de usuario y contraseña"));
        exit;
    }
}

// Función para autenticar el token
function authenticar($auth)
{
    // Procesar el JSON de la solicitud
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);
    $token = $data['Authorization'];

    if ($token == null) {
        // Si no se proporciona el token
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["message" => "No se proporcionó un token de autenticación"]);
        exit;
    } else {
        // Decodificar y autenticar el token
        $decodedToken = $auth->authenticateToken($token);
        if ($decodedToken == null) {
            // Si el token no es válido
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["message" => "Token no válido"]);
            exit;
        } else {
            // Si el token es válido
            header("HTTP/1.1 200 OK");
            echo json_encode(["message" => "Token correcto"]);
        }
    }
}
