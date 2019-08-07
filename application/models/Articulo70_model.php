<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Articulo70_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_auditorias_id";
        $this->table_prefix = "a70";
        $this->model_name = __CLASS__;
    }
}