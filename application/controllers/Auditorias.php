<?php

class Auditorias extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'auditorias';
        $this->module['controller'] = 'Auditorias';
        $this->module['title'] = 'Auditorías';
        $this->module['title_list'] = "Mis auditorias";
        $this->module['title_new'] = "Nueva auditoría";
        $this->module['title_edit'] = "Editar auditoría";
        $this->module['title_delete'] = "Eliminar auditoría";
        $this->module["id_field"] = "idAuditoria";
        $this->module['tabla'] = "cat_" . $this->module['name'];
        $this->module['prefix'] = "au";

        $this->rulesForm = array(
            array('field' => 'clientes_nombre', 'label' => 'nombre del cliente', 'rules' => 'required|trim',),
            array('field' => 'clientes_rfc', 'label' => 'RFC', 'rules' => 'required|trim|min_length[10]|max_length[14]|callback_validaRFC', 'errors' => array('is_unique' => 'Este %s ya ha sido capturado.')),
            array('field' => 'clientes_email', 'label' => 'Correo electrónico', 'rules' => 'required|trim|max_length[200]')
        );
        $this->_initialize();
    }

    function index() {
        $data = array(
            'areas' => array('UNCAI', 'UNCJU', 'UNCTI', 'UCOAI', 'UCOJU', 'UCOTI', 'CMAI', 'CMCS', 'CMNJ', 'CMTI', 'CMJU', 'CMNP', 'CMSA'),
            'tipos' => array('AE', 'AP', 'CV', 'IC', 'SA', 'MO'),
            'anios' => range(date("Y"), 2007, -1),
            'estados' => array(),
            'direcciones' => $this->Catalogos_model->getDirecciones(),
        );
        $this->listado($data);
    }

//    function listado($data = array()) {
//        parent::listado($data);
//        $this->visualizar(NULL, $data);
//    }

    function nuevo($data = array(), $modal = FALSE) {
        $this->module['function'] = ucfirst(__FUNCTION__);
        $data = array(
            "tituloModulo" => $this->module['title_new'],
            "etiquetaBoton" => "Agregar",
            "urlAction" => $this->module['new_url'],
            'areas' => array('UNCAI', 'UNCJU', 'UNCTI', 'UCOAI', 'UCOJU', 'UCOTI', 'CMAI', 'CMCS', 'CMNJ', 'CMTI', 'CMJU', 'CMNP', 'CMSA'),
            'tipos' => array('AE', 'AP', 'CV', 'IC', 'SA', 'MO'),
            'anios' => array(date("Y") - 1, date("Y"), date("Y") + 1),
            'direcciones' => $this->Catalogos_model->getDirecciones(),
            'auditores' => $this->Catalogos_model->getAuditoresLider(33)
        );
        parent::nuevo($data);
    }

    function modificar($id = NULL, $data = array()) {
        $idEmpresa = $this->session->apps[ADVANS_NAMESPACE]['id_empresa_actual'];
        $data = array(
            "tituloModulo" => $this->module['title_edit'],
            "etiquetaBoton" => "Actualizar",
            "urlAction" => $this->module['edit_url'] . "/" . $id,
            "id" => $id,
            "idEmpresa" => $idEmpresa
        );
        parent::modificar($id, $data);
    }

    function eliminar($id = NULL, $data = array()) {
        $data = array(
            "etiqueta" => "¿Esta seguro que desea eliminar este cliente?",
            "urlActionDelete" => $this->module['delete_url'],
            "urlActionCancel" => $this->module['listado_url'],
            "id" => $id
        );
        parent::eliminar($id, $data);
    }

    function reporte_incidentes() {
        $data = array(
            "tituloModulo" => "Reporte de Incidentes",
            "etiquetaBoton" => "Agregar",
            "urlAction" => $this->module['new_url']
        );
        $this->visualizar($this->module['controller'] . "_reportes_view", $data);
    }

    function ajax_get_subdirecciones() {
        $return = array('success' => FALSE);
        $idDireccion = $this->input->post("idDireccion");
        $subdirecciones = $this->Catalogos_model->getSubdirecciones($idDireccion);
        if (count($subdirecciones) > 0) {
            $return['success'] = TRUE;
            $return['data'] = $subdirecciones;
        } else {
            $return['message'] = "No se encontraron subdirecciones para esta dirección";
        }
        echo json_encode($return);
    }

    function ajax_get_departamentos() {
        $return = array('success' => FALSE);
        $idDireccion = $this->input->post("idDireccion");
        $idSubdireccion = $this->input->post("idSubdireccion");
        $departamentos = $this->Catalogos_model->getDepartamentos($idDireccion, $idSubdireccion);
        if (count($departamentos) > 0) {
            $return['success'] = TRUE;
            $return['data'] = $departamentos;
        } else {
            $return['message'] = "No se encontraron departamentos para esta subdirección";
        }
        echo json_encode($return);
    }

    function listadoServerSide() {
        $aux = $this->input->post("order");
        $indexColumn = $aux[0]['column'];
        $orderColumn = $this->input->post("columns[" . $indexColumn . "][name]");
        $order = $aux[0]['dir'];
        $length = intval($this->input->post('length'));
        $start = intval($this->input->post('start'));
        $aux = $this->input->post("search");
        $searchValue = $aux['value'];
        $postVars = array(
            'idStatus' => $this->input->post("idStatus"),
            'idTipo' => $this->input->post("idTipo"),
            'idArea' => $this->input->post("idArea"),
            'anio' => $this->input->post("anio"),
            'clv_dir' => $this->input->post("clv_dir")
        );
        $postVars = array_map("intval", $postVars);
        $data = array(
            'draw' => $this->input->post('draw'),
            'recordsTotal' => $this->{$this->module['controller'] . "_model"}->record_count(),
            'recordsFiltered' => $this->{$this->module['controller'] . "_model"}->record_count_ajax($postVars),
            'data' => $this->{$this->module['controller'] . "_model"}->getResultadosAjax($orderColumn, $order, $start, $length, $searchValue, $postVars)
        );
        echo json_encode($data);
    }

}
