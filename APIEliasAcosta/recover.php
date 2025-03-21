<?php

// Manejo de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    http_response_code(200);
    exit();
}

require 'C:\laragon\www\APIEliasAcosta\vendor\autoload.php';
require 'conexionDB.php';
require 'JWT_Helper.php';
require 'auth.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new Conexion();
$key = $_ENV['SECRET_KEY'];
$auth = new Authentication($key);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['GMAIL_USER'];
    $mail->Password = $_ENV['GMAIL_PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Obtener el cuerpo de la solicitud
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? null;

    if (!$email) {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["message" => "Correo electrónico es requerido."]);
        exit();
    }

    // Buscar el ID del usuario en la base de datos usando el correo electrónico
    $stmt = $pdo->prepare("SELECT IdUser, role FROM user WHERE UserName = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "Usuario no encontrado."]);
        exit();
    }

    // Generar el JWT
    $jwt = $auth->generateToken($user['IdUser'], $user['role']);

    // Enviar el correo
    $mail->setFrom($_ENV['GMAIL_USER'], 'Soporte');
    $mail->addAddress($email);
    $mail->Subject = 'Solicitud de restablecimiento de contraseña';
    $mail->Body = "Para restablecer tu contraseña, haz clic en el siguiente enlace: 
    token = $jwt"; 

    $mail->send();

    header("HTTP/1.1 200 OK");
    echo json_encode(["success" => true, "message" => "Se ha enviado un enlace para restablecer la contraseña a tu correo."]);
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(["error" => "Error al enviar el correo: " . $mail->ErrorInfo]);
}
?>
