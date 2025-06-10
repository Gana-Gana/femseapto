<?php
require_once '../src/controllers/CargueArchivosAuxiliosController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idSolicitud = $_POST['id_solicitud'] ?? null;

    if (!$idSolicitud) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de solicitud requerido']);
        exit;
    }

    CargueArchivosAuxiliosController::subirArchivos($idSolicitud);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
?>