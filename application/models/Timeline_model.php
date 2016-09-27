<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Timeline_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = "procesos";
        $this->id_field = "procesos_id";
        $this->table_prefix = "p";
        $this->model_name = "Timeline_model";
    }

    function record_count() {
        return 0;
    }

    function getResultados($limit, $start) {
        //parent::getResultados($limit, $start);
        return array();
    }

    function getDatabase($dbName) {
        if ($dbName == "timeline") {
            $db = array(
                'dsn' => '',
                'hostname' => 'localhost',
                'username' => 'root',
                'password' => '',
                'database' => 'timeline',
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => FALSE,
                'db_debug' => TRUE,
                'cache_on' => FALSE,
                'cachedir' => '',
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci',
                'swap_pre' => '',
                'encrypt' => FALSE,
                'compress' => FALSE,
                'stricton' => FALSE,
                'failover' => array(),
                'save_queries' => TRUE
            );
            $CI = &get_instance();
            return $CI->load->database($db, TRUE);
        } else {
            return parent::getDatabase($dbName);
        }
    }

    /**
     * Esta función permite obtener información de un proceso
     * @param mixed $idAuditoria Identificador al cual pertenece una auditoría
     * @return array Arreglo con la información correspondiente al proceso de la auditoría
     */
    function get_proceso($idAuditoria = NULL) {
        $return = array();
        if (!empty($idAuditoria)) {
            $dbTL = $this->getDatabase('timeline');
            $res = $dbTL->where("procesos_id", $idAuditoria)->get("procesos");
            if ($res->num_rows() == 1) {
                $return = $res->row_array();
            }
        }

        return $return;
    }

    /**
     * Esta función permite obtener las etapas correspondientes a un procreso de auditoría
     * @param mixed $idProceso Identificador del proceso
     * @return array Las etapas que corresponden al proceso
     */
    function get_etapas($idProceso = NULL) {
        $return = array();
        if (!empty($idProceso)) {
            $dbTL = $this->getDatabase('timeline');
            $res = $dbTL->where("etapas_procesos_id", $idProceso)->get("etapas");
            if ($res->num_rows() > 0) {
                $return = $res->result_array();
            }
        }
        return $return;
    }

    /**
     * Esta función permite obtener las tareas de un etapa
     * @param integer $idAuditoria Identificador de la auditoría
     * @param integer $idEtapa Identificador de la etapa
     * @param array $auditoria Array con información de la auditoría
     * @return array Esta función regresa un arreglo con la información de las tareas de una etapa
     */
    function get_tareas($idAuditoria = NULL, $idEtapa = NULL, $auditoria = array()) {
        $return = array();
//        $fechaInicioAuditoria = new DateTime($auditoria['fechaIniAudit']);
//        $fechaFinAuditoria = new DateTime($auditoria['fechaFinAudit']);
        $fechaInicioAuditoriaReal = new DateTime($auditoria['fechaIniReal']);
        $fechaFinAuditoriaReal = new DateTime($auditoria['fechaFinReal']);

        // Expresión regular para obtener la la estructura el nombre de un campo que siempre viene de la siguiente forma
        // {BASEDATO} . {TABLA} . {CAMPO}
        $expresionRegular = '/(([A-z\d\-}]{1,})\.){2}([A-z\d-]){1,}/';

        $hoy = new DateTime("now");
        if (!empty($idEtapa)) {
            $dbTL = $this->getDatabase('timeline');
            if (is_array($idEtapa)) {
                $dbTL->where_in("tareas_etapas_id", $idEtapa);
            } else {
                $dbTL->where('tareas_etapas_id', $idEtapa);
            }
            $res = $dbTL
                    ->where("tareas_activo", TRUE)
                    ->order_by('tareas_orden_ejecucion', 'DESC')
                    ->get("tareas");
            if ($res->num_rows() > 0) {
                $return = $res->result_array();
                foreach ($res->result_array() as $index => $r) {
                    $return[$index]['class'] = 'default';
                    $return[$index]['icon'] = 'more_horiz'; // more_horiz  more_vert  hourglass_empty

                    if (strtotime($auditoria['fechaFinReal']) > 0) {
                        $return[$index]['class'] = 'success';
                        $return[$index]['icon'] = 'check'; // more_horiz  more_vert  hourglass_empty
                    }

                    $fechaProgramada = NULL;
                    if (!empty($r['tareas_fecha_programada'])) {
                        // Obtengo el campo real del cual se obtendrá el valor
                        //$return[$index]['campo_programada'] = $r['tareas_fecha_programada'];
                        if (!is_null($r['tareas_fecha_programada'])) {
                            preg_match($expresionRegular, $r['tareas_fecha_programada'], $matches);
                            if (count($matches) > 0) {
                                list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
                                $return[$index]['campo_programada_real'] = implode(".", array($basedatos, $tabla, $this->get_campo_dependiendo_de_tipo_auditoria($auditoria['tipo'], $campo)));
                            }
                        }
                        $duracion = $return[$index]['duracion'] = intval($r['tareas_duracion']);
                        $select = "DATE(" . $return[$index]['tareas_fecha_programada'] . ")";
                        $fechaProgramada = $this->db->select($select . " AS fecha_programada", FALSE)->where('idAuditoria', $idAuditoria, FALSE)->get($basedatos . "." . $tabla)->row()->fecha_programada;
                        if (!is_null($fechaProgramada)) {
                            $fechaProgramada = $this->getTotalHabiles_v2($fechaProgramada, $duracion);
                        }
                        $return[$index]['tareas_fecha_programada'] = $fechaProgramada;
                    }

                    // Verifico si la tarea se ejecutó antes de la fecha limite
                    if (!is_null($fechaProgramada) && !empty($fechaProgramada)) {
                        if (!empty($r['tareas_fecha_ejecucion'])) {
                            $return[$index]['success'] = FALSE;
                            $fechaProgramadaAux = new DateTime($fechaProgramada);
                            //$return[$index]['campo_ejecucion'] = $r['tareas_fecha_ejecucion'];
                            if (!is_null($r['tareas_fecha_ejecucion'])) {
                                preg_match($expresionRegular, $r['tareas_fecha_ejecucion'], $matches);
                                if (count($matches) > 0) {
                                    list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
                                    $return[$index]['campo_ejecucion_real'] = implode(".", array($basedatos, $tabla, $this->get_campo_dependiendo_de_tipo_auditoria($auditoria['tipo'], $campo)));
                                }
                            }
                            $select = "DATE(" . $return[$index]['tareas_fecha_ejecucion'] . ")";
                            $return[$index]['tareas_fecha_ejecucion'] = $this->db->select($select . " AS fecha_ejecucion")->where('idAuditoria', $idAuditoria, FALSE)->get($basedatos . "." . $tabla)->row()->fecha_ejecucion;
                            $fechaEjecucion = new DateTime($return[$index]['tareas_fecha_ejecucion']);
                            $retraso = $fechaProgramadaAux->diff($fechaEjecucion);
                            $return[$index]['diferencia_dias_naturales'] = intval($retraso->format('%R%a'));
                            $return[$index]['diferencia_dias_habiles'] = getDiasHabiles($fechaProgramada, $return[$index]['tareas_fecha_ejecucion']);

                            // Elegimos icono
                            if ($fechaInicioAuditoriaReal <= $fechaProgramadaAux) {
                                $return[$index]['icon'] = "check";
                            }
                            if ($fechaProgramadaAux < $fechaEjecucion) {
                                $return[$index]['icon'] = "close";
                            }
                            // Elegimos class
                            if ($fechaProgramadaAux <= $hoy) {
                                $return[$index]['class'] = 'danger';
                                if (!empty($r['tareas_fecha_ejecucion'])) {
                                    if ($fechaEjecucion <= $fechaProgramadaAux) {
                                        $return[$index]['class'] = 'success';
                                    } elseif ($fechaEjecucion > $fechaProgramadaAux) {
                                        $return[$index]['class'] = "danger";
                                    }
                                }
                            }
                        } else {
                            // Elegimos class
                            $fechaProgramadaAux = $fechaProgramada;
                            if ($fechaProgramadaAux <= $hoy) {
                                $return[$index]['class'] = 'danger';
                                if (isset($fechaEjecucion) && !empty($r['tareas_fecha_ejecucion'])) {
                                    if ($fechaEjecucion <= $fechaProgramadaAux) {
                                        $return[$index]['class'] = 'success';
                                    } elseif ($fechaEjecucion > $fechaProgramadaAux) {
                                        $return[$index]['class'] = "danger";
                                    }
                                }
                            }
                        }
                    } else {
                        $return[$index]['tareas_fecha_ejecucion'] = NULL;
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Función que permite obtener los entregables de una tarea
     * @param int $idTarea Identificador de la tarea
     * @return array Arreglo con la información de los entregables de una tarea
     */
    function get_entregables($idTarea = NULL) {
        $return = array();
        if (!empty($idTarea)) {
            $dbTL = $this->getDatabase('timeline');
            $res = $dbTL->where_in("entregables_tareas_id", $idTarea)->get("entregables");
            if ($res->num_rows() > 0) {
                $return = $res->result_array();
            }
        }
        return $return;
    }

    /**
     * Función que permite obtener el campo real al cual se debe referir dependiendo del tipo de auditoría
     * @param string $tipo Identificador del tipo de auditoría (AP,AE,SA)
     * @param string $campo Nombre del campo referencia
     * @return string
     */
    function get_campo_dependiendo_de_tipo_auditoria($tipo, $campo) {
        if (empty($campo)) {
            return "";
        }

        $return = $campo;
        if ($campo === 'fechaSelloOEA') {
            return $campo;
        }

        if (trim(strtoupper($tipo)) === "SA") {
            switch ($campo) {
                case 'fechaAprovacionS' :
                    $campo = 'fechaAprovacionRev1S';
                    break;
                case 'fechaAprovacionJ' :
                    $campo = 'fechaAprovacionRev1J';
                    break;
                case 'fechaOEDRes':
                    $campo = 'fechaOEDRev1';
                    break;
                default :
                    $campo .= 'Rev1';
            }
        }

        $return = $campo;
        return $return;
    }

}
