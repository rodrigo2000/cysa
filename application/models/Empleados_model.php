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

    function get_empleado($empleados_id) {
        return $this->SAC_model->get_empleado($empleados_id);
    }

    function get_auditorias_de_empleado($empleados_id) {
        $return = array();
        if (!empty($empleados_id)) {
            $return = $this->Auditorias_model->get_auditorias_de_empleado($empleados_id);
        }
        return $return;
    }

}
