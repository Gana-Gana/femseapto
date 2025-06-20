<?php

require_once __DIR__ . '/../models/TipoAuxilioModel.php';

class TipoAuxilioController {

    public function obtenerPorId($id) {
        $tipoAuxilio = TipoAuxilio::obtenerPorId($id);
        if ($tipoAuxilio) {
            return $tipoAuxilio;
        } else {
            http_response_code(404);
            return array("message" => "Tipo de Auxilio no encontrado.");
        }
    }

    public function obtenerTodos() {
        $tiposAuxilio = TipoAuxilio::obtenerTodos();
        if ($tiposAuxilio) {
            return $tiposAuxilio;
        } else {
            http_response_code(404);
            return array("message" => "No se encontraron tipos de Auxilio.");
        }
    }

    public function obtenerDisponibles() {
        $tiposDisponibles = TipoAuxilio::obtenerDisponibles();
        if ($tiposDisponibles) {
            return $tiposDisponibles;
        } else {
            http_response_code(404);
            return array("message" => "No hay tipos de Auxilio disponibles en este momento.");
        }
    }
    
}
?>