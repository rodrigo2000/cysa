<?php

class Reportes extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'reportes';
        $this->module['controller'] = 'Reportes';
        $this->module['title'] = 'Reportes';
        $this->module['title_list'] = "Reportes";

        $this->_initialize();
    }

    function index() {
        $this->visualizar($this->module['name'] . "_view");
    }

}
