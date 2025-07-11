<?php
require_once __DIR__ . '/../../config/config.php';

class SaldoCredito
{
    public $id;
    public $idUsuario;
    public $idLineaCredito;
    public $cuotaActual;
    public $cuotasTotales;
    public $valorSolicitado;
    public $cuotaQuincenal;
    public $valorPagado;
    public $valorSaldo;
    public $fechaCorte;
    public $creadoEl;
    public $actualizadoEl;

    public function __construct($id = null, $idUsuario = null, $idLineaCredito = null, $cuotaActual = null, $cuotasTotales = null, $valorSolicitado = null, $cuotaQuincenal = null, $valorPagado = null, $valorSaldo = null, $fechaCorte = null, $creadoEl = null, $actualizadoEl = null)
    {
        $this->id = $id;
        $this->idUsuario = $idUsuario;
        $this->idLineaCredito = $idLineaCredito;
        $this->cuotaActual = $cuotaActual;
        $this->cuotasTotales = $cuotasTotales;
        $this->valorSolicitado = $valorSolicitado;
        $this->cuotaQuincenal = $cuotaQuincenal;
        $this->valorPagado = $valorPagado;
        $this->valorSaldo = $valorSaldo;
        $this->fechaCorte = $fechaCorte;
        $this->creadoEl = $creadoEl;
        $this->actualizadoEl = $actualizadoEl;
    }

    public function guardar()
    {
        $db = getDB();
        $db->begin_transaction();
        try {
            $queryStr = $this->id === null ?
                "INSERT INTO saldo_creditos (id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, cuota_quincenal, valor_pagado, valor_saldo, fecha_corte) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)" :
                "UPDATE saldo_creditos SET id_linea_credito = ?, cuota_actual = ?, cuotas_totales = ?, valor_solicitado = ?, cuota_quincenal = ?, valor_pagado = ?, valor_saldo = ?, fecha_corte = ? WHERE id = ?";

            $stmt = $db->prepare($queryStr);

            if ($this->id === null) {
                $stmt->bind_param("iiiidddds", $this->idUsuario, $this->idLineaCredito, $this->cuotaActual, $this->cuotasTotales, $this->valorSolicitado, $this->cuotaQuincenal, $this->valorPagado, $this->valorSaldo, $this->fechaCorte);
            } else {
                $stmt->bind_param("iiiddddsi", $this->idLineaCredito, $this->cuotaActual, $this->cuotasTotales, $this->valorSolicitado, $this->cuotaQuincenal, $this->valorPagado, $this->valorSaldo, $this->fechaCorte, $this->id);
            }

            $stmt->execute();

            if ($stmt->error) {
                throw new Exception("Error en la consulta: " . $stmt->error);
            }

            if ($this->id === null) {
                $this->id = $stmt->insert_id;
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            error_log($e->getMessage());
            throw $e;
        } finally {
            $stmt->close();
            $db->close();
        }
    }

    public static function guardarEnLote($datos)
    {
        $db = getDB();
        $db->begin_transaction();
        try {
            $stmt = $db->prepare(
                "INSERT INTO saldo_creditos (id_usuario, id_linea_credito, cuota_actual, cuotas_totales, valor_solicitado, cuota_quincenal, valor_pagado, valor_saldo, fecha_corte) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) 
                ON DUPLICATE KEY UPDATE cuota_actual = VALUES(cuota_actual), cuotas_totales = VALUES(cuotas_totales), valor_solicitado = VALUES(valor_solicitado), cuota_quincenal = VALUES(cuota_quincenal), valor_pagado = VALUES(valor_pagado), valor_saldo = VALUES(valor_saldo), fecha_corte = VALUES(fecha_corte)"
            );

            foreach ($datos as $dato) {
                if (isset($dato['idUsuario'], $dato['idLineaCredito'], $dato['cuotaActual'], $dato['cuotasTotales'], $dato['valorSolicitado'], $dato['cuotaQuincenal'], $dato['valorPagado'], $dato['valorSaldo'], $dato['fechaCorte'])) {
                    $stmt->bind_param("iiiidddds", $dato['idUsuario'], $dato['idLineaCredito'], $dato['cuotaActual'], $dato['cuotasTotales'], $dato['valorSolicitado'], $dato['cuotaQuincenal'], $dato['valorPagado'], $dato['valorSaldo'], $dato['fechaCorte']);
                    $stmt->execute();

                    if ($stmt->error) {
                        throw new Exception("Error en la consulta: " . $stmt->error);
                    }
                } else {
                    error_log("Datos incompletos: " . json_encode($dato));
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            error_log($e->getMessage());
            throw $e;
        } finally {
            $stmt->close();
            $db->close();
        }
    }

    public static function obtenerPorId($id)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_linea_credito,
                cuota_actual,
                cuotas_totales,
                valor_solicitado,
                cuota_quincenal,
                valor_pagado,
                valor_saldo,
                fecha_corte,
                creado_el,
                actualizado_el
            FROM saldo_creditos
            WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt->bind_result($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $cuotaQuincenal, $valorPagado, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldoCredito = null;
        if ($stmt->fetch()) {
            $saldoCredito = new SaldoCredito($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $cuotaQuincenal, $valorPagado, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldoCredito;
    }

    public static function obtenerPorIdUsuario($idUsuario)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_linea_credito,
                cuota_actual,
                cuotas_totales,
                valor_solicitado,
                cuota_quincenal,
                valor_pagado,
                valor_saldo,
                DATE_FORMAT(fecha_corte, '%d/%m/%Y') as fecha_corte,
                DATE_FORMAT(creado_el, '%d/%m/%Y') AS creado_el,
                DATE_FORMAT(actualizado_el, '%d/%m/%Y') AS actualizado_el
            FROM saldo_creditos
            WHERE id_usuario = ?");
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $stmt->bind_result($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $cuotaQuincenal, $valorPagado, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldos = [];
        while ($stmt->fetch()) {
            $saldos[] = new SaldoCredito($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $cuotaQuincenal, $valorPagado, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldos;
    }

    public static function obtenerPorIdUsuarioYLineaCredito($idUsuario, $idLineaCredito)
    {
        $db = getDB();
        $stmt = $db->prepare(
            "SELECT
                id,
                id_usuario,
                id_linea_credito,
                cuota_actual,
                cuotas_totales,
                valor_solicitado,
                cuota_quincenal,
                valor_pagado,
                valor_saldo,
                fecha_corte,
                creado_el,
                actualizado_el
            FROM saldo_creditos
            WHERE id_usuario = ?
            AND id_linea_credito = ?");
        $stmt->bind_param("ii", $idUsuario, $idLineaCredito);
        $stmt->execute();
        $stmt->bind_result($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $cuotaQuincenal, $valorPagado, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);

        $saldoCredito = null;
        if ($stmt->fetch()) {
            $saldoCredito = new SaldoCredito($id, $idUsuario, $idLineaCredito, $cuotaActual, $cuotasTotales, $valorSolicitado, $cuotaQuincenal, $valorPagado, $valorSaldo, $fechaCorte, $creadoEl, $actualizadoEl);
        }

        $stmt->close();
        $db->close();

        return $saldoCredito;
    }

    public static function obtenerTodos()
    {
        $db = getDB();
        $result = $db->query(
            "SELECT
                id,
                id_usuario,
                id_linea_credito,
                cuota_actual,
                cuotas_totales,
                valor_solicitado,
                cuota_quincenal,
                valor_pagado,
                valor_saldo,
                fecha_corte,
                creado_el,
                actualizado_el
            FROM saldo_creditos");

        $saldos = [];
        while ($row = $result->fetch_assoc()) {
            $saldos[] = new SaldoCredito($row['id'], $row['id_usuario'], $row['id_linea_credito'], $row['cuota_actual'], $row['cuotas_totales'], $row['valor_solicitado'], $row['cuota_quincenal'], $row['valor_pagado'], $row['valor_saldo'], $row['fecha_corte'], $row['creado_el'], $row['actualizado_el']);
        }

        $db->close();

        return $saldos;
    }

    public function eliminar()
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM saldo_creditos WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();

        if ($stmt->error) {
            error_log("Error in SaldoCredito::eliminar - " . $stmt->error);
            throw new Exception("Database Error: " . $stmt->error);
        }

        $stmt->close();
        $db->close();
    }
}