<?php

class Procesos_model extends MY_Model {

    function __construct() {
	parent::__construct();
	$this->table_prefix = "p";
	$this->table_name = "procesos";
	$this->id_field = "procesos_id";
	$this->model_name = __CLASS__;
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);
    }

    function getResultados($limit, $start) {
	$this->db->order_by("procesos_nombre", "ASC");
	return parent::getResultados($limit, $start);
    }

    function delete($id) {
	$etapas = $this->Etapas_model->get_etapas_del_proceso($id);
	if (count($etapas) > 0) {
	    foreach ($etapas as $e) {
		$this->Etapas_model->delete($e['etapas_id']);
	    }
	}
	return parent::delete($id);
    }

}
