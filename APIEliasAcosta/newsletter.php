<?php
// Habilitar la captura de errores y excepciones para evitar respuestas HTML
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "PHP Error: $errstr en $errfile línea $errline"]);
    exit();
});

set_exception_handler(function ($exception) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Excepción no manejada: " . $exception->getMessage()]);
    exit();
});

// Manejo de CORS para permitir solicitudes desde cualquier origen
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    http_response_code(200);
    exit;
}

// Incluir el autoload de Composer y cargar variables de entorno
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Incluir el archivo de autenticación (auth.php)
require_once 'auth.php';

// Recuperar la clave secreta desde el archivo .env
$key = $_ENV['SECRET_KEY']; 

// Verificar si la clave se cargó correctamente
if (!$key) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Clave JWT_SECRET_KEY no encontrada. Verifica el archivo .env"]);
    exit();
}

// Instanciar la clase Authentication
$auth = new Authentication($key);

// Obtener el encabezado Authorization
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_X_AUTHORIZATION'] ?? apache_request_headers()['Authorization'] ?? null;

if (!$authHeader) {
    // Si no se proporciona el encabezado Authorization, responder con error 400
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "Falta el encabezado Authorization"]);
    exit();
}

// Verificar el token JWT
$arr = explode(" ", $authHeader);
$token = $arr[1] ?? null;

if (!$token) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(["message" => "Token no válido"]);
    exit();
}

$decoded = $auth->authenticateToken($token);

if ($decoded === null) {
    // Si el token no es válido o ha expirado, responder con error 401
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(["message" => "Acceso no autorizado. Token inválido o expirado."]);
    exit();
}

// Clase para manejar los datos de newsletters
class NewsletterAPI {
    private $db;

    public function __construct() {
        $this->db = new Conexion();
    }

    public function getNewsletters() {
        $sql = "SELECT * FROM newsletter";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $newsletters = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($newsletters) {
                header("HTTP/1.1 200 OK");
                echo json_encode($newsletters);
            } else {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["message" => "No newsletters found"]);
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => "Error al obtener los newsletters: " . $e->getMessage()]);
        }
    }
}

// Lógica para manejar las solicitudes
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $newsletterAPI = new NewsletterAPI();
    $newsletterAPI->getNewsletters();
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(["message" => "Método no permitido. Use GET"]);
}
?>
