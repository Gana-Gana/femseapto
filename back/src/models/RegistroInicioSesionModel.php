<?php
require_once __DIR__ . '/../../config/config.php';

class RegistroInicioSesion {
    public $id;
    public $idUsuario;
    public $direccionIP;
    public $agenteUsuario;
    public $fechaHoraInicio;
    public $inicioExitoso;
    public $fechaHoraCierre;

    public function __construct($id = null, $idUsuario = null, $direccionIP = null,
        $agenteUsuario = null, $fechaHoraInicio = null,
        $inicioExitoso = null, $fechaHoraCierre = null
    ) {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->direccionIP = $direccionIP;
        $this->agenteUsuario = $agenteUsuario;
        $this->fechaHoraInicio = $fechaHoraInicio;
        $this->inicioExitoso = $inicioExitoso;
        $this->fechaHoraCierre = $fechaHoraCierre;
    }

    public function registrarInicioSesion() {
        $db = getDB();
        
        $query = $db->prepare("INSERT INTO registros_inicio_sesion (id_usuario, direccion_ip, agente_usuario, inicio_exitoso) VALUES (?, ?, ?, ?)");
        $query->bind_param("issi", $this->idUsuario, $this->direccionIP, $this->agenteUsuario, $this->inicioExitoso);
        $query->execute();
        $query->close();
        $db->close();
    }

    public function registrarCierreSesion() {
        $db = getDB();

        $subconsulta = $db->prepare("SELECT id FROM registros_inicio_sesion WHERE id_usuario = ? AND fecha_hora_cierre IS NULL ORDER BY id DESC LIMIT 1");
        $subconsulta->bind_param("i", $this->idUsuario);
        $subconsulta->execute();
        $subconsulta->bind_result($id);
        $subconsulta->fetch();
        $subconsulta->close();

        if ($id) {
            $query = $db->prepare("UPDATE registros_inicio_sesion SET fecha_hora_cierre = NOW() WHERE id = ?");
            $query->bind_param("i", $id);
            $query->execute();
            $query->close();
        }

        $db->close();
    }
}
?>