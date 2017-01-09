<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Campos_model extends MY_Model {

    function __construct() {
	parent::__construct();
	$this->table_name = "campos";
	$this->id_field = "campos_id";
	$this->table_prefix = "c";
	$this->model_name = __CLASS__;
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);
    }

}
