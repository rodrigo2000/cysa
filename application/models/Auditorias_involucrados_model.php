<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Auditorias_involucrados_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_auditorias_id";
        $this->table_prefix = "ai";
        $this->model_name = __CLASS__;
    }

    function get_empleados_involucrados_en_auditoria($auditorias_id = NULL, $tipo = NULL) {
        $return = array();
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->where($this->id_field, $auditorias_id)
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
                foreach ($return as $index => $r) {
                    $e = $this->SAC_model->get_empleado($r['auditorias_involucrados_empleados_id']);
                    $return[$index] = array_merge($r, $e);
                }
            }
        }
        return $return;
    }

    function set_empleados_involucrados($auditorias_id = NULL, $empleados_id = NULL) {
        $return = FALSE;
        if (!empty($auditorias_id) && !empty($empleados_id)) {
            $emp = $empleados_id;
            if (is_numeric($empleados_id)) {
                $emp = array($empleados_id);
            }
            foreach ($emp as $e) {

            }
//            $return = TRUE;
        }
        return $return;
    }

}
