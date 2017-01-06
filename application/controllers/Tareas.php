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

    function ajax_get_areas($idSubdireccion = NULL) {
	$return = array('success' => FALSE);
	if (empty($idSubdireccion)) {
	    $idSubdireccion = $this->input->post("idSubdireccion");
	}
	$res = $this->{$this->module['controller'] . "_model"}->get_areas("sac");
	if (count($res) > 0) {
	    $return = array(
		'success' => TRUE,
		'data' => $res
	    );
	}

	echo json_encode($return);
    }

    function ajax_get_motivos_incumplimiento($idMotivo = NULL) {
	$return = array('success' => FALSE);
	if (empty($idMotivo)) {
	    $idMotivo = $this->input->post("idMotivoIncumplimiento");
	}
	$res = $this->{$this->module['controller'] . "_model"}->get_motivos($idMotivo);
	if (count($res) > 0) {
	    $return = array(
		'success' => TRUE,
		'data' => $res
	    );
	}

	echo json_encode($return);
    }

    function cargar_producto_no_conforme() {
	$dbSAC = $this->{$this->module['controller'] . "_model"}->getDatabase(APP_DATABASE_SAC);
	$data = array(
	    'modal' => TRUE,
	    "tituloModulo" => $this->module['title_new'],
	    "etiquetaBoton" => "Agregar",
	    "urlAction" => $this->module['new_url'],
	    "auditorias" => $this->{$this->module['controller'] . "_model"}->get_auditorias(0),
	    "etapas" => $this->db->order_by("id_Proceso", "ASC")->get("cat_etapa_auditoria")->result_array(),
	    "subdirecciones" => $dbSAC->select("DISTINCT (denSubdireccion), clv_subdir")->join("ayunta_subdireccion a", "idSubdireccion = clv_subdir", "LEFT")->where("a.clv_dir", 5)->where("activo", 1)->order_by("clv_subdir", "ASC")->get("dcont_areas_auditoria c")->result_array(),
	    "areas" => $this->{$this->module['controller'] . "_model"}->get_areas("sac"),
	    "motivosIncumplimiento" => $this->{$this->module['controller'] . "_model"}->get_motivos("formato"),
	    "acciones" => $this->db->get("cat_acciones_pnc")->result_array(),
	    "responsablesAutorizan" => $this->{$this->module['controller'] . "_model"}->get_responsables(),
	    "documentos" => $this->{$this->module['controller'] . "_model"}->get_documentos()
	);
	$this->load->view("productos_nuevo_view", $data);
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
