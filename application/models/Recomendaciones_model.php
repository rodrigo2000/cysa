<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Recomendaciones_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_prefix = "rec";
        $this->table_name = "recomendaciones";
        $this->id_field = "recomendaciones_id";
        $this->model_name = __CLASS__;
    }

    function get_recomendacion($recomendaciones_id) {
        $return = array();
        if (!empty($recomendaciones_id)) {
            $return = $this->get_uno($recomendaciones_id);
        }
        return $return;
    }

    function get_recomendaciones($observaciones_id) {
        $return = array();
        if (!empty($observaciones_id)) {
            $this->db->where("recomendaciones_observaciones_id", $observaciones_id);
            $return = $this->get_todos();
        }
        return $return;
    }

}
