<?php
require_once '../src/controllers/PersonaNaturalController.php';
require_once '../auth/verifyToken.php';
require_once '../config/cors.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

// $decodedToken = verifyJWTToken($token, $key);

// if ($decodedToken === null) {
//     http_response_code(401);
//     echo json_encode(array("message" => "Token no válido o no proporcionado."));
//     exit();
// }

$controlador = new PersonaNaturalController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idNuevaPersona = $controlador->crear($datos);
    echo json_encode(['id' => $idNuevaPersona]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idPersonaExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idPersonaExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $persona = $controlador->obtenerPorId($id);
        if ($persona) {
            header('Content-Type: application/json');
            echo json_encode($persona);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Persona no encontrada."));
        }
    } elseif (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $persona = $controlador->obtenerPorIdUsuario($id);
        if ($persona) {
            header('Content-Type: application/json');
            echo json_encode($persona);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Persona no encontrada."));
        }
    } elseif (isset($_GET['val'])) {
        $id = $_GET['val'];
        $infoFinanc = $controlador->validarPersonaNatural($id);
        if ($infoFinanc) {
            header('Content-Type: application/json');
            echo json_encode($infoFinanc);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID de persona no proporcionado."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>