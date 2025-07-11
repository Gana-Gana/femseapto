<?php
require_once __DIR__ . '/../../config/config.php';

class TipoAuxilio {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare("SELECT id, nombre FROM tipos_auxilios WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id, $nombre);
        $tiposAux = null;
        if ($query->fetch()) {
            $tiposAux = new TipoAuxilio($id, $nombre);
        }
        $query->close();
        $db->close();
        return $tiposAux;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT id, nombre FROM tipos_auxilios";
        $result = $db->query($query);
        $tiposAux = [];
        while ($row = $result->fetch_assoc()) {
            $tiposAux[] = new TipoAuxilio($row['id'], $row['nombre']);
        }
        $db->close();
        return $tiposAux;
    }

public static function obtenerDisponibles(){
    $db = getDB();

    $query = $db->prepare("
        SELECT id, nombre 
        FROM tipos_auxilios
        WHERE
            (fecha_inicio IS NULL AND fecha_fin IS NULL)
            OR
            (fecha_inicio IS NOT NULL AND fecha_fin IS NOT NULL
             AND NOW() BETWEEN fecha_inicio AND fecha_fin)
    ");

    $query->execute();
    $result = $query->get_result();

    $tiposDisponibles = [];
    while ($row = $result->fetch_assoc()) {
        $tiposDisponibles[] = $row;
    }
    $db->close();
    return $tiposDisponibles;
}



}
?>