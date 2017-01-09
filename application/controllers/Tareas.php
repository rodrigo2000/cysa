<?php

class Tareas extends MY_Controller {

    function __construct() {
	parent::__construct();
	$this->module['name'] = strtolower(__CLASS__);
	$this->module['controller'] = __CLASS__;
	$this->module['title'] = 'Tareas';
	$this->module['title_list'] = "Catálogo de tareas";
	$this->module['title_new'] = "Nuevo tarea";
	$this->module['title_edit'] = "Editar tarea";
	$this->module['title_delete'] = "Eliminar tarea";
	$this->module["id_field"] = "tareas_id";
	$this->module['tabla'] = $this->module['name'] . "_aux";
	$this->module['prefix'] = "t";
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);

	$this->rulesForm = array(
	    array('field' => 'tareas_nombre', 'label' => 'Nombre del tarea', 'rules' => 'required|trim|max_length[100]',)
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

    function eliminar($id = NULL, $data = NULL) {
//	if ($this->isTipoCuenta(ADVANS_CUENTA_PROFESIONAL)) {
//	    redirect(base_url() . $this->module['controller']);
//	}
//	if ($this->isTipoCuenta(ADVANS_CUENTA_ADMINISTRADOR) && $id == 1) {
//	    $informacion = array('state' => 'warning', 'message' => 'El usuario <b>Administrador de Soluciones ADVANS</b> es imposible de desactivar');
//	    $this->session->set_flashdata("informacion", $informacion);
//	    redirect(base_url() . $this->module['controller']);
//	}

	$data = array(
	    "etiqueta" => "¿Esta seguro que desea eliminar esta tarea?",
	    "urlActionDelete" => $this->module['delete_url'],
	    "urlActionCancel" => $this->module['listado_url'],
	    "id" => $id
	);
	parent::eliminar($id, $data);
    }

    /*
     * Asigna al tarea indicado que sea el nuevo tareas vigente para la linea de tiempo
     */

    function nuevo_tarea_vigente($tareas_id) {
	if (!empty($tareas_id)) {
	    $tipoAuditoria = $this->db
			    ->where("tareas_id", $tareas_id)
			    ->limit(1)
			    ->get($this->module['tabla'])
			    ->row()->tareas_tipo_auditoria;
	    $this->db
		    ->set("tareas_vigente", 0)
		    ->where("tareas_tipo_auditoria", $tipoAuditoria)
		    ->where("tareas_vigente", 1)
		    ->limit(1)
		    ->update($this->module['tabla']);

	    $this->db
		    ->set("tareas_vigente", 1)
		    ->where("tareas_id", $tareas_id)
		    ->limit(1)
		    ->update($this->module['tabla']);
	    header("location:" . base_url() . $this->module['controller']);
	}
    }

}
