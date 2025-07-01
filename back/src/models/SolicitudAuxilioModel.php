<?php
require_once __DIR__ . '/../../config/config.php';

class SolicitudAuxilio{
    public $id;
    public $idUsuario;
    public $idTipoAuxilio;
    public $descripcion;
    public $fechaSolicitud;
    public $estado;
    public $observaciones;
    public $adjuntosAuxilio = [];


    public function __construct($id = null, $idUsuario = null, $idTipoAuxilio = null, $descripcion = null, $fechaSolicitud = null, $adjuntosAuxilio = []) {
        
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idTipoAuxilio = $idTipoAuxilio;
        $this->descripcion = $descripcion;
        $this->fechaSolicitud = $fechaSolicitud;
        null;
        null;
        $this->adjuntosAuxilio = $adjuntosAuxilio;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO solicitudes_auxilios (id_usuario, id_tipo_auxilio, descripcion) VALUES (?, ?, ?)");
            $query->bind_param("iis", $this->idUsuario, $this->idTipoAuxilio, $this->descripcion);
            $query->execute();
            $this->id = $query->insert_id;
            $query->close();
            
        } else {
            $query = $db->prepare("UPDATE solicitudes_auxilios SET id_tipo_auxilio = ?, descripcion = ?, fecha_solicitud = ?, estado = ?, observaciones = ? WHERE id = ?");
            $query->bind_param("issisi", $this->idTipoAuxilio, $this->descripcion, $this->fechaSolicitud, $this->estado, $this->observaciones, $this->id);
            $query->execute();
            $query->close();
        }

        if (!empty($this->adjuntosAuxilio)) {
        $insertAdjunto = $db->prepare("INSERT INTO adjuntos_auxilios (id_solicitud, ruta_archivo) VALUES (?, ?)");
        foreach ($this->adjuntosAuxilio as $ruta) {
            $insertAdjunto->bind_param("is", $this->id, $ruta);
            $insertAdjunto->execute();
        }
        $insertAdjunto->close();
    }
}

public function actualizarEstadoYObservaciones() {
        $db = getDB();

        $query = $db->prepare("UPDATE solicitudes_auxilios SET estado = ?, observaciones = ? WHERE id = ?");
        $query->bind_param("isi", $this->estado, $this->observaciones, $this->id);
        $query->execute();
        $query->close();

        $db->close();
    }

public static function obtenerPorId($id) {
    $db = getDB();
    $query = $db->prepare(
        "SELECT+
            id,
            id_usuario,
            id_tipo_auxilio,
            descripcion,
            CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud,
                estado,
                observaciones
        FROM solicitudes_auxilios
        WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $query->bind_result($id, $idUsuario, $idTipoAuxilio, $descripcion, $fechaSolicitud, $estado, $observaciones);
    $solicitud = null;
    if ($query->fetch()) {
        $query->close();

       
        $adjuntosQuery = $db->prepare("SELECT ruta_archivo FROM adjuntos_auxilios WHERE id_solicitud = ?");
        $adjuntosQuery->bind_param("i", $id);
        $adjuntosQuery->execute();
        $adjuntosResult = $adjuntosQuery->get_result();
        $adjuntos = [];
        while ($row = $adjuntosResult->fetch_assoc()) {
            $adjuntos[] = $row['ruta_archivo'];
        }
        $adjuntosQuery->close();

        $solicitud = new SolicitudAuxilio($id, $idUsuario, $idTipoAuxilio, $descripcion, $fechaSolicitud, $estado, $observaciones, $adjuntos);
    } else {
        $query->close();
    }

    $db->close();
    return $solicitud;
}


    public static function obtenerPorIdUsuario($idUsuario) {
    $db = getDB();
    $query = $db->prepare(
        "SELECT
            id,
            id_usuario,
            id_tipo_auxilio,
            descripcion,
            CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud,
                estado,
                observaciones
        FROM solicitudes_auxilios
        WHERE id_usuario = ?"
    );
    $query->bind_param("i", $idUsuario);
    $query->execute();
    $query->bind_result($id, $idUsuario, $idTipoAuxilio, $descripcion, $fechaSolicitud, $estado, $observaciones);
    
    $solAux = [];

    while ($query->fetch()) {
       
        $adjuntosQuery = $db->prepare("SELECT ruta_archivo FROM adjuntos_auxilios WHERE id_solicitud = ?");
        $adjuntosQuery->bind_param("i", $id);
        $adjuntosQuery->execute();
        $adjuntosResult = $adjuntosQuery->get_result();

        $adjuntos = [];
        while ($row = $adjuntosResult->fetch_assoc()) {
            $adjuntos[] = $row['ruta_archivo'];
        }
        $adjuntosQuery->close();

        
        $solAux[] = new SolicitudAuxilio($id, $idUsuario, $idTipoAuxilio, $descripcion, $fechaSolicitud, $estado, $observaciones, $adjuntos);
    }

    $query->close();
    $db->close();

    return $solAux;
}


    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT
                    id,
                    id_usuario,
                    id_tipo_auxilio,
                    descripcion,
                    fecha_solicitud,
                    estado,
                    observaciones
                FROM solicitudes_auxilios";
        $result = $db->query($query);
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $adjuntosQuery = $db->prepare("SELECT ruta_archivo FROM adjuntos_auxilios WHERE id_solicitud = ?");
            $adjuntosQuery->bind_param("i", $row['id']);
            $adjuntosQuery->execute();
            $adjuntosResult = $adjuntosQuery->get_result();
            $adjuntos = [];
            while ($a = $adjuntosResult->fetch_assoc()) {
                $adjuntos[] = $a['ruta_archivo'];
            }
            $adjuntosQuery->close();

            $solicitudes[] = new SolicitudAuxilio(
                $row['id'], 
                $row['id_usuario'], 
                $row['id_tipo_auxilio'], 
                $row['descripcion'], 
                $row['fecha_solicitud'], 
                $row['estado'], 
                $row['observaciones']
            );
        }
        $db->close();
        return $solicitudes;
    }

    public static function obtenerConPaginacion($page, $size, $search = null, $fechaSolicitud = null) {
        $db = getDB();
        $offset = ($page - 1) * $size;
    
        $baseQuery = "
        FROM solicitudes_auxilios sa
        INNER JOIN usuarios u ON sa.id_usuario = u.id
        INNER JOIN tipos_auxilios ta ON sa.id_tipo_auxilio = ta.id
    ";
    
        $selectQuery = "
        SELECT 
            sa.id,
            sa.id_usuario,
            sa.id_tipo_auxilio,
            sa.descripcion,
            fecha_solicitud,
            sa.estado,
            sa.observaciones,
            u.primer_nombre,
            u.segundo_nombre,
            u.primer_apellido,
            u.segundo_apellido,
            u.numero_documento,
            ta.nombre AS nombre_tipo_auxilio
    ";
    
    $countQuery = "SELECT COUNT(*) AS total";

    $whereConditions = [];
    $params = [];
    $types = "";

    if (!empty($search)) {
        $whereConditions[] = "(
            u.primer_nombre LIKE ?
            OR u.primer_apellido LIKE ?
            OR u.numero_documento LIKE ?
            OR ta.nombre LIKE ?
        )";
        $searchParam = "%" . $search . "%";
        $params = array_merge($params, array_fill(0, 4, $searchParam));
        $types .= str_repeat("s", 4);
    }

    
          if (!empty($fechaSolicitud)) {
        $fechaConvertida = DateTime::createFromFormat('d/m/Y', $fechaSolicitud);
        if ($fechaConvertida === false) {
            die('Formato de fecha inválido, debe ser DD/MM/YYYY.');
        }
        $fechaSQL = $fechaConvertida->format('Y-m-d');

        $whereConditions[] = "DATE(CONVERT_TZ(sa.fecha_solicitud, '+00:00', '-05:00')) = ?";
        $params[] = $fechaSQL;
        $types .= "s";
    }
    
        $whereClause = count($whereConditions) > 0 ? " WHERE " . implode(" AND ", $whereConditions) : "";

    $finalSelectQuery = $selectQuery . $baseQuery . $whereClause . " ORDER BY sa.fecha_solicitud DESC LIMIT ? OFFSET ?";
    $finalCountQuery = $countQuery . $baseQuery . $whereClause;

    $params[] = $size;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $db->prepare($finalSelectQuery);
    if ($stmt === false) {
        die('Error en la preparación de la consulta: ' . $db->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    if ($stmt->errno) {
        die('Error en la ejecución de la consulta: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $solicitudes = [];

    while ($row = $result->fetch_assoc()) {
        $adjuntosQuery = $db->prepare("SELECT ruta_archivo FROM adjuntos_auxilios WHERE id_solicitud = ?");
        $adjuntosQuery->bind_param("i", $row['id']);
        $adjuntosQuery->execute();
        $adjuntosResult = $adjuntosQuery->get_result();
        $adjuntos = [];
        while ($a = $adjuntosResult->fetch_assoc()) {
            $adjuntos[] = $a['ruta_archivo'];
        }
        $adjuntosQuery->close();

        $solicitudes[] = [
            'id' => $row['id'],
            'idUsuario' => $row['id_usuario'],
            'idTipoAuxilio' => $row['id_tipo_auxilio'],
            'descripcion' => $row['descripcion'],
            'fechaSolicitud' => $row['fecha_solicitud'],
            'estado' => $row['estado'],
            'observaciones' => $row['observaciones'],
            'numeroDocumento' => $row['numero_documento'],
            'nombreAsociado' => trim("{$row['primer_nombre']} {$row['segundo_nombre']} {$row['primer_apellido']} {$row['segundo_apellido']}"),
            'nombreTipoAuxilio' => $row['nombre_tipo_auxilio'],
            'adjuntos_auxilio' => $adjuntos
        ];
    }

    $countStmt = $db->prepare($finalCountQuery);
    if ($countStmt === false) {
        die('Error en la preparación de la consulta de conteo: ' . $db->error);
    }

    if (!empty($countParams = array_slice($params, 0, count($params) - 2))) {
        $countTypes = substr($types, 0, -2);
        $countStmt->bind_param($countTypes, ...$countParams);
    }

    $countStmt->execute();
    if ($countStmt->errno) {
        die('Error en la ejecución de la consulta de conteo: ' . $countStmt->error);
    }

    $countResult = $countStmt->get_result();
    $total = $countResult->fetch_assoc()['total'];

    $db->close();

    return [
        'data' => $solicitudes,
        'total' => $total
    ];
}

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM solicitudes_auxilios WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }

    public static function obtenerPorRangoDeFechas($startDate, $endDate) {
        $db = getDB();
        
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_tipo_auxilio,
                descripcion,
                CONVERT_TZ(fecha_solicitud, '+00:00', '-05:00') AS fecha_solicitud,
                estado,
                observaciones
            FROM solicitudes_auxilios
            WHERE fecha_solicitud
            BETWEEN ?
            AND ?");
        $query->bind_param("ss", $startDateTime, $endDateTime);
        $query->execute();
        $result = $query->get_result();
    
        $solicitudes = [];
        while ($row = $result->fetch_assoc()) {
            $solicitudes[] = new SolicitudAuxilio(
                $row['id'],
                $row['id_usuario'],
                $row['id_tipo_auxilio'],
                $row['descripcion'],
                $row['fecha_solicitud'],
                $row['estado'],
                $row['observaciones']
            );
        }
        error_log("Número de solicitudes encontradas: " . count($solicitudes));
    
        $query->close();
        $db->close();
        return $solicitudes;
    }
}