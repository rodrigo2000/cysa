<?php

class SAC extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->_initialize();
    }

    function get_direcciones() {
        $grupo = $this->input->post('grupo');
        $periodos_id = $this->input->post('periodos_id');
        $return = array(
            'success' => TRUE,
            'grupo' => $grupo,
            'data' => $this->SAC_model->get_direcciones_de_periodo($periodos_id)
        );
        header("Content-type: application/json");
        echo json_encode($return);
    }

    function get_subdirecciones() {
        $grupo = $this->input->post('grupo');
        $periodos_id = $this->input->post('periodos_id');
        $direcciones_id = $this->input->post('direcciones_id');
        $return = array(
            'success' => TRUE,
            'grupo' => $grupo,
            'data' => $this->SAC_model->get_subdirecciones_de_direccion($periodos_id, $direcciones_id),
            'empleados' => $this->SAC_model->get_empleados_de_cc2($periodos_id, $direcciones_id)
        );
        header("Content-type: application/json");
        echo json_encode($return);
    }

    function get_departamentos() {
        $grupo = $this->input->post('grupo');
        $periodos_id = $this->input->post('periodos_id');
        $direcciones_id = $this->input->post('direcciones_id');
        $subdirecciones_id = $this->input->post('subdirecciones_id');
        $return = array(
            'success' => TRUE,
            'grupo' => $grupo,
            'data' => $this->SAC_model->get_departamentos_de_subdireccion($periodos_id, $direcciones_id, $subdirecciones_id),
            'empleados' => $this->SAC_model->get_empleados_de_cc2($periodos_id, $direcciones_id, $subdirecciones_id)
        );
        header("Content-type: application/json");
        echo json_encode($return);
    }

    function get_empleados_de_departamento() {
        $grupo = $this->input->post('grupo');
        $periodos_id = $this->input->post('periodos_id');
        $direcciones_id = $this->input->post('direcciones_id');
        $subdirecciones_id = $this->input->post('subdirecciones_id');
        $departamentos_id = $this->input->post('departamentos_id');
        $return = array(
            'success' => TRUE,
            'grupo' => $grupo,
            'data' => $this->SAC_model->get_empleados_de_cc2($periodos_id, $direcciones_id, $subdirecciones_id, $departamentos_id)
        );
        header("Content-type: application/json");
        echo json_encode($return);
    }

}
