<?php

require_once '../config/cors.php';
require_once '../src/controllers/TipoAuxilioController.php';

$controller = new TipoAuxilioController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        echo json_encode($controller->obtenerPorId($_GET['id']));
    } elseif (isset($_GET['disponibles'])) {
        echo json_encode($controller->obtenerDisponibles());
    } else {
        echo json_encode($controller->obtenerTodos());
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
}
?>