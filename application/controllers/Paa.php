<?php

class Paa extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Programa Anual de Auditorías";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nueva auditoría";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = "auditorias_id";
        $this->module['tabla'] = "auditorias";
        $this->module['prefix'] = "a";
        $this->_initialize();
    }

}
