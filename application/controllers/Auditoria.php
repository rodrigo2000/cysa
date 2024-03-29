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
        $this->is_catalogo = FALSE;
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
            'is_finalizada' => !in_array($auditoria['auditorias_status_id'], array(AUDITORIAS_STATUS_EN_PROCESO, AUDITORIAS_STATUS_SIN_INICIAR)),
        );
        if (!empty($auditorias_id)) {
            $this->{$this->module['controller'] . "_model"}->actualizar_session('auditorias_id', intval($auditorias_id));
            $anio = -1 * intval($auditoria['auditorias_anio']);
            if ($auditoria['auditorias_status_id'] == 1) {
                $anio = abs($anio);
            }
            $this->{$this->module['controller'] . "_model"}->actualizar_session('auditorias_anio', intval($anio));
            $data['etapa_auditoria'] = $this->Auditoria_model->get_etapa();
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
        $contralor = $this->SAC_model->get_director_de_ua(APP_DIRECCION_CONTRALORIA, $auditoria['auditorias_periodos_id']);
        $de_empleados_id = isset($contralor['empleados_id']) ? $contralor['empleados_id'] : NULL;
        $accion = "nuevo";
        $index = 0;
        $vista = NULL;
        $documentos[$index] = $this->Documentos_model->get_template($documentos_tipos_id);
        if ($documentos_id !== "nuevo") {
            $aux = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
            if (empty($aux[0]['documentos_versiones_archivo_impresion'])) {
                $documentos[0] = array_merge($aux[0], $documentos[$index]);
            } else {
                $documentos = $aux;
            }
            $accion = "modificar";
            if (intval($documentos_id) > 0) {
                $index = array_search($documentos_id, array_column($documentos, 'documentos_id'));
            } elseif (isset($documentos[$index]['documentos_id'])) {
                $documentos_id = $documentos[$index]['documentos_id'];
            }
            $documentos[$index]['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
        }
        $mi_data = array();
        switch ($documentos_tipos_id) {
            case TIPO_DOCUMENTO_ORDEN_AUDITORIA:
                if ($documentos_id !== "nuevo") {
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
                $this->module['title_list'] = "Acta de Inicio de Auditoría";
                $mi_data['asistencias'] = $documentos[$index]['asistencias'];
                break;
            case TIPO_DOCUMENTO_CITATORIO:
                if ($documentos_id !== "nuevo") {
                    if (!empty($documentos[$index]['valores'][CITATORIO_ID_UA])) {
                        $para_direcciones_id = $documentos[$index]['valores'][CITATORIO_ID_UA];
                    }
                    if (!empty($documentos[$index]['valores'][CITATORIO_ID_DIR_CONTRA])) {
                        $de_empleados_id = $documentos[$index]['valores'][CITATORIO_ID_DIR_CONTRA];
                    }
                }
                $this->module['title_list'] = "Oficio de Citatorio";
                break;
            case TIPO_DOCUMENTO_ENVIO_DOCUMENTOS:
                $this->module['title_list'] = "Oficio de Envío de Documentos";
                break;
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_AUDITORIA:
                $mi_data['asistencias'] = $documentos[$index]['asistencias'];
                $this->module['title_list'] = "Acta de Resultados";
                break;
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_REVISION:
                $mi_data['asistencias'] = $documentos[$index]['asistencias'];
                $this->module['title_list'] = "Acta de Resultados";
                break;
            case TIPO_DOCUMENTO_ACTA_CIERRE_ENTREGA_INFORMACION:
                $this->module['title_list'] = "Acta de Cierre de Entrega de Información";
                break;
            case TIPO_DOCUMENTO_ACTA_ADMINISTRATIVA:
                $this->module['title_list'] = "Acta Administrativa";
                break;
            case TIPO_DOCUMENTO_AUTORIZACION_AUDITORIA_NO_PROGRAMADA:
                $this->module['title_list'] = "Autorización de Auditoría No Programada";
                $mi_data['auditoroas_por_sustituir'] = $this->Auditorias_model->get_auditorias_sin_numero();
                break;
            case TIPO_DOCUMENTO_AMPLIACION:
                $this->module['title_list'] = "Solicitud de Ampliación";
                break;
            case TIPO_DOCUMENTO_REPROGRAMACION:
                $this->module['title_list'] = "Solicitud de Reprogramación";
                break;
            case TIPO_DOCUMENTO_RESOLUCION_AMPLIACION_PLAZO:
                $this->module['title_list'] = "Resolución de Ampliación de Plazo";
                break;
            case TIPO_DOCUMENTO_RESOLUCION_PRORROGA:
                $this->module['title_list'] = "Resolución de Prórroga";
                break;
            case TIPO_DOCUMENTO_SOLICITUD_INFORMACION:
                $this->module['title_list'] = "Oficio de Solicitud de Información";
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
        $para_empleados_id = NULL;
        if (!empty($para_direcciones_id)) {
            $e = $this->SAC_model->get_director_de_ua($para_direcciones_id, $periodos_id);
            if (!empty($e)) {
                $cc_empleado = $this->SAC_model->get_empleado($e['empleados_id']);
                $para_empleados_id = $e['empleados_id'];
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
        $hidden = !isset($documentos[$index]['documentos_id']) || empty($documentos[$index]['documentos_id']) ? 'hidden-xs-up' : '';
        $documento_autorizado = isset($documentos[$index]['documentos_is_aprobado']) && $documentos[$index]['documentos_is_aprobado'] == 1 ? TRUE : FALSE;
        // Texto que va debajo de cada foja
        $texto_foja = "";
        $direcciones = array();
        $asistencias = isset($documentos[$index]['asistencias']) ? $documentos[$index]['asistencias'] : array();
        if (empty($asistencias)) {
            $asistencias[$auditoria['auditorias_direcciones_id']] = array(
                TIPO_ASISTENCIA_INVOLUCRADO => 0
            );
        }
        foreach ($asistencias as $direcciones_id => $d) {
            if (isset($d[TIPO_ASISTENCIA_INVOLUCRADO])) {
                $aux = $this->SAC_model->get_direccion($direcciones_id);
                if (!empty($aux) && isset($aux['nombre_completo_direccion'])) {
                    array_push($direcciones, $aux['nombre_completo_direccion']);
                } elseif (!empty($aux)) {
                    array_push($direcciones, $aux['direcciones_nombre']);
                }
            }
        }
        if (count($direcciones) > 1) {
            $ultimo = array_pop($direcciones);
            $texto_foja = implode(", ", $direcciones) . " y " . $ultimo;
        } else {
            $texto_foja = implode(", ", $direcciones);
        }
        $periodos_id = intval($auditoria['cc_periodos_id']);
        $direcciones_id = intval($auditoria['cc_direcciones_id']);
        $subdirecciones_id = intval($auditoria['cc_subdirecciones_id']);
        $data = array(
            'documentos_tipos_id' => $documentos_tipos_id,
            'auditoria' => $auditoria,
            'registros' => array(),
            'documentos' => $documentos,
            'index' => $index,
            'etapa_auditoria' => $this->Auditoria_model->get_etapa(),
            'documento' => $documentos[$index],
            'tooltiptext' => isset($documentos[$index]['tooltiptext']) ? $documentos[$index]['tooltiptext'] : array(),
            'descripciones' => isset($documentos[$index]['descripciones']) ? $documentos[$index]['descripciones'] : array(),
            'r' => isset($documentos[$index]['valores']) ? $documentos[$index]['valores'] : array(),
            'documento_autorizado' => $documento_autorizado,
            'hidden' => $hidden,
            'texto_foja' => $texto_foja,
            'urlAction' => $this->module['url'] . "/documento",
            'logotipos' => $this->Logotipos_model->get_todos(),
            'direcciones_select' => $this->SAC_model->get_direcciones_de_periodo($auditoria['auditorias_periodos_id']),
            'etiquetaBoton' => "Guardar",
            'id' => $auditorias_id,
            'accion' => $accion,
            'oficio_para' => array(
                'direcciones_id' => $para_direcciones_id,
                'nombre' => $para_nombre,
                'cargo' => $para_cargo,
                'tratamiento' => $para_tratamiento,
                'empleados_id' => $para_empleados_id
            ),
            'oficio_de' => array(
                'empleados_id' => $de_empleados_id,
                'nombre' => $de_nombre,
                'cargo' => $de_cargo,
                'tratamiento' => $de_tratamiento
            ),
            'periodos' => $this->SAC_model->get_periodos(),
            'direcciones' => $this->SAC_model->get_direcciones_de_periodo($periodos_id),
            'subdirecciones' => $this->SAC_model->get_subdirecciones_de_direccion($periodos_id, $direcciones_id),
            'departamentos' => $this->SAC_model->get_departamentos_de_subdireccion($periodos_id, $direcciones_id, $subdirecciones_id),
            'is_finalizada' => !in_array($auditoria['auditorias_status_id'], array(AUDITORIAS_STATUS_EN_PROCESO, AUDITORIAS_STATUS_SIN_INICIAR)),
            'vigente_documentos_versiones_id' => $this->Documentos_versiones_model->get_version_vigente_del_tipo_de_documento($documentos_tipos_id),
            'vista' => $vista
        );
        $data = array_merge($data, $mi_data);
        $this->visualizar('documentos/template_view', $data);
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
            $status_id = array(AUDITORIAS_STATUS_EN_PROCESO, AUDITORIAS_STATUS_SIN_INICIAR, NULL);
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
        echo json_encode($return, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
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

        $this->Auditoria_model->set_empleados_involucrados(NULL, $empleados_id);
        $data = array();
        if (is_array($empleados_id) && !empty($empleados_id)) {
            foreach ($empleados_id as $e) {
                $aux = $this->SAC_model->get_empleado($e);
                array_push($data, array(
                    'empleados_nombre_titulado_siglas' => $aux['empleados_nombre_titulado_siglas'],
                    'empleados_id' => $aux['empleados_id']
                ));
            }
        }
        $json['success'] = TRUE;
        $json['message'] = 'Empleados actualizados';
        $json['empleados'] = $empleados_id;
        $json['data'] = $data;

        echo json_encode($json);
    }

    function set_equipo_de_auditoria() {
        $empleados = $this->input->post("empleados_id");
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa['auditorias_id'];
        $this->Auditorias_equipo_model->eliminar_equipo_de_auditoria($auditorias_id);
        if (!empty($empleados)) {
            foreach ($empleados as $empleados_id) {
                $insert = array(
                    'auditorias_equipo_auditorias_id' => $auditorias_id,
                    'auditorias_equipo_empleados_id' => $empleados_id,
                    'auditorias_equipo_tipo' => TIPO_PERMISO_EQUIPO_TRABAJO
                );
                $return = $this->Auditorias_equipo_model->insert($insert);
            }
        } else {
            $return['state'] = "success";
        }
        $return['success'] = $return['state'] === 'success' ? TRUE : FALSE;
        echo json_encode($return);
    }

    function set_permisos_adicionales() {
        $empleados = $this->input->post("empleados_id");
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa['auditorias_id'];
        $this->Auditorias_equipo_model->eliminar_equipo_adicional_auditoria($auditorias_id);
        if (!empty($empleados)) {
            foreach ($empleados as $empleados_id) {
                $insert = array(
                    'auditorias_equipo_auditorias_id' => $auditorias_id,
                    'auditorias_equipo_empleados_id' => $empleados_id,
                    'auditorias_equipo_tipo' => TIPO_PERMISO_EQUIPO_TRABAJO
                );
                $return = $this->Auditorias_equipo_model->insert($insert);
            }
        } else {
            $return['state'] = "success";
        }
        $return['success'] = $return['state'] === 'success' ? TRUE : FALSE;
        echo json_encode($return);
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
        if ($this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_AUTORIZAR_DOCUMENTO) && !empty($documentos_id)) {
            $return = $this->Auditoria_model->autorizar_documento($documentos_id, 1);
            $informacion['state'] = 'success';
            $informacion['message'] = 'Documento autorizado';
            $documento = $this->Documentos_model->get_documento($documentos_id);
//            $fecha = $documento['valores'][ORD_ENT_FECHA];
//            $this->db
//                    ->set("auditorias_fechas_sello_orden_entrada", $fecha)
//                    ->set("auditorias_fechas_inicio_real", $fecha)
//                    ->where("auditorias_fechas_auditorias_id", $documento['documentos_auditorias_id'])
//                    ->update("auditorias_fechas");
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

    function imprimir($documentos_id, $return_html = FALSE) {
        if (!empty($documentos_id)) {
            $documento = $this->Documentos_blob_model->get_uno($documentos_id);
            $server = ENVIRONMENT === 'development' ? 'http://DCON-ATI-RSEVI/contraloria2/' : 'http://SVRDCONT02/contraloria/';
            $html = '<!DOCTYPE html>
                        <html lang="en">
                            <head>
                                <meta charset="utf-8">
                                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                                <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1">
                                <link rel="icon" href="' . $server . 'sac/resources/images/ico/32x32.png" type="image/png">
                                <link href="' . $server . 'sac/resources/styles/app.min.css" rel="stylesheet">
                                <link href="' . $server . 'sac/resources/styles/personalizados.css" rel="stylesheet" type="text/css"/>
                                <link href="' . $server . 'sac/resources/styles/personalizados_sac.css" rel="stylesheet" type="text/css"/>
                                <!-- Personalizado -->
                                <script src="' . $server . 'cysa/resources/scripts/auditoria_view.js" type="text/javascript"></script>
                                <script src="' . $server . 'cysa/resources/scripts/auditorias_documentos_generico.js" type="text/javascript"></script>

                                <link href="' . $server . 'cysa/resources/styles/oficios.css" rel="stylesheet" type="text/css"/>
                                <link href="' . $server . 'cysa/resources/styles/media_print.css" rel="stylesheet" type="text/css"/>
                                <link href="' . $server . 'sac/resources/styles/emular_impresora.css" rel="stylesheet" type="text/css"/>
                                <script src="' . $server . 'sac/resources/scripts/emular_impresora.js" type="text/javascript"></script>
                                <link href="' . $server . 'sac/resources/styles/fuentes.css" rel="stylesheet" type="text/css"/>
                                </head>
                                <body>'
                    . (!empty($documento['documentos_blob_contenido']) ? utf8_encode($documento['documentos_blob_contenido']) : 'NO SE ENCONTRÓ EL DOCUMENTO')
                    . '</body>
                    </html>';
            if ($return_html) {
                return $html;
            } else {
                echo $html;
            }
        }
    }

    function descargar($documentos_id) {
        if ($documentos_id !== "CO") {
            $documento = $this->Documentos_model->get_documento($documentos_id);
            $documentos_tipos_id = intval($documento['documentos_documentos_tipos_id']);
        } else {
            $documento = array();
            $documentos_id = NULL;
            $documentos_tipos_id = TIPO_DOCUMENTO_CEDULAS_OBSERVACION;
        }
        $template = $this->Documentos_model->get_template($documentos_tipos_id);
        $documento = array_merge($documento, $template);
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa['auditorias_id'];
        $periodos_id = NULL;
        if (!empty($documento) && isset($documento['documentos_auditorias_id'])) {
            $auditorias_id = intval($documento['documentos_auditorias_id']);
            $periodos_id = $documento['documentos_periodos_id'];
            if ($documento['documentos_is_aprobado'] == 1) {
                $this->imprimir($documentos_id);
                exit();
            }
        }
        $auditoria = $this->Auditoria_model->get_auditoria($auditorias_id);
        $contralor = $this->SAC_model->get_director_de_ua(APP_DIRECCION_CONTRALORIA, $auditoria['auditorias_periodos_id']);
        $de_empleados_id = $contralor['empleados_id'];
        $accion = "descargar";
        $is_oficio = TRUE;
        $documento['asistencias'] = $this->Asistencias_model->get_asistencias_de_documento($documentos_id);
        $vista = "documentos" . DIRECTORY_SEPARATOR . basename($documento['documentos_versiones_archivo_impresion'], ".php");
        $index = 0;
        $mi_data = array();
        switch ($documentos_tipos_id) {
            case TIPO_DOCUMENTO_ORDEN_AUDITORIA:
                $para_direcciones_id = $documento['valores'][ORD_ENT_ID_DIR_AUDIT];
                $de_empleados_id = $documento['valores'][ORD_ENT_ID_DIR_CONTRA];
                $this->module['title_list'] = "Orden de Auditoría";
                break;
            case TIPO_DOCUMENTO_ACTA_INICIO_AUDITORIA:
                $is_oficio = FALSE;
                $this->module['title_list'] = "Acta de Inicio de Auditoría";
                break;
            case TIPO_DOCUMENTO_CITATORIO:
                $para_direcciones_id = $documento['valores'][CITATORIO_ID_UA];
                $de_empleados_id = $documento['valores'][CITATORIO_ID_DIR_CONTRA];
                $this->module['title_list'] = "Oficio de Citatorio";
                break;
            case TIPO_DOCUMENTO_ENVIO_DOCUMENTOS:
                $this->module['title_list'] = "Oficio de Envío de Documentos";
                break;
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_AUDITORIA:
                $is_oficio = FALSE;
                $mi_data['asistencias'] = $documento['asistencias'];
                $this->module['title_list'] = "Acta de Resultados";
                break;
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_REVISION:
                $is_oficio = FALSE;
                $mi_data['asistencias'] = $documento['asistencias'];
                $this->module['title_list'] = "Acta de Resultados";
                break;
            case TIPO_DOCUMENTO_ACTA_CIERRE_ENTREGA_INFORMACION:
                $is_oficio = FALSE;
                $this->module['title_list'] = "Acta de Cierre de Entrega de Información";
                break;
            case TIPO_DOCUMENTO_ACTA_ADMINISTRATIVA:
                $this->module['title_list'] = "Acta Administrativa";
                break;
            case TIPO_DOCUMENTO_AUTORIZACION_AUDITORIA_NO_PROGRAMADA:
                $is_oficio = FALSE;
                break;
            case TIPO_DOCUMENTO_AMPLIACION:
            case TIPO_DOCUMENTO_REPROGRAMACION:
                $is_oficio = FALSE;
                break;
            case TIPO_DOCUMENTO_CEDULAS_OBSERVACION:
                $is_oficio = FALSE;
                $observaciones_id = $this->uri->segment(4);
                $etapa = $this->uri->segment(5);
                $observaciones = NULL;
                if (!empty($observaciones_id)) {
                    $observaciones = array(
                        0 => $this->Observaciones_model->get_observacion($observaciones_id)
                    );
                } else {
                    $observaciones = $this->Observaciones_model->get_observaciones($auditorias_id);
                }
                $mi_data = array(
                    'observaciones' => $observaciones
                );
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
        $hidden = !isset($documento['documentos_id']) || empty($documento['documentos_id']) ? 'hidden-xs-up' : '';
        $documento_autorizado = isset($documento['documentos_is_aprobado']) && $documento['documentos_is_aprobado'] == 1 ? TRUE : FALSE;
        // Texto que va debajo de cada foja
        $texto_foja = "";
        $direcciones = array();
        $asistencias = isset($documento['asistencias']) ? $documento['asistencias'] : array();
        if (empty($asistencias)) {
            $asistencias[$auditoria['auditorias_direcciones_id']] = array(
                TIPO_ASISTENCIA_INVOLUCRADO => 0
            );
        }
        foreach ($asistencias as $direcciones_id => $d) {
            if (isset($d[TIPO_ASISTENCIA_INVOLUCRADO])) {
                $aux = $this->SAC_model->get_direccion($direcciones_id);
                array_push($direcciones, $aux['nombre_completo_direccion']);
            }
        }
        if (count($direcciones) > 1) {
            $ultimo = array_pop($direcciones);
            $texto_foja = implode(", ", $direcciones) . " y " . $ultimo;
        } else {
            $texto_foja = implode(", ", $direcciones);
        }
        $data = array(
            'documentos_tipos_id' => $documentos_tipos_id,
            'auditoria' => $auditoria,
            'registros' => array(),
            'documentos' => array(
                $index => $documento
            ),
            'index' => $index,
            'documento' => $documento,
            'r' => isset($documento['valores']) ? $documento['valores'] : array(),
            'documento_autorizado' => $documento_autorizado,
            'hidden' => $hidden,
            'texto_foja' => $texto_foja,
            'urlAction' => $this->module['url'] . "/documento",
            'logotipos' => $this->Logotipos_model->get_todos(),
            'direcciones' => $this->SAC_model->get_direcciones_de_periodo($auditoria['auditorias_periodos_id']),
            'etiquetaBoton' => "Guardar",
            'id' => $auditorias_id,
            'accion' => $accion,
            'is_finalizada' => !in_array($auditoria['auditorias_status_id'], array(AUDITORIAS_STATUS_EN_PROCESO, AUDITORIAS_STATUS_SIN_INICIAR)),
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
        $data['vista'] = $vista;
        $data = array_merge($data, $mi_data);
        $this->visualizar('documentos/template_view', $data);
    }

    function portada() {
        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $post = $this->input->post();
            $empleados_id = $this->session->userdata("empleados_id");
            $expedientes_id = intval($post['expedientes_id']);
            $auditorias_id = $post["auditorias_id"];
            if (!isset($post['expedientes_isPPR'])) {
                $post['expedientes_isPPR'] = 0;
            }
            if (!isset($post['expedientes_isReservada'])) {
                $post['expedientes_isRerservada'] = 0;
            }

            $post['expedientes_fecha_apertura'] = empty($post['expedientes_fecha_apertura']) ? NULL : $post['expedientes_fecha_apertura'];
            $post['expedientes_fecha_cierre'] = empty($post['expedientes_fecha_cierre']) ? NULL : $post['expedientes_fecha_cierre'];
            $post['expedientes_numero_fojas'] = implode(",", $_POST['numero_fojas']);
            $post['expedientes_numero_tomo'] = count($post['numero_fojas']);
            $post['expedientes_numero_total_tomos'] = count($post['numero_fojas']);
            $post['expedientes_fecha_desclasificacion'] = empty($post['expedientes_fecha_desclasificacion']) ? NULL : $post['expedientes_fecha_desclasificacion'];
            unset($post['numero_fojas'], $post['accion'], $post['auditorias_id']);

            if (empty($expedientes_id)) {
                $post['expedientes_idEmpleado'] = $empleados_id;
                $post['expedientes_fecha_creacion'] = ahora();
                $r = $this->EXPEDIENTES_model->insert($post);
            } else {
                $r = $this->EXPEDIENTES_model->update_expedientes_por_auditoria($auditorias_id, $post);
            }
            $auditorias_origen_actualizados = FALSE;
            if ($r['state'] === 'success') {
                // Si se capturó la fecha de desclasificación, entonces actualizamos la auditoría origen, en caso de que exista
                if (isset($post['expedientes_fecha_desclasificacion']) && !empty($post['expedientes_fecha_desclasificacion'])) {
                    $auditorias_origen = $this->Auditorias_model->get_auditorias_origen($auditorias_id);
                    foreach ($auditorias_origen as $ao) {
                        $data['expedientes_fecha_desclasificacion'] = $post['expedientes_fecha_desclasificacion'];
                        $this->EXPEDIENTES_model->update_expedientes_por_auditoria($au['idAuditoria'], $data);
                        $auditorias_origen_actualizados = TRUE;
                    }
                }
                $return['success'] = TRUE;
            }
            $s['informacion'] = array(
                'state' => 'success',
                'message' => 'Se ha actualizado los datos de la auditoría' . ($auditorias_origen_actualizados ? ' y la fecha de desclasificación de sus auditorías origen' : '') . "."
            );
            $this->session->set_flashdata($s);
            redirect($this->module['url'] . "/portada");
        }
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa[$this->module['id_field']];
        $r = array(
            $this->module['id_field'] => $auditorias_id
        );
        $expediente = $this->EXPEDIENTES_model->get_expediente_de_auditoria($auditorias_id);
        if (is_array($expediente) && !empty($expediente)) {
            $r = array_merge($r, $expediente);
        }
        $data = array(
            'urlAction' => $this->module['url'] . "/portada",
            'tituloModulo' => 'Portada o guardar exterior',
            'etiquetaBoton' => 'Guardar',
            'accion' => 'modificar',
            'r' => $r
        );
        $vista = "auditorias_portada_view";
        $this->visualizar($vista, $data);
    }

    function imprimir_portada($expedientes_id = NULL) {
        // La siguiente condición sirve para poder descagar el expediente desde SIMA, ya que impide cargar las funciones de validacion de sesion
        if (!($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['forzar_descargar']) && intval($_GET['forzar_descargar']) == 1)) {
            
        }

        $error = "";
        $where = "";
        $PREFIX = "proto_";
        if (empty($expedientes_id)) {
            $cysa = $this->session->userdata(APP_NAMESPACE);
            $where = "e.expedientes_idAuditoria = " . $cysa['auditorias_id'];
        } else {
            $where = "e.expedientes_id = " . $expedientes_id;
        }
        $strSQL = "SELECT e.*, sec.secciones_nombre, ssec.subsecciones_nombre, ser.series_nombre, sser.subseries_nombre, secciones_clave, "
                . "f.fondos_nombre, sf.subfondos_nombre, d.denDireccion, s.denSubdireccion, dep.denDepartamento, a.*, af.fechas_envios_osi "
                . "FROM expedientes e "
                . "INNER JOIN fondos f ON f.fondos_id = e.expedientes_fondos_id "
                . "INNER JOIN subfondos sf ON sf.subfondos_id = e.expedientes_subfondos_id AND sf.subfondos_fondos_id = e.expedientes_fondos_id "
                . "LEFT JOIN secciones sec ON sec.secciones_id = e.expedientes_secciones_id "
                . "LEFT JOIN subsecciones ssec ON ssec.subsecciones_numero = e.expedientes_subsecciones_numero AND sec.secciones_id = e.expedientes_secciones_id "
                . "LEFT JOIN series ser ON ser.series_numero = e.expedientes_series_numero "
                . "    AND ser.series_subsecciones_numero = e.expedientes_subsecciones_numero "
                . "    AND ser.series_secciones_id = e.expedientes_secciones_id "
                . "LEFT JOIN subseries sser ON sser.subseries_numero = e.expedientes_subseries_numero "
                . "    AND sser.subseries_secciones_id = e.expedientes_secciones_id "
                . "    AND sser.subseries_subsecciones_numero = e.expedientes_subsecciones_numero "
                . "    AND sser.subseries_series_numero = e.expedientes_series_numero "
                . "LEFT JOIN " . $PREFIX . "sac.ayunta_direccion d ON d.clv_dir = e.expedientes_clv_dir AND d.direccionActiva=1 "
                . "LEFT JOIN " . $PREFIX . "sac.ayunta_subdireccion s ON s.clv_dir = e.expedientes_clv_dir AND s.clv_subdir = e.expedientes_clv_subdir "
                . "LEFT JOIN " . $PREFIX . "sac.ayunta_departamento dep ON dep.clv_dir = e.expedientes_clv_dir AND dep.clv_subdir = e.expedientes_clv_subdir AND dep.clv_depto = e.expedientes_clv_depto "
                . "LEFT JOIN " . $PREFIX . "cysa.cat_auditoria a ON a.idAuditoria = e.expedientes_idAuditoria "
                . "LEFT JOIN " . $PREFIX . "cysa.cat_auditoria_fechas af ON af.idAuditoria = e.expedientes_idAuditoria "
                . "WHERE " . $where . " LIMIT 1";
        $dbExpedientes = $this->EXPEDIENTES_model->get_proto_expedientes();
        $result = $dbExpedientes->query($strSQL);
        if ($result && $result->num_rows() == 1) {
            $row = $result->row_array();
            $filenameTemplate = "../Expedientes/resources/template.docx";
            if (!empty($row['expedientes_idAuditoria'])) {
                $filenameTemplate = "../Expedientes/resources/portada_legajo_auditoria_ISO002.docx";
            }
            $this->load->helper("Expedientes");
            $this->load->library("Word");
            $docx = new Word($filenameTemplate);
            $labelCC = parse_cc($row['expedientes_anio'], $row['expedientes_clv_dir'], $row['expedientes_clv_subdir'], $row['expedientes_clv_depto']);
            $label_clv_dir = $label_clv_subdir = $label_clv_depto = "NA";
            if (is_array($labelCC) && count($labelCC) > 0) {
                $label_clv_dir = $labelCC['label_clv_dir'];
                $label_clv_subdir = $labelCC['label_clv_subdir'];
                $label_clv_depto = $labelCC['label_clv_depto'];
            }
            $row['label_clv_dir'] = $label_clv_dir;
            $row['label_clv_subdir'] = $label_clv_subdir;
            $row['label_clv_depto'] = $label_clv_depto;
            if (!isset($row['anio'])) {
                $row['anio'] = NULL;
            }
            if (empty($row['anio'])) {
                $row['anio'] = $row['expedientes_anio'];
            }
            $folio = get_folio($row);
            $folioDocto = $folio;
            if (!empty($row['expedientes_idAuditoria'])) {
                $datosAudit = $this->Auditorias_model->get_Auditoria($row['expedientes_idAuditoria']);
                $fechaOEA = mysqlDate2OnlyDate($row['fechaSelloOEA']);
                $fechaARA = mysqlDate2OnlyDate($row['fechaLectura']);
                if (empty($row['fechaLectura'])) {
                    $fechaARR = '';
                };
                $fechaARR = mysqlDate2OnlyDate($row['fechaLecturaRev1']);
                if (empty($row['fechaLecturaRev1'])) {
                    $fechaARR = '';
                };
                // Verificamos que tenga el OSI
                if (!empty($row['fechas_envios_osi']) && empty($row['expedientes_fecha_apertura'])) {
                    $row['expedientes_fecha_apertura'] = $row['fechas_envios_osi'];
                    $strSQL = "UPDATE expedientes SET expedientes_fecha_apertura = '" . $row['fechas_envios_osi'] . "' WHERE expedientes_idAuditoria = " . $row['expedientes_idAuditoria'] . " LIMIT 1";
                    $dbExpedientes->ejecutaQuery($strSQL);
                }

                $equipoAuditoria = $this->Auditorias_model->get_equipo_auditoria($row['expedientes_idAuditoria']);
                $equipoNombres = $equipoPuestos = array();
                array_push($equipoNombres, $datosAudit['empleados_nombre_titulado_siglas']);
                array_push($equipoPuestos, 'AUDITOR LÍDER');
                foreach ($equipoAuditoria as $index => $e) {
                    array_push($equipoNombres, $e['empleados_nombre_titulado_siglas']);
                    array_push($equipoPuestos, strtoupper($e['puestos_nombre']));
                }
                $auditoriaEquipoNombres = implode("\n", $equipoNombres);
                $auditoriaEquipoPuestos = implode("\n", $equipoPuestos);
                $numeroRevision = "NO APLICA";
                if (!empty($datosAudit['auditorias_origen_id'])) {
                    $datosAudit2 = $this->Auditorias_model->get_Auditoria($datosAudit['auditorias_origen_id']);
                    $numeroRevision = $datosAudit2['numero_auditoria'];
                }
                //$folioDocto .= "            " . $datosAudit['num'];
                $docx->set('AUDITORIA_NUMERO', "  " . $datosAudit['numero_auditoria'] . "  ");
                $docx->set('AUDITORIA_DIRECCION_AUDITADA', $datosAudit['direcciones_nombre']);
                $docx->set('AUDITORIA_SUBDIRECCION_AUDITADA', $datosAudit['subdirecciones_nombre']);
                $docx->set('AUDITORIA_DEPARTAMENTO_AUDITADA', $datosAudit['departamentos_nombre']);
                //$docx->set('AUDITORIA_OBJETIVO', $datosAudit['objetivo']);
                $docx->set('AUDITORIA_NUMERO_REVISION', $numeroRevision);
                $docx->set('AUDITORIA_EQUIPO_NOMBRES', $auditoriaEquipoNombres);
                $docx->set('AUDITORIA_EQUIPO_PUESTOS', $auditoriaEquipoPuestos);
                $docx->set('AUDITORIA_FECHA_OEA', $fechaOEA);
                $docx->set('AUDITORIA_FECHA_ARA', $fechaARA);
                $docx->set('AUDITORIA_FECHA_ARR', $fechaARR);
            }
            $docx->set('FOLIO', $folioDocto);
            if ($row['expedientes_anio'] < 2018) {
                $nombresRealesDeCC = get_nombre_actual_de_mi_cc($row['expedientes_anio'], $row['expedientes_clv_dir'], $row['expedientes_clv_subdir'], $row['expedientes_clv_depto']);
                $row['denSubdireccion'] = $nombresRealesDeCC['subdireccion'];
                $row['denDepartamento'] = $nombresRealesDeCC['departamento'];
            }
//            $row['denSubdireccion'] = html_entity_decode(Capitalizar(mb_strtolower($row['denSubdireccion'], 'ISO-8859-1')), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
//            $row['denDepartamento'] = html_entity_decode(Capitalizar(mb_strtolower($row['denDepartamento'], 'ISO-8859-1')), ENT_COMPAT | ENT_HTML401, 'ISO-8859-1');
            $row['subfondos_nombre'] .= " / " . capitalizar($row['denSubdireccion']) . " / " . capitalizar($row['denDepartamento']);
            if (intval($row['expedientes_subsecciones_numero']) === 0) {
                $row['expedientes_subsecciones_numero'] = 0;
                $row['subsecciones_nombre'] = "NO APLICA";
            }
            if (intval($row['expedientes_series_numero']) === 0) {
                $row['expedientes_series_numero'] = 0;
                $row['series_nombre'] = "NO APLICA";
            }
            if (intval($row['expedientes_subseries_numero']) === 0) {
                $row['expedientes_subseries_numero'] = 0;
                $row['subseries_nombre'] = "NO APLICA";
            }
            if (!empty($row['expedientes_fecha_apertura'])) {
                list($anio, $mes, $dia) = explode("-", $row['expedientes_fecha_apertura']);
                $row['expedientes_fecha_apertura'] = implode("-", array($dia, $mes, $anio));
            }
            if (!empty($row['expedientes_fecha_cierre'])) {
                list($anio, $mes, $dia) = explode("-", $row['expedientes_fecha_cierre']);
                $row['expedientes_fecha_cierre'] = implode("-", array($dia, $mes, $anio));
            }
            if (!empty($row['expedientes_fecha_desclasificacion'])) {
                list($anio, $mes, $dia) = explode("-", $row['expedientes_fecha_desclasificacion']);
                $row['expedientes_fecha_desclasificacion'] = implode("-", array($dia, $mes, $anio));
            }
            $docx->set('CD_1', ($row['expedientes_contenido_documentos'] == "original" ? 'X' : ''));
            $docx->set('CD_2', ($row['expedientes_contenido_documentos'] == "copia" ? 'X' : ''));
            $docx->set('CD_3', ($row['expedientes_contenido_documentos'] == "acuse" ? 'X' : ''));
            $docx->set('FECHA_DESCLASIFICACION', ($row['expedientes_isReservada'] == 1 ? '' : 'NO APLICA'));
            $row['expedientes_isReservada'] = ""; //($row['expedientes_isReservada'] == 1 ? 'X' : '   ');
            $row['expedientes_isConfidencial'] = ($row['expedientes_isConfidencial'] == 1 ? 'X' : '');
            $fojas = explode(",", $row['expedientes_numero_fojas']);
            $total_fojas = array_sum($fojas);
            if ($total_fojas === 0) {
                $row['expedientes_numero_fojas'] = "";
            } else {
                $row['expedientes_numero_fojas'] = $total_fojas;
            }
            $row['expedientes_isPPR'] = empty($row['expedientes_isPPR']) ? 'N/A' : 'X';
            $row['NT'] = $row['expedientes_numero_tomo'];
            $row['NTT'] = $row['expedientes_numero_total_tomos'];
            foreach ($row as $key => $valor) {
                $variable = strtoupper(str_replace("expedientes_", "", $key));
                $docx->set($variable, $valor);
            }
            //$docx->downloadAs("Expediente " . $folio . '.docx');
            $nombreArchivo = "Expediente " . $folio;
            $directorio = realpath(".");
            $rutaArchivo = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo . ".docx";
            // Instrcciones para que por el momento se pueda descargar la portada o guarda exterior
            $docx->downloadAs("Expediente " . $folio . '.docx');
            die();
            // [FINALIZA]
            $docx->saveAs($rutaArchivo);
            $rutaArchivoPDF = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo . ".pdf";

            $word = new COM("Word.Application") or die("Could not initialise Object.");
            // set it to 1 to see the MS Word window (the actual opening of the document)
            $word->Visible = 0;
            // recommend to set to 0, disables alerts like "Do you want MS Word to be the default .. etc"
            $word->DisplayAlerts = 0;
            // open the word 2007-2013 document
            $word->Documents->Open($rutaArchivo);
            // save it as word 2003
            //$word->ActiveDocument->SaveAs('newdocument.docx');
            // convert word 2007-2013 to PDF
            $word->ActiveDocument->ExportAsFixedFormat($rutaArchivoPDF, 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
            // quit the Word process
            $word->Quit(false);
            // clean up
            unset($word);
            // Eliminamos el archio de WORD
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
            // Si se creó el PDF, entonces lo descargamos
            if (file_exists($rutaArchivoPDF)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Type: application/force-download");
                header('Content-Disposition: attachment; filename=' . basename($rutaArchivoPDF));
                // header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($rutaArchivoPDF));
                ob_clean();
                flush();
                // Leemos el archivo PDF para cargarlo en memoria
                readfile($rutaArchivoPDF);
                // Como ya esta cargado en memoria, entonces lo eliminamos el disco duro
                unlink($rutaArchivoPDF);
                exit;
            }
            exit();
        } else {
            $error = "Error con el Query: " . $strSQL;
        }
        die($error);
    }

    function word($documentos_id) {
        if (!empty($documentos_id)) {
            
        }
    }

    function pdf($documentos_id) {
        if (!empty($documentos_id) && FALSE) {
            $path = realpath(".") . '\application\third_party\html2pdf\src';
            //Incluimos la librería
            require_once $path . '\Html2Pdf.php';

            //Recogemos el contenido de la vista
            ob_start();
            $this->imprimir($documentos_id);
            $html = ob_get_clean();

            //Pasamos esa vista a PDF
            //Le indicamos el tipo de hoja y la codificación de caracteres
            $mipdf = new \Spipu\Html2Pdf('P', 'Letter', 'es', 'true', 'UTF-8');

            //Escribimos el contenido en el PDF
            $mipdf->writeHTML($html);

            //Generamos el PDF
            $mipdf->Output('PdfGeneradoPHP.pdf');
        }

        if (!empty($documentos_id)) {
            // load the library
            $this->load->library('html2pdf_lib');

            //Set the paper defaults
            $this->html2pdf->paper('a4', 'portrait');
            /*             * ******
             * $content = the html content to be converted
             * you can use file_get_content() to get the html from other location
             *
             * $filename = filename of the pdf file, make sure you put the extension as .pdf
             * $save_to = location where you want to save the file,
             *            set it to null will not save the file but display the file directly after converted
             * ***** */
            $content = $this->imprimir($documentos_id, TRUE);

            $filename = 'testing.pdf';
            $save_to = $this->config->item('upload_root');
            if ($this->html2pdf_lib->converHtml2pdf($content, $filename, $save_to)) {
                echo $save_to . '/' . $filename;
            } else {
                echo 'failed';
            }
        }
    }

    function actualizar_campo() {
        $return = array(
            'success' => FALSE,
            'message' => 'No se pudieron actualizar los datos. Error desconocido.'
        );
        $campo = $this->input->post('campo');
        $valor = $this->input->post('valor');
        $auditorias_id = $this->input->post('auditorias_id');
        $result = FALSE;
        if (strpos($campo, '_fechas_') !== FALSE) {
            $result = $this->db
                    ->set($campo, $valor)
                    ->where('auditorias_fechas_auditorias_id', $auditorias_id)
                    ->update('auditorias_fechas');
            $return = array(
                'success' => TRUE,
                'message' => 'Datos actualizados'
            );
        } else {
            $result = $this->db
                    ->set($campo, $valor)
                    ->where('auditorias_id', $auditorias_id)
                    ->update('auditorias');
            $return = array(
                'success' => TRUE,
                'message' => 'Datos actualizados'
            );
        }
        //var_dump($result, $this->db->last_query());
        echo json_encode($return);
    }

    function actualizar_version_documento($documentos_id = NULL) {
        $return = FALSE;
        if (!empty($documentos_id)) {
            $documento = $this->Documentos_model->get_uno($documentos_id);
            $tipo = $this->Documentos_tipos_model->get_uno($documento['documentos_documentos_tipos_id']);
            $vigente = $this->Documentos_versiones_model->get_version_vigente_del_tipo_de_documento($documento['documentos_documentos_tipos_id']);
            $result = $this->db->set("documentos_documentos_versiones_id", $vigente['documentos_versiones_id'])
                    ->where("documentos_id", $documentos_id)
                    ->limit(1)
                    ->update("documentos");
            if ($result && $this->db->affected_rows() > 0) {
                $s['informacion'] = array(
                    'state' => 'success',
                    'message' => 'Se ha actualizado la versión del documento.'
                );
                $this->session->set_flashdata($s);
            }
            redirect($this->module['url'] . "/documento/" . $tipo['documentos_tipos_abreviacion']);
        }
    }

    function get_fecha_para_calendario() {
        $return = array(
            'success' => TRUE,
            'fechas_auditoria' => array(),
            'inhabiles' => $this->SAC_model->get_dias_inhabiles(),
            'festivos' => $this->SAC_model->get_dias_festivos(),
            'cumpleanos' => $this->SAC_model->get_cumpleaños(),
            'data' => array()
        );
        echo json_encode($return);
    }

}
