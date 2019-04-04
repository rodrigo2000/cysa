<?php

class Auditoria extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Mis auditorias";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = "auditorias_id";
        $this->module['tabla'] = "auditorias";
        $this->module['prefix'] = "a";
        $this->_initialize();
    }

    function index($auditorias_id = NULL) {
        if (empty($auditorias_id) && isset($this->session->cysa['auditorias_id'])) {
            redirect('/Auditoria/' . $this->session->cysa['auditorias_id']);
        }
        parent::index();
    }

    function auditoria($auditoria) {
        $auditorias_id = $this->Auditoria_model->get_real_auditoria($auditoria);
        $auditoria = $this->Auditorias_model->get_auditoria($auditorias_id);
        $periodos_id = intval($auditoria['cc_periodos_id']);
        $direcciones_id = intval($auditoria['cc_direcciones_id']);
        $subdirecciones_id = intval($auditoria['cc_subdirecciones_id']);
        $data = array(
            'auditorias_id' => intval($auditorias_id),
            'auditoria' => $auditoria,
            'periodos' => $this->SAC_model->get_periodos(),
            'direcciones' => $this->SAC_model->get_direcciones_de_periodo($periodos_id),
            'subdirecciones' => $this->SAC_model->get_subdirecciones_de_direccion($periodos_id, $direcciones_id),
            'departamentos' => $this->SAC_model->get_departamentos_de_subdireccion($periodos_id, $direcciones_id, $subdirecciones_id),
        );
        if (!empty($auditorias_id)) {
            $this->{$this->module['controller'] . "_model"}->actualizar_session('auditorias_id', intval($auditorias_id));
        }
        $this->listado($data);
    }

    /**
     * Obtiene los documentos de una auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @param string|integer $documentos_tipos_id Identificador del tipo de auditoría o siglas del documento
     */
    function documento($documentos_tipos_id = NULL, $documentos_id = NULL) {
        $auditorias_id = $this->session->cysa['auditorias_id'];
        $auditoria = $this->Auditoria_model->get_auditoria($auditorias_id);
        $this->module['title_list'] = "Documentos";
        $data = array(
            'auditoria' => $auditoria,
            'registros' => array(), //$this->Auditoria_model->getResultados(NULL, NULL),
            'documentos' => $this->Auditoria_model->get_documentos(),
            'urlAction' => "#",
            'mis_auditorias_id' => $this->Auditoria_model->get_mis_auditorias(),
            'mis_auditorias_anio' => $this->Auditoria_model->get_anios_para_select(),
            'logotipos' => $this->Logotipos_model->get_todos(),
            'direcciones' => $this->SAC_model->get_direcciones_de_periodo($auditoria['auditorias_periodos_id']),
        );
        if (!is_numeric($documentos_tipos_id)) {
            $documentos_tipos_id = strtoupper($documentos_tipos_id);
            $documentos_tipos_id = $this->Documentos_tipos_model->parse_siglas($documentos_tipos_id);
            $documentos_tipos_id = intval($documentos_tipos_id);
        }
        switch ($documentos_tipos_id) {
            case 10:
                $d = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
                $data['documentos_auditoria'] = $d;
                $this->module['title_list'] = "Orden de Auditoría";
                $vista = "documentos/orden_auditoria";
                break;
            default:
                $vista = "auditoria/documentos_view";
                break;
        }
        $data['etiquetaBoton'] = "Guardar";
        $data['id'] = $auditorias_id;
        $data['accion'] = "nuevo";
        $nombre = $cargo = $tratamiento = 'SIN ESPECIFICAR';
        if (!empty($data['auditoria']['cc_empleados_id'])) {
            $cc_empleado = $this->SAC_model->get_empleado($data['auditoria']['cc_empleados_id']);
            $nombre = $cc_empleado['empleados_nombre_titulado_siglas'];
            $cargo = $cc_empleado['empleados_cargo'];
            $tratamiento = '';
        }
        $data['director_ua'] = array(
            'nombre' => $nombre,
            'cargo' => $cargo,
            'tratamiento' => $tratamiento
        );
        $this->visualizar($vista, $data);
    }

    function timeline($auditoria = NULL) {
        redirect("Timeline/" . $auditoria);
    }

    /**
     * Obtiene las auditorias de un empleado para el años especificado
     * @return string JSON con las auditorias
     */
    function get_mis_auditorias() {
        $return = array();
        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $anio = intval($this->input->post('auditorias_anio'));
            $this->{$this->module['controller'] . "_model"}->actualizar_session('auditorias_anio', $anio);
            $status_id = AUDITORIAS_STATUS_EN_PROCESO;
            if ($anio < 0) {
                $status_id = array(
                    AUDITORIAS_STATUS_FINALIZADA,
                    AUDITORIAS_STATUS_FINALIZADA_RESERVADA,
                    AUDITORIAS_STATUS_FINALIZADA_MANUAL
                );
                $anio = abs($anio);
            }
            $auditorias = $this->Auditoria_model->get_mis_auditorias($anio, NULL, $status_id);
            if (empty($auditorias)) {
                echo $this->db->last_query();
                die();
            }

            $tipo_auditoria_AP = array(1, 2, 3);
            $APs = $ICs = array();
            foreach ($auditorias as $a) {
                if (in_array($a['auditorias_tipo'], $tipo_auditoria_AP)) {
                    array_push($APs, $a);
                } else {
                    array_push($ICs, $a);
                }
            }
            $return = array(
                'auditorias_AP' => $APs,
                'auditorias_IC' => $ICs
            );
        }
        echo json_encode($return);
    }

    function nuevo_documento($auditorias_id, $documentos_tipos_id = NULL) {
        var_dump($auditorias_id, $documentos_tipos_id);
        die();
    }

    function asignar_enlace_designado() {
        $empleados_id = $this->input->post("empleados_id");
        $return = $this->Auditoria_model->asignar_enlace_designado(NULL, $empleados_id);
        $empleado = $this->SAC_model->get_empleado($empleados_id);
        $json = array(
            'success' => $return,
            'empleado' => $empleado
        );
        echo json_encode($json);
    }

    function set_empleados_involucrados() {
        $empleados_id = $this->input->post("empleados_id");
        $json = array(
            'success' => FALSE,
            'message' => 'No se actualizó ningún empleado'
        );
        if (!empty($empleados_id)) {
            $return = $this->Auditoria_model->set_empleados_involucrados(NULL, $empleados_id);
            if ($return) {
                $json['success'] = TRUE;
                $json['message'] = 'Empleados actualizados';
                $json['empleados'] = $empleados_id;
            }
        }
        echo json_encode($json);
    }

    function set_equipo_de_auditoria() {
        $empleados_id = $this->input->post("empleados_id");
        echo "<pre>" . print_r($empleados_id, true) . "</pre>";
    }

    function set_permisos_adicionales() {
        $empleados_id = $this->input->post("empleados_id");
        echo "<pre>" . print_r($empleados_id, true) . "</pre>";
    }

}
