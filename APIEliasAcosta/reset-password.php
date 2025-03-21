<?php
// Habilitar CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require 'vendor/autoload.php';
require 'conexionDB.php'; // Asegúrate de que este archivo esté correctamente configurado
require 'JWT_Helper.php'; // Asegúrate de incluir tu helper
require 'auth.php'; // Asegúrate de incluir tu archivo de autenticación

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new Conexion();
$key = $_ENV['SECRET_KEY'];
$auth = new Authentication($key);

// Captura de errores y excepciones
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    header("HTTP/1.1
    500 Internal Server Error");
    echo json_encode(["error" => "PHP Error: $errstr en $errfile línea $errline"]);
    exit();
});

set_exception_handler(function ($exception) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Excepción no manejada: " . $exception->getMessage()]);
    exit();
});

// Obtener el token de la URL
$token = $_GET['token'] ?? null;

if (!$token) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "Token no proporcionado."]);
    exit();
}

try {
    // Decodificar el token
    $decoded = $auth->authenticateToken($token);
    
    if (!$decoded) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["message" => "Token inválido o expirado."]);
        exit();
    }

    // Obtener el ID del usuario del payload
    $userId = $decoded->data->id;

    // Manejar la solicitud de restablecimiento de contraseña
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $newPassword = $data['newPassword'] ?? null;

        if (!$newPassword) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Nueva contraseña es requerida."]);
            exit();
        }

        // Actualizar la contraseña en la base de datos
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE user SET Password = ? WHERE IdUser = ?"); // Cambiado a user y Password
        $stmt->execute([$hashedPassword, $userId]);

        header("HTTP/1.1 200 OK");
        echo json_encode(["message" => "Contraseña restablecida con éxito."]);
    } else {
        // Si no es una solicitud POST, devolver un mensaje de error
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Método no permitido. Usa POST para restablecer la contraseña."]);
    }
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error al procesar la solicitud: " . $e->getMessage()]);
}
?>
