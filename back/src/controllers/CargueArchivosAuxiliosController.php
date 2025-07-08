<?php
require_once '../config/config.php';

class CargueArchivosAuxiliosController {
    public static function subirArchivos($idSolicitud) {
        $db = getDB();

        if (!isset($_FILES['archivos'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No se han enviado archivos.']);
            return;
        }

        $archivos = $_FILES['archivos'];
        $rutasGuardadas = [];

        $rutaDestino = "../uploads/documentsAllowances/" . $idSolicitud;
        if (!file_exists($rutaDestino)) {
            mkdir($rutaDestino, 0777, true);
        }

        $esMultiple = is_array($archivos['name']);

        if ($esMultiple) {
            for ($i = 0; $i < count($archivos['name']); $i++) {
                $nombreArchivo = basename($archivos['name'][$i]);
                $rutaArchivo = $rutaDestino . '/' . uniqid() . "_" . $nombreArchivo;

                if (move_uploaded_file($archivos['tmp_name'][$i], $rutaArchivo)) {
                    $rutasGuardadas[] = str_replace('../', '', $rutaArchivo);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al mover el archivo: ' . $nombreArchivo]);
                    return;
                }
            }
        } else {
            $nombreArchivo = basename($archivos['name']);
            $rutaArchivo = $rutaDestino . '/' . uniqid() . "_" . $nombreArchivo;

            if (move_uploaded_file($archivos['tmp_name'], $rutaArchivo)) {
                $rutasGuardadas[] = str_replace('../', '', $rutaArchivo);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al mover el archivo: ' . $nombreArchivo]);
                return;
            }
        }


        $rutasComoJson = json_encode($rutasGuardadas);


        $query = $db->prepare("INSERT INTO adjuntos_auxilios (id_solicitud, ruta_archivo) VALUES (?, ?)");
        $query->bind_param("is", $idSolicitud, $rutasComoJson);
        $query->execute();

        $query->close();
        $db->close();

        echo json_encode([
            'mensaje' => 'Archivos cargados correctamente',
            'rutas' => $rutasGuardadas
        ]);
    }
}
