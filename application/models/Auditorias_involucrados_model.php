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

    function get_empleados_involucrados_en_auditoria($auditorias_id = NULL, $etapa = NULL, $tipo = NULL) {
        $return = array();
        if (!empty($auditorias_id)) {
            if (!is_null($etapa)) {
                switch ($etapa) {
                    case AUDITORIA_ETAPA_AP:
                        $this->db->where('auditorias_involucrados_asistio_en_AP IS NOT NULL');
                        break;
                    case AUDITORIA_ETAPA_REV1:
                        $this->db->where('auditorias_involucrados_asistio_en_rev1 IS NOT NULL');
                        break;
                    case AUDITORIA_ETAPA_REV2:
                        $this->db->where('auditorias_involucrados_asistio_en_rev2 IS NOT NULL');
                        break;
                    default:
                        break;
                }
            }
//            if (!empty($tipo)) {
//                $this->db->where('', $tipo);
//            }
            $result = $this->db
                    ->where($this->id_field, $auditorias_id)
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result && $result->num_rows() > 0) {
                $involucrados = $result->result_array();
                foreach ($involucrados as $index => $i) {
                    $e = $this->SAC_model->get_empleado($i['auditorias_involucrados_empleados_id']);
                    if (isset($e['cc_direcciones_id']) && $e['cc_direcciones_id'] != 5) {
                        $involucrados[$index] = array_merge($i, $e);
                        array_push($return, $involucrados[$index]);
                    }
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
