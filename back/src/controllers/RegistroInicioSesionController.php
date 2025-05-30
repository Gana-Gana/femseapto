<?php

require_once __DIR__ . '/../models/RegistroInicioSesionModel.php';

class RegistroInicioSesionController {
    
    public function registrarInicioSesion($datos) {
        $direccionIP = $this->obtenerDireccionIP();
        $agenteUsuario = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

        $registroInicioSesion = new RegistroInicioSesion(
            null,
            $datos['idUsuario'],
            $direccionIP,
            $agenteUsuario,
            null,
            $datos['inicioExitoso'],
            null
        );

        $registroInicioSesion->registrarInicioSesion();
        
        return $registroInicioSesion->id;
    }

    public function registrarCierreSesion($idUsuario) {
        $registro = new RegistroInicioSesion();
        $registro->idUsuario = $idUsuario;
        $registro->registrarCierreSesion();
    }

    function obtenerDireccionIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
?>