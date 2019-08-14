<?php

class Timeline extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = 'Línea de tiempo';
        $this->module['title_list'] = "Línea de tiempo de";
        $this->module['title_new'] = "Nuevo hito";
        $this->module['title_edit'] = "Editar hito";
        $this->module['title_delete'] = "Eliminar hito";
        $this->module["id_field"] = "configuraciones_id";
        $this->module['tabla'] = "configuraciones";
        $this->module['prefix'] = "conf";

        $this->rulesForm = array(
            array('field' => 'clientes_nombre', 'label' => 'nombre del cliente', 'rules' => 'required|trim',),
            array('field' => 'clientes_rfc', 'label' => 'RFC', 'rules' => 'required|trim|min_length[10]|max_length[14]|callback_validaRFC', 'errors' => array('is_unique' => 'Este %s ya ha sido capturado.')),
            array('field' => 'clientes_email', 'label' => 'Correo electrónico', 'rules' => 'required|trim|max_length[200]')
        );
        $this->_initialize();
    }

    function index($auditorias_id = NULL) {
        $auditorias_id = $this->Auditoria_model->get_real_auditoria($auditoria);
        $this->visualizar("timeline_view");
    }

    function visualizar($view = NULL, $data = array()) {
        $auditorias_id = 3;
        $proceso = $this->{$this->module['controller'] . "_model"}->get_proceso($auditorias_id);
        $auditorias_id = isset($this->session->auditoria['id_auditoria']) ? $this->session->auditoria['id_auditoria'] : 694;
        $tipoAuditoria = $this->db
                        ->select("tipo")
                        ->where("idAuditoria", $auditorias_id)
                        ->get("cat_auditoria")
                        ->row()->tipo;
        switch ($tipoAuditoria) {
            case 'SA':
                $this->db
                        ->select('fechaIniRev1 fechaIniAudit')
                        ->select('fechaFinRev1 fechaFinAudit')
                        ->select('fechaIniRealRev1 fechaIniReal')
                        //->select('DATE(FROM_UNIXTIME(fechaFinReal)) fechaFinReal')
                        ->select("fechaAprovacionRev1 fechaFinReal")
                        ->select('DATEDIFF(fechaIniRealRev1, fechaIniRev1) reprogramacion_inicio_dias_naturales')
                        ->select('DATEDIFF(fechaFinRealRev1, fechaFinRev1) reprogramacion_fin_dias_naturales');
                break;
            default:
                $this->db
                        ->select('DATE(FROM_UNIXTIME(fechaIniAudit)) fechaIniAudit')
                        ->select('DATE(FROM_UNIXTIME(fechaFinAudit)) fechaFinAudit')
                        ->select('DATE(FROM_UNIXTIME(fechaIniReal)) fechaIniReal')
                        //->select('DATE(FROM_UNIXTIME(fechaFinReal)) fechaFinReal')
                        ->select("fechaAprovacion fechaFinReal")
                        ->select('DATEDIFF(DATE(FROM_UNIXTIME(fechaIniReal)), DATE(FROM_UNIXTIME(fechaIniAudit))) reprogramacion_inicio_dias_naturales')
                        ->select('DATEDIFF(DATE(FROM_UNIXTIME(fechaFinReal)), DATE(FROM_UNIXTIME(fechaFinAudit))) reprogramacion_fin_dias_naturales');
                break;
        }
        $auditoria = $this->db
                ->select('idAuditoria')
                ->select("CONCAT(area,'/',tipo,'/',numero,'/',anio) AS nombreAuditoria")
                ->select("tipo, idEmpleado")
                ->where('idAuditoria', $auditorias_id)
                ->get("cat_auditoria")
                ->row_array();
        $etapas = $this->{$this->module['controller'] . "_model"}->get_etapas($proceso['procesos_id']);
        $tareas = $this->{$this->module['controller'] . "_model"}->get_tareas($auditorias_id, array_column($etapas, 'etapas_id'), $auditoria);
        $entregables = $this->{$this->module['controller'] . "_model"}->get_entregables(array_column($tareas, 'tareas_id'));
        $auditoria['reprogramacion_inicio_dias_habiles'] = getDiasHabiles($auditoria['fechaIniAudit'], $auditoria['fechaIniReal']);
        $auditoria['reprogramacion_fin_dias_habiles'] = getDiasHabiles($auditoria['fechaFinAudit'], $auditoria['fechaFinReal']);
        $lider = $this->Empleados_model->get_empleado($auditoria['idEmpleado']);
        $auditoria['lider'] = $lider['nombre'] . " " . $lider['aPaterno'] . " " . $lider['aMaterno'];
        $equipoArray = $this->Auditorias_model->get_equipo_auditoria($auditorias_id);
        $equipo = array();
        foreach ($equipoArray as $e) {
            $nombre = $e['nombre'] . " " . $e['aPaterno'] . " " . $e['aMaterno'];
            array_push($equipo, $nombre);
        }
        if (count($equipo) > 0) {
            $auditoria['equipo'] = $equipo;
        }

        $data = array(
            'procesos_id' => $proceso['procesos_id'],
            'etapas' => $etapas,
            'tareas' => $tareas,
            'entregables' => $entregables,
            'auditoria' => $auditoria
        );
        parent::visualizar($view, $data);
    }

    function guardar_fecha() {
        $auditorias_id = $this->input->post("auditorias_id");
        $configuraciones_id = $this->input->post("idConfiguraciones");
        $campo_ejecucion = $this->input->post("campoEjecucion");
        $fecha = $this->input->post("fecha");
        $fecha_alt = $this->input->post("fechaAlt") . ":00";

        $expresionRegular = '/(([A-z\d\-}]{1,})\.){2}([A-z\d-]){1,}/';
        preg_match($expresionRegular, $campo_ejecucion, $matches);
        $strSQL = "";
        $response = array();
        $auditoria = $this->Auditorias_model->get_auditoria($auditorias_id);

        if (count($matches) > 0) {
            list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
            $result = $this->db->where("auditorias_fechas_auditorias_id", $auditorias_id)->get("auditorias_fechas");
            if ($result && $result->num_rows() == 0) {
                $insert = array("auditorias_fechas_auditorias_id" => $auditorias_id);
                $this->db->insert("auditorias_fechas", $insert);
            }
            // En caso de ser la fecha de envio de documentos,
            // entonces calculamos la fecha de inicio de revisión de solventación programada (fechaIniRealRev1)
            if ($campo === "auditorias_fechas_oficio_envio_documentos") {
                $f = explode(" ", $fecha_alt);
                $g = explode("CYSA", $_SERVER['HTTP_REFERER']);
                $data['fOEDRes'] = $f[0];
                $data['idAuditoria'] = $auditorias_id;
                $url = $g[0] . 'CYSA/modelo/auditoria.php?t=' . time();
                $request = post_request($url, $data);
                if ($request !== "Fecha del sello del Oficio de Orden de Entrada de Auditor&iacute;a actualizada" && $request !== "Fecha del Sello del Env&iacute;o de Documentos actualizada") {
                    $response['campoEjecucion'] = "proto_cysa.cat_auditoria.fechaOEDRes";
                    $response['class'] = "danger";
                    $response['fecha'] = $fecha_alt;
                    $response['icon'] = "close";
//            $response['message'] = mysqlDate2OnlyDate($fecha_alt, TRUE);
                    $response['message'] = strip_tags($request);
                    $response['success'] = FALSE;
                    $return = $response;
                    echo json_encode($return);
                    die();
                }
                // Actualizamos EXPEDIENTES
                // Solo si no tuvo observaciones o todas fueron solventadas, entonces actulizamos
                $sinObservaciones = isset($auditoria['bSinObservacionAP']) && $auditoria['bSinObservacionAP'] == 1;
                $susRecomendacionesEstanTodasSolventadas = is_auditoria_solventada($auditorias_id);
                if ($sinObservaciones || $susRecomendacionesEstanTodasSolventadas) {
                    // Actualizamos fecha de cierre y la fecha de desclasificación
                    $DT_fecha = new DateTime($f[0]);
                    $DT_fecha->modify('+1 day'); // Agregamos un día natural
                    $fechaDesclasificacion = $DT_fecha->format("Y-m-d");
                    // Ejecutamos query
                    $strSQL = "UPDATE expedientes "
                            . "SET expedientes_fecha_cierre = '" . $f[0] . "', expedientes_fecha_desclasificacion = '" . $fechaDesclasificacion . "' "
                            . "WHERE expedientes_idAuditoria = " . $auditorias_id . " LIMIT 1";
                    $dbExpedientes = conectarBD(DB_PREFIX . "expedientes");
                    $dbExpedientes->ejecutaQuery($strSQL);
                }
            } elseif ($campo === "auditorias_fechas_solicitud_informacion") {
                // Actualizamos  fecha de apertura en el expediente
                $f = explode(" ", $fecha_alt);
                $strSQL = "UPDATE expedientes SET expedientes_fecha_apertura = '" . $f[0] . "' WHERE expedientes_idAuditoria = " . $auditorias_id . " LIMIT 1";
                $dbExpedientes = conectarBD(DB_PREFIX . "expedientes");
                $dbExpedientes->ejecutaQuery($strSQL);
            } elseif (strpos($campo, 'fechas_lectura') !== FALSE) { // en caso de ser alguna lectura, entonces actualizamos la hora en el documento del ARA/ARR y del citatorio
                list($fecha, $hora) = explode(" ", $fecha_alt);
                list($h, $m, $s) = explode(":", $hora);
                $valor = substr("00" . $h, -2) . ":" . $m;
                $etapa = $this->Auditorias_model->get_etapa_de_auditoria($auditorias_id);
                if ($etapa == AUDITORIA_ETAPA_AP) {
                    $documento = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, TIPO_DOCUMENTO_ACTA_RESULTADOS_AUDITORIA);
                } else {
                    $documento = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, TIPO_DOCUMENTO_ACTA_RESULTADOS_REVISION);
                }
                // Actualizamos ARA o ARR
                if (!empty($documento)) {
                    foreach ($documento as $d) {
                        if (isset($d['documentos_id']) && !empty($d['documentos_id'])) {
                            $constante = $etapa == AUDITORIA_ETAPA_AP ? 33 : 74; // Hora del ARA/ARR
                            $strSQL = "INSERT INTO documentos_valores VALUES(" . $constante . ", " . $d['documentos_id'] . ", '" . $valor . "') ON DUPLICATE KEY UPDATE valor='" . $valor . "'";
                            $this->db->query($strSQL);
                        }
                    }
                }

                // Actualizamos citatorios
                $listaOficiosCitatorio = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, TIPO_DOCUMENTO_CITATORIO);
                if ($auditoria['auditorias_anio'] < 2018) {
                    foreach ($listaOficiosCitatorio as $key => $oficio) {
                        foreach ($listaOficiosCitatorioFiltro as $key2 => $filtro) {
                            if ($oficio['idDocto'] == $filtro['idDocto']) {
                                unset($listaOficiosCitatorio[$key]);
                            }
                        }
                    }
                }
                foreach ($listaOficiosCitatorio as $d) {
                    if (isset($d['documentos_id']) && !empty($d['documentos_id'])) {
                        $constante = 217; // Hora
                        $strSQL = "INSERT INTO documentos_valores VALUES(" . $constante . ", " . $d['documentos_id'] . ", '" . $valor . "') ON DUPLICATE KEY UPDATE valor='" . $valor . "'";
                        $dbCYSA->ejecutaQuery($strSQL);
                        $valor_fecha = mysqlDate2Date($fecha);
                        $constante = 218; // Fecha
                        $strSQL = "INSERT INTO documentos_valores VALUES(" . $constante . ", " . $d['documentos_id'] . ", '" . $valor_fecha . "') ON DUPLICATE KEY UPDATE valor='" . $valor_fecha . "'";
                        $dbCYSA->ejecutaQuery($strSQL);
                    }
                }
            }
            $result = $this->db->set($campo, $fecha_alt)
                    ->where("auditorias_fechas_auditorias_id", $auditorias_id)
                    ->update($basedatos . "." . $tabla);
            $return['success'] = $result;
            $return['message'] = ($this->Timeline_model->get_tipo_campo_mysql($matches[0]) === "DATETIME") ? mysqlDate2Date($fecha_alt, FALSE) : mysqlDate2OnlyDate($fecha_alt, TRUE);
            $return['fecha'] = $fecha_alt;
            $return['campoEjecucion'] = $campo_ejecucion;
            // Si la tarea es diferente a la de actualizar la fecha en que se recibe
            // la información del área auditada
            if (isset($configuraciones_id) && $configuraciones_id != "00") {
                // Obtengo la fecha programada
                $fecha_programada = $this->Timeline_model->get_fecha_programada_de_tarea($auditorias_id, $configuraciones_id);
                // Verificamos si la fecha programa requiere re-programación
                $res = $this->db->select("configuraciones_fecha_reprogramada")
                        ->where("configuraciones_id", $configuraciones_id)
                        ->limit(1)
                        ->get("configuraciones");
                if ($res && $res->num_rows() > 0) {
                    $tarea = mysql_fetch_array($res);
                    $diferencia_reprogramacion = $this->Timeline_model->get_diferencia_de_reprogramacion($auditorias_id, $tarea['configuraciones_fecha_reprogramada']);
                    // Calculamos la fecha reprogramada
                    if ($diferencia_reprogramacion != 0) {
                        $fecha_programada = agregar_dias($fecha_programada, $diferencia_reprogramacion);
                    }
                }
                // Esta validación sirve para mostrar correctamente el icono y color de la tarea,
                // ya que la tarea "Convocar revisión de avances con el área auditada" tiene como fecha
                // programa un intervalo de fechas, por lo tanto a la fecha programa establecida se le añaden 4 días
                // para que se considere el intervalo de tiempo
                if ($campo === "fechas_revision_avances_auditoria") {
                    $fecha_programada = agregar_dias($fecha_programada, 4);
                }
                $return['icon'] = $this->Timeline_model->get_icono_de_timeline($this->Timeline_model->parseDatetime2Date($fecha_programada), $this->Timeline_model->parseDatetime2Date($fecha_alt), $auditorias_id);
                $return['class'] = $this->Timeline_model->get_clase_de_timeline($this->Timeline_model->parseDatetime2Date($fecha_programada), $this->Timeline_model->parseDatetime2Date($fecha_alt));
                $dias_habiles = getDiasHabiles($this->Timeline_model->parseDatetime2Date($fecha_programada), $this->Timeline_model->parseDatetime2Date($fecha_alt));
                $plural = ($dias_habiles > 1 ? TRUE : FALSE);
                if ($dias_habiles > 0) {
                    $return['message_retraso'] = 'Esta acci&oacute;n se realiz&oacute; con <strong class="text text-danger" data-toggle="tooltip" title="Se debi&oacute;realizar a m&aacute;s tardar el<br>' . mysqlDate2OnlyDate($fecha_programada) . '">' . $dias_habiles . ' d&iacute;a' . ($plural ? 's' : '') . ' h&aacute;bil' . ($plural ? 'es' : '') . ' de atraso</strong>';
                }
            } else {
                $return['icon'] = "flag";
                $return['class'] = "purple-darker";
            }
        } else {
            $return['success'] = FALSE;
            $return['message'] = "Error con el nombre del campo de ejecuci&oacute;n.";
        }

        echo json_encode($return);
    }

}
