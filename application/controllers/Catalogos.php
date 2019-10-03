<?php

class Catalogos extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Catálogos";
        $this->module['title_list'] = $this->module['title'];
        $this->is_catalogo = FALSE;
        $this->_initialize();
    }

    function index($vista = NULL, $data = array()) {
        $this->visualizar("catalogos_view");
    }

}
