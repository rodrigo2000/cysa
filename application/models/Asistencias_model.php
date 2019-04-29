<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Asistencias_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_documentos_id";
        $this->table_prefix = "m";
        $this->model_name = __CLASS__;
    }

    function get_asistencias($documentos_id = NULL, $incluir_eliminados = FALSE) {
        $return = array();
        if (!$incluir_eliminados) {
            $this->db->where("asistencias_tipo >", 0);
        }
        if (!empty($documentos_id)) {
            $result = $this->db
                    ->where($this->id_field, $documentos_id)
                    ->get($this->table_name);
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

}
