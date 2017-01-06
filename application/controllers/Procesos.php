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

    function eliminar($id, $data = NULL) {
//	if ($this->isTipoCuenta(ADVANS_CUENTA_PROFESIONAL)) {
//	    redirect(base_url() . $this->module['controller']);
//	}
//	if ($this->isTipoCuenta(ADVANS_CUENTA_ADMINISTRADOR) && $id == 1) {
//	    $informacion = array('state' => 'warning', 'message' => 'El usuario <b>Administrador de Soluciones ADVANS</b> es imposible de desactivar');
//	    $this->session->set_flashdata("informacion", $informacion);
//	    redirect(base_url() . $this->module['controller']);
//	}

	$data = array(
	    "etiqueta" => "¿Esta seguro que desea eliminar esta cuenta?",
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
