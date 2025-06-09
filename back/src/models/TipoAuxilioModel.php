<?php
require_once __DIR__ . '/../../config/config.php';

class TipoAuxilio {
    public $id;
    public $nombre;

    public function __construct($id = null, $nombre = '') {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO tipos_auxilios (nombre) VALUES (?)");
            $query->bind_param("s", $this->nombre);
        } else {
            $query = $db->prepare("UPDATE tipos_auxilios SET nombre = ? WHERE id = ?");
            $query->bind_param("si", $this->nombre, $this->id);
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
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

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM tipos_auxilios WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>