<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Empleados_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = "dcont_empleado";
        $this->id_field = "idEmpleado";
        $this->table_prefix = "e";
        $this->model_name = __CLASS__;
    }

    function get_empleado($idEmpleado) {
        $return = FALSE;
        if (!empty($idEmpleado)) {
            $dbSAC = $this->getDatabase(APP_DATABASE_SAC);
            $result = $dbSAC->where($this->id_field, $idEmpleado)
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result->num_rows() == 1) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

}
