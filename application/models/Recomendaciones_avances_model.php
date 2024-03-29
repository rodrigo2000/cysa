<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Recomendaciones_avances_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_recomendaciones_id";
        $this->table_prefix = "rec_ava";
        $this->model_name = __CLASS__;
    }

    function get_todos($limit = NULL, $start = NULL, $incluirEliminados = FALSE) {
        $this->db->select($this->table_prefix . ".*")
                ->join("recomendaciones_clasificaciones rc", "recomendaciones_clasificaciones_id = recomendaciones_avances_recomendaciones_clasificaciones_id", "INNER")->select("rc.recomendaciones_clasificaciones_nombre")
                ->join("recomendaciones_status rs", "recomendaciones_status_id = recomendaciones_avances_recomendaciones_status_id", "INNER")->select("rs.recomendaciones_status_nombre");
        $return = parent::get_todos($limit, $start, $incluirEliminados);
        foreach ($return as $index => $r) {
            if (!empty($r['recomendaciones_avances_empleados_id'])) {
                $return[$index]['empleado'] = $this->SAC_model->get_empleado($r['recomendaciones_avances_empleados_id']);
            }
        }
        return $return;
    }

    /**
     * Devuelve todos los avances de una recomendación
     * @param integer $recomendaciones_id Identificador de la recomendación
     * @param integer $numero_revision Número de revisión (1, 3, 5 ó 2, 4). Cero para todas las revisiones
     * @return array Arreglo con la información de los avances
     */
    function get_avances_de_recomendacion($recomendaciones_id, $numero_revision = NULL) {
        $return = array();
        if (empty($numero_revision)) {
            $result = $this->db->select_max("recomendaciones_avances_numero_revision", 'maximo')
                    ->where($this->id_field, $recomendaciones_id)
                    ->limit(1)
                    ->get($this->table_name);
            if ($result && $result->num_rows() == 1) {
                $numero_revision = $result->row()->maximo;
            }
        }

        if (!empty($recomendaciones_id)) {
            if (!empty($numero_revision)) {
                $this->db->where("recomendaciones_avances_numero_revision", $numero_revision);
            }
            $this->db->where($this->id_field, $recomendaciones_id);
            $return = $this->get_todos();
        }
        return $return;
    }

}
