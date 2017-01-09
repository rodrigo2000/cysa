<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Configuraciones_model extends MY_Model {

    function __construct() {
	parent::__construct();
	$this->table_name = "configuraciones";
	$this->id_field = "configuraciones_id";
	$this->table_prefix = "conf";
	$this->model_name = __CLASS__;
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);
    }

    function get_configuraciones_de_etapa($etapas_id) {
	$result = $this->db->where("configuraciones_etapas_id", $etapas_id)->get($this->table_name);
	$return = array();
	if ($result->num_rows() > 0) {
	    $return = $result->result_array();
	}
	return $return;
    }

}
