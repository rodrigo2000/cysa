<?php

class Campos extends MY_Controller {

    function __construct() {
	parent::__construct();
	$this->module['name'] = strtolower(__CLASS__);
	$this->module['controller'] = __CLASS__;
	$this->module['title'] = 'Campos';
	$this->module['title_list'] = "Catálogo de campos";
	$this->module['title_new'] = "Nuevo campo";
	$this->module['title_edit'] = "Editar campo";
	$this->module['title_delete'] = "Eliminar campo";
	$this->module["id_field"] = "campos_id";
	$this->module['tabla'] = $this->module['name'];
	$this->module['prefix'] = "c";
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);

	$this->rulesForm = array(
	    array('field' => 'campos_nombre', 'label' => 'Nombre del campo', 'rules' => 'required|trim|max_length[100]'),
	    array('field' => 'campos_etiqueta', 'label' => 'Etiqueta del campo', 'rules' => 'required|trim|max_length[100]'),
	    array('field' => 'campos_funcion', 'label' => 'Función del campo', 'rules' => 'trim|max_length[100]')
	);

	$this->_initialize();
    }

    function nuevo($data = array(), $modal = FALSE) {
//        if ($this->isTipoCuenta(ADVANS_CUENTA_PROFESIONAL)) {
//            redirect(base_url() . $this->module['controller']);
//        }
	$data = array(
	    "tituloModulo" => $this->module['title_new'],
	    "etiquetaBoton" => "Agregar",
	    "urlAction" => $this->module['new_url']
	);

	if ($modal !== FALSE) {
	    $data["tituloModulo"] = "";
	    $data["ocultarBotones"] = TRUE;
	    parent::nuevoEnModal($data);
	} else {
	    parent::nuevo($data);
	}
    }

    function modificar($id = NULL, $data = array()) {
	$data = array(
	    "tituloModulo" => $this->module['title_edit'],
	    "etiquetaBoton" => "Actualizar",
	    "urlAction" => $this->module['edit_url'] . "/" . $id,
	    "id" => $id
	);
	parent::modificar($id, $data);
    }

    function eliminar($id = 0, $data = NULL) {
	$data = array(
	    "etiqueta" => "¿Esta seguro que desea eliminar este campo?",
	    "urlActionDelete" => $this->module['delete_url'],
	    "urlActionCancel" => $this->module['listado_url'],
	    "id" => $id
	);
	parent::eliminar($id, $data);
    }

}
