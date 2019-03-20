<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Observaciones_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_prefix = "o";
        $this->table_name = "observaciones";
        $this->id_field = "observaciones_id";
        $this->model_name = __CLASS__;
    }

    function getResultados($limit = NULL, $start = NULL) {
        $this->db->order_by("observaciones_numero", "ASC");
        return parent::getResultados($limit, $start);
    }

    function get_observacion($observaciones_id = NULL) {
        $return = array();
        return $return;
    }

    function get_observaciones($auditorias_id = NULL, $incluir_eliminadas = FALSE) {
        $return = array();
        if (!empty($auditorias_id)) {
            $real_auditoria_origen_id = $this->Auditorias_model->get_real_auditoria_origen($auditorias_id);
            if (!$incluir_eliminadas) {
                $this->db
                        ->group_start()
                        ->where("observaciones_is_eliminada !=", 1)
                        ->or_where("fecha_delete", NULL)
                        ->group_end();
            }
            $this->db->where("observaciones_auditorias_id", $real_auditoria_origen_id);
            $return = $this->getResultados();
        }
        return $return;
    }

    function get_recomendaciones_de_observacion($idObservacion) {
        $return = array();
        $this->load->model("Recomendaciones_model");
        $return = $this->Recomendaciones_model->get_recomendaciones($idObservacion);
        return $return;
    }

}
