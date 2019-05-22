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

    /**
     * Devuelve el jefe inmediato del empleado
     * @param integer $periodos_id Identificador del período. El valor NULL considera el período actual.
     * @param integer $empleados_id Identificador del empleado. El valor NULL considera el empleado que tiene sesión iniciada
     * @return array Arreglo con la información del jefe del empleado
     */
    function get_jefe($periodos_id = NULL, $empleados_id = NULL) {
        $return = array();
        if (empty($periodos_id)) {
            $p = $this->SAC_model->get_ultimo_periodo();
            $periodos_id = intval($p['periodos_id']);
        }
        if (empty($empleados_id)) {
            $empleados_id = $this->session->userdata("empleados_id");
        }
        $return = $this->SAC_model->get_jefe_de_empleado($empleados_id);
        return $return;
    }

}
