<?php

require_once '../src/controllers/SolicitudAuxilioController.php';
require_once '../auth/verifyToken.php';
require_once '../config/cors.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit();
}

$controlador = new SolicitudAuxilioController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;

    $idNuevo = $controlador->crear($data);

    if (!$idNuevo) {
        http_response_code(500);
        echo json_encode(['message' => 'Error al crear la solicitud']);
        exit();
    }

    $uploadFileDir = __DIR__ . '/../uploads/documentsAllowances/';
    
    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0777, true);
    }

    if (isset($_FILES['adjuntosAuxilio'])) {
            $fileNames   = (array) $_FILES['adjuntosAuxilio']['name'];
    $fileTmp     = (array) $_FILES['adjuntosAuxilio']['tmp_name'];
    $fileErrors  = (array) $_FILES['adjuntosAuxilio']['error'];

    $rutasGuardadas = [];
error_log(print_r($_FILES['adjuntosAuxilio'], true));
    foreach ($fileNames as $idx => $originalFileName) {
        // Saltamos ficheros con error
        if ($fileErrors[$idx] !== UPLOAD_ERR_OK) {
            continue;
        }

        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        // Ej.: 2699_15_1.pdf, 2699_15_2.png…
        $newFileName   = sprintf(
            '%s_%s_%d.%s',
            $data['idUsuario'],
            $idNuevo,
            $idx + 1,
            $fileExtension
        );

        $destPath = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmp[$idx], $destPath)) {
            $rutasGuardadas[] = 'uploads/documentsAllowances/' . $newFileName;
        }
    }

    if (!$rutasGuardadas) {           // si ningún archivo se movió
        http_response_code(400);
        echo json_encode(['message' => 'No se pudo mover ningún archivo']);
        exit();
    }

    // Pasamos **array** de rutas al controlador
    $data['adjuntosAuxilio'] = $rutasGuardadas;

            if ($controlador->actualizar($idNuevo, $data)) {
                http_response_code(201);
                echo json_encode([
            'id'      => $idNuevo,
            'message' => 'Solicitud creada con éxito y archivos guardados',
            'rutas'   => $rutasGuardadas,
            'files'  => $_FILES['adjuntosAuxilio']
        ]);
            
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al mover el archivo PDF']);
            exit();
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'No se subió ningún archivo o hubo un error en la subida', 'hola' => $_FILES]);
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id']) && isset($_GET['download']) && $_GET['download'] === 'pdf') {
        $id = $_GET['id'];
        $solicitud = $controlador->obtenerPorId($id);

        if ($solicitud && isset($solicitud->adjuntosAuxilio)) {
            $filepath = realpath(__DIR__ . '/../' . $solicitud->adjuntosAuxilio);

            error_log("Ruta del archivo: $filepath");

            if(file_exists($filepath)) {
                if (ob_get_length()) ob_end_clean();
                
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="'.basename($filepath) . '"');
                header('Content-Length: ' . filesize($filepath));
                header('Pragma: public');
                header('Cache-Control: public, must-revalidate, max-age=0');
                readfile($filepath);
                exit();
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Archivo PDF no encontrado en la ruta: ' . $filepath]);
            }          
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Solicitud no encontrada.']);
        }
    } elseif (isset($_GET['id'])) {
        $id = $_GET['id'];
        $resp = $controlador->obtenerPorId($id);
        header('Content-Type: application/json');
        echo json_encode($resp);
    } elseif (isset($_GET['idUsuario'])) {
        $idUsuario = $_GET['idUsuario'];
        $resp = $controlador->obtenerPorIdUsuario($idUsuario);
        header('Content-Type: application/json');
        echo json_encode($resp);
    } elseif (isset($_GET['startDate']) && isset($_GET['endDate'])) {
        $startDate = $_GET['startDate'];
        $endDate = $_GET['endDate'];

        error_log("Consultando solicitudes de crédito desde: $startDate 00:00:00 hasta: $endDate 23:59:59");

        $resp = $controlador->obtenerPorRangoDeFechas($startDate, $endDate);
        header('Content-Type: application/json');
        echo json_encode($resp);
    } else {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 10;
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        $date = isset($_GET['date']) ? $_GET['date'] : null;

        $resp = $controlador->obtenerConPaginacion($page, $size, $search, $date);
        header('Content-Type: application/json');
        echo json_encode($resp);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}