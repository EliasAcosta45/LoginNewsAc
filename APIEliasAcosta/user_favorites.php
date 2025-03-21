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
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    http_response_code(200);
    exit;
} else {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    http_response_code(200);
}

// Incluir el autoload de Composer y cargar variables de entorno
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Incluir el archivo de autenticación (auth.php)
require_once 'auth.php';

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

// Clase para manejar los datos de user_favorites
class UserFavoritesAPI {
    private $db;

    public function __construct() {
        $this->db = new Conexion(); // Asumimos que la conexión a la base de datos es en la clase Conexion.
    }

    // Obtener todos los favoritos
    public function getFavorites() {
        $sql = "SELECT * FROM `favorites`";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($favorites) {
                header("HTTP/1.1 200 OK");
                echo json_encode($favorites);
            } else {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["message" => "No se encontraron favoritos"]);
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => "Error al obtener los favoritos: " . $e->getMessage()]);
        }
    }

    // Obtener un favorito por ID
    public function getFavorite($favNombre) {
        $sql = "SELECT * FROM `favorites` WHERE favNombre LIKE :favNombre"; 
        try {
            $searchTerm = '%' . $favNombre . '%';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':favNombre', $searchTerm, PDO::PARAM_STR); 
            $stmt->execute();
            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    
            if ($favorites) {
                echo json_encode($favorites);
            } else {
                echo json_encode(["message" => "No favorites found"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => "Error al obtener los favoritos: " . $e->getMessage()]);
        }
    }

    // Crear un nuevo favorito
    public function createFavorite($favNombre, $NewsletterId, $UserId) {
        $sql = "INSERT INTO user_favorites (favNombre, NewsletterId, UserId) 
                VALUES (:favNombre, :NewsletterId, :UserId)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':favNombre', $favNombre);
            $stmt->bindParam(':NewsletterId', $NewsletterId);
            $stmt->bindParam(':UserId', $UserId);
            $stmt->execute();
            echo json_encode(["message" => "Favorito creado exitosamente"]);
        } catch (PDOException $e) {
            echo json_encode(["message" => "Error al crear favorito: " . $e->getMessage()]);
        }
    }

    function updateFavorite() {
        $data = json_decode(file_get_contents("php://input"));
        $idFavorite = $data->idFavorite;  
        $favNombre = $data->favNombre;  
        
        $sql = "UPDATE user_favorites SET favNombre = :favNombre WHERE idFavorite = :idFavorite";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':favNombre', $favNombre);
            $stmt->bindParam(':idFavorite', $idFavorite, PDO::PARAM_INT);
            
            $stmt->execute();
            
            echo json_encode(["message" => "Favorite updated"]);
        } catch (PDOException $e) {
            echo json_encode(["message" => "Error updating favorite: " . $e->getMessage()]);
        }
    }
    
    

    // Eliminar un favorito
    public function deleteFavorite($idFavorite) {
        $sql = "DELETE FROM user_favorites WHERE idFavorite = :idFavorite";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idFavorite', $idFavorite, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["message" => "Favorito eliminado"]);
        } catch (PDOException $e) {
            echo json_encode(["message" => "Error al eliminar favorito: " . $e->getMessage()]);
        }
    }
}

// Lógica para manejar las solicitudes
$method = $_SERVER['REQUEST_METHOD'];

$userFavoritesAPI = new UserFavoritesAPI();

switch ($method) {
    case 'GET':
        if (isset($_GET['favNombre'])) {
            $userFavoritesAPI->getFavorite($_GET['favNombre']);
        } else {
            $userFavoritesAPI->getFavorites();
        }
        break;
    

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $userFavoritesAPI->createFavorite($data->favNombre, $data->NewsletterId, $data->UserId);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $userFavoritesAPI->updateFavorite();
        break;

    case 'DELETE':
        $idFavorite = $_GET['idFavorite'] ?? null;
        if ($idFavorite) {
            $userFavoritesAPI->deleteFavorite($idFavorite);
        } else {
            echo json_encode(["message" => "ID de favorito no proporcionado"]);
        }
        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Método no permitido. Use GET, POST, PUT, DELETE"]);
}
?>
