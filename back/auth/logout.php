<?php
require_once '../config/cors.php';
require_once '../src/controllers/RegistroInicioSesionController.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (isset($_COOKIE['auth_token'])) {
    $jwt = $_COOKIE['auth_token'];
    $key = $_ENV['JWT_SECRET_KEY'];
    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userId = $decoded->userId ?? null;
        if ($userId) {
            $registroController = new RegistroInicioSesionController();
            $registroController->registrarCierreSesion($userId);
        }
    } catch (Exception $e) {
        error_log('Error al decodificar JWT en logout: ' . $e->getMessage());
    }
}

setcookie('auth_token', '', time() - (24 * 60 * 60), '/', '', false, true);

$response = [
    'success' => true,
    'message' => 'Sesión cerrada exitosamente.'
];

echo json_encode($response);
?>