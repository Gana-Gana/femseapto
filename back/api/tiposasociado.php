<?php
require_once '../config/cors.php';
require_once '../src/controllers/TipoAsociadoController.php';

$controlador = new TipoAsociadoController();

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
        $resp = $controlador->obtenerPorId($id);
        if ($resp) {
            header('Content-Type: application/json');
            echo json_encode($resp);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Tipo de vinculación no encontrada."));
        }
    } else {
        $resp = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($resp);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>