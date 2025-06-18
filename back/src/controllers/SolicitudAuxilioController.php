<?php

require_once __DIR__ . '/../models/SolicitudAuxilioModel.php';

class SolicitudAuxilioController {

    /**
     * Crea una nueva solicitud de Auxilio.
     * @param array $datos Datos de la solicitud de crédito a crear.
     * @return int|null ID de la solicitud de crédito creada.
     */
    public function crear($datos) {
        $solicitud = new SolicitudAuxilio(
            null,
            $datos['idUsuario'],
            $datos['idTipoAuxilio'],
            $datos['descripcion'],
            date('Y-m-d'),
            null,
            null,
            $datos['adjuntosAuxilio'] ?? []
        );

        $solicitud->guardar();
        
        return $solicitud->id;
    }

    /**
     * Actualiza una solicitud de auxilio existente.
     * @param int $id ID de la solicitud de auxilio.
     * @param array $datos Datos nuevos.
     * @return bool True si fue exitosa, false si falló o no existe.
     */
    public function actualizar($id, $datos) {
        $solicitud = SolicitudAuxilio::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->idTipoAuxilio = $datos['idTipoAuxilio'] ?? $solicitud->idTipoAuxilio;
        $solicitud->descripcion = $datos['descripcion'] ?? $solicitud->descripcion;
        $solicitud->fechaSolicitud = $datos['fechaSolicitud'] ?? date('Y-m-d');
        $solicitud->adjuntosAuxilio = $datos['adjuntosAuxilio'] ?? null;

        $solicitud->guardar();

        return true;
    }

    /**
     * Obtiene una solicitud por su ID.
     * @param int $id
     * @return SolicitudAuxilio|array
     */
    public function obtenerPorId($id) {
        $solicitud = SolicitudAuxilio::obtenerPorId($id);
        if ($solicitud) {
            return $solicitud;
        } else {
            http_response_code(404);
            return array("message" => "Solicitud de auxilio no encontrada.");
        }
    }

    /**
     * Obtiene las solicitudes de auxilio por usuario.
     * @param int $idUsuario
     * @return array
     */
    public function obtenerPorIdUsuario($idUsuario) {
        $solicitudes = SolicitudAuxilio::obtenerPorIdUsuario($idUsuario);
        if ($solicitudes) {
            return $solicitudes;
        } else {
            http_response_code(404);
            return array("message" => "Solicitudes de auxilio no encontradas.");
        }
    }

    /**
     * Obtiene todas las solicitudes de auxilio.
     * @return array
     */
    public function obtenerTodos() {
        $solicitudes = SolicitudAuxilio::obtenerTodos();
        if ($solicitudes) {
            return $solicitudes;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron solicitudes de auxilio.");
        }
    }

    /**
     * Obtiene solicitudes con paginación, búsqueda y filtro por fecha.
     * @param int $page Página actual.
     * @param int $size Tamaño de la página.
     * @param string|null $search Término de búsqueda.
     * @param string|null $fechaSolicitud Fecha de solicitud.
     * @return array
     */
    public function obtenerConPaginacion($page, $size, $search = null, $fechaSolicitud = null) {
        return SolicitudAuxilio::obtenerConPaginacion($page, $size, $search, $fechaSolicitud);
    }

    /**
     * Elimina una solicitud de auxilio por su ID.
     * @param int $id
     * @return bool
     */
    public function eliminar($id) {
        $solicitud = SolicitudAuxilio::obtenerPorId($id);
        if (!$solicitud) {
            return false;
        }

        $solicitud->eliminar();

        return true;
    }

    /**
     * Obtiene solicitudes entre dos fechas.
     * @param string $startDate Fecha inicial (formato YYYY-MM-DD).
     * @param string $endDate Fecha final (formato YYYY-MM-DD).
     * @return array
     */
    public function obtenerPorRangoDeFechas($startDate, $endDate) {
        return SolicitudAuxilio::obtenerPorRangoDeFechas($startDate, $endDate);
    }
}
?>