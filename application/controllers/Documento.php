<?php

class Documento extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Documento";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "s_id";
        $this->module['tabla'] = strtolower(__CLASS__) . "s";
        $this->module['prefix'] = "doc";
        $this->_initialize();
    }

    function _initialize() {
        return TRUE;
    }

    function index() {
        $documentos_id = $this->uri->segment(2);
        if (!empty($documentos_id)) {
            $documento = $this->Documentos_blob_model->get_uno($documentos_id);
            echo $documento['documentos_blob_contenido'];
        }
    }

    function antiguo_cysa($idDocto) {
        $aux = $this->Importar_model->get_html_de_documento($idDocto);
        if (!empty($aux) && isset($aux['contenido'])) {
            $html = $aux['contenido'];
            $html = str_replace('../vista/', 'http://SVRDCONT02/contraloria/CYSA/vista/', $html);
            $search = array('../../', '../');
            $html = str_replace($search, 'http://SVRDCONT02/contraloria/', $html);
            echo $html;
        }
    }

}
