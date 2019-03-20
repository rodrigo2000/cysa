<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Paa_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = "auditorias";
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "a";
        $this->model_name = __CLASS__;
    }

}
