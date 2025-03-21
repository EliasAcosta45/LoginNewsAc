<?php
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type");
    http_response_code(200);
    exit;
}

include 'ConexionDB.php';      
$pdo = new Conexion();

$ruta = "/APIEliasAcosta/register.php"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SERVER['REQUEST_URI'] == "$ruta") {
        registerUser($pdo); 
    } else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(array("message" => "Ruta no encontrada"));
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("message" => "Método no permitido. Use POST"));
}

// Función para registrar un usuario
function registerUser($pdo)
{
    // Obtener los datos de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        try {
            // Verificar si el usuario ya existe
            $stmt = $pdo->prepare("SELECT idUser FROM user WHERE UserName = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Si el usuario ya existe
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(array("message" => "El usuario ya existe"));
                exit;
            }

            // Encriptar la contraseña con bcrypt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insertar el nuevo usuario con rol 1 (Administrador por defecto)
            $stmt = $pdo->prepare("INSERT INTO user (UserName, Password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                // Usuario registrado correctamente
                header("HTTP/1.1 201 Created");
                echo json_encode(array("message" => "Usuario registrado correctamente. Debe iniciar sesión para obtener un token."));
            } else {
                // Error al registrar el usuario
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(array("message" => "Error al registrar el usuario"));
            }
        } catch (PDOException $e) {
            // Error en la base de datos
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("message" => "Error al registrar el usuario: " . $e->getMessage()));
            exit;
        }
    } else {
        // Si no se proporcionan los datos necesarios
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("message" => "Debe proporcionar nombre de usuario y contraseña"));
        exit;
    }
}
?>
