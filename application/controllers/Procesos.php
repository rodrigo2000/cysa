<?php

class Procesos extends MY_Controller {

    function __construct() {
	parent::__construct();
	$this->module['name'] = strtolower(__CLASS__);
	$this->module['controller'] = __CLASS__;
	$this->module['title'] = 'Procesos';
	$this->module['title_list'] = "Catálogo de procesos";
	$this->module['title_new'] = "Nuevo proceso";
	$this->module['title_edit'] = "Editar proceso";
	$this->module['title_delete'] = "Eliminar proceso";
	$this->module["id_field"] = "procesos_id";
	$this->module['tabla'] = $this->module['name'];
	$this->module['prefix'] = "p";
	$this->db = $this->getDatabase(APP_DATABASE_TIMELINE);

	$this->rulesForm = array(
	    array('field' => 'procesos_nombre', 'label' => 'Nombre del proceso', 'rules' => 'required|trim|max_length[100]',),
	    array('field' => 'procesos_descripcion', 'label' => 'Descripción', 'rules' => 'required|trim|max_length[500]', 'errors' => array('is_unique' => 'Este %s ya ha sido capturado.')),
	    array('field' => 'procesos_version_iso', 'label' => 'Versión ISO', 'rules' => 'required|trim|numeric'),
	    array('field' => 'procesos_tipo_auditoria', 'label' => 'Tipos de auditoría', 'rules' => 'required|trim')
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
	    "etiqueta" => "¿Esta seguro que desea eliminar este proceso?",
	    "urlActionDelete" => $this->module['delete_url'],
	    "urlActionCancel" => $this->module['listado_url'],
	    "id" => $id
	);
	parent::eliminar($id, $data);
    }

    /*
     * Asigna al proceso indicado que sea el nuevo procesos vigente para la linea de tiempo
     */

    function nuevo_proceso_vigente($procesos_id) {
	if (!empty($procesos_id)) {
	    $tipoAuditoria = $this->db
			    ->where("procesos_id", $procesos_id)
			    ->limit(1)
			    ->get($this->module['tabla'])
			    ->row()->procesos_tipo_auditoria;
	    $this->db
		    ->set("procesos_vigente", 0)
		    ->where("procesos_tipo_auditoria", $tipoAuditoria)
		    ->where("procesos_vigente", 1)
		    ->limit(1)
		    ->update($this->module['tabla']);

	    $this->db
		    ->set("procesos_vigente", 1)
		    ->where("procesos_id", $procesos_id)
		    ->limit(1)
		    ->update($this->module['tabla']);
	    header("location:" . base_url() . $this->module['controller']);
	}
    }

}
