<?php

class Documentos_tipos extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Catálogo de documentos";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo tipo de documento";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "d";
        $this->_initialize();
	}
}