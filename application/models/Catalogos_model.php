<?php

class Catalogos_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table_name = "";
        $this->id_field = "";
        $this->table_prefix = "";
        $this->model_name = __CLASS__;
    }

}
