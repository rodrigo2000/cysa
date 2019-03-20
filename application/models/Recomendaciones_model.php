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

    function get_recomendacion($idRecomendacion) {

    }

    function get_recomendaciones($idObservacion) {
        $return = array();
        return $return;
    }

}
