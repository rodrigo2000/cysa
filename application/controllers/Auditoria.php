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

    function _initialize() {
        $url = base_url() . $this->module['controller'];
        $this->module['autorizar_url'] = $url . "/autorizar";
        $this->module['desautorizar_url'] = $url . "/desautorizar";
        $this->module['documentos_url'] = $url . "/documento";
        parent::_initialize();
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
    function documento($documentos_tipos_id = NULL, $documentos_id = NULL, $para_direcciones_id = NULL) {
        $auditorias_id = $this->session->cysa['auditorias_id'];
        $auditoria = $this->Auditoria_model->get_auditoria($auditorias_id);
        $this->module['title_list'] = "Documentos";
        if (!is_numeric($documentos_tipos_id)) {
            $documentos_tipos_id = strtoupper($documentos_tipos_id);
            $documentos_tipos_id = $this->Documentos_tipos_model->parse_siglas($documentos_tipos_id);
            $documentos_tipos_id = intval($documentos_tipos_id);
        }
        $titular = $this->SAC_model->get_director_de_ua(APP_DIRECCION_CONTRALORIA, $auditoria['auditorias_periodos_id']);
        $de_empleados_id = $titular['empleados_id'];
        $accion = "nuevo";
        $index = 0;
        $vista = NULL;
        switch ($documentos_tipos_id) {
            case TIPO_DOCUMENTO_ORDEN_AUDITORIA:
                $documentos[$index] = $this->Documentos_model->get_template($documentos_tipos_id);
                if ($documentos_id !== "nuevo") {
                    $documentos = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
                    $accion = "modificar";
                    if (intval($documentos_id) > 0) {
                        $index = array_search($documentos_id, array_column($documentos, 'documentos_id'));
                    } elseif (isset($documentos[$index]['documentos_id'])) {
                        $documentos_id = $documentos[$index]['documentos_id'];
                    }
                    if (!empty($documentos[$index]['valores'][ORD_ENT_ID_DIR_AUDIT])) {
                        $para_direcciones_id = $documentos[$index]['valores'][ORD_ENT_ID_DIR_AUDIT];
                    }
                    if (!empty($documentos[$index]['valores'][ORD_ENT_ID_DIR_CONTRA])) {
                        $de_empleados_id = $documentos[$index]['valores'][ORD_ENT_ID_DIR_CONTRA];
                    }
                }
                $this->module['title_list'] = "Orden de Auditoría";
                break;
            case TIPO_DOCUMENTO_ACTA_INICIO_AUDITORIA:
                $documentos[$index] = $this->Documentos_model->get_template($documentos_tipos_id);
                if ($documentos_id !== "nuevo") {
                    $documentos = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
                    $accion = "modificar";
                    if (intval($documentos_id) > 0) {
                        $index = array_search($documentos_id, array_column($documentos, 'documentos_id'));
                    } elseif (isset($documentos[$index]['documentos_id'])) {
                        $documentos_id = $documentos[$index]['documentos_id'];
                    }
                    $documentos[$index]['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
                }
                $this->module['title_list'] = "Acta de Inicio de Auditoría";
                break;
            case TIPO_DOCUMENTO_CITATORIO:
                $documentos[$index] = $this->Documentos_model->get_template($documentos_tipos_id);
                if ($documentos_id !== "nuevo") {
                    $documentos = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
                    $accion = "modificar";
                    if (intval($documentos_id) > 0) {
                        $index = array_search($documentos_id, array_column($documentos, 'documentos_id'));
                    } elseif (isset($documentos[$index]['documentos_id'])) {
                        $documentos_id = $documentos[$index]['documentos_id'];
                    }
                    if (!empty($documentos[$index]['valores'][CITATORIO_ID_UA])) {
                        $para_direcciones_id = $documentos[$index]['valores'][CITATORIO_ID_UA];
                    }
                    if (!empty($documentos[$index]['valores'][CITATORIO_ID_DIR_CONTRA])) {
                        $de_empleados_id = $documentos[$index]['valores'][CITATORIO_ID_DIR_CONTRA];
                    }
                    $documentos[$index]['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
                }
                $this->module['title_list'] = "Oficio de Citatorio";

                break;
            case TIPO_DOCUMENTO_ENVIO_DOCUMENTOS:
                $this->module['title_list'] = "Oficio de Envío de Documentos";

                break;
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_AUDITORIA:
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_REVISION:
                $this->module['title_list'] = "Acta de Resultados";

                break;
            case TIPO_DOCUMENTO_ACTA_CIERRE_ENTREGA_INFORMACION:
                $documentos[$index] = $this->Documentos_model->get_template($documentos_tipos_id);
                if ($documentos_id !== "nuevo") {
                    $documentos = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
                    $accion = "modificar";
                    if (intval($documentos_id) > 0) {
                        $index = array_search($documentos_id, array_column($documentos, 'documentos_id'));
                    } elseif (isset($documentos[$index]['documentos_id'])) {
                        $documentos_id = $documentos[$index]['documentos_id'];
                    }
                    $documentos[$index]['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
                }
                $this->module['title_list'] = "Acta de Cierre de Entrega de Información";
                break;
            case TIPO_DOCUMENTO_ACTA_ADMINISTRATIVA:
                $this->module['title_list'] = "Acta Administrativa";
                break;
            default:
                $vista = "auditoria/documentos_view";
                break;
        }
        if (empty($vista)) {
            $vista = "documentos/" . basename($documentos[$index]['documentos_versiones_archivo_impresion'], ".php");
        }
        if (empty($para_direcciones_id)) {
            $para_direcciones_id = $auditoria['auditorias_direcciones_id'];
        }
        if (empty($periodos_id)) {
            $periodos = $this->SAC_model->get_ultimo_periodo();
            $periodos_id = intval($periodos['periodos_id']);
        }
        $para_nombre = $para_cargo = $para_tratamiento = 'SIN ESPECIFICAR';
        $de_nombre = $de_cargo = $de_tratamiento = 'SIN ESPECIFICAR';
        if (!empty($para_direcciones_id)) {
            $e = $this->SAC_model->get_director_de_ua($para_direcciones_id, $periodos_id);
            if (!empty($e)) {
                $cc_empleado = $this->SAC_model->get_empleado($e['empleados_id']);
                $para_nombre = $cc_empleado['empleados_nombre_titulado_siglas'];
                $para_cargo = $cc_empleado['empleados_cargo'];
                $para_tratamiento = '';
            }
        }
        if (!empty($de_empleados_id)) {
            $cc_empleado = $this->SAC_model->get_empleado($de_empleados_id);
            $de_nombre = $cc_empleado['empleados_nombre_titulado_siglas'];
            $de_cargo = $cc_empleado['empleados_cargo'];
            $de_tratamiento = '';
        }
        $data = array(
            'auditoria' => $auditoria,
            'registros' => array(),
            'documentos' => $documentos,
            'index' => $index,
            'urlAction' => $this->module['url'] . "/documento",
            'mis_auditorias_id' => $this->Auditoria_model->get_mis_auditorias(),
            'mis_auditorias_anio' => $this->Auditoria_model->get_anios_para_select(),
            'logotipos' => $this->Logotipos_model->get_todos(),
            'direcciones_select' => $this->SAC_model->get_direcciones_de_periodo($auditoria['auditorias_periodos_id']),
            'etiquetaBoton' => "Guardar",
            'id' => $auditorias_id,
            'accion' => $accion,
            'oficio_para' => array(
                'direcciones_id' => $para_direcciones_id,
                'nombre' => $para_nombre,
                'cargo' => $para_cargo,
                'tratamiento' => $para_tratamiento
            ),
            'oficio_de' => array(
                'empleados_id' => $de_empleados_id,
                'nombre' => $de_nombre,
                'cargo' => $de_cargo,
                'tratamiento' => $de_tratamiento
            )
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

    function nuevo_documento($documentos_tipos_id, $direcciones_id) {
        $data = array(
            'direcciones_id' => $direcciones_id
        );
        $this->Auditoria_model->crear_documento($documentos_tipos_id, $data);
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

    function autorizar($documentos_id = NULL) {
        $return = FALSE;
        $informacion = array(
            'state' => 'danger',
            'message' => 'No tiene permisos para desautorizar documentos.'
        );
        $documentos_tipo = NULL;
        if (!empty($documentos_id)) {
            $aux = $this->Documentos_model->get_tipo_de_documento_de_documento($documentos_id);
            $a = strrpos($aux['documentos_tipos_abreviacion'], "|");
            if ($a !== FALSE && is_numeric($a)) {
                $a++;
            }
            $documentos_tipo = substr($aux['documentos_tipos_abreviacion'], $a);
        } else {
            $informacion['message'] = "No se especificó el identificador del documento.";
        }
        if ($this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_DESAUTORIZAR_DOCUMENTO) && !empty($documentos_id)) {
            $return = $this->Auditoria_model->autorizar_documento($documentos_id, 1);
            $informacion['state'] = 'success';
            $informacion['message'] = 'Documento autorizado';
        }
        $this->session->set_flashdata('informacion', $informacion);
        if (!empty($documentos_tipo)) {
            redirect(base_url() . $this->module['controller'] . "/documento/" . $documentos_tipo . "/" . $documentos_id);
        }
        redirect(base_url() . $this->module['controller'] . "/Audtoria/");
    }

    function desautorizar($documentos_id = NULL) {
        $return = FALSE;
        $informacion = array(
            'state' => 'danger',
            'message' => 'No tiene permisos para desautorizar documentos'
        );
        $documentos_tipo = NULL;
        if (!empty($documentos_id)) {
            $aux = $this->Documentos_model->get_tipo_de_documento_de_documento($documentos_id);
            $a = strrpos($aux['documentos_tipos_abreviacion'], "|");
            if ($a !== FALSE && is_numeric($a)) {
                $a++;
            }
            $documentos_tipo = substr($aux['documentos_tipos_abreviacion'], $a);
        } else {
            $informacion['message'] = "No se especificó el identificador del documento.";
        }
        if ($this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_DESAUTORIZAR_DOCUMENTO) && !empty($documentos_id)) {
            $return = $this->Auditoria_model->autorizar_documento($documentos_id, 0);
            $informacion['state'] = 'success';
            $informacion['message'] = 'Documento desautorizado';
        }
        $this->session->set_flashdata('informacion', $informacion);
        if (!empty($documentos_tipo)) {
            redirect(base_url() . $this->module['controller'] . "/documento/" . $documentos_tipo . "/" . $documentos_id);
        }
        redirect(base_url() . $this->module['controller'] . "/Audtoria/");
    }

    function descargar($documentos_id) {
        $documento = $this->Documentos_model->get_documento($documentos_id);
        $documentos_tipos_id = intval($documento['documentos_documentos_tipos_id']);
        $template = $this->Documentos_model->get_template($documentos_tipos_id);
        $documento = array_merge($documento, $template);
        $auditorias_id = intval($documento['documentos_auditorias_id']);
        $periodos_id = $documento['documentos_periodos_id'];
        $auditoria = $this->Auditoria_model->get_auditoria($auditorias_id);
        $titular = $this->SAC_model->get_director_de_ua(APP_DIRECCION_CONTRALORIA, $auditoria['auditorias_periodos_id']);
        $de_empleados_id = $titular['empleados_id'];
        $accion = "descargar";
        $is_oficio = TRUE;
        $vista = "documentos/" . basename($documento['documentos_versiones_archivo_impresion'], ".php");
        switch ($documentos_tipos_id) {
            case TIPO_DOCUMENTO_ORDEN_AUDITORIA:
                $para_direcciones_id = $documento['valores'][ORD_ENT_ID_DIR_AUDIT];
                $de_empleados_id = $documento['valores'][ORD_ENT_ID_DIR_CONTRA];
                $this->module['title_list'] = "Orden de Auditoría";
                break;
            case TIPO_DOCUMENTO_ACTA_INICIO_AUDITORIA:
                $is_oficio = FALSE;
                $documento['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
                $this->module['title_list'] = "Acta de Inicio de Auditoría";
                break;
            case TIPO_DOCUMENTO_CITATORIO:
                $para_direcciones_id = $documento['valores'][CITATORIO_ID_UA];
                $de_empleados_id = $documento['valores'][CITATORIO_ID_DIR_CONTRA];
                $this->module['title_list'] = "Oficio de Citatorio";
                $documento['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
                break;
            case TIPO_DOCUMENTO_ENVIO_DOCUMENTOS:
                $this->module['title_list'] = "Oficio de Envío de Documentos";
                break;
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_AUDITORIA:
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_REVISION:
                $this->module['title_list'] = "Acta de Resultados";
                break;
            case TIPO_DOCUMENTO_ACTA_CIERRE_ENTREGA_INFORMACION:
                $is_oficio = FALSE;
                $documento['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
                $this->module['title_list'] = "Acta de Cierre de Entrega de Información";
                break;
            case TIPO_DOCUMENTO_ACTA_ADMINISTRATIVA:
                $this->module['title_list'] = "Acta Administrativa";
                break;
            default :
                echo "Tipo de documento no encontrado";
                die();
        }
        if ($is_oficio) {
            $e = $this->SAC_model->get_director_de_ua($para_direcciones_id, $periodos_id);
            if (!empty($e)) {
                $cc_empleado = $this->SAC_model->get_empleado($e['empleados_id']);
                $para_nombre = $cc_empleado['empleados_nombre_titulado_siglas'];
                $para_cargo = $cc_empleado['empleados_cargo'];
                $para_tratamiento = '';
            }
            $cc_empleado = $this->SAC_model->get_empleado($de_empleados_id);
            $de_nombre = $cc_empleado['empleados_nombre_titulado_siglas'];
            $de_cargo = $cc_empleado['empleados_cargo'];
            $de_tratamiento = '';
        }
        $data = array(
            'auditoria' => $auditoria,
            'registros' => array(),
            'documentos' => array(
                0 => $documento
            ),
            'index' => 0,
            'urlAction' => $this->module['url'] . "/documento",
            'mis_auditorias_id' => $this->Auditoria_model->get_mis_auditorias(),
            'mis_auditorias_anio' => $this->Auditoria_model->get_anios_para_select(),
            'logotipos' => $this->Logotipos_model->get_todos(),
            'direcciones' => $this->SAC_model->get_direcciones_de_periodo($auditoria['auditorias_periodos_id']),
            'etiquetaBoton' => "Guardar",
            'id' => $auditorias_id,
            'accion' => $accion
        );
        if ($is_oficio) {
            $data['oficio_para'] = array(
                'direcciones_id' => $para_direcciones_id,
                'nombre' => $para_nombre,
                'cargo' => $para_cargo,
                'tratamiento' => $para_tratamiento
            );
            $data['oficio_de'] = array(
                'empleados_id' => $de_empleados_id,
                'nombre' => $de_nombre,
                'cargo' => $de_cargo,
                'tratamiento' => $de_tratamiento
            );
        }
        if (isset($data['r']['oficios_omisos_is_autorizado']) && intval($data['r']['oficios_omisos_is_autorizado']) === 1) {
            $data["is_autorizado"] = TRUE;
        }
        $this->visualizar($vista, $data);
    }

}
