<?php
require_once '../config/cors.php';
require_once '../src/controllers/InfoNucleoFamiliarController.php';
require_once '../auth/verifyToken.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit();
}

$controlador = new InformacionNucleoFamiliarController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idNuevo = $controlador->crear($datos);
    echo json_encode(['id' => $idNuevo]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $usuario = $controlador->obtenerPorId($id);
        if ($usuario) {
            header('Content-Type: application/json');
            echo json_encode($usuario);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } elseif (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $usuario = $controlador->obtenerPorIdUsuario($id);
        if ($usuario) {
            header('Content-Type: application/json');
            echo json_encode($usuario);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } elseif (isset($_GET['val'])) {
        $id = $_GET['val'];
        $infoFinanc = $controlador->validarInformacionNucleoFamiliar($id);
        if ($infoFinanc) {
            header('Content-Type: application/json');
            echo json_encode($infoFinanc);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información familiar no encontrada."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID no proporcionado."));
    }
} elseif($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $idEliminar = $_GET['id'];

    $eliminacionExitosa = $controlador->eliminar($idEliminar);
    echo json_encode(['success' => $eliminacionExitosa]);
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>