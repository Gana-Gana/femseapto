<?php

require_once __DIR__ . '/../models/TipoAuxilioModel.php';

class TipoAuxilioController {

    public function crear($datos) {
        $tipoAuxilio = new TipoAuxilio(
            null, // El id se genera automáticamente al guardar
            $datos['nombre']
        );

        $tipoAuxilio->guardar();
        
        return $tipoAuxilio->id;
    }

    public function actualizar($id, $datos) {
        $tipoAuxilio = TipoAuxilio::obtenerPorId($id);
        if (!$tipoAuxilio) {
            return false;
        }

        $tipoAuxilio->nombre = $datos['nombre'];

        $tipoAuxilio->guardar();

        return true;
    }

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

    public function eliminar($id) {
        $tipoAuxilio = TipoAuxilio::obtenerPorId($id);
        if (!$tipoAuxilio) {
            return false;
        }

        $tipoAuxilio->eliminar();

        return true;
    }
}
?>