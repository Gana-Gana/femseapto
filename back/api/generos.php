<?php
require_once '../config/cors.php';
require_once '../src/controllers/GeneroController.php';

$controlador = new GeneroController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $genero = $controlador->obtenerPorId($id);
        if ($genero) {
            header('Content-Type: application/json');
            echo json_encode($genero);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Género no encontrado."));
        }
    } else {
        $generos = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($generos);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>