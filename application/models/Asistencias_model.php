<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Asistencias_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_documentos_id";
        $this->table_prefix = "asis";
        $this->model_name = __CLASS__;
    }

    function get_asistencias($documentos_id = NULL, $incluir_eliminados = FALSE) {
        $return = array();
        if (!$incluir_eliminados) {
            $this->db->where("asistencias_tipo >", 0);
        }
        if (!empty($documentos_id)) {
            $result = $this->db->select($this->table_prefix . ".*")
                    ->where($this->id_field, $documentos_id)
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = " . $this->table_prefix . ".asistencias_empleados_id", "LEFT")
                    ->order_by("CASE
                    WHEN empleados_puestos_id IN (155) THEN 0
                    WHEN empleados_puestos_id IN (45, 290, 145, 294, 293) THEN 1
                    WHEN empleados_puestos_id IN (106, 157) THEN 2
                    WHEN empleados_puestos_id IN (59, 296, 60, 272) THEN 3
                    WHEN empleados_puestos_id IN (40, 269) THEN 4
                    ELSE 5 END ASC")
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result && $result->num_rows($result) > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_asistencias_de_documento($documentos_id = NULL, $incluir_eliminados = FALSE) {
        $return = array();
        if (!empty($documentos_id)) {
            $asistencias = $this->get_asistencias($documentos_id);
            foreach ($asistencias as $a) {
                $asistencias_tipo = intval($a['asistencias_tipo']);
                $empleado = $this->SAC_model->get_empleado($a['asistencias_empleados_id']);
                $direcciones_id = intval($empleado['cc_direcciones_id']);
                if (!isset($return[$direcciones_id])) {
                    $return[$direcciones_id] = array();
                }
                if (!isset($return[$direcciones_id][$asistencias_tipo])) {
                    $return[$direcciones_id][$asistencias_tipo] = array();
                }
                array_push($return[$direcciones_id][$asistencias_tipo], $empleado);
            }
        }
        return $return;
    }

    function insert_update($documentos_id = NULL, $empleados_id = NULL, $asistencias_tipo = NULL) {
        $return = FALSE;
        if (!empty($documentos_id) && !empty($empleados_id)) {
            $result = $this->db
                    ->where($this->id_field, $documentos_id)
                    ->where("asistencias_empleados_id", $empleados_id)
                    ->get($this->table_name);
            if ($result) {
                if ($result->num_rows() == 0) {
                    $data = array(
                        $this->id_field => $documentos_id,
                        'asistencias_empleados_id' => $empleados_id,
                        'asistencias_tipo' => $asistencias_tipo
                    );
                    $return = $this->db->insert($this->table_name, $data);
                } else {
                    $tipo_asistencia_actual = intval($result->row()->asistencias_tipo);
                    if ($tipo_asistencia_actual == 0 || $tipo_asistencia_actual != $asistencias_tipo || $asistencias_tipo = 0) {
                        $return = $this->db
                                ->set("asistencias_tipo", $asistencias_tipo)
                                ->where($this->id_field, $documentos_id)
                                ->where("asistencias_empleados_id", $empleados_id)
                                ->update($this->table_name);
                    } else {
                        $tipos = array(
                            TIPO_ASISTENCIA_INVOLUCRADO => 'involucrado', // 3
                            TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA => 'involucrado_contraloria', // 4
                            TIPO_ASISTENCIA_RESPONSABLE => 'responsable', // 1
                            TIPO_ASISTENCIA_TESTIGO => 'testigo' // 2
                        );
                        $return = array('success' => FALSE, 'message' => 'El usuario ya ha sido agregado como ' . $tipos[$tipo_asistencia_actual]);
                    }
                }
            }
        }
        return $return;
    }

    function insert_update_declaracion($auditorias_id = NULL, $empleados_id = NULL, $etapa = 1, $txt_declaracion = NULL) {
        $return = array();
        if (!empty($auditorias_id) && !empty($empleados_id)) {
            $data = array(
                'asistencias_comentarios_auditorias_id' => $auditorias_id,
                'asistencias_comentarios_empleados_id' => $empleados_id
            );
            switch ($etapa) {
                case 1:
                    $data['asistencias_comentarios_declaracion_ap'] = $txt_declaracion;
                    $data['asistencias_comentarios_ap_asistencias_tipo'] = TIPO_ASISTENCIA_INVOLUCRADO;
                    break;
                case 2:
                    $data['asistencias_comentarios_declaracion_rev1'] = $txt_declaracion;
                    $data['asistencias_comentarios_rev1_asistencias_tipo'] = TIPO_ASISTENCIA_INVOLUCRADO;
                    break;
                case 3:
                    $data['asistencias_comentarios_declaracion_rev1'] = $txt_declaracion;
                    $data['asistencias_comentarios_rev1_asistencias_tipo'] = TIPO_ASISTENCIA_INVOLUCRADO;
                    break;
                default:
                    break;
            }

            $this->Asistencias_model->insert($data);
        }
        return $return;
    }

}
