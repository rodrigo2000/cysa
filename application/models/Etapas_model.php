<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Etapas_model extends MY_Model {

    function __construct() {
	parent::__construct();
	$this->table_name = "etapas";
	$this->id_field = "etapas_id";
	$this->table_prefix = "e";
	$this->model_name = __CLASS__;
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);
    }

    function get_etapas_del_proceso($procesos_id) {
	$result = $this->db->where("etapas_procesos_id", $procesos_id)->get($this->table_name);
	$return = array();
	if ($result->num_rows() > 0) {
	    $return = $result->result_array();
	}
	return $return;
    }

    function delete($id) {
	$configuraciones = $this->Configuraciones_model->get_configuraciones_de_etapa($id);
	if (count($configuraciones) > 0) {
	    foreach ($configuraciones as $c) {
		$this->Configuraciones_model->delete($c['configuraciones_id']);
	    }
	}
	return parent::delete($id);
    }

}
