<?php

class Tareas_model extends MY_Model {

    function __construct() {
	parent::__construct();
	$this->table_prefix = "t";
	$this->table_name = "tareas_aux";
	$this->id_field = "tareas_id";
	$this->model_name = __CLASS__;
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);
    }

    function getResultados($limit, $start) {
	$this->db->order_by("tareas_nombre", "ASC");
	return parent::getResultados($limit, $start);
    }

}
