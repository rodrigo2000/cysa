<?php

class Iso extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'iso';
        $this->module['controller'] = 'Iso';
        $this->module['title'] = 'ISO SGC';
        $this->module['title_list'] = "Documentos ISO";

        $this->_initialize();
    }

    function index() {
        $this->visualizar($this->module['name'] . "_view");
    }

}
