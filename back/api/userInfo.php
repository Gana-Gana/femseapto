<?php
require_once '../vendor/autoload.php';
require_once '../src/models/UsuarioModel.php';
require_once '../auth/verifyToken.php';
require_once '../config/config.php';
require_once '../config/cors.php';

function getUsuarioData() {
    $key = $_ENV['JWT_SECRET_KEY'];
    $token = $_COOKIE['auth_token'] ?? '';

    try {
        $userData = verifyJWTToken($token, $key);

        if ($userData) {
            $usuario = Usuario::obtenerPorId($userData->userId);

            if ($usuario) {
                $response = [
                    'success' => true,
                    'hola' => "HOLAAA",
                    'data' => [
                        'usuario' => [
                            'primerNombre' => $usuario->primerNombre,
                            'segundoNombre' => $usuario->segundoNombre,
                            'primerApellido' => $usuario->primerApellido,
                            'segundoApellido' => $usuario->segundoApellido,
                            'idTipoDocumento' => $usuario->idTipoDocumento,
                            'numeroDocumento' => $usuario->numeroDocumento
                        ]
                    ]
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Token no válido o expirado'
            ];
        }

        echo json_encode($response);

    } catch (Exception $e) {
        http_response_code(500);
        $response = [
            'success' => false,
            'message' => 'Error del servidor: ' . $e->getMessage()
        ];
        echo json_encode($response);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    getUsuarioData();
} else {
    http_response_code(405);
    $response = [
        'success' => false,
        'message' => 'Método no permitido'
    ];
    echo json_encode($response);
}
?>