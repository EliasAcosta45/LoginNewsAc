<?php
// Habilitar la captura de errores y excepciones
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

// Manejo de CORS
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
require 'ConexionDB.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Clase para manejar los datos de usuarios
class User {
    private $db;

    public function __construct() {
        $this->db = new Conexion(); // Asumimos que la conexión a la base de datos es en la clase Conexion.
    }

    // Obtener todos los usuarios
    public function getUsers() {
        $sql = "SELECT IdUser, UserName, `Role` FROM user"; // Cambia 'users' por el nombre de tu tabla
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($users) {
                header("HTTP/1.1 200 OK");
                echo json_encode($users);
            } else {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["message" => "No se encontraron usuarios"]);
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["message" => "Error al obtener los usuarios: " . $e->getMessage()]);
        }
    }

    // Obtener un usuario por ID
    public function getUser($UserName) {
        $sql = "SELECT IdUser, UserName, `Role` FROM user WHERE UserName LIKE :UserName"; // Cambia 'user' por el nombre de tu tabla
        try {
            $searchTerm = '%' . $UserName . '%'; // Permitir coincidencias parciales
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':UserName', $searchTerm, PDO::PARAM_STR); // Cambiado a PARAM_STR
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Cambiado a fetchAll para obtener múltiples resultados
    
            if ($users) {
                echo json_encode($users);
            } else {
                echo json_encode(["message" => "Usuario no encontrado"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["message" => "Error al obtener el usuario: " . $e->getMessage()]);
        }
    }

    // Actualizar un usuario
    public function updateUser($idUser, $userName, $role) {
        // Construimos la consulta para actualizar solo UserName y Role
        $sql = "UPDATE user SET UserName = :userName, Role = :role WHERE IdUser = :idUser"; // Cambia 'users' por el nombre de tu tabla
    
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':userName', $userName);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["message" => "Usuario actualizado"]);
        } catch (PDOException $e) {
            echo json_encode(["message" => "Error al actualizar usuario: " . $e->getMessage()]);
        }
    }
    

    // Eliminar un usuario
    public function deleteUser($idUser) {
        $sql = "DELETE FROM user WHERE IdUser = :idUser"; // Cambia 'users' por el nombre de tu tabla
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(["message" => "Usuario eliminado"]);
        } catch (PDOException $e) {
            echo json_encode(["message" => "Error al eliminar usuario: " . $e->getMessage()]);
        }
    }
}

// Lógica para manejar las solicitudes
$method = $_SERVER['REQUEST_METHOD'];

$userAPI = new User();

switch ($method) {
    case 'GET':
        if (isset($_GET['UserName'])) {
            $userAPI->getUser($_GET['UserName']);
        } else {
            $userAPI->getUsers();
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $userAPI->updateUser($data->IdUser, $data->UserName, $data->Role,);
        break;

    case 'DELETE':
        $idUser = $_GET['idUser'] ?? null;
        if ($idUser) {
            $userAPI->deleteUser($idUser);
        } else {
            echo json_encode(["message" => "ID de usuario no proporcionado"]);
        }
        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Método no permitido. Use GET, POST, PUT, DELETE"]);
}
?>
