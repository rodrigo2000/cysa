<?php

class CYSA extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
    }

    function agregar_dias() {
        $fecha = $this->input->post('fecha');
        $dias = $this->input->post('dias');
        $solo_habiles = $this->input->post('solo_habiles');
        $nueva_fecha = agregar_dias($fecha, $dias, $solo_habiles);
        $return = array(
            'success' => TRUE,
            'fecha' => $fecha,
            'nueva_fecha' => $nueva_fecha,
        );
        echo json_encode($return);
    }

}
