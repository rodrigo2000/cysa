<?php

class Ayuda extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'ayuda';
        $this->module['controller'] = 'Ayuda';
        $this->module['title'] = 'Ayuda';
        $this->module['title_list'] = "Documentación de Ayuda";

        $this->_initialize();
    }

    function index() {
        $this->visualizar($this->module['name'] . "_view");
    }

}
