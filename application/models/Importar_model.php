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
        $batch_aa = $batch_aaf = $batch_aafRev1 = $batch_aafRev2 = array();
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
            $aa = array(
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
            $aaf = array(
                'auditorias_fechas_auditorias_id' => $a['idAuditoria'],
                'auditorias_fechas_etapa' => 0,
                'auditorias_fechas_inicio_programado' => $a['fechaIniAudit'],
                'auditorias_fechas_inicio_real' => $a['fechaIniReal'],
                'auditorias_fechas_fin_programado' => $a['fechaFinAudit'],
                'auditorias_fechas_fin_real' => $a['fechaFinAudit'],
                'auditorias_fechas_sello_orden_entrada' => $a['fechaSelloOEA'],
                'auditorias_fechas_vobo_jefe' => $a['fechaAprovacionJ'],
                'auditorias_fechas_vobo_subdirector' => $a['fechaAprovacionS'],
                'auditorias_fechas_vobo_director' => $a['fechaAprovacion'],
                'auditorias_fechas_lectura' => $a['fechaLectura'],
                'auditorias_fechas_oficio_envio_documentos' => $a['fechaOEDRes'],
                'auditorias_fechas_recibe_informacion' => $a['fechas_oficio_representante_designado'],
                'auditorias_fechas_sello_oficio_representante_designado' => $a['fechas_oficio_representante_designado'],
            );

            $aafRev1 = array(
                'auditorias_fechas_auditorias_id' => $a['idAuditoria'],
                'auditorias_fechas_etapa' => 1,
                'auditorias_fechas_inicio_programado' => $a['fechaIniAudit'],
                'auditorias_fechas_inicio_real' => $a['fechaIniRev1'],
                'auditorias_fechas_fin_programado' => $a['fechaFinAudit'],
                'auditorias_fechas_fin_real' => $a['fechaFinRev1'],
                'auditorias_fechas_sello_orden_entrada' => $a['fechaSelloOEA'],
                'auditorias_fechas_vobo_jefe' => $a['fechaAprovacionRev1J'],
                'auditorias_fechas_vobo_subdirector' => $a['fechaAprovacionRev1S'],
                'auditorias_fechas_vobo_director' => $a['fechaAprovacionRev1'],
                'auditorias_fechas_lectura' => $a['fechaLecturaRev1'],
                'auditorias_fechas_oficio_envio_documentos' => $a['fechaOEDRev1'],
                'auditorias_fechas_recibe_informacion' => $a['fechas_oficio_representante_designado'],
                'auditorias_fechas_sello_oficio_representante_designado' => $a['fechas_oficio_representante_designado'],
            );

            $aafRev2 = array(
                'auditorias_fechas_auditorias_id' => $a['idAuditoria'],
                'auditorias_fechas_etapa' => 2,
                'auditorias_fechas_inicio_programado' => $a['fechaIniAudit'],
                'auditorias_fechas_inicio_real' => $a['fechaIniRev2'],
                'auditorias_fechas_fin_programado' => $a['fechaFinAudit'],
                'auditorias_fechas_fin_real' => $a['fechaFinRev2'],
                'auditorias_fechas_sello_orden_entrada' => $a['fechaSelloOEA'],
                'auditorias_fechas_vobo_jefe' => $a['fechaAprovacionRev2J'],
                'auditorias_fechas_vobo_subdirector' => $a['fechaAprovacionRev2S'],
                'auditorias_fechas_vobo_director' => $a['fechaAprovacionRev2'],
                'auditorias_fechas_lectura' => $a['fechaLecturaRev2'],
                'auditorias_fechas_oficio_envio_documentos' => $a['fechaOEDRev2'],
                'auditorias_fechas_recibe_informacion' => $a['fechas_oficio_representante_designado'],
                'auditorias_fechas_sello_oficio_representante_designado' => $a['fechas_oficio_representante_designado'],
            );
            $result = $this->dbNuevoCYSA->insert('auditorias', $aa);
            $error = $this->dbNuevoCYSA->error();
            if (empty($error['error'])) {
                if (!empty($a['fechaIniReal'])) {
                    array_push($batch_aaf, $aaf);
                }
                if (!empty($a['fechaIniRev1'])) {
                    array_push($batch_aafRev1, $aafRev1);
                }
                if (!empty($a['fechaIniRev2'])) {
                    array_push($batch_aafRev2, $aafRev2);
                }
            } else {
                $errores[] = "Error en la auditoria con ID: " . $a['idAuditoria'] . "<br>";
                if ($flush) {
                    ob_flush();
                    flush();
                }
            }
        }
        $this->dbNuevoCYSA->insert_batch('auditorias_fechas', $batch_aaf);
        $this->dbNuevoCYSA->insert_batch('auditorias_fechas', $batch_aafRev1);
        $this->dbNuevoCYSA->insert_batch('auditorias_fechas', $batch_aafRev2);
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

}
