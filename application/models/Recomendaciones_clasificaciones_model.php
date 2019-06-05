<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Recomendaciones_clasificaciones_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "rc";
        $this->model_name = __CLASS__;
    }

}
