<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Timeline_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = "configuraciones";
        $this->id_field = "configuraciones_id";
        $this->table_prefix = "conf";
        $this->model_name = __CLASS__;
    }

    function record_count() {
        return 0;
    }

    function getDatabase($dbName) {
        if ($dbName == "timeline") {
            $db = array(
                'dsn' => '',
                'hostname' => 'localhost',
                'username' => 'root',
                'password' => '',
                'database' => 'nuevo_timeline',
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
     * @param mixed $auditorias_id Identificador al cual pertenece una auditoría
     * @return array Arreglo con la información correspondiente al proceso de la auditoría
     */
    function get_proceso($auditorias_id = NULL) {
        $return = array();
        if (!empty($auditorias_id)) {
            $dbTL = $this->getDatabase('timeline');
            $res = $dbTL->where("procesos_id", $auditorias_id)->get("procesos");
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
     * @param integer $auditorias_id Identificador de la auditoría
     * @param integer $idEtapa Identificador de la etapa
     * @param array $auditoria Array con información de la auditoría
     * @return array Esta función regresa un arreglo con la información de las tareas de una etapa
     */
    function get_tareas($auditorias_id = NULL, $idEtapa = NULL, $auditoria = array()) {
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
                        $fechaProgramada = $this->db->select($select . " AS fecha_programada", FALSE)->where('idAuditoria', $auditorias_id, FALSE)->get($basedatos . "." . $tabla)->row()->fecha_programada;
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
                            $return[$index]['tareas_fecha_ejecucion'] = $this->db->select($select . " AS fecha_ejecucion")->where('idAuditoria', $auditorias_id, FALSE)->get($basedatos . "." . $tabla)->row()->fecha_ejecucion;
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

    /**
     * Esta función permite conocer la fecha programada (plazo máximo) en la cual se debe realizar la tarae
     * @param integer $auditorias_id Indentificador de la auditoría en la cual se ejecutará la tarea
     * @param integer $idConfiguracion Identificador de la configuracion
     * @param boolean $returnCampoReal Indica si se desea solo la fecha o un array[fecha, nombre del campo]
     * @return mixed Si $returnCampoReal es FALSO entonces solo se regresa la cadena con la fecha obtenida en formato YYYY-MM-DD. Cuando en VERDADERO se regresa un arreglo que contiene la fecha y el nombre del campo real que hace referencia a la fecha
     */
    function get_fecha_programada_de_tarea($auditorias_id, $idConfiguracion, $returnCampoReal = FALSE) {

    }

    /**
     * Esta función regresa la fecha en que se ejecutó la tarea
     * @param integer $auditorias_id Indentificador de la auditoría en la cual se ejecutará la tarea
     * @param integer $idConfiguracion Identificador de la configuracion
     * @param boolean $returnCampoReal Indica si se desea solo la fecha o un array[fecha, nombre del campo]
     * @return mixed Si $returnCampoReal es FALSO entonces solo se regresa la cadena con la fecha obtenida en formato YYYY-MM-DD. Cuando en VERDADERO se regresa un arreglo que contiene la fecha y el nombre del campo real que hace referencia a la fecha
     */
    function get_fecha_ejecucion_de_tarea($auditorias_id, $idConfiguracion, $returnCampoReal = FALSE) {

    }

    /**
     * Función que regresa la diferencia de la fecha reprogramada dependiendo de una tarea específica
     * @param integer $auditorias_id Identificador de la auditoría
     * @param integer $idConfiguracion Identificador de la configuracion
     * @return integer Valor entero que indica la diferencia que se debe sumar (positivo) o restar(negativo) a la fecha programada para ajustar la reprogramación
     */
    function get_diferencia_de_reprogramacion($auditorias_id, $idConfiguracion) {

    }

    /**
     * Función que regresa la clase CSS del icono dependiendo de dos fechas proporcionadas
     * @param date $fechaProgramada Fecha en formato YYYY-MM-DD de la fecha máxima en la que se debe realizar la tarea
     * @param date $fechaEjecucion Fecha en formto YYYY-MM-DD en la que se ejecutó la tarea
     * @return string Regresa el nombre de la clase correspondiente
     */
    function get_clase_de_timeline($fechaProgramada, $fechaEjecucion) {

    }

    /**
     * Esta función regresa el icono correcto que se debe mostrar en la linea de tiempo segun la fecha de ejecucion y la fecha programada
     * @param string $fechaProgramada Valor de la fecha programada. YYYY-MM-DD
     * @param string $fechaEjecucion Valor de la fecha de ejecución YYYY-MM-DD
     * @param mixed $auditorias_id Valor de la fecha de auditoria, o también puede ser el ID de la auditoria
     * @return string Regresa el nombre del icono
     */
    function get_icono_de_timeline($fechaProgramada, $fechaEjecucion, $auditorias_id = NULL) {

    }

    /**
     * Función que regresa información de la linea de tiempo de una auditoría
     * @param int $auditorias_id Identificador de la auditoría
     * @return mixed. Regresa un arreglo con la información de la línea de tiempo de una auditoría. Regresa FALSE en caso de error.
     */
    function get_timeline($auditorias_id = NULL) {

    }

    /**
     * Función que obtiene el texto de un documento DOCX
     * @param string $filename Ruta del documento
     * @return string Texto que tiene el documento
     */
    function read_docx($filename) {

    }

    /**
     * Obtiene el nombre del campo dependiendo del ID de referencia
     * @param integer $idCampo Identificador del campo de la base de datos
     * @return string Nombre del campo de la base de datos
     */
    function get_campo($idCampo = NULL) {

    }

    /*
     * Función que regresa el tipo de dato MYSQL de un campo específico
     * @param string $campoEjecucion Nombre del campo al cual se desea obtener su tipo
     * @return string Devuelve el tipo de campo MySQL (int, string, date, datetime, text,...)
     */

    function get_tipo_campo_mysql($campoEjecucion = NULL) {

    }

    /**
     * Función que determina el rango de fechas de la semana, según la fecha proporcionada
     * @param string $fecha Cadena con formato YYYY-MM-DD que especifica una fecha. De forma predeterminada es la fecha actual.
     * @param boolen $esSemanaInglesa Indica si se desea como fecha final el viernes o el domingo. De forma predeterminada se usa la semana inglesa (De lunes a viernes)
     * @return array Arreglo con los valores de FECHA_INICIO y FECHA_FIN que contienen las fechas de inicio y fin de la semana solicitada
     */
    function get_rango_de_semana($fecha = "now", $esSemanaInglesa = TRUE) {

    }

    /**
     * Esta función devuelve los documentos relacionados a un tarea (configuracion) de la linea de tiempo para su VoBo
     * @param integer $idConfiguracion Identificador de la configuracion
     * @param integer $auditorias_id [Optional] Identificador de la auditoria
     * @return array Devuelve la informacion correspondiente a los documentos relacionados a la configuracion de la linea de tiempo
     */
    function get_documentos_de_configuracion($idConfiguracion, $auditorias_id = NULL) {

    }

    /**
     * Obtiene los Vistos Buenos de un documento, regresándolos en orden cronologico descendiente (del mas reciente al mas antiguo)
     * @param integer $idDocto Identificador del documento
     * @param integer $idEmpleado [Optional] Identificador del empleado
     * @return array Inforación del Visto Bueno de un documento
     */
    function get_vobos($idDocto, $idEmpleado = NULL, $orderBy = "DESC") {

    }

    /**
     * Esta función regresa la URL que permite desplegar el documento de forma correcta
     * @param integer $idTipoDocto Identificador del tipo de documento
     * @return string URL del tipo de documento para que al hacerle clic se visualice en otra ventana
     */
    function get_url_de_documento($idTipoDocto) {

    }

    /**
     * Obtiene toda la información referente al documento especificado
     * @param integer $idDocto Identificador del documento
     * @return array Regresa el información, detalles y el HTML del documento especificado. FALSO en caso de que no se encuentre el documento
     */
    function get_documento($idDocto) {

    }

    /**
     * Función que regresa la fecha máxima para poder generar prorrogas de una auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @return date Fecha límite en formato YYYY-MM-DD para generar una prórroga
     */
    function get_fecha_maxima_para_generar_prorroga($auditorias_id) {

    }

    function has_seguimiento($auditorias_id) {

    }

    function has_concluida_sin_seguimiento($auditorias_id) {

    }

    /**
     * Devuelve una cadena de texto de las recomendaciones relacionadas a la auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @return string Cadena de texto de las recomendaciones
     */
    function get_texto_recomendaciones($auditorias_id) {

    }

    /**
     * Indica si la auditoría se es reservada
     * @param integer $auditorias_id Identificador de la Auditoría
     * @return boolean Devuelve TRUE para indicar que la auditoría es RESERVADA, FALSE para PÚBLICA
     */
    function is_reservada($auditorias_id) {

    }

    /**
     * Esta función regresa la última revisión relacionada con el ciclo de vida de una auditoría (incluye seguimientos)
     * @param integer $auditorias_id Identificador de la auditoría
     * @return integer Devuelve el valor de la última revisión de un auditoría
     */
    function get_maxima_revision($auditorias_id) {

    }

    /**
     * Esta función devuelve el identificador de la auditoría origen. Se entiende como auditoría origen cualquier auditoría de tipo AP, AE o IC.
     * @param integer $auditorias_id Indentificador de la auditoría
     * @return integer Devuelve el Identificador de auditoría AP/AE/IC origen
     */
    function get_real_auditoria_origen($auditorias_id) {

    }

    /**
     * Devuelve las observaciones propias de una auditoría, es decir, que si es un seguimiento, devuelve sólo las observaciones relacionadas
     * con el seguimiento; de lo contrario regresa todas las observaciones, excepto las eliminadas.
     * @param integer $auditorias_id Identificador de la auditoría
     * @return array Arreglo con las observaciones relacionadas a la auditoría
     */
    function get_observaciones_propias_de_auditoria($auditorias_id) {

    }

    function get_revision_N_de_auditoria($auditorias_id, $numeroRevision) {

    }

    function get_revision_N_de_observacion($idObservacion, $numeroRevision) {

    }

}
