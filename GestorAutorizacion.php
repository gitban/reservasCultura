<?php

class GestorAutorizacion {
    private $tabla = "reservas_entry";
    private $columna = "estado_autorizacion";

    public function cambiarEstado($id_reserva, $nuevo_estado) {
        $id = (int)$id_reserva;
        $nuevo = (int)$nuevo_estado; // 1: Autorizado, 2: Rechazado
        // 1. Consultar estado actual
        $sql = "SELECT $this->columna FROM $this->tabla WHERE id = $id LIMIT 1";
        $res = sql_query($sql);
        
        if ($res && sql_count($res) > 0) {
            $row = sql_row($res, 0);
            $estado_actual = (int)$row[0];

            // REGLA DE IRREVERSIBILIDAD: 
            // Si ya no es 0 (Recibido), significa que ya se tomó una decisión (1 o 2).
            if ($estado_actual !== 0) {
                return [
                    "status" => "error", 
                    "msg" => "Esta reserva ya fue procesada y no puede modificarse."
                ];
            }

            // 2. Aplicar el nuevo estado (solo si es 1 o 2)
            if ($nuevo === 1 || $nuevo === 2) {
                $sql_update = "UPDATE $this->tabla SET $this->columna = $nuevo WHERE id = $id";
                if (sql_command($sql_update) > 0) {
                    $txt = ($nuevo === 1) ? "AUTORIZADA" : "RECHAZADA";
                    return ["status" => "success", "msg" => "Reserva $txt con éxito."];
                }
            }
        }
        return ["status" => "error", "msg" => "Error al procesar la solicitud."];
    }
}