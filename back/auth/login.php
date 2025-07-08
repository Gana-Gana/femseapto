<?php
require_once '../config/cors.php';
require_once '../vendor/autoload.php';
require_once '../src/models/UsuarioModel.php';
require_once '../src/controllers/RegistroInicioSesionController.php';
require_once '../config/config.php';
require_once 'verifyToken.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$key = $_ENV['JWT_SECRET_KEY'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $usuario = $data['usuario'] ?? '';
    $contrasenia = $data['contrasenia'] ?? '';

    $usuarioObj = Usuario::buscarPorUsuario($usuario);
    $registroController = new RegistroInicioSesionController();

    if ($usuarioObj && password_verify($contrasenia, $usuarioObj->contrasenia)) {
        if ($usuarioObj->activo) {
            $issuedAt = time();
            $expirationTime = $issuedAt + (24 * 60 * 60);
            $payload = [
                'iss' => 'localhost',
                'iat' => $issuedAt,    
                'exp' => $expirationTime,  
                'userId' => $usuarioObj->id,
                'id_rol' => $usuarioObj->id_rol,
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');
            setcookie('auth_token', $jwt, $expirationTime, '/', '', false, true);

            $registroController->registrarInicioSesion([
                'idUsuario' => $usuarioObj->id,
                'inicioExitoso' => 1
            ]);

            $response = [
                'success' => true,
                'message' => 'Login exitoso.',
                'token' => $jwt,
                'primer_ingreso' => $usuarioObj->primerIngreso
            ];
        } else {
            $registroController->registrarInicioSesion([
                'idUsuario' => $usuarioObj->id,
                'inicioExitoso' => 0
            ]);

            $response = [
                'success' => false,
                'message' => 'Cuenta inactiva. Contacte al administrador.'
            ];
        }
    } else {
        $registroController->registrarInicioSesion([
            'idUsuario' => $usuarioObj->id,
            'inicioExitoso' => 0
        ]);

        $response = [
            'success' => false,
            'message' => 'Usuario o contraseña incorrecta.'
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Método no permitido.'
    ];
}

echo json_encode($response);
?>
