<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Importar_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->model_name = __CLASS__;
        $config['hostname'] = 'localhost';
        $config['username'] = 'root';
        $config['password'] = '1234';
        $config['database'] = 'proto_cysa';
        $config['dbdriver'] = 'mysqli';
        $config['dbprefix'] = '';
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = '';
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';

        ///////////////////////////////////
        $config['database'] = 'proto_sac';
        $this->dbProtoSAC = $this->load->database($config, TRUE);
        /// CYSA
        $config['database'] = 'proto_cysa';
        $this->dbProtoCYSA = $this->load->database($config, TRUE);

        // nuevo_cysa
        $config['database'] = 'nuevo_cysa';
        $this->dbNuevoCYSA = $this->load->database($config, TRUE);

        // nuevo_cysa
        $config['database'] = 'nuevo_sac';
        $this->dbNuevoSAC = $this->load->database($config, TRUE);
    }

    function importar_documentos_tipos($flush = FALSE) {
        $this->dbNuevoCYSA->truncate("documentos_tipos");
        // Primero las UA centralizadas
        $data = $this->dbProtoCYSA
                ->order_by("idTipoDocto", 'ASC')
                ->get("cat_documentos")
                ->result_array();
        $ahora = ahora();
        $batch = array();
        foreach ($data as $d) {
            $insert = array(
                'documentos_tipos_id' => NULL,
                'documentos_tipos_codigo' => $d['codigo'],
                'documentos_tipos_nombre' => $d['descDocto'],
                'documentos_tipos_abreviacion' => NULL,
                'fecha_insert' => $ahora,
                'fecha_update' => NULL,
                'fecha_delete' => NULL
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('documentos_tipos', $batch);
        $update = array(
            array('documentos_tipos_id' => 3, 'documentos_tipos_abreviacion' => 'ARA'),
            array('documentos_tipos_id' => 4, 'documentos_tipos_abreviacion' => 'ORP'),
            array('documentos_tipos_id' => 5, 'documentos_tipos_abreviacion' => 'ARR'),
            array('documentos_tipos_id' => 6, 'documentos_tipos_abreviacion' => 'ARIC'),
            array('documentos_tipos_id' => 7, 'documentos_tipos_abreviacion' => 'ARM'),
            array('documentos_tipos_id' => 8, 'documentos_tipos_abreviacion' => 'ARRM'),
            array('documentos_tipos_id' => 9, 'documentos_tipos_abreviacion' => 'OSI'),
            array('documentos_tipos_id' => 10, 'documentos_tipos_abreviacion' => 'OE|OA'),
            array('documentos_tipos_id' => 11, 'documentos_tipos_abreviacion' => 'ACEI'),
            array('documentos_tipos_id' => 12, 'documentos_tipos_abreviacion' => 'OC'),
            array('documentos_tipos_id' => 18, 'documentos_tipos_abreviacion' => 'OED'),
            array('documentos_tipos_id' => 21, 'documentos_tipos_abreviacion' => 'AA'),
            array('documentos_tipos_id' => 29, 'documentos_tipos_abreviacion' => 'AIA'),
            array('documentos_tipos_id' => 31, 'documentos_tipos_abreviacion' => 'RAP'),
        );
        $this->dbNuevoCYSA->update_batch('documentos_tipos', $update, 'documentos_tipos_id');
        $return = "Catálogo de documentos importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_documentos_constantes($flush = FALSE) {
        $this->dbNuevoCYSA->truncate("documentos_constantes");
        // Primero las UA centralizadas
        $data = $this->dbProtoCYSA
                ->order_by("idTipoDocto", 'ASC')
                ->get("cat_documentos_detalle")
                ->result_array();
        $batch = array();
        foreach ($data as $d) {
            $insert = array(
                'documentos_constantes_id' => NULL,
                'documentos_constantes_documentos_tipos_id' => $d['idTipoDocto'],
                'documentos_constantes_nombre' => $d['denParrafo'],
                'documentos_constantes_descripcion' => $d['descParrafo'],
                'fecha_insert' => ahora(),
                'fecha_update' => NULL,
                'fecha_delete' => NULL
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('documentos_constantes', $batch);
        $return = "Catálogo de detalles de documentos importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_versiones($flush = FALSE) {
        $this->dbNuevoCYSA->truncate("documentos_versiones");
        // Primero las UA centralizadas
        $data = $this->dbProtoCYSA
                ->order_by("idTipoDocto", 'ASC')
                ->get("cat_documentos_versiones")
                ->result_array();
        $batch = array();
        foreach ($data as $d) {
            $insert = array(
                'documentos_versiones_id' => NULL,
                'documentos_versiones_documentos_tipos_id' => $d['idTipoDocto'],
                'documentos_versiones_numero_iso' => $d['versionISO'],
                'documentos_versiones_prefijo_iso' => $d['prefijoISO'],
                'documentos_versiones_codigo_iso' => $d['codigoISO'],
                'documentos_versiones_is_vigente' => $d['bVigente'],
                'documentos_versiones_archivo_registro' => $d['archivo_registro'],
                'documentos_versiones_archivo_impresion' => $d['archivo_impresion'],
                'fecha_insert' => ahora(),
                'fecha_update' => NULL,
                'fecha_delete' => NULL
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('documentos_versiones', $batch);
        $return = "Catálogo de versiones de documentos importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_auditorias($flush = FALSE) {
        $errores = array();
        $auditorias = $this->dbProtoCYSA
                ->select("*")
                ->select("FROM_UNIXTIME(a.fechaIniAudit,'%Y-%m-%d') as 'fechaIniAudit'")
                ->select("FROM_UNIXTIME(a.fechaIniReal,'%Y-%m-%d') as 'fechaIniReal'")
                ->select("FROM_UNIXTIME(a.fechaFinAudit,'%Y-%m-%d') as 'fechaFinAudit'")
                ->select("FROM_UNIXTIME(a.fechaFinReal,'%Y-%m-%d') as 'fechaFinReal'")
                ->join("cat_auditoria_fechas caf", "caf.idAuditoria = a.idAuditoria", "INNER")
                ->where("a.anio >", 2014)
                ->get("cat_auditoria a")
                ->result_array();
        $this->dbNuevoCYSA->truncate("auditorias");
        $this->dbNuevoCYSA->truncate("auditorias_fechas");
        $aux = $this->dbNuevoCYSA->get("auditorias_areas")->result_array();
        $areas = array_column($aux, 'auditorias_areas_siglas', 'auditorias_areas_id');
        $aux = $this->dbNuevoCYSA->get("auditorias_tipos")->result_array();
        $tipos = array_column($aux, 'auditorias_tipos_siglas', 'auditorias_tipos_id');
        $batch_auditorias = $batch_auditorias_fechas = array();
        foreach ($auditorias as $a) {
            $cc = $this->SAC_model->get_cc_por_etiquetas(2, $a['clv_dir'], $a['clv_subdir'], $a['clv_depto']);
            if (empty($cc)) {
                $cc = array(
                    'cc_id' => NULL,
                    'cc_periodos_id' => 2,
                    'cc_direcciones_id' => NULL,
                    'cc_subdirecciones_id' => NULL,
                    'cc_departamentos_id' => NULL
                );
                $aux = "La auditoría ID: " . $a['idAuditoria'] . " no se encontró el CC (" . implode(",", array($a['clv_dir'], $a['clv_subdir'], $a['clv_depto'])) . ").<br>";
                $errores[] = $aux;
                if ($flush) {
                    echo $aux . "<br>";
                    ob_flush();
                    flush();
                }
            }
            $e = $this->SAC_model->get_empleado($a['idEmpleado'], TRUE, TRUE);
            $ed = $this->SAC_model->get_empleado($a['encargadoAudit'], TRUE, TRUE);
            $empleados_id = $enlace_designado = NULL;
            if (!empty($e)) {
                $empleados_id = $e['empleados_id'];
            }
            if (!empty($ed)) {
                $enlace_designado = $ed['empleados_id'];
            }
            $ahora = date("Y-m-d H:i:s");
            $insert = array(
                'auditorias_id' => $a['idAuditoria'],
                'auditorias_origen_id' => $a['idAuditoriaOrigen'],
                'auditorias_area' => array_search($a['area'], $areas),
                'auditorias_tipo' => array_search($a['tipo'], $tipos),
                'auditorias_numero' => $a['numero'],
                'auditorias_anio' => $a['anio'],
                'auditorias_is_programada' => $a['auditProgramada'],
                'auditorias_segundo_periodo' => $a['segundoPeriodo'],
                'auditorias_cc_id' => $cc['cc_id'],
                'auditorias_periodos_id' => $cc['cc_periodos_id'],
                'auditorias_direcciones_id' => $cc['cc_direcciones_id'],
                'auditorias_subdirecciones_id' => $cc['cc_subdirecciones_id'],
                'auditorias_departamentos_id' => $cc['cc_departamentos_id'],
                'clv_dir' => $a['clv_dir'],
                'clv_subdir' => $a['clv_subdir'],
                'clv_depto' => $a['clv_depto'],
                'auditorias_rubro' => $a['rubroAudit'],
                'auditorias_objetivo' => $a['objetivoAudit'],
                'auditorias_alcance' => $a['alcance'],
                'auditorias_auditor_lider' => $empleados_id,
                'auditorias_status_id' => ($a['statusAudit'] >= 0 ? $a['statusAudit'] : 6),
                'auditorias_enlace_designado' => $enlace_designado,
                'auditorias_is_sin_observaciones' => $a['bSinObservacionAP'],
                'auditorias_folio_oficio_representante_designado' => $a['folio_oficio_representante_designado'],
                'auditorias_notificacion_correo_electronico' => $a['bNotificacionIniCorreo'],
                'fecha_insert' => date("Y-m-d H:i:s"),
                'fecha_update' => NULL,
                'fecha_delete' => NULL,
            );
            array_push($batch_auditorias, $insert);

            $insert = array(
                'auditorias_fechas_auditorias_id' => $a['idAuditoria'],
                // *********  ETAPA AUDITORIA PROGRAMADA  ********* */
                'auditorias_fechas_inicio_programado' => $a['fechaIniAudit'],
                'auditorias_fechas_inicio_real' => $a['fechaIniReal'],
                'auditorias_fechas_fin_programado' => $a['fechaFinAudit'],
                'auditorias_fechas_fin_real' => $a['fechaFinAudit'],
                'auditorias_fechas_sello_orden_entrada' => $a['fechaSelloOEA'],
                'auditorias_fechas_solicitud_informacion' => $a['fechas_envios_osi'],
                'auditorias_fechas_sello_oficio_representante_designado' => $a['fechas_oficio_representante_designado'],
                'auditorias_fechas_acei' => $a['fechas_acei'],
                'auditorias_fechas_vobo_jefe' => $a['fechaAprovacionJ'],
                'auditorias_fechas_vobo_subdirector' => $a['fechaAprovacionS'],
                'auditorias_fechas_vobo_director' => $a['fechaAprovacion'],
                'auditorias_fechas_citatorio' => $a['fechas_envio_citatorio'],
                'auditorias_fechas_lectura' => $a['fechaLectura'],
                'auditorias_fechas_oficio_envio_documentos' => $a['fechaOEDRes'],
                'auditorias_fechas_envio_evaluacion_general' => $a['fechas_evaluacion_general'],
                // *********  ETAPA DE PRIMERA REVISION (Para auditorías menores a 2018)  *********
                // *********  ETAPA DE SOLVENTACIÓN  *********
                'auditorias_fechas_recibir_informacion_etapa_1' => $a['fechaRecibeInfoRev1'],
                'auditorias_fechas_limite_recibir_informacion_etapa_1' => $a['fLimiteInfoRev1'],
                'auditorias_fechas_inicio_programado_etapa_1' => $a['fechaIniRev1'],
                'auditorias_fechas_inicio_real_etapa_1' => $a['fechaIniRealRev1'],
                'auditorias_fechas_fin_programado_etapa_1' => $a['fechaFinRev1'],
                'auditorias_fechas_fin_real_etapa_1' => $a['fechaFinRealRev1'],
                'auditorias_fechas_vobo_jefe_etapa_1' => $a['fechaAprovacionRev1J'],
                'auditorias_fechas_vobo_subdirector_etapa_1' => $a['fechaAprovacionRev1S'],
                'auditorias_fechas_vobo_director_etapa_1' => $a['fechaAprovacionRev1'],
                'auditorias_fechas_citatorio_etapa_1' => $a['fechas_envio_citatorioRev1'],
                'auditorias_fechas_lectura_etapa_1' => $a['fechaLecturaRev1'],
                'auditorias_fechas_oficio_envio_documentos_etapa_1' => $a['fechaOEDRev1'],
                // *********  ETAPA DE SEGUNDA REVISIÓN (Para auditorías menores a 2018)  ********* */
                'auditorias_fechas_recibir_informacion_etapa_2' => $a['fechaRecibeInfoRev2'],
                'auditorias_fechas_limite_recibir_informacion_etapa_2' => $a['fLimiteInfoRev2'],
                'auditorias_fechas_inicio_programado_etapa_2' => $a['fechaIniRev2'],
                'auditorias_fechas_inicio_real_etapa_2' => $a['fechaIniRealRev2'],
                'auditorias_fechas_fin_programado_etapa_2' => $a['fechaFinRev2'],
                'auditorias_fechas_fin_real_etapa_2' => $a['fechaFinRealRev2'],
                'auditorias_fechas_acei_etapa_2' => NULL,
                'auditorias_fechas_vobo_jefe_etapa_2' => $a['fechaAprovacionRev2J'],
                'auditorias_fechas_vobo_subdirector_etapa_2' => $a['fechaAprovacionRev2S'],
                'auditorias_fechas_vobo_director_etapa_2' => $a['fechaAprovacionRev2'],
                'auditorias_fechas_citatorio_etapa_2' => NULL,
                'auditorias_fechas_lectura_etapa_2' => $a['fechaLecturaRev2'],
                'auditorias_fechas_oficio_envio_documentos_etapa_2' => $a['fechaOEDRev2'],
                // *********  OTROS  ********* */
                'auditorias_fechas_compromiso_observaciones' => $a['fechasCompromisoObservaciones'],
                // ***************************** */
                'fecha_insert' => $ahora,
                'fecha_update' => NULL,
                'fecha_delete' => NULL,
            );
            array_push($batch_auditorias_fechas, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('auditorias', $batch_auditorias);
        $this->dbNuevoCYSA->insert_batch('auditorias_fechas', $batch_auditorias_fechas);
        $return = "Auditorías importadas.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_equipos_trabajo($flush = FALSE) {
        $this->dbNuevoCYSA->truncate("auditorias_equipo");

        $data = $this->dbProtoCYSA
                ->where("idAuditoria >=", 639)
                ->order_by("idAuditoria", 'ASC')
                ->get("cat_auditoria_equipo")
                ->result_array();
        $batch = array();
        foreach ($data as $d) {
            $e = $this->SAC_model->get_empleado($d['idEmpleado'], TRUE, TRUE);
            $insert = array(
                'auditorias_equipo_auditorias_id' => $d['idAuditoria'],
                'auditorias_equipo_empleados_id' => $e['empleados_id'],
                'auditorias_equipo_tipo' => $d['tipoU'],
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('auditorias_equipo', $batch);
        $return = "Equipos de trabajo importados.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_involucrados($flush = FALSE) {
        $this->dbNuevoCYSA->truncate("auditorias_involucrados");
        // Primero las UA centralizadas
        $data = $this->dbProtoCYSA
                ->where("idAuditoria >", 638)
                ->order_by("idAuditoria", 'ASC')
                ->get("cat_auditoria_involucrados")
                ->result_array();
        $batch = array();
        foreach ($data as $d) {
            $e = $this->SAC_model->get_empleado($d['idEmpleado'], TRUE);
            $empleados_id = NULL;
            if (!empty($e)) {
                $empleados_id = $e['empleados_id'];
            }
            $insert = array(
                'auditorias_involucrados_auditorias_id' => $d['idAuditoria'],
                'auditorias_involucrados_empleados_id' => $empleados_id,
                'auditorias_involucrados_declara_en_ap' => $d['declara_ap'],
                'auditorias_involucrados_declara_en_rev1' => $d['declara_rev1'],
                'auditorias_involucrados_declara_en_rev2' => $d['declara_rev2'],
                'auditorias_involucrados_asistio_en_ap' => $d['bAsistencia_ap'],
                'auditorias_involucrados_asistio_en_rev1' => $d['bAsistencia_rev1'],
                'auditorias_involucrados_asistio_en_rev2' => $d['bAsistencia_rev2'],
                'fecha_insert' => ahora(),
                'fecha_update' => NULL,
                'fecha_delete' => NULL
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('auditorias_involucrados', $batch);
        $return = "Catálogo de involucrados en la auditoría.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_observaciones($flush = FALSE) {
        $this->dbNuevoCYSA->truncate('observaciones');
        $data = $this->dbProtoCYSA
                ->where("idAuditoria >=", 639)
                ->order_by('idAuditoria', 'ASC')
                ->get('observaciones')
                ->result_array();
        $ahora = ahora();
        $batch = array();
        foreach ($data as $d) {
            $insert = array(
                'observaciones_id' => $d['idObservacion'],
                'observaciones_auditorias_id' => $d['idAuditoria'],
                'observaciones_numero' => $d['numObservacion'],
                'observaciones_titulo' => $d['denObservacion'],
                'observaciones_descripcion' => $d['descObservacion'],
                'observaciones_comentarios' => $d['comentario'],
                'observaciones_has_anexos' => $d['bAnexo'],
                'observaciones_is_eliminada' => $d['bEliminada'],
                'fecha_insert' => ahora(),
                'fecha_delete' => ($d['bEliminada'] == 1 ? $ahora : NULL),
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('observaciones', $batch);
        $return = "Catálogo de observaciones y recomendaciones importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_recomendaciones($flush = FALSE) {
        $this->dbNuevoCYSA->truncate('recomendaciones');
        $data = $this->dbProtoCYSA
                ->order_by("idObservacion", "ASC")
                ->get("recomendacion")
                ->result_array();
        $ahora = ahora();
        $batch = array();
        foreach ($data as $d) {
            $empleados_id = NULL;
            $e = $this->SAC_model->get_empleado($d['idEmpleado'], TRUE, TRUE);
            if (!empty($e) && isset($e['empleados_id'])) {
                $empleados_id = $e['empleados_id'];
            }
            $insert = array(
                'recomendaciones_id' => $d['idRecomendacion'],
                'recomendaciones_numero' => $d['numRecomendacion'],
                'recomendaciones_observaciones_id' => $d['idObservacion'],
                'recomendaciones_clasificaciones_id' => $d['idClasificacion'],
                'recomendaciones_status_id' => $d['idEstatus'],
                'recomendaciones_empleados_id' => $empleados_id,
                'recomendaciones_descripcion' => $d['descRecomendacion'],
                'fecha_insert' => $ahora
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('recomendaciones', $batch);
        $return = "Catálogo de recomendaciones importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_avances_recomendaciones($flush = FALSE) {
        $this->dbNuevoCYSA->truncate('recomendaciones_avances');
        $data = $this->dbProtoCYSA
                ->get("revision_recomendacion")
                ->result_array();
        $ahora = ahora();
        $batch = array();
        foreach ($data as $d) {
            $empleados_id = NULL;
            $e = $this->SAC_model->get_empleado($d['idResponsable'], TRUE, TRUE);
            if (!empty($e) && isset($e['empleados_id'])) {
                $empleados_id = $e['empleados_id'];
            }
            $insert = array(
                'recomendaciones_avances_numero_revision' => $d['numRevision'],
                'recomendaciones_avances_recomendaciones_id' => $d['idRecomendacion'],
                'recomendaciones_avances_recomendaciones_clasificaciones_id' => $d['idClasificacion'],
                'recomendaciones_avances_recomendaciones_status_id' => $d['idEstatus'],
                'recomendaciones_avances_empleados_id' => $empleados_id,
                'recomendaciones_avances_descripcion' => $d['avance'],
                'fecha_insert' => $ahora
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('recomendaciones_avances', $batch);
        $return = "Catálogo de avances de recomendaciones importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function importar_asistencias($flush = FALSE) {
        $this->dbNuevoCYSA->truncate('asistencias');
        $data = $this->dbProtoCYSA
                ->where('iddocto >', 0)
                ->get('cat_asistencia_acei')
                ->result_array();
        $ahora = ahora();
        $batch = array();
        foreach ($data as $d) {
            $e = $this->SAC_model->get_empleado($d['idEmpleado'], TRUE);
            $empleados_id = NULL;
            if (!empty($e)) {
                $empleados_id = $e['empleados_id'];
            }
            $insert = array(
                'asistencias_documentos_id' => $d['iddocto'],
                'asistencias_empleados_id' => $empleados_id,
                'asistencias_tipo' => $d['asiste']
            );
            array_push($batch, $insert);
        }
        $this->dbNuevoCYSA->insert_batch('asistencias', $batch);
        $return = "Catálogo de asistencias de documentos importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    function get_documento_de_auditoria($auditorias_id, $documentos_tipos_id) {
        $return = array();
        if (!empty($auditorias_id) && !empty($documentos_tipos_id)) {
            $result = $this->dbProtoCYSA->select("d.*")
                    ->join("documentos_html h", "h.idDocto = d.idDocto", "LEFT")->select("h.contenido")
                    ->where('idAuditoria', $auditorias_id)
                    ->where('idTipoDocto', $documentos_tipos_id)
                    ->where('bCancelado', 0)
                    ->get("documentos d");
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function get_html_de_documento($idDocto) {
        $return = array();
        if (!empty($idDocto)) {
            $result = $this->dbProtoCYSA
                    ->where('idDocto', $idDocto)
                    ->limit(1)
                    ->get("documentos_html");
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function importar_documentos($flush = FALSE) {
        $this->dbNuevoCYSA->truncate('documentos');
        $this->dbNuevoCYSA->truncate('documentos_valores');
        $data = $this->dbProtoCYSA
                ->where("idAuditoria >=", 639)
                ->get("documentos")
                ->result_array();
        $tabla = $this->equivalencias_detalles_valores();
        $ahora = ahora();
        $batch = $batch_valores = array();
        foreach ($data as $d) {
            $insert = array(
                'documentos_id' => $d['idDocto'],
                'documentos_documentos_tipos_id' => $d['idTipoDocto'],
                'documentos_documentos_versiones_id' => $d['idVersion'],
                'documentos_periodos_id' => 2,
                'documentos_auditorias_id' => $d['idAuditoria'],
                'documentos_logotipos_id' => 2,
                'documentos_misiones_id' => 1,
                'documentos_is_cancelado' => $d['bCancelado'],
                'documentos_is_aprobado' => $d['bAprovado'],
                'fecha_insert' => $d['fechaCreacion']
            );
            array_push($batch, $insert);
            // DETALLES
            $detalles = $this->dbProtoCYSA
                    ->select("dd.*")
                    ->where('idDocto', $d['idDocto'])
                    ->join("cat_documentos_detalle cdd", "cdd.idParrafo = dd.idParrafo", "INNER")->select("cdd.denParrafo")
                    ->get("documentos_detalle dd")
                    ->result_array();
            foreach ($detalles as $dd) {
                $tipo = $d['idTipoDocto'];
                $c = $dd['idParrafo'];

                if (!isset($tabla[$tipo][$c])) {
                    //var_dump($dd['idDocto'], $tipo, $c);
                } else {
                    $insert_valores = array(
                        'documentos_valores_documentos_constantes_id' => $tabla[$tipo][$c],
                        'documentos_valores_documentos_id' => $dd['idDocto'],
                        'documentos_valores_valor' => trim($dd['valor'])
                    );
                    array_push($batch_valores, $insert_valores);
                }
            }
        }

        $this->dbNuevoCYSA->insert_batch('documentos', $batch);
        $this->dbNuevoCYSA->insert_batch('documentos_valores', $batch_valores);
        $return = "Catálogo de documentos importado.";
        if ($flush) {
            echo $return . "<br>";
            ob_flush();
            flush();
            $return = TRUE;
        }
        return $return;
    }

    private function equivalencias_detalles_valores() {
        $return = array();
        $data = $this->dbNuevoCYSA
                ->select("cdd.idTipoDocto,c.documentos_constantes_id, cdd.idParrafo, cdd.denParrafo")
                ->join("proto_cysa.cat_documentos_detalle cdd", "cdd.denParrafo LIKE c.documentos_constantes_nombre AND c.documentos_constantes_documentos_tipos_id=cdd.idTipoDocto", "LEFT")
                ->order_by("cdd.idTipoDocto", "ASC")
                ->order_by("cdd.idParrafo", "ASC")
                ->get("documentos_constantes c")
                ->result_array();
        foreach ($data as $d) {
            $tipos_documento_id = intval($d['idTipoDocto']);
            if (!isset($return[$tipos_documento_id])) {
                $return[$tipos_documento_id] = array();
            }
            $return[$tipos_documento_id][$d['idParrafo']] = intval($d['documentos_constantes_id']);
        }
        return $return;
    }

}
