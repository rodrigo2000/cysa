<?php

class Productos extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'productos';
        $this->module['controller'] = 'Productos';
        $this->module['title'] = 'Productos No Conforme';
        $this->module['title_list'] = "Productos No Conforme";
        $this->module['title_new'] = "Nuevo Producto NO Conforme";
        $this->module['title_edit'] = "Editar Producto NO Conforme";
        $this->module['title_delete'] = "Eliminar Producto NO Conforme";
        $this->module["id_field"] = "idAuditoria";
        $this->module['tabla'] = "cat_" . $this->module['name'];
        $this->module['prefix'] = "prod";

        $this->_initialize();
    }

    function index() {
        $this->visualizar($this->module['name'] . "_view");
    }

    function nuevo($data = array(), $modal = FALSE) {
        $dbSAC = $this->{$this->module['controller'] . "_model"}->getDatabase("sac");
        $data = array(
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
        parent::nuevo($data);
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
        $dbSAC = $this->{$this->module['controller'] . "_model"}->getDatabase("sac");
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

}
