<?php

class Catalogos extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'catalogos';
        $this->module['controller'] = 'Catalogos';
        $this->module['title'] = 'Catalogos';
        $this->module['title_list'] = "Catálogos";

        $this->_initialize();
    }

    function index() {
        $this->visualizar($this->module['name'] . "_view");
    }

}
