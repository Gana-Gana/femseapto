<?php
require_once '../config/config.php';

class PersonaNatural {
    public $id;
    public $idUsuario;
    public $idGenero;
    public $fechaExpDoc;
    public $idDeptoExpDoc;
    public $mpioExpDoc;
    public $fechaNacimiento;
    public $paisNacimiento;
    public $idDeptoNacimiento;
    public $mpioNacimiento;
    public $otroLugarNacimiento;
    public $idDeptoResidencia;
    public $mpioResidencia;
    public $idZonaResidencia;
    public $idTipoVivienda;
    public $estrato;
    public $direccionResidencia;
    public $antigVivienda;
    public $idEstadoCivil;
    public $cabezaFamilia;
    public $personasACargo;
    public $tieneHijos;
    public $numeroHijos;
    public $correoElectronico;
    public $telefono;
    public $celular;
    public $telefonoOficina;
    public $idNivelEducativo;
    public $profesion;
    public $ocupacionOficio;
    public $idEmpresaLabor;
    public $idTipoContrato;
    public $dependenciaEmpresa;
    public $cargoOcupa;
    public $jefeInmediato;
    public $antigEmpresa;
    public $mesesAntigEmpresa;
    public $mesSaleVacaciones;
    public $nombreEmergencia;
    public $numeroCedulaEmergencia;
    public $numeroCelularEmergencia;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = '',
        $idGenero = '',
        $fechaExpDoc = '',
        $idDeptoExpDoc = null,
        $mpioExpDoc = '',
        $fechaNacimiento = '',
        $paisNacimiento = '',
        $idDeptoNacimiento = null,
        $mpioNacimiento = null,
        $otroLugarNacimiento = null, 
        $idDeptoResidencia = null,
        $mpioResidencia = '',
        $idZonaResidencia = '', 
        $idTipoVivienda = '',
        $estrato = '',
        $direccionResidencia = '',
        $antigVivienda = '',
        $idEstadoCivil = '',
        $cabezaFamilia = '',
        $personasACargo = '',
        $tieneHijos = '',
        $numeroHijos = null,
        $correoElectronico = '',
        $telefono = null,
        $celular = '',
        $telefonoOficina ='',
        $idNivelEducativo = '',
        $profesion = '',
        $ocupacionOficio = '',
        $idEmpresaLabor = '',
        $idTipoContrato = '',
        $dependenciaEmpresa = '',
        $cargoOcupa = '',
        $jefeInmediato = '',
        $antigEmpresa = '',
        $mesesAntigEmpresa = '',
        $mesSaleVacaciones = '',
        $nombreEmergencia = '',
        $numeroCedulaEmergencia = '',
        $numeroCelularEmergencia = '',
        $creadoEl = '',
        $actualizadoEl = '') {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idGenero = $idGenero;
        $this->fechaExpDoc = $fechaExpDoc;
        $this->idDeptoExpDoc = $idDeptoExpDoc;
        $this->mpioExpDoc = $mpioExpDoc;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->paisNacimiento = $paisNacimiento;
        $this->idDeptoNacimiento = $idDeptoNacimiento;
        $this->mpioNacimiento = $mpioNacimiento;
        $this->otroLugarNacimiento = $otroLugarNacimiento;
        $this->idDeptoResidencia = $idDeptoResidencia;
        $this->mpioResidencia = $mpioResidencia;
        $this->idZonaResidencia = $idZonaResidencia;
        $this->idTipoVivienda = $idTipoVivienda;
        $this->estrato = $estrato;
        $this->direccionResidencia = $direccionResidencia;
        $this->antigVivienda = $antigVivienda;
        $this->idEstadoCivil = $idEstadoCivil;
        $this->cabezaFamilia = $cabezaFamilia;
        $this->personasACargo = $personasACargo;
        $this->tieneHijos = $tieneHijos;
        $this->numeroHijos = $numeroHijos;
        $this->correoElectronico = $correoElectronico;
        $this->telefono = $telefono;
        $this->celular = $celular;
        $this->telefonoOficina = $telefonoOficina;
        $this->idNivelEducativo = $idNivelEducativo;
        $this->profesion = $profesion;
        $this->ocupacionOficio = $ocupacionOficio;
        $this->idEmpresaLabor = $idEmpresaLabor;
        $this->idTipoContrato = $idTipoContrato;
        $this->dependenciaEmpresa = $dependenciaEmpresa;
        $this->cargoOcupa = $cargoOcupa;
        $this->jefeInmediato = $jefeInmediato;
        $this->antigEmpresa = $antigEmpresa;
        $this->mesesAntigEmpresa = $mesesAntigEmpresa;
        $this->mesSaleVacaciones = $mesSaleVacaciones;
        $this->nombreEmergencia = $nombreEmergencia;
        $this->numeroCedulaEmergencia = $numeroCedulaEmergencia;
        $this->numeroCelularEmergencia = $numeroCelularEmergencia;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar() {
        $db = getDB();
        if ($this->id === null) {
            $query = $db->prepare("INSERT INTO personas_naturales (id_usuario, id_genero, fecha_expedicion_doc, id_dpto_exp_doc, mpio_expedicion_doc, fecha_nacimiento, pais_nacimiento, id_dpto_nac, mpio_nacimiento, otro_lugar_nacimiento, id_dpto_residencia, mpio_residencia, id_zona_residencia, id_tipo_vivienda, estrato, direccion_residencia, antiguedad_vivienda, id_estado_civil, cabeza_familia, personas_a_cargo, tiene_hijos, numero_hijos, correo_electronico, telefono, celular, telefono_oficina, id_nivel_educativo, profesion, ocupacion_oficio, id_empresa_labor, id_tipo_contrato, dependencia_empresa, cargo_ocupa, jefe_inmediato, antiguedad_empresa, mes_sale_vacaciones, nombre_emergencia, numero_cedula_emergencia, numero_celular_emergencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $query->bind_param("iissssssssssiiissisisissssissiissssssss", $this->idUsuario, $this->idGenero,
                $this->fechaExpDoc, $this->idDeptoExpDoc, $this->mpioExpDoc, $this->fechaNacimiento,
                $this->paisNacimiento, $this->idDeptoNacimiento, $this->mpioNacimiento, $this->otroLugarNacimiento, $this->idDeptoResidencia, $this->mpioResidencia, $this->idZonaResidencia, $this->idTipoVivienda, $this->estrato,
                $this->direccionResidencia, $this->antigVivienda, $this->idEstadoCivil, $this->cabezaFamilia, $this->personasACargo, $this->tieneHijos, $this->numeroHijos, $this->correoElectronico, $this->telefono, $this->celular, $this->telefonoOficina, $this->idNivelEducativo, $this->profesion, $this->ocupacionOficio, $this->idEmpresaLabor, $this->idTipoContrato, $this->dependenciaEmpresa, $this->cargoOcupa, $this->jefeInmediato, $this->antigEmpresa, $this->mesSaleVacaciones, $this->nombreEmergencia,
                $this->numeroCedulaEmergencia, $this->numeroCelularEmergencia
            );
        } else {
            $query = $db->prepare("UPDATE personas_naturales SET id_genero = ?, fecha_expedicion_doc = ?, id_dpto_exp_doc = ?, mpio_expedicion_doc = ?, fecha_nacimiento = ?, pais_nacimiento = ?, id_dpto_nac = ?, mpio_nacimiento = ?, otro_lugar_nacimiento = ?, id_dpto_residencia = ?, mpio_residencia = ?, id_zona_residencia = ?, id_tipo_vivienda = ?, estrato = ?, direccion_residencia = ?, antiguedad_vivienda = ?, id_estado_civil = ?, cabeza_familia = ?, personas_a_cargo = ?, tiene_hijos = ?, numero_hijos = ?, correo_electronico = ?, telefono = ?, celular = ?, telefono_oficina = ?, id_nivel_educativo = ?, profesion = ?, ocupacion_oficio = ?, id_empresa_labor = ?, id_tipo_contrato = ?, dependencia_empresa = ?, cargo_ocupa = ?, jefe_inmediato = ?, antiguedad_empresa = ?, mes_sale_vacaciones = ?, nombre_emergencia = ?, numero_cedula_emergencia = ?, numero_celular_emergencia = ? WHERE id = ?");
            $query->bind_param("issssssssssiiissisisissssissiissssssssi", $this->idGenero,
            $this->fechaExpDoc, $this->idDeptoExpDoc, $this->mpioExpDoc, $this->fechaNacimiento,
            $this->paisNacimiento, $this->idDeptoNacimiento, $this->mpioNacimiento, $this->otroLugarNacimiento, $this->idDeptoResidencia, $this->mpioResidencia, $this->idZonaResidencia, $this->idTipoVivienda, $this->estrato,
            $this->direccionResidencia, $this->antigVivienda, $this->idEstadoCivil, $this->cabezaFamilia,  $this->personasACargo, $this->tieneHijos, $this->numeroHijos, $this->correoElectronico, $this->telefono, $this->celular, $this->telefonoOficina, $this->idNivelEducativo, $this->profesion, $this->ocupacionOficio, $this->idEmpresaLabor, $this->idTipoContrato, $this->dependenciaEmpresa, $this->cargoOcupa, $this->jefeInmediato, $this->antigEmpresa, $this->mesSaleVacaciones, $this->nombreEmergencia,
            $this->numeroCedulaEmergencia, $this->numeroCelularEmergencia, $this->id
            );
        }
        $query->execute();
        if ($this->id === null) {
            $this->id = $query->insert_id;
        }
        $query->close();
        $db->close();
    }

    public static function validarPersonaNatural($id) {
        $db = getDB();
        $query = $db->prepare("SELECT validarPersonaNaturalUsuario(?) AS isValid");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($isValid);
        $query->fetch();
        $query->close();
        $db->close();
        return (bool)$isValid;
    }

    public static function obtenerPorId($id) {
        $db = getDB();
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_genero,
                fecha_expedicion_doc,
                id_dpto_exp_doc,
                mpio_expedicion_doc,
                fecha_nacimiento,
                pais_nacimiento,
                id_dpto_nac,
                mpio_nacimiento,
                otro_lugar_nacimiento,
                id_dpto_residencia,
                mpio_residencia,
                id_zona_residencia,
                id_tipo_vivienda,
                estrato,
                direccion_residencia,
                antiguedad_vivienda,
                id_estado_civil,
                cabeza_familia,
                personas_a_cargo,
                tiene_hijos,
                numero_hijos,
                correo_electronico,
                telefono,
                celular,
                telefono_oficina,
                id_nivel_educativo,
                profesion,
                ocupacion_oficio,
                id_empresa_labor,
                id_tipo_contrato,
                dependencia_empresa,
                cargo_ocupa,
                jefe_inmediato,
                antiguedad_empresa,
                meses_antiguedad_empresa,
                mes_sale_vacaciones,
                nombre_emergencia,
                numero_cedula_emergencia,
                numero_celular_emergencia,
                creado_el,
                actualizado_el
            FROM personas_naturales
            WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($id,
        $idUsuario,
        $idGenero,
        $fechaExpDoc,
        $idDeptoExpDoc,
        $mpioExpDoc,
        $fechaNacimiento,
        $paisNacimiento,
        $idDeptoNacimiento,
        $mpioNacimiento,
        $otroLugarNacimiento,
        $idDeptoResidencia,
        $mpioResidencia,
        $idZonaResidencia,
        $idTipoVivienda,
        $estrato,
        $direccionResidencia,
        $antigVivienda,
        $idEstadoCivil,
        $cabezaFamilia,
        $personasACargo,
        $tieneHijos,
        $numeroHijos,
        $correoElectronico,
        $telefono,
        $celular,
        $telefonoOficina,
        $idNivelEducativo,
        $profesion,
        $ocupacionOficio,
        $idEmpresaLabor,
        $idTipoContrato,
        $dependenciaEmpresa,
        $cargoOcupa,
        $jefeInmediato,
        $antigEmpresa,
        $mesesAntigEmpresa,
        $mesSaleVacaciones,
        $nombreEmergencia,
        $numeroCedulaEmergencia,
        $numeroCelularEmergencia,
        $creadoEl,
        $actualizadoEl);
        $personasNaturales = null;
        if ($query->fetch()) {
            $personasNaturales = new PersonaNatural($id,
            $idUsuario,
            $idGenero,
            $fechaExpDoc,
            $idDeptoExpDoc,
            $mpioExpDoc,
            $fechaNacimiento,
            $paisNacimiento,
            $idDeptoNacimiento,
            $mpioNacimiento,
            $otroLugarNacimiento,
            $idDeptoResidencia,
            $mpioResidencia,
            $idZonaResidencia,
            $idTipoVivienda,
            $estrato,
            $direccionResidencia,
            $antigVivienda,
            $idEstadoCivil,
            $cabezaFamilia,
            $personasACargo,
            $tieneHijos,
            $numeroHijos,
            $correoElectronico,
            $telefono,
            $celular,
            $telefonoOficina,
            $idNivelEducativo,
            $profesion,
            $ocupacionOficio,
            $idEmpresaLabor,
            $idTipoContrato,
            $dependenciaEmpresa,
            $cargoOcupa,
            $jefeInmediato,
            $antigEmpresa,
            $mesesAntigEmpresa,
            $mesSaleVacaciones,
            $nombreEmergencia,
            $numeroCedulaEmergencia,
            $numeroCelularEmergencia,
            $creadoEl,
            $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $personasNaturales;
    }

    public static function obtenerPorIdUsuario($idUsuario) {
        $db = getDB();
        $query = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_genero,
                fecha_expedicion_doc,
                id_dpto_exp_doc,
                mpio_expedicion_doc,
                fecha_nacimiento,
                pais_nacimiento,
                id_dpto_nac,
                mpio_nacimiento,
                otro_lugar_nacimiento,
                id_dpto_residencia,
                mpio_residencia,
                id_zona_residencia,
                id_tipo_vivienda,
                estrato,
                direccion_residencia,
                antiguedad_vivienda,
                id_estado_civil,
                cabeza_familia,
                personas_a_cargo,
                tiene_hijos,
                numero_hijos,
                correo_electronico,
                telefono,
                celular,
                telefono_oficina,
                id_nivel_educativo,
                profesion,
                ocupacion_oficio,
                id_empresa_labor,
                id_tipo_contrato,
                dependencia_empresa,
                cargo_ocupa,
                jefe_inmediato,
                antiguedad_empresa,
                meses_antiguedad_empresa,
                mes_sale_vacaciones,
                nombre_emergencia,
                numero_cedula_emergencia,
                numero_celular_emergencia,
                creado_el,
                actualizado_el
            FROM personas_naturales
            WHERE id_usuario = ?");
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $query->bind_result($id,
        $idUsuario,
        $idGenero,
        $fechaExpDoc,
        $idDeptoExpDoc,
        $mpioExpDoc,
        $fechaNacimiento,
        $paisNacimiento,
        $idDeptoNacimiento,
        $mpioNacimiento,
        $otroLugarNacimiento,
        $idDeptoResidencia,
        $mpioResidencia,
        $idZonaResidencia,
        $idTipoVivienda,
        $estrato,
        $direccionResidencia,
        $antigVivienda,
        $idEstadoCivil,
        $cabezaFamilia,
        $personasACargo,
        $tieneHijos,
        $numeroHijos,
        $correoElectronico,
        $telefono,
        $celular,
        $telefonoOficina,
        $idNivelEducativo,
        $profesion,
        $ocupacionOficio,
        $idEmpresaLabor,
        $idTipoContrato,
        $dependenciaEmpresa,
        $cargoOcupa,
        $jefeInmediato,
        $antigEmpresa,
        $mesesAntigEmpresa,
        $mesSaleVacaciones,
        $nombreEmergencia,
        $numeroCedulaEmergencia,
        $numeroCelularEmergencia,
        $creadoEl,
        $actualizadoEl);
        $personasNaturales = null;
        if ($query->fetch()) {
            $personasNaturales = new PersonaNatural($id,
            $idUsuario,
            $idGenero,
            $fechaExpDoc,
            $idDeptoExpDoc,
            $mpioExpDoc,
            $fechaNacimiento,
            $paisNacimiento,
            $idDeptoNacimiento,
            $mpioNacimiento,
            $otroLugarNacimiento,
            $idDeptoResidencia,
            $mpioResidencia,
            $idZonaResidencia,
            $idTipoVivienda,
            $estrato,
            $direccionResidencia,
            $antigVivienda,
            $idEstadoCivil,
            $cabezaFamilia,
            $personasACargo,
            $tieneHijos,
            $numeroHijos,
            $correoElectronico,
            $telefono,
            $celular,
            $telefonoOficina,
            $idNivelEducativo,
            $profesion,
            $ocupacionOficio,
            $idEmpresaLabor,
            $idTipoContrato,
            $dependenciaEmpresa,
            $cargoOcupa,
            $jefeInmediato,
            $antigEmpresa,
            $mesesAntigEmpresa,
            $mesSaleVacaciones,
            $nombreEmergencia,
            $numeroCedulaEmergencia,
            $numeroCelularEmergencia,
            $creadoEl,
            $actualizadoEl);
        }
        $query->close();
        $db->close();
        return $personasNaturales;
    }

    public static function obtenerTodos() {
        $db = getDB();
        $query = "SELECT
                    id,
                    id_usuario,
                    id_genero,
                    fecha_expedicion_doc,
                    id_dpto_exp_doc,
                    mpio_expedicion_doc,
                    fecha_nacimiento,
                    pais_nacimiento,
                    id_dpto_nac,
                    mpio_nacimiento,
                    otro_lugar_nacimiento,
                    id_dpto_residencia,
                    mpio_residencia,
                    id_zona_residencia,
                    id_tipo_vivienda,
                    estrato,
                    direccion_residencia,
                    antiguedad_vivienda,
                    id_estado_civil,
                    cabeza_familia,
                    personas_a_cargo,
                    tiene_hijos,
                    numero_hijos,
                    correo_electronico,
                    telefono,
                    celular,
                    telefono_oficina,
                    id_nivel_educativo,
                    profesion,
                    ocupacion_oficio,
                    id_empresa_labor,
                    id_tipo_contrato,
                    dependencia_empresa,
                    cargo_ocupa,
                    jefe_inmediato,
                    antiguedad_empresa,
                    meses_antiguedad_empresa,
                    mes_sale_vacaciones,
                    nombre_emergencia,
                    numero_cedula_emergencia,
                    numero_celular_emergencia,
                    creado_el,
                    actualizado_el
                FROM personas_naturales";
        $result = $db->query($query);
        $personasNaturales = [];
        while ($row = $result->fetch_assoc()) {
            $personasNaturales[] = new PersonaNatural($row['id'],
            $row['id_usuario'],
            $row['id_genero'],
            $row['fecha_expedicion_doc'],
            $row['id_dpto_exp_doc'],
            $row['mpio_expedicion_doc'],
            $row['fecha_nacimiento'],
            $row['pais_nacimiento'],
            $row['id_dpto_nac'],
            $row['mpio_nacimiento'],
            $row['otro_lugar_nacimiento'],
            $row['id_dpto_residencia'],
            $row['mpio_residencia'],
            $row['id_zona_residencia'],
            $row['id_tipo_vivienda'],
            $row['estrato'],
            $row['direccion_residencia'],
            $row['antiguedad_vivienda'],
            $row['id_estado_civil'],
            $row['cabeza_familia'],
            $row['personas_a_cargo'],
            $row['tiene_hijos'],
            $row['numero_hijos'],
            $row['correo_electronico'],
            $row['telefono'],
            $row['celular'],
            $row['telefono_oficina'],
            $row['id_nivel_educativo'],
            $row['profesion'],
            $row['ocupacion_oficio'],
            $row['id_empresa_labor'],
            $row['id_tipo_contrato'],
            $row['dependencia_empresa'],
            $row['cargo_ocupa'],
            $row['jefe_inmediato'],
            $row['antiguedad_empresa'],
            $row['meses_antiguedad_empresa'],
            $row['mes_sale_vacaciones'],
            $row['nombre_emergencia'],
            $row['numero_cedula_emergencia'],
            $row['numero_celular_emergencia'],
            $row['creado_el'],
            $row['actualizado_el']);
        }
        $db->close();
        return $personasNaturales;
    }

    public function eliminar() {
        $db = getDB();
        if ($this->id !== null) {
            $query = $db->prepare("DELETE FROM personas_naturales WHERE id = ?");
            $query->bind_param("i", $this->id);
            $query->execute();
            $query->close();
        }
        $db->close();
    }
}
?>