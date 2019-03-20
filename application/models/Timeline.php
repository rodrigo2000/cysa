<?php

class Timeline extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = "procesos";
        $this->id_field = "procesos_id";
        $this->table_prefix = "p";
        $this->model_name = __CLASS__;
    }

    /**
     * Función que convierte un valor entero a su representación en fecha/hora
     * @param int $integer Valor entero a convertir
     * @return date Regresa la representación en formato de fecha Y-m-d H:i:s de un entero
     */
    function int2date($integer) {
        $date = $integer;
        if (!empty($integer) && strpos($integer, "-") === FALSE) {
            $format = 'Y-m-d H:i:s';
            $date = date($format, $integer);
        }
        return $date;
    }

    /**
     * Función que permite obtener el campo real al cual se debe referir dependiendo del tipo de auditoría
     * @param string $tipo Identificador del tipo de auditoría (AP,AE,SA)
     * @param string $campo Nombre del campo referencia
     * @return string Regresa el nombre correcto del campo dependiento del tipo de auditoría
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
     * Esta función se conecta a la tabla de TIMELINE
     * @return recordset Regresa la conexión a la base de datos
     */
    function conectar_timeline() {
        $prefix = get_prefix();
        return conectarBD($prefix . "timeline");
    }

    /**
     * Devuelve la información del proceso asociado a la auditoría
     * @param integer $idAuditoria Identificador de la auditoria
     * @param integer $tipoAuditoria [opcional]<br>Identificador del tipo de auditoria (AP,SA,IC)
     * @return integer Devuelve el identificador del proceso al que corresponde la auditoria
     */
    function get_proceso_de_auditoria($idAuditoria = NULL) {
        $return = array();

        $dbTimeline = conectar_timeline();
        $strSQL = "SELECT * FROM auditorias WHERE auditorias_id = " . $idAuditoria . " LIMIT 1";
        $result = $dbTimeline->ejecutaQuery($strSQL);
        if (is_resource($result) && mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            $idProceso = $row['procesos_id'];
        } else {
            // Como nunca se ha asignado a un proceso, entonces vemos qué tipo de auditoría es
            $dbCYSA = conectarBD();
            $strSQL = "SELECT tipo, anio FROM cat_auditoria WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
            $result = $dbCYSA->ejecutaQuery($strSQL);
            $r = mysql_fetch_assoc($result);
            $tipoAuditoria = $r['tipo'];
            // Obtenemos el proceso correspondiente al tipo de auditoría
            if ($r['anio'] >= 2018 && $r['tipo'] !== 'IC' && empty($r['idAuditoriaOrigen'])) {
                $strSQL = "SELECT * FROM procesos WHERE procesos_tipo_auditoria LIKE '%" . $tipoAuditoria . "%' AND procesos_vigente = 1 LIMIT 1";
            } else {
                $strSQL = "SELECT * FROM procesos WHERE procesos_tipo_auditoria LIKE '%" . $tipoAuditoria . "%' AND procesos_vigente = " . ($r['tipo'] === 'IC' ? '1' : '0') . " LIMIT 1";
            }
            $dbTimeline = conectar_timeline();
            $result = $dbTimeline->ejecutaQuery($strSQL);
            $r = mysql_fetch_assoc($result);
            $idProceso = $r['procesos_id'];
            $strSQL = "INSERT INTO auditorias VALUES(" . $idAuditoria . "," . $idProceso . ")";
            $dbTimeline->ejecutaQuery($strSQL);
        }
        $return = get_procesos($idProceso);
        return $return;
    }

    /**
     * Esta función permite obtener información de un proceso
     * @param mixed $idProceso Identificador al cual pertenece una auditoría
     * @return array Arreglo con la información correspondiente al proceso de la auditoría
     */
    function get_procesos($idProceso = NULL) {
        $dbTimeline = conectar_timeline();
        $strSQL = "SELECT * FROM procesos WHERE procesos_id = " . $idProceso;
        $result = $dbTimeline->ejecutaQuery($strSQL);
        $return = array();
        if (is_resource($result) && mysql_num_rows($result) > 0) {
            while ($r = mysql_fetch_assoc($result)) {
//$return = array_map('utf8_encode', $r);
                $return = $r;
            }
        }
        return $return;
    }

    /**
     * Esta función permite obtener las etapas correspondientes a un procreso de auditoría
     * @param mixed $idAuditoria Identificador de la auditoría
     * @param mixed $idProceso Identificador del proceso
     * @return array Las etapas que corresponden al proceso
     */
    function get_etapas($idAuditoria, $idProceso = NULL, $audi = array()) {
        $return = array();
        if (!empty($idProceso)) {
            $dbCYSA = conectarBD();
            $strSQL = "SELECT * FROM cat_auditoria WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
            $result = $dbCYSA->ejecutaQuery($strSQL);
            $res = mysql_fetch_assoc($result);
            $etapaAuditoria = getEtapaAuditoria($idAuditoria);
            $strSQL = "SELECT * FROM etapas "
                    . "WHERE etapas_procesos_id = " . $idProceso . " "
                    . "AND etapas_codigo <= " . $etapaAuditoria['etapa'];
            if (!empty($datosAudit['idAuditoriaOrigen'])) {
                $strSQL .= " AND etapas_codigo = " . ETAPA_RV1;
            }
            $strSQL .= " ORDER BY etapas_orden_ejecucion DESC";
            $dbTimeline = conectar_timeline();
            $result = $dbTimeline->ejecutaQuery($strSQL);
            if (mysql_num_rows($result)) {
                while ($r = mysql_fetch_assoc($result)) {
//$r = array_map('utf8_encode', $r);
                    $r['tareas'] = get_tareas($idAuditoria, $r['etapas_id'], $audi);
                    array_push($return, $r);
                }
            }
        }
        return $return;
    }

    /**
     * Esta función permite obtener las tareas de un etapa
     * @param integer $idAuditoria Identificador de la auditoría
     * @param integer $idEtapa Identificador de la etapa
     * @param array $audi Array con información de la auditoría
     * @return array Esta función regresa un arreglo con la información de las tareas de una etapa
     */
    function get_tareas($idAuditoria = NULL, $idEtapa = NULL, $audi = array()) {
        $PREFIX = get_prefix();
        $ocultarTareas = array(
            'Envío de Citatorio',
            'Agendar Lectura por Correo Electrónico',
            'Lectura de Resultados de Revisión'
        );
        $ocultarTareasSinObservaciones = array(
            'Realizar Prelectura',
            'Solicitar Fecha de Lectura',
            'Envío de Citatorio',
            'Convocar Lectura por Correo Electrónico',
            'Convocar revisión de avances con el área auditada'
        );
        $return = array();
        if (count($audi) > 0) {
//        $fechaInicioAuditoria = new DateTime($audi['fechaIniAudit']);
//        $fechaFinAuditoria = new DateTime($audi['fechaFinAudit']);
            $fechaInicioAuditoriaReal = new DateTime($audi['fechaIniReal']);
            $fechaFinAuditoriaReal = new DateTime($audi['fechaFinReal']);
        }
// Obtenemos el ID del empleado del auditor lider
        $idEmpleadoAuditorLider = get_empleado($audi['idEmpleado']);
// Expresión regular para obtener la la estructura el nombre de un campo que siempre viene de la siguiente forma
// {BASEDATO} . {TABLA} . {CAMPO}
        $expresionRegular = '/(([A-z\d\-}]{1,})\.){2}([A-z\d-]){1,}/';

        $hoy = new DateTime("now");
        if (!empty($idEtapa)) {
            $dbTL = conectar_timeline();
            if (is_array($idEtapa)) {
                $where = "configuraciones_etapas_id IN (" . implode(",", $idEtapa) . ")";
            } else {
                $where = "configuraciones_etapas_id = " . $idEtapa;
            }
            $strSQL = "SELECT t.tareas_nombre, e.etapas_nombre, e.etapas_codigo, "
                    . "c.* "
                    . "FROM configuraciones c "
                    . "INNER JOIN tareas t ON t.tareas_id = c.configuraciones_tareas_id "
                    . "INNER JOIN etapas e ON e.etapas_id = c.configuraciones_etapas_id "
                    . "WHERE c.configuraciones_activo = TRUE AND " . $where . " "
                    . "ORDER BY e.etapas_orden_ejecucion DESC, c.configuraciones_orden_ejecucion DESC";
            $res = $dbTL->ejecutaQuery($strSQL);
            if (mysql_num_rows($res) > 0) {
                $return = array();
                while ($r = mysql_fetch_assoc($res)) {
                    //array_push($return, array_map('utf8_encode', $r));
                    array_push($return, $r);
                }
                // Obtenemos las observaciones
                $observaciones = array();
                $conex = conectarBD(BD_CYSA);
                if (getNumObservaciones($idAuditoria) > 0) {
                    $observaciones = getListaObservaciones($idAuditoria);
                    foreach ($observaciones as $index => $o) {
                        $aux = getestatusObservacion($o['idObs']);
                        $observaciones[$index] = $aux;
                    }
                }

                // Revisamos si todas las observaciones estan solventadas
                $observacionesSolventadas = FALSE;
                if (count($observaciones) > 0) {
                    $observacionesSolventadas = (((array_sum($observaciones) / count($observaciones)) == 2) ? TRUE : FALSE);
                }
                // Revisamos si es una auditoría sin observaciones
                $isAuditoriaSinObservaciones = is_sin_observaciones($idAuditoria);

                $indexes = array();
                foreach ($return as $index => $r) {
                    $mostrarTarea = TRUE;
                    if ($isAuditoriaSinObservaciones && $audi['anio'] < 2018) { // En caso de que la auditoría no tenga observaciones, entonces ocultamos ciertas tareas
                        $mostrarTarea = !(in_array(trim($r['tareas_nombre']), $ocultarTareasSinObservaciones));
                    } elseif ($r['etapas_codigo'] > ETAPA_AP && $observacionesSolventadas && $audi['anio'] < 2018) { // En caso de que sus observaciones esten solventadas, entonces se procede a ocultar ciertas tareas
                        $mostrarTarea = !(in_array(trim($r['tareas_nombre']), $ocultarTareas));
                    }
                    if ($mostrarTarea) {
                        $return[$index]['class'] = 'default';
                        $return[$index]['icon'] = 'more_horiz'; // more_horiz  more_vert  hourglass_empty

                        if (isset($audi['fechaFinReal']) && strtotime($audi['fechaFinReal']) > 0) {
                            $return[$index]['class'] = 'success';
                            $return[$index]['icon'] = 'check'; // more_horiz  more_vert  hourglass_empty
                        }

                        // Verificamos si la fecha programa requiere re-programación
                        $diferenciaReprogramacion = 0;
                        if (!empty($r['configuraciones_fecha_reprogramada'])) {
                            $strSQL = "SELECT configuraciones_id FROM configuraciones "
                                    . "WHERE configuraciones_tareas_id = " . $r['configuraciones_fecha_reprogramada'] . " "
                                    . "AND configuraciones_procesos_id = " . $r['configuraciones_procesos_id'] . " "
                                    . "AND configuraciones_etapas_id = " . $r['configuraciones_etapas_id'] . " "
                                    . "LIMIT 1";
                            $resAux = $dbTL->ejecutaQuery($strSQL);
                            if (is_resource($resAux) && mysql_num_rows($resAux) == 1) {
                                $aux = mysql_fetch_assoc($resAux);
                                $idConfAux = $aux['configuraciones_id'];
                                $diferenciaReprogramacion = get_diferencia_de_reprogramacion($idAuditoria, $idConfAux);
                            }
                        }
                        // Generamos la fecha programada
                        list($fechaProgramada, $nombreCampoProgramada) = get_fecha_programada_de_tarea($idAuditoria, $r['configuraciones_id'], TRUE);
                        $return[$index]['tareas_fecha_programada'] = $fechaProgramada;
                        $return[$index]['campo_programada_real'] = $nombreCampoProgramada;

                        // Calculamos la fecha reprogramada
                        if ($diferenciaReprogramacion != 0) {
                            $return[$index]['tareas_fecha_reprogramada'] = $fechaProgramada;
                            $return[$index]['tareas_fecha_programada'] = getTotalHabiles_v2($fechaProgramada, intval($diferenciaReprogramacion));
                        } else {
                            unset($return[$index]['tareas_fecha_reprogramada']);
                        }

                        // Verifico si la tarea se ejecutó antes de la fecha limite
                        if (!empty($r['configuraciones_fecha_ejecucion'])) {
                            $return[$index]['success'] = FALSE;
                            list($fechaEjecucion, $nombreCampoEjecucion) = get_fecha_ejecucion_de_tarea($idAuditoria, $r['configuraciones_id'], TRUE);
                            $return[$index]['tareas_fecha_ejecucion'] = $fechaEjecucion;
                            $return[$index]['campo_ejecucion_real'] = $nombreCampoEjecucion;
                            $fechaEjecucionAux = new DateTime($fechaEjecucion);
                            // Esta validación sirve para mostrar correctamente el icono y color de la tarea,
                            // ya que la tarea "Convocar revisión de avances con el área auditada" tiene como fecha
                            // programa un intervalo de fechas, por lo tanto a la fecha programa establecida se le añaden 4 días
                            // para que se considere el intervalo de tiempo
                            if (trim($r['tareas_nombre']) === "Convocar revisión de avances con el área auditada") {
                                $fechaProgramada = getTotalHabiles_v2($fechaProgramada, 4);
                            }
                            $retraso = diferencia_entre_fechas(parseDatetime2Date($fechaProgramada), parseDatetime2Date($fechaEjecucion));
                            $return[$index]['diferencia_dias_naturales'] = $retraso;
                            $return[$index]['diferencia_dias_habiles'] = getDiasHabiles(parseDatetime2Date($fechaProgramada), parseDatetime2Date($fechaEjecucion));
                            $return[$index]['icon'] = get_icono_de_timeline(parseDatetime2Date($fechaProgramada), parseDatetime2Date($fechaEjecucion), parseDatetime2Date($audi['fechaIniReal']));
                            $return[$index]['class'] = get_clase_de_timeline(parseDatetime2Date($fechaProgramada), parseDatetime2Date($fechaEjecucion));

                            // Verificamos si las fechas de Revision de Jefe y Aprobacion del Subdirector suman máximo 10 días
                            if (stripos($r['tareas_nombre'], " jefe/coordinador") > 10 || stripos($r['tareas_nombre'], " subdirector") > 10) {
                                array_push($indexes, $index);
                            }
                        } else {
                            if (!empty($fechaProgramada)) {
                                // Elegimos class
                                $DTFechaProgramada = new DateTime($fechaProgramada);

                                if ($DTFechaProgramada < $hoy) {
                                    $return[$index]['icon'] = "check";
                                    $return[$index]['class'] = "info"; //get_clase_de_timeline($fechaProgramada, $fechaEjecucion);
                                } else {
                                    $fechaEjecucionDeMiFechaProgramada = NULL;
                                    // Verificamos si la fecha de ejecución de la tarea con la cual se obtiene la fecha programada tiene valor
                                    // en caso de que el campo configuraciones_duracion sea negativo
                                    if ($r['configuraciones_duracion'] < 0) {
                                        $strSQL = "SELECT configuraciones_id FROM configuraciones WHERE "
                                                . "configuraciones_procesos_id = " . $r['configuraciones_procesos_id'] . " "
                                                . " AND configuraciones_etapas_id = " . $r['configuraciones_etapas_id'] . " "
                                                . " AND configuraciones_fecha_ejecucion = " . $r['configuraciones_fecha_programada'] . " "
                                                . " ORDER BY configuraciones_orden_ejecucion ASC "
                                                . " LIMIT 1 ";
                                        $dbTL = conectar_timeline();
                                        $resAux = $dbTL->ejecutaQuery($strSQL);

                                        if (mysql_num_rows($resAux) > 0) {
                                            $aux = mysql_fetch_assoc($resAux);
                                            $fechaEjecucionDeMiFechaProgramada = get_fecha_ejecucion_de_tarea($idAuditoria, $aux['configuraciones_id']);
                                        }
                                    }

                                    if (!empty($fechaEjecucionDeMiFechaProgramada)) {
                                        $return[$index]['icon'] = 'check';
                                        $return[$index]['class'] = 'info';
                                    } else {
                                        $return[$index]['icon'] = "more_horiz";
                                        $return[$index]['class'] = "default";
                                    }
                                }
                            } else {
                                $return[$index]['tareas_fecha_ejecucion'] = NULL;
                                $return[$index]['class'] = 'default';
                                $return[$index]['icon'] = 'more_horiz';
                            }
                        }

                        // Busco los entregables
                        //$return[$index]['entregables'] = get_entregables($r['tareas_id']);
                        // Verifico si los campos para editar la fecha son distintos de vacio
                        $return[$index]['editar_fecha'] = $audi['statusAudit'] == AUDIT_PROCESO ? TRUE : FALSE;
                        if (!empty($r['configuraciones_permisos_editar_fecha']) && $return[$index]['editar_fecha']) {
                            $dbCYSA = conectarBD();
                            $campos = explode(";", $r['configuraciones_permisos_editar_fecha']);
                            $auxRes = TRUE;
                            foreach ($campos as $idCampo) {
                                $c = get_campo($idCampo);
                                preg_match($expresionRegular, $c, $matches);
                                if (count($matches) > 0) {
                                    list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
                                    $strSQL = "SELECT " . $campo . " FROM " . $PREFIX . $basedatos . "." . $tabla . " WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
                                    $result = $dbCYSA->ejecutaQuery($strSQL);
                                    if ($result && mysql_num_rows($result) == 1) {
                                        $res = mysql_fetch_assoc($result);
                                        if (is_null($res[$campo])) {
                                            $auxRes = FALSE;
                                        }
                                    }
                                }
                                $return[$index]['editar_fecha'] = $auxRes;
                            }
                        }

                        if ($return[$index]['tareas_nombre'] === "Envío de Oficio de Orden de Auditoría") {
                            $strSQL = "SELECT cysaRegAuditoria FROM " . $PREFIX . "sac.dcont_modulo WHERE idEmpleado = " . $_SESSION['usuario']->getIdEmpleado() . " LIMIT 1";
                            $dbSAC = conectarBD(DB_PREFIX . "sac");
                            $result11 = $dbSAC->ejecutaQuery($strSQL);
                            $aux = mysql_fetch_assoc($result11);
                            $return[$index]['editar_fecha'] = ($aux['cysaRegAuditoria'] > 0 ? TRUE : FALSE);
                        }

                        // Verificamos si tiene documentos para Vo.Bo.
                        $documentos = get_documentos_de_configuracion($r['configuraciones_id'], $idAuditoria);
                        if (!empty($documentos)) {
                            $conex = conectarBD();
                            $datosAudit = getAuditoria($idAuditoria);
                            $firmas = getFirmasContraloria($idAuditoria);
                            $jefe = getJefesDepto($datosAudit['area']);
                            $coord = get_coordinador_de_auditoria($idAuditoria);
                            if (!empty($coord)) {
                                $return[$index]['titulares_vobos']['coordinador'] = $coord;
                            }
                            if (!empty($jefe)) {
                                $return[$index]['titulares_vobos']['revisado'] = $jefe['jefeId'];
                            }
                            $return[$index]['titulares_vobos']['aprobado'] = $firmas['subdirectorId'];
                            $return[$index]['titulares_vobos']['autorizado'] = $firmas['directorId'];
                            $return[$index]['documentos'] = $documentos;
                        }
                    } else {
                        unset($return[$index]);
                    }
                    // Verificamos que si la tarea es FIN REVISION DEL JEFE/COORDINADOR y el lider de la auditoria
                    // es el jefe/coordinador, entonces esta tarea no debe aparecer en la linea de tiempo                
                    if ($r['tareas_nombre'] == "Fin Revisión del Jefe/Coordinador" && $idEmpleadoAuditorLider['idPuesto'] == JEFE_DE_DEPARTAMENTO) {
                        $return[$index - 1]['editar_fecha'] = $return[$index]['editar_fecha'];
                        unset($return[$index]);
                        continue;
                    }
                }

                // Esta variable nos indica si ambas fechas de ejecucion son diferentes de NULL
                $existenFechasEjecucion = TRUE;
                // Suma de ambos días menor o igual que 10
                $sumaAmbasFechas = 0;
                foreach ($indexes as $key => $index) {
                    if (isset($return[$index])) {
                        $aux = $return[$index];
                        $existenFechasEjecucion = !is_null($aux['tareas_fecha_ejecucion']) && $existenFechasEjecucion;
                        $sumaAmbasFechas += $aux['diferencia_dias_habiles'];
                    }
                }
                // La suma debe ser menor que 5 debido a que en el peor de los casos, el jefe puede tomar los 10 dias
                // para hacer la revisión, y entonces al subdirector ese mismo día tendrá que hacer su aprobación.
                // Pero recordemos que ya transcurrieron los 5 días hábiles del jefe.
                if ($existenFechasEjecucion && $sumaAmbasFechas <= 0) {
                    foreach ($indexes as $index) {
                        if (isset($return[$index])) {
                            $return[$index]['icon'] = 'check';
                            $return[$index]['class'] = 'success';
                        }
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Función que permite obtener los entregables de una tarea
     * @param int $idTarea Identificador del campo
     * @return array Arreglo con la información de los entregables de una tarea
     */
    function get_entregables($idTarea = NULL) {
        $return = array();
        return $return;
        if (!empty($idTarea)) {
            $dbTimeline = conectar_timeline();
            $strSQL = "SELECT * FROM entregables WHERE entregables_tareas_id = " . $idTarea;
            $result = $dbTimeline->ejecutaQuery($strSQL);
            if (mysql_num_rows($result)) {
                while ($r = mysql_fetch_assoc($result)) {
                    array_push($return, array_map('utf8_encode', $r));
                }
            }
        }
        return $return;
    }

    /**
     * Esta función permite conocer la fecha programada (plazo máximo) en la cual se debe realizar la tarae
     * @param integer $idAuditoria Indentificador de la auditoría en la cual se ejecutará la tarea
     * @param integer $idConfiguracion Identificador de la configuracion
     * @param boolean $returnCampoReal Indica si se desea solo la fecha o un array[fecha, nombre del campo]
     * @return mixed Si $returnCampoReal es FALSO entonces solo se regresa la cadena con la fecha obtenida en formato YYYY-MM-DD. Cuando en VERDADERO se regresa un arreglo que contiene la fecha y el nombre del campo real que hace referencia a la fecha
     */
    function get_fecha_programada_de_tarea($idAuditoria, $idConfiguracion, $returnCampoReal = FALSE) {
        $fechaProgramada = NULL;
        $expresionRegular = '/(([A-z\d\-}]{1,})\.){2}([A-z\d-]){1,}/';
        $PREFIX = get_prefix();
        $dbTL = conectar_timeline();
        $strSQL = "SELECT configuraciones_fecha_programada, configuraciones_duracion tareas_duracion FROM configuraciones WHERE configuraciones_id = " . $idConfiguracion . " LIMIT 1";
        $resConfiguracion = $dbTL->ejecutaQuery($strSQL);
        $tarea = @mysql_fetch_assoc($resConfiguracion);
        $tarea['tareas_fecha_programada'] = get_campo($tarea['configuraciones_fecha_programada']);
        $dbCYSA = conectarBD();
        $strSQL = "SELECT tipo FROM cat_auditoria WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
        $resAuditoria = $dbCYSA->ejecutaQuery($strSQL);
        $auditoria = mysql_fetch_assoc($resAuditoria);
        if (!empty($tarea['tareas_fecha_programada'])) {
// Obtengo el campo real del cual se obtendrá el valor
//$return[$index]['campo_programada'] = $tarea['tareas_fecha_programada'];
            if (!is_null($tarea['tareas_fecha_programada'])) {
                preg_match($expresionRegular, $tarea['tareas_fecha_programada'], $matches);
                if (count($matches) > 0) {
                    list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
                    $tarea['campo_programada_real'] = implode(".", array($PREFIX . $basedatos, $tabla, $campo));
                }
            }
            $duracion = intval($tarea['tareas_duracion']);
            $select = "DATE(" . str_replace("cysa", $PREFIX . "cysa", $tarea['tareas_fecha_programada']) . ")";
            $strSQL = "SELECT " . $select . " fecha_programada FROM " . $PREFIX . $basedatos . "." . $tabla . " WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
            $result = $dbCYSA->ejecutaQuery($strSQL);
            $aux = @mysql_fetch_assoc($result);
            $fechaProgramada = $aux['fecha_programada'];
            if (!is_null($fechaProgramada)) {
                $fechaProgramada = getTotalHabiles_v2($fechaProgramada, $duracion);
            }
        }
        if ($returnCampoReal) {
            $d = isset($tarea['campo_programada_real']) ? $tarea['campo_programada_real'] : NULL;
            return array($fechaProgramada, $d);
        } else {
            return $fechaProgramada;
        }
    }

    /**
     * Esta función regresa la fecha en que se ejecutó la tarea
     * @param integer $idAuditoria Indentificador de la auditoría en la cual se ejecutará la tarea
     * @param integer $idConfiguracion Identificador de la configuracion
     * @param boolean $returnCampoReal Indica si se desea solo la fecha o un array[fecha, nombre del campo]
     * @return mixed Si $returnCampoReal es FALSO entonces solo se regresa la cadena con la fecha obtenida en formato YYYY-MM-DD. Cuando en VERDADERO se regresa un arreglo que contiene la fecha y el nombre del campo real que hace referencia a la fecha
     */
    function get_fecha_ejecucion_de_tarea($idAuditoria, $idConfiguracion, $returnCampoReal = FALSE) {
        $fechaEjecucion = NULL;
        $expresionRegular = '/(([A-z\d\-}]{1,})\.){2}([A-z\d-]){1,}/';
        $PREFIX = get_prefix();

        $dbTL = conectar_timeline();
        $strSQL = "SELECT configuraciones_fecha_ejecucion, configuraciones_duracion tareas_duracion FROM configuraciones WHERE configuraciones_id = " . $idConfiguracion . " LIMIT 1";
        $resTarea = $dbTL->ejecutaQuery($strSQL);
        $tarea = mysql_fetch_assoc($resTarea);
        $tarea['tareas_fecha_ejecucion'] = get_campo($tarea['configuraciones_fecha_ejecucion']);
        $dbCYSA = conectarBD();
        $strSQL = "SELECT tipo FROM cat_auditoria WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
        $resAuditoria = $dbCYSA->ejecutaQuery($strSQL);
        if (isset($tarea['tareas_fecha_ejecucion']) && !is_null($tarea['tareas_fecha_ejecucion'])) {
            if (strtoupper(substr($tarea['tareas_fecha_ejecucion'], 0, 5)) == "FUNC_") {
                $funcion = substr($tarea['tareas_fecha_ejecucion'], 5);
                $campoDB = str_replace("__", ".", $funcion);
                switch ($campoDB) {
                    case "cysa.cat_auditoria_fechas.fechas_elaboracion_acta_cierre":
                        $fechaEjecucion = NULL;
                        preg_match($expresionRegular, $campoDB, $matches);
                        list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
                        $tarea['campo_ejecucion_real'] = $PREFIX . $campoDB;
                        $strSQL = "SELECT valor FROM documentos d INNER JOIN documentos_detalle dd ON dd.idDocto=d.idDocto WHERE d.idAuditoria = " . $idAuditoria . " AND d.idTipoDocto = 11 AND dd.idParrafo = 166";
                        $resDocto = $dbCYSA->ejecutaQuery($strSQL);
                        if (is_resource($resDocto) && $resDocto && mysql_num_rows($resDocto) > 0) {
                            $documento = mysql_fetch_assoc($resDocto);
                            list($dia, $mes, $anio) = explode(" de ", $documento['valor']);
                            $mes = strtoupper($mes);
                            $meses = array("", "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
                            $indexMes = array_search($mes, $meses);
                            $tarea['campo_ejecucion_real'] = $PREFIX . $campoDB;
                            $fechaEjecucion = $anio . "-" . substr("0" . $indexMes, -2) . "-" . $dia;
                            $strSQL = "UPDATE " . $PREFIX . $basedatos . "." . $tabla . " SET " . $campo . " = '" . $fechaEjecucion . "' WHERE idAuditoria = " . $idAuditoria;
                            $dbCYSA->ejecutaQuery($strSQL);
                        } else {
                            $strSQL = "SELECT " . $campo . " FROM " . $PREFIX . $basedatos . "." . $tabla . " WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
                            $result = $dbCYSA->ejecutaQuery($strSQL);
                            if (is_resource($result)) {
                                $aux = mysql_fetch_assoc($result);
                                $fechaEjecucion = $aux[$campo];
                            }
                        }
                        break;
                }
            } else {
                $basedatos = $tabla = "";
                preg_match($expresionRegular, $tarea['tareas_fecha_ejecucion'], $matches);
                if (count($matches) > 0) {
                    list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
                    $auditoria = getAuditoria($idAuditoria);
                    $tarea['campo_ejecucion_real'] = implode(".", array($PREFIX . $basedatos, $tabla, $campo));
                }
                $select = "(" . str_replace("cysa", $PREFIX . "cysa", $tarea['tareas_fecha_ejecucion']) . ")";
                $strSQL = "SELECT " . $select . " AS fecha_ejecucion FROM " . $PREFIX . $basedatos . "." . $tabla . " WHERE idAuditoria = " . $idAuditoria;
                $result = $dbCYSA->ejecutaQuery($strSQL);
                $aux = @mysql_fetch_assoc($result);
                $fechaEjecucion = $aux['fecha_ejecucion'];
            }
        }

        if ($returnCampoReal) {
            return array($fechaEjecucion, $tarea['campo_ejecucion_real']);
        } else {
            return $fechaEjecucion;
        }
    }

    /**
     * Función que regresa la diferencia de la fecha reprogramada dependiendo de una tarea específica
     * @param integer $idAuditoria Identificador de la auditoría
     * @param integer $idConfiguracion Identificador de la configuracion
     * @return integer Valor entero que indica la diferencia que se debe sumar (positivo) o restar(negativo) a la fecha programada para ajustar la reprogramación
     */
    function get_diferencia_de_reprogramacion($idAuditoria, $idConfiguracion) {
        $diferencia = 0;
        if (!empty($idConfiguracion) && intval($idConfiguracion) > 0) {
            $fechaProgramada = get_fecha_programada_de_tarea($idAuditoria, $idConfiguracion);
            $fechaEjecucion = get_fecha_ejecucion_de_tarea($idAuditoria, $idConfiguracion);

            $DTFechaProgramada = new DateTime($fechaProgramada);
            $DTFechaEjecucion = new DateTime($fechaEjecucion);
            if ($DTFechaEjecucion != $DTFechaProgramada) {
                $diferencia = getDiasHabiles($fechaProgramada, $fechaEjecucion);
            }
        }
        return $diferencia;
    }

    /**
     * Esta función devuelve la cantidad de días hábiles (quita sábados y domingos) entre dos fechas. Las
     * fechas no tienen que estar en un orden específico.
     * @link http://mx1.php.net/manual/es/function.date.php
     * @param string $fecha1 Fecha en formato YYYY-MM-DD
     * @param string $fecha2 Fecha en formato YYYY-MM-DD
     * @return int Cantidad de días hábiles entre dos fechas. Si fecha1 > $fecha 2, entonces el resultado 
     * es negativo, es decir, que existe un atraso de la activiad
     */
    function getDiasHabiles($fecha1, $fecha2) {
        if (empty($fecha1) || empty($fecha2)) {
            return NULL;
        }
        $version = explode('.', phpversion());
        $phpVersion = $version[0] * 10000 + $version[1] * 100 + $version[2];
        if ($phpVersion < 50207) { // Si la version de PHP es menor a 5.2.7
            return get_dias_habiles_entre_fechas($fecha1, $fecha2);
        } else {
            $diasHabiles = 0;

            $fecha1 = new DateTime($fecha1);
            $fecha2 = new DateTime($fecha2);
            $oneDay = new DateInterval("P1D");

            $signo = "+";
            if ($fecha1 > $fecha2) {
                $temp = $fecha1;
                $fecha1 = $fecha2;
                $fecha2 = $temp;
                $signo = "-";
            }

            $diasNoHabiles = array(6, 7); // 1 (para lunes) hasta 7 (para domingo)

            for ($fecha1; $fecha1 < $fecha2; $fecha1->add($oneDay)) {
                if (!in_array($fecha1->format("N"), $diasNoHabiles)) {
                    $diasHabiles++;
                }
            }

            return intval($signo . $diasHabiles);
        }
    }

    /**
     * Función que regresa el nombre de día de la semana
     * @param mixed $d Numero del día de la semana
     * @return string Nombre del día de la semana
     */
    function getNombreDelDia($d) {
        $d = intval($d);
        if ($d > 6) {
            return "";
        }
        $dias = array("Domingo", "Lunes", "Martes", "Mi&eacute;rcoles", "Jueves", "Viernes", "S&aacute;bado");
        return $dias[$d];
    }

    /**
     * Función que regresa el nombre de mes del año
     * @param mixed $m Numero del mes del año
     * @return string Nombre del mes del año
     */
    function getNombreDelMes($m) {
        $m = intval($m);
        if ($m > 12) {
            return "";
        }
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        return strtolower($meses[$m]);
    }

    /**
     * Convierte una variable Fecha/Hora en su represetación legible para un humano
     * @param data $f Fecha en formto YYYY-MM-DD HH:MM:SS
     * @param boolen $addBR Si es VERDADERO entonces se agrega ula etiqueta BR para mostrar un salto de carro
     * @param string $texto Cadena de texto que se pondrá entre la fecha y la hora
     * @return string Devuelve la fecha/hora en su representación de frase la cual es legible con más facilidad por un ser humano
     */
    function mysqlDate2Date($f, $addBR = TRUE, $texto = " a las ") {  // yyyy-mm-dd H:m:ss    ==>     13 de Febrero de 2015 <br> 00:00pm
        if (trim($f) == "")
            return "";
        $pos = strpos($f, " ");
        $hora = $fecha = "";
        if ($pos !== false) {
            list($fecha, $hora) = explode(" ", $f);
        } else {
            $fecha = $f;
            $hora = "";
        }
        list($a, $m, $d) = preg_split("/[\/|-]/", $fecha);
        if ($hora != "") {
            list($hh, $mm, $ss) = explode(":", $hora . ":00");
// Concatenamos un ":00" para cuando el hora llegue de la siguiente forma "8:00", 
// entonces para evitar una warning de tipo NOTICE, mejor le concatenamos los segundos
        }
        $ampm = " a.m.";
        if (isset($hh) && intval($hh) >= 12) {
            $ampm = " p.m.";
            if ($hh > 12) {
                $hh -= 12;
            }
        }
        $cadena = $d . ' de ' . getNombreDelMes($m) . ' de ' . $a . ($hora != "" ? ($addBR ? "<br>" : '') . $texto . substr("0" . $hh, -2) . ":" . $mm . $ampm : '');
        return $cadena;
    }

    /**
     * Convierte una variable Fecha/hora en su represetación de fecha legible para un humano
     * @param date $f Fecha en formto YYYY-MM-DD HH:MM:SS
     * @param boolean $addDayName Si es VERDADERO se antepone el nombre del día a la fecha
     * @return string Devuelve la fecha en su representación de frase la cual es legible con más facilidad por un ser humano
     */
    function mysqlDate2OnlyDate($f, $addDayName = FALSE) { // yyyy-mm-dd H:m:ss    ==>     Lunes, 13 de Febrero de 2015
        $cadena = "";
        if (!empty($f)) {
            $pos = strpos($f, " ");
            $fecha = "";
            if ($pos !== false) {
                list ($fecha, $hora) = explode(" ", $f);
            } else {
                $fecha = $f;
            }

            if ($fecha !== "") {
                list($a, $m, $d) = preg_split("/[\/|-]/", $fecha);
                $dateVal = new DateTime($a . "-" . $m . "-" . $d);
                $dia = $dateVal->format("w"); // w = 0 (para domingo) hasta 6 (para sábado)
                $cadena = ($addDayName ? getNombreDelDia($dia) . ', ' : '' ) . $d . ' de ' . getNombreDelMes($m) . ' de ' . $a;
            }
        }
        return $cadena;
    }

    /**
     * Función que regresa la clase CSS del icono dependiendo de dos fechas proporcionadas
     * @param date $fechaProgramada Fecha en formato YYYY-MM-DD de la fecha máxima en la que se debe realizar la tarea
     * @param date $fechaEjecucion Fecha en formto YYYY-MM-DD en la que se ejecutó la tarea
     * @return string Regresa el nombre de la clase correspondiente
     */
    function get_clase_de_timeline($fechaProgramada, $fechaEjecucion) {
        $class = "default";
        $hoy = new DateTime("now");
        if (empty($fechaProgramada)) {
            return $class;
        }

        $DTfechaProgramada = new DateTime($fechaProgramada);
        $DTfechaEjecucion = new DateTime($fechaEjecucion);

// Elegimos la clase
        if ($DTfechaProgramada <= $hoy) {
            $class = 'danger';
            if (!empty($fechaEjecucion)) {
                if ($DTfechaEjecucion <= $DTfechaProgramada) {
                    $class = 'success';
                } elseif ($DTfechaEjecucion > $DTfechaProgramada) {
                    $class = "danger";
                }
            }
        } elseif (!empty($fechaEjecucion)) {
            if ($DTfechaEjecucion <= $DTfechaProgramada) {
                $class = "success";
            } else {
                $class = "danger";
            }
        }
        return $class;
    }

    /**
     * Esta función regresa el icono correcto que se debe mostrar en la linea de tiempo segun la fecha de ejecucion y la fecha programada
     * @param string $fechaProgramada Valor de la fecha programada. YYYY-MM-DD
     * @param string $fechaEjecucion Valor de la fecha de ejecución YYYY-MM-DD
     * @param mixed $idAuditoria Valor de la fecha de auditoria, o también puede ser el ID de la auditoria
     * @return string Regresa el nombre del icono
     */
    function get_icono_de_timeline($fechaProgramada, $fechaEjecucion, $idAuditoria = NULL) {
        $icono = "more_horiz";
        $aux = new DateTime("now");
        $hoy = new DateTime($aux->format("Y-m-d"));
        $fechaInicioAuditoria = $idAuditoria;
        if (empty($fechaProgramada)) {
            return $icono;
        }

        if (!empty($idAuditoria) && strpos($idAuditoria, "-") === FALSE) {
            $strSQL = "SELECT DATE(FROM_UNIXTIME(fechaIniReal, '%Y-%m-%d')) fechaIniReal FROM cat_auditoria WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
            $dbCYSA = conectarBD();
            $resAuditoria = $dbCYSA->ejecutaQuery($strSQL);
            $auditoria = mysql_fetch_assoc($resAuditoria);
            $fechaInicioAuditoria = $auditoria['fechaIniReal'];
        }

        $DTfechaProgramada = new DateTime($fechaProgramada);
        $DTfechaEjecucion = new DateTime($fechaEjecucion);
        $DTfechaInicioAuditoria = new DateTime($fechaInicioAuditoria);

// Elegimos icono
        if ($DTfechaInicioAuditoria < $hoy && $DTfechaProgramada < $hoy) {
            if ($DTfechaInicioAuditoria <= $DTfechaProgramada) {
                $icono = "check";
            }
            if (trim($fechaEjecucion) != "" && !empty($fechaEjecucion)) {
                if ($DTfechaProgramada < $DTfechaEjecucion) {
                    $icono = "close";
                }
                if ($DTfechaEjecucion <= $DTfechaProgramada) {
                    $icono = "check";
                }
            } else {
                $icono = "help_outline"; //help help_outline
            }
        } else {
            if (!empty($fechaEjecucion)) {
                if ($DTfechaEjecucion <= $DTfechaProgramada) {
                    $icono = "check";
                } else {
                    $icono = "close";
                }
            }
        }
        return $icono;
    }

    /**
     * Función que regresa el nombre del auditor líder de una auditoría
     * @param int $idAuditoria Identificador de la auditoría
     * @return string Cadena de texto del líder de la auditoría
     */
    function get_auditor_lider($idAuditoria) {
        $data = getAuditoria($idAuditoria);
        $return = "";
        if (!empty($data) && is_array($data)) {
            $return = $data['lider'];
        }
        return $return;
    }

    /**
     * Función que regresa información de la linea de tiempo de una auditoría
     * @param int $idAuditoria Identificador de la auditoría
     * @return mixed. Regresa un arreglo con la información de la línea de tiempo de una auditoría. Regresa FALSE en caso de error.
     */
    function get_timeline($idAuditoria = NULL) {
        if (empty($idAuditoria)) {
            return FALSE;
        }

        $dbCYSA = conectarBD();
        $strSQL = "SELECT * FROM cat_auditoria WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
        $result = $dbCYSA->ejecutaQuery($strSQL);
        $r = mysql_fetch_assoc($result);
        if (!empty($r['idAuditoriaOrigen'])) {
            $select = array(
                'fechaIniRev1 fechaIniAudit',
                'fechaFinRev1 fechaFinAudit',
                'fechaIniRealRev1 fechaIniReal',
                'fechaAprovacionRev1 fechaFinReal',
                'DATEDIFF(fechaIniRealRev1, fechaIniRev1) reprogramacion_inicio_dias_naturales',
                'DATEDIFF(fechaFinRealRev1, fechaFinRev1) reprogramacion_fin_dias_naturales'
            );
        } else {
            $select = array(
                'DATE(FROM_UNIXTIME(fechaIniAudit)) fechaIniAudit',
                'DATE(FROM_UNIXTIME(fechaFinAudit)) fechaFinAudit',
                'DATE(FROM_UNIXTIME(fechaIniReal)) fechaIniReal',
                'DATE(FROM_UNIXTIME(fechaFinReal)) fechaFinReal',
                'DATEDIFF(DATE(FROM_UNIXTIME(fechaIniReal)), DATE(FROM_UNIXTIME(fechaIniAudit))) reprogramacion_inicio_dias_naturales',
                'DATEDIFF(DATE(FROM_UNIXTIME(fechaFinReal)), DATE(FROM_UNIXTIME(fechaFinAudit))) reprogramacion_fin_dias_naturales'
            );
        }
        $strSQL = "SELECT CONCAT(area,'/',tipo,'/',numero,'/',anio) AS nombreAuditoria, tipo, idEmpleado, bSinObservacionAP, fLimiteInfoRev1, fechaRecibeInfoRev1, fechaAprovacionRev1, statusAudit, fechaSelloOEA, anio, " . implode(",", $select) . " FROM cat_auditoria WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
        $result = $dbCYSA->ejecutaQuery($strSQL);

        $audi = array();
        if (mysql_num_rows($result) > 0) {
            $audi = mysql_fetch_assoc($result);
        }
        $proceso = get_proceso_de_auditoria($idAuditoria);
        $etapas = get_etapas($idAuditoria, $proceso['procesos_id'], $audi);
//$tareas = get_tareas($idAuditoria, array_column($etapas, 'etapas_id'), $audi);
//$entregables = get_entregables(array_column($tareas, 'tareas_id'));
        $audi['reprogramacion_inicio_dias_habiles'] = getDiasHabiles($audi['fechaIniAudit'], $audi['fechaIniReal']);
        $audi['reprogramacion_fin_dias_habiles'] = getDiasHabiles($audi['fechaFinAudit'], $audi['fechaFinReal']);
        $audi['idAuditoria'] = $idAuditoria;
        $prorrogas = get_prorrogas($idAuditoria);
        if (count($prorrogas) > 0) {
            $audi['prorrogas'] = $prorrogas;
        }
        $audi['prorrogas_fecha_maxima_para_generarlas'] = get_fecha_maxima_para_generar_prorroga($idAuditoria);

// Obtenemos el nombre del líder de la auditoría
        $lider = get_auditor_lider($idAuditoria);
        if (!empty($lider)) {
            $audi['lider'] = $lider;
        }
// Obtenemos el nombre del equipo
        $equipo = getEquipo($idAuditoria);
        if (count($equipo) > 0) {
            $audi['equipo'] = $equipo;
        }

        $etapaActual = getEtapaAuditoria($idAuditoria, NULL);
        include_once ('../modelo/configCYSA.php');
        $audi['etapa'] = $etapaActual['etapa'];
        $datosAudit = getAuditoria($idAuditoria);
        if (is_null($datosAudit['idAuditoriaOrigen'])) {
            $observaciones = getListaObservaciones($idAuditoria);
        } else {
            $observaciones = getListaObservaciones($datosAudit['idAuditoriaOrigen']);
        }
        $aux = get_recomendaciones($idAuditoria);
        $estadosRecomendaciones = $aux[0];
        $revisiones = $aux[1];
        $numeroRecomendaciones = $aux[2];
        $isSinObservaciones = is_sin_observaciones($idAuditoria);
        if ($isSinObservaciones AND count($etapas) > 1) {
            unset($etapas[0]);
        }

        $recomendaciones = array_count_values($estadosRecomendaciones);
        $totalRecomendaciones = count($estadosRecomendaciones);
// Si todas las recomendaciones han sido solventadas, entonces
// ocultar el hito del citatorio
        if (isset($recomendaciones[2]) && $totalRecomendaciones == $recomendaciones[2]) {
            $key = array_search('89', array_column($etapas[0]['tareas'], 'configuraciones_id'));
            if (!empty($key)) {
                unset($etapas[0]['tareas'][$key]);
                $etapas[0]['tareas'] = array_values($etapas[0]['tareas']);
            }
        }
// Revisamos si tiene una auditoria de seguimiento
        $idAuditoriaSeguimiento = get_id_auditoria_de_seguimiento($idAuditoria);
// Creamos la variable que servirá para generar el JSON
        $data = array(
            'procesos_id' => $proceso['procesos_id'],
            'etapas' => $etapas,
            'auditoria' => $audi,
            'auditoria_seguimiento' => $idAuditoriaSeguimiento,
            'observaciones' => $observaciones,
            'revisiones' => $revisiones,
            'is_sin_observaciones' => $isSinObservaciones,
            // El dato "recomendaciones" viene de la siguiente forma
// $variable[$index] => $valor
// $variable[status] = Cantidad de recomendaciones que tienen el status del index
// status 4 = ATENDIDA
// status 2 = SOLVENTADA
// status 1 = NO ATENDIDA
            'recomendaciones' => $recomendaciones,
            'totalRecomendaciones' => $totalRecomendaciones,
            // El dato "numeroRecomendaciones" viene de la sigueiente forma
// $array[$index] => array(Arreglo que contiene el numObservacion.numRecomendacion)
// $array[status] = array(Arreglo que contiene el numObservacion.numRecomendacion)
            'numeroRecomendaciones' => $numeroRecomendaciones,
            'estadosRecomendaciones' => $estadosRecomendaciones
        );
        return $data;
    }

    /**
     * Obtiene información sobre una Dirección del Ayuntamiento
     * @param int $idDireccion Identificador de la Dirección
     * @return array Arreglo que contiene información de la Dirección
     */
    function get_direccion_del_ayuntamiento($idDireccion) {
        require_once ('../../CISOP/modelo/conexion.php');
        $return = array();
        $strSQL = "SELECT * FROM ayunta_direccion WHERE clv_dir = " . $idDireccion . " AND direccionActiva = 1";
        $dbSAC = conectarBD(BD_CONT);
        $result = $dbSAC->ejecutaQuery($strSQL);
        if ($result && mysql_num_rows($result) == 1) {
            $return = mysql_fetch_assoc($result);
        }
        return $return;
    }

    /**
     * Función que permite conocer la información de una subdirección
     * @param integer $idDireccion Identificador de la Dirección del Ayuntamiento
     * @param integer $idSubdireccion Identificador de la Subdirección de la Dirección
     * @return array Devuelve un arreglo con la información de la subdirección
     */
    function get_subdireccion_del_ayuntamiento($idDireccion, $idSubdireccion) {
        require_once ('../../CISOP/modelo/conexion.php');
        $return = array();
        $strSQL = "SELECT * FROM ayunta_subdireccion WHERE clv_dir = " . $idDireccion . " AND clv_subdir = " . $idSubdireccion . " AND subdirActiva = 1";
        $dbSAC = conectarBD(BD_CONT);
        $result = $dbSAC->ejecutaQuery($strSQL);
        if ($result && mysql_num_rows($result) == 1) {
            $return = mysql_fetch_assoc($result);
        }
        return $return;
    }

    /**
     * Función que devuelve información de un departamento
     * @param mixed $idDireccion Identificador de la Dirección del Ayuntamiento
     * @param mixed $idSubdireccion Identificador de la Subdirección de la Dirección
     * @param mixed $idDepartamento Identificador del Departamento de la Subdirección
     * @return array Regresa un arreglo con la información del departamento
     */
    function get_departamento_del_ayuntamiento($idDireccion, $idSubdireccion, $idDepartamento) {
        require_once ('../../CISOP/modelo/conexion.php');
        $return = array();
        $strSQL = "SELECT * FROM ayunta_departamento WHERE clv_dir = " . $idDireccion . " AND clv_subdir = " . $idSubdireccion . " AND clv_depto = " . $idDepartamento . " AND deptoActivo = 1";
        $dbSAC = conectarBD(BD_CONT);
        $result = $dbSAC->ejecutaQuery($strSQL);
        if ($result && mysql_num_rows($result) == 1) {
            $return = mysql_fetch_assoc($result);
        }
        return $return;
    }

    /**
     * Función que obtiene el texto de un documento DOCX
     * @param string $filename Ruta del documento
     * @return string Texto que tiene el documento
     */
    function read_docx($filename) {
        $striped_content = '';
        $content = '';

        if (!$filename || !file_exists($filename))
            return false;

        $zip = zip_open($filename);
        if (!$zip || is_numeric($zip))
            return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE)
                continue;

            if (zip_entry_name($zip_entry) != "word/document.xml")
                continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }
        zip_close($zip);
        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

    /**
     * Esta función regresa una bitacora de los correos electrónicos enviados a un funcionario
     * @param int $idEmpleado Identificador del empleado
     * @param int $idAuditoria Identificador de la auditoría
     * @return array Arreglo que contiene la bitacora de correos electrónicos enviados al funcionario.
     */
    function get_correos_enviados_al_funcionario($idEmpleado = NULL, $idAuditoria = NULL) {
        $return = array();
        if (!empty($idEmpleado) && intval($idEmpleado) > 0) {
            $dbCYSA = conectarBD();
            $strSQL = "SELECT cfec.*, CONCAT(f.apF, ' ', f.amF, ' ', f.nombreF) AS nombreUsuario, CONCAT(f2.apF, ' ', f2.amF, ' ', f2.nombreF) AS nombreEmpleado "
                    . "FROM cat_funcionario_envio_correo cfec "
                    . "LEFT JOIN cat_funcionario f ON f.idEmpleado = cfec.idUsuario "
                    . "LEFT JOIN cat_funcionario f2 ON f2.idEmpleado = cfec.idEmpleado "
                    . "WHERE cfec.idEmpleado = " . $idEmpleado;
            if (!empty($idAuditoria)) {
                $strSQL .= " AND idAuditoria = " . $idAuditoria;
            }
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if ($result && mysql_num_rows($result) > 0) {
                while ($r = mysql_fetch_assoc($result)) {
                    array_push($return, $r);
                }
            }
        }
        return $return;
    }

    /**
     * Con esta función se obtiene el prefijo correspondiente usado para los nombres de las bases de datos.
     * @return string Prefijo usado para los nombres de las bases de datos
     */
    function get_prefix() {
        return DB_PREFIX;
    }

    /**
     * Obtiene la versión de PHP del servidor web
     * @return float Devuelve el valor que contiene la versión del analizador de PHP en ejecución o extensión
     */
    function get_phpversion() {
        $version = phpversion();
        list($a, $b, $c) = explode(".", $version);
        $return = $a * 10000 + $b * 100 + $c;
        return $return;
    }

    /**
     * Función que obtiene la fecha de un valor de tipo Fecha/Hora.
     * @param string $date Fecha/Hora en formato YYYY-MM-DD HH:MM:SS
     * @return string Devuelve la fecha en formato YYYY-MM-DD
     */
    function parseDatetime2Date($date) {
        list($fecha, $tiempo) = explode(" ", $date . " ");
        return $fecha;
    }

    /**
     * Devuelve la cadena de texto correspondiente al status de la Observación
     * @param integer $status Identificador del status de la observación
     * @param boolean $plural TRUE para indicar que la frase será en plural.
     * @param string $tiempo Puede ser [presente, pasado o futuro] y sirve para indicar en qué tiempo se desea la frase.
     * @return string Devuelve el mensaje con las características especificadas; asi como el icono y clase CSS.
     */
    function get_estado_observacion($status, $plural = FALSE, $tiempo = "presente") {
        $msg = "SIN ESPECIFICAR";
        $icon = "face";
        $class = "default";
        switch ($status) {
            case OBSERVACION_NO_SOLVENTADA:
                switch ($tiempo) {
                    case "presente":
                        $msg = "No Solventada" . ($plural ? 's' : '');
                        break;
                    case "pasado":
                        $msg = "No solvent" . ($plural ? 'aron' : 'ó');
                        break;
                    case "futuro":
                        break;
                }
                $icon = "mood_bad";
                $class = "danger";
                break;
            case OBSERVACION_SOLVENTADA:
                switch ($tiempo) {
                    case "presente":
                        $msg = "Solventada" . ($plural ? 's' : '');
                        break;
                    case "pasado":
                        $msg = "Solvent" . ($plural ? 'aron' : 'ó');
                        break;
                    case "futuro":
                        break;
                }
                $icon = "mood";
                $class = "success";
                break;
            case OBSERVACION_ATENDIDA:
                switch ($tiempo) {
                    case "presente":
                        $msg = "Atendida" . ($plural ? 's' : '');
                        break;
                    case "pasado":
                        $msg = "Atendi" . ($plural ? 'eron' : 'ó');
                        break;
                    case "futuro":
                        break;
                }
                $icon = "mood";
                $class = "warning";
                break;
        }
        $return = array(
            'status' => $msg,
            'class' => $class,
            'icon' => $icon
        );
        return $return;
    }

    /**
     * Devuelve los documentos de prorroga solicitados durante la solventación
     * @param integer $idAuditoria Identificador de la auditoria
     * @return array Devuelve el arreglo con los documentos de prorroga relacionados a la auditoria. NULL en caso contrario.
     */
    function get_prorrogas($idAuditoria) {
        $strSQL = "SELECT * FROM documentos WHERE idTipoDocto = 4 AND bAprovado=1 AND idAuditoria = " . $idAuditoria;
        $dbCYSA = conectarBD();
        $resultProrroga = $dbCYSA->ejecutaQuery($strSQL);
        $prorrogas = NULL;
        if ($resultProrroga && mysql_num_rows($resultProrroga) > 0) {
            $arrayProrrogas = array();
            while ($r = mysql_fetch_assoc($resultProrroga)) {
                $strSQL = "SELECT idParrafo, valor FROM documentos_detalle WHERE idDocto = " . $r['idDocto'];
                $resDoctoDetalles = $dbCYSA->ejecutaQuery($strSQL);
                if ($resDoctoDetalles && mysql_num_rows($resDoctoDetalles) > 0) {
                    $aux = array();
                    while ($rr = mysql_fetch_assoc($resDoctoDetalles)) {
//array_push($aux, $rr);
                        $aux[$rr['idParrafo']] = $rr['valor'];
                    }
                    $r['prorroga_detalles'] = $aux;
                }
                array_push($arrayProrrogas, $r);
            }
            $prorrogas = $arrayProrrogas;
        }
        return $prorrogas;
    }

    /**
     * Devuelve el texto del status de la auditoria
     * @param integer $idAuditoria Identificador de la auditoria
     * @return string Cadena de texto que refleja el status de la auditoria. (Corresponde al campo tareas.tareas_fecha_reportes_label de la linea de tiempo.
     */
    function get_status_auditoria($idAuditoria) {
        $aux = getEtapaAuditoria($idAuditoria);
        $etapaActual = intval($aux['etapa']);
        $auditoria = getAuditoria($idAuditoria);
        if (intval($aux['etapa']) == ETAPA_FIN) {
            $observaciones = get_observaciones($idAuditoria);
            if (!empty($observaciones)) {
                $todasSolventadas = TRUE;
                foreach ($observaciones as $obs) {
                    $o = getestatusObservacion($obs['idObs']);
                    if (intval($o) != SOLVENTADA) {
                        $todasSolventadas = FALSE;
                        break;
                    }
                }
                if (!$todasSolventadas) {
                    $idAuditoriaSeguimiento = get_id_auditoria_de_seguimiento($idAuditoria);
                    $auditoriaSeguimiento = getAuditoria($idAuditoriaSeguimiento);
                }
            }
            return array(
                'status' => 'Concluida.' . (!empty($idAuditoriaSeguimiento) ? ' Seguimiento programado' : ' Pendiente programar Seguimiento'),
                'fecha_por_mostrar' => !empty($idAuditoriaSeguimiento) ? date("Y-m-d", $auditoriaSeguimiento['fIniReal']) : $auditoria['fechaOEDRes']
            );
        } elseif (intval($aux['etapa']) > 20002) {
            $etapaActual = 20002;
        }
        $idEmpleadoAuditorLider = get_empleado($auditoria['idLider']);
// Verificamos que si la tarea es FIN REVISION DEL JEFE/COORDINADOR y el lider de la auditoria
// es el jefe/coordinador, entonces esta tarea no debe aparecer en la linea de tiempo
        $validacion1 = "";
        if (isset($idEmpleadoAuditorLider['idPuesto']) && $idEmpleadoAuditorLider['idPuesto'] == JEFE_DE_DEPARTAMENTO) {
            $validacion1 = " AND t.tareas_nombre NOT LIKE 'Fin Revisión del Jefe/Coordinador' ";
        }
        $ultimaTareaEjecutada = NULL;
        $strSQL = "
    SELECT
	e.etapas_nombre,
	c.configuraciones_id,
	t.tareas_id,
	t.tareas_nombre,
	c.configuraciones_orden_ejecucion,
	c.configuraciones_fecha_programada,
	c.configuraciones_fecha_ejecucion,
	es.etiquetas_status_nombre,
	c.configuraciones_etiquetas_status_id,
	c.configuraciones_permisos_editar_fecha,
	c.configuraciones_etiquetas_status_id_next
    FROM configuraciones c
    INNER JOIN tareas t ON t.tareas_id = c.configuraciones_tareas_id
    INNER JOIN etapas e ON e.etapas_id = c.configuraciones_etapas_id
    INNER JOIN procesos p ON p.procesos_id = c.configuraciones_procesos_id
    INNER JOIN etiquetas_status es ON es.etiquetas_status_id = c.configuraciones_etiquetas_status_id
    WHERE p.procesos_tipo_auditoria LIKE '%" . $auditoria['tipo'] . "%' 
        AND configuraciones_activo = 1
	AND c.configuraciones_etiquetas_status_id IS NOT NULL 
        " . $validacion1 . "
    ORDER BY p.procesos_id DESC, e.etapas_orden_ejecucion DESC, c.configuraciones_orden_ejecucion DESC";
        $dbTimeline = conectar_timeline();
        $resTareas = $dbTimeline->ejecutaQuery($strSQL);
        $fechaEjecucion = NULL;
        $tareaAnterior = NULL;
        $fechaProgramada = NULL;
        $fechaLectura = NULL; // Auxiliar para no perder la fecha de ARA o ARR
// Expresión regular para obtener la la estructura el nombre de un campo que siempre viene de la siguiente forma
// {BASEDATO} . {TABLA} . {CAMPO}
        $expresionRegular = '/(([A-z\d\-}]{1,})\.){2}([A-z\d-]){1,}/';
        if ($resTareas && mysql_num_rows($resTareas)) {
            while (empty($ultimaTareaEjecutada) && is_resource($resTareas) && $tarea = mysql_fetch_assoc($resTareas)) {
                if (empty($ultimaTareaEjecutada)) {
                    $fechaEjecucion = NULL;
                    $fechaProgramada = get_fecha_programada_de_tarea($idAuditoria, $tarea['configuraciones_id']);
// Obtengo el valor del campo de ejecucion relacionada a la tarea
                    if (!empty($tarea['configuraciones_fecha_ejecucion'])) {
                        $fechaEjecucion = get_fecha_ejecucion_de_tarea($idAuditoria, $tarea['configuraciones_id']);
                        if ($tarea['configuraciones_fecha_ejecucion'] == 18) {
                            $fechaEjecucion = NULL;
                        }
// Revisamos si ya se capturo el campo requerido para activar la tarea
// y en caso de que sea NULL entonces continuamos iterando
// También verificamos que si la tarea no sea FIN REVISION DEL JEFE/COORDINADOR
// y el lider de la auditoria no sea Jefe de Departamento para entonces tambien omitir la validacion
                        if (!empty($tarea['configuraciones_permisos_editar_fecha']) && $tarea['tareas_nombre'] != "Fin Aprobación del Subdirector" && isset($idEmpleadoAuditorLider['idPuesto']) && $idEmpleadoAuditorLider['idPuesto'] != JEFE_DE_DEPARTAMENTO) {
                            $PREFIX = get_prefix();
                            $c = get_campo($tarea['configuraciones_permisos_editar_fecha']);
                            preg_match($expresionRegular, $c, $matches);
                            if (count($matches) > 0) {
                                list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
                                $strSQL = "SELECT " . $campo . " FROM " . $PREFIX . $basedatos . "." . $tabla . " WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
                                $dbCYSA = conectarBD();
                                $aux_result = $dbCYSA->ejecutaQuery($strSQL);
                                if (is_resource($aux_result) && mysql_num_rows($aux_result) > 0) {
                                    $aux = mysql_fetch_assoc($aux_result);
                                    if (empty($aux[$campo])) {
                                        $fechaEjecucion = NULL;
                                    }
                                } else {
                                    $fechaEjecucion = NULL;
                                }
                            }
                        }
                    }
// Si es diferente de vacio, entonces significa que la tarea ya se realizó
// y entonces es la ultima tarea realizada en donde se haya capturada la fecha
                    if (!empty($fechaEjecucion)) {
// Para el caso de la LECTURA DEL ARA o ARR falta validar que la fecha de ejecución
// ya haya llegado, porque sino, puede que esta en el envio de citatorio, pero aun
// falten los dos dias para la lectura, asi que por eso se hace la siguiente validacion
//
                        // 12 = Lectura de ARA, 22 = Lecuta de Resultados de ARR, 25 = Lectura de Resultados de Revision
                        if (in_array($tarea['tareas_id'], array(12, 22, 25))) {
                            $fechaLectura = $fechaEjecucion;
                            $DT_hoy = new DateTime("now");
                            $DT_fechaEjecucion = new DateTime($fechaEjecucion);
                            if ($DT_hoy < $DT_fechaEjecucion) {
                                $fechaEjecucion = NULL;
                            }
                        }
// Esta condición se puso, porque cuando actualmente esta el ENVÍO DE DOCUMENTOS se debe mostrar
// la fecha de Inicio Real de la Primera Revision
                        if ($tarea['tareas_id'] == 14 && empty($tarea['idAuditoriaOrigen'])) {
                            $strSQL = "SELECT fechaIniRealRev1 FROM " . CAT_AUDITORIAS . " WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
                            $dbCYSA = conectarBD();
                            $aux_result = $dbCYSA->ejecutaQuery($strSQL);
                            if (is_resource($aux_result)) {
                                $aux = mysql_fetch_assoc($aux_result);
                                $fechaEjecucion = $aux['fechaIniRealRev1'];
                            }
                        }
                        if (!empty($fechaEjecucion)) {
                            if (!is_null($fechaLectura)) {
                                $fechaEjecucion = $fechaLectura;
                            }
//                        if (strpos($tarea['etiquetas_status_nombre'], "__FECHA__") !== FALSE) {
//                            $ultimaTareaEjecutada = str_replace("__FECHA__", "\n" . mysqlDate2OnlyDate($fechaEjecucion), $tarea['etiquetas_status_nombre']);
//                        } else {
//                            $ultimaTareaEjecutada = $tarea['etiquetas_status_nombre'];
//                        }
                            $ultimaTareaEjecutada = $tarea['etiquetas_status_nombre'];
                        }
                    } else {
// Almacenamos la tarea anterior por si acaso hubiese prórroga
                        $tareaAnterior = $tarea;
                    }
                } else {
                    $resTareas = FALSE;
                }
            }
            if (intval($tarea['configuraciones_etiquetas_status_id_next']) > 0) {
                $fechaEjecucion = get_fecha_ejecucion_de_tarea($idAuditoria, $tareaAnterior['configuraciones_id']);
                $fechaProgramada = get_fecha_programada_de_tarea($idAuditoria, $tareaAnterior['configuraciones_id']);
                $ultimaTareaEjecutada = $tareaAnterior['etiquetas_status_nombre'];
            }
            if (!empty($ultimaTareaEjecutada) && strpos($ultimaTareaEjecutada, "__PRORROGA__") !== FALSE) {
                $prorrogas = get_prorrogas($idAuditoria);
                if (count($prorrogas) > 0) {
                    $auditoria = getCtrlAuditoriaDatos($idAuditoria);
                    $fechaProgramada = $auditoria['fIniReal'];
                    if (!empty($auditoria['fIniRealRev1'])) {
                        $fechaProgramada = $auditoria['fIniRealRev1'];
                    }
                    if (!empty($auditoria['fIniRealRev2'])) {
                        $fechaProgramada = $auditoria['fIniRealRev2'];
                    }
                    $nuevaFecha = mysqlDate2OnlyDate($fechaProgramada);
                    $ultimaTareaEjecutada = str_replace("__PRORROGA__", " con prorroga", $ultimaTareaEjecutada);
                    $aux = explode(":", $ultimaTareaEjecutada);
                    $ultimaTareaEjecutada = $aux[0] . ": " . $nuevaFecha;
                } else {
                    $ultimaTareaEjecutada = str_replace("__PRORROGA__", "", $ultimaTareaEjecutada);
                }
            }
        }
        if (is_null($ultimaTareaEjecutada)) {
            if (!empty($auditoria['idAuditoriaOrigen'])) {
                $ultimaTareaEjecutada = "Paquete Inicial";
            } else {
                if ($etapaActual = 20002) {
                    $ultimaTareaEjecutada = utf8_decode("Inicio de Revisión de Solventación");
                } else {
                    $ultimaTareaEjecutada = utf8_decode("Inicio de Auditoría");
                }
            }
        }
        $fechaPorMostrar = empty($fechaEjecucion) ? $fechaProgramada : $fechaEjecucion;
        $fechaVencimientoDeTarea = empty($tareaAnterior) ? NULL : get_fecha_programada_de_tarea($idAuditoria, $tareaAnterior['configuraciones_id']);
        $return = array(
            'status' => $ultimaTareaEjecutada,
            'fecha_por_mostrar' => $fechaPorMostrar,
            'fecha_programada' => $fechaProgramada,
            'fecha_ejecucion' => $fechaEjecucion,
            // Esta fecha se refiere a la variable $tareaAnterior, y es la fecha programada de la tarea anterior
            'fecha_vencimiento' => $fechaVencimientoDeTarea
        );
        return $return;
    }

    /**
     * Obtiene la estructura JSON que sirve para generar la gráfica de flowchart
     * @param integer $idProceso Identificador del proceso
     * @param integer $idEtapa Identificador de la etapa
     * @return string Cadena JSON que sirve generar el flowchart
     */
    function get_flowchart_data($idProceso = NULL, $idEtapa = NULL) {
        $tareas = get_configuraciones_tareas($idProceso, $idEtapa);
        $operators = array();
        $links = array();
        $top = 20;
        $left = 20;
        $idAnterior = NULL;
        foreach ($tareas as $index => $t) {
            $posicion = $t['configuraciones_flowchart_operator_position'] != "" ? json_decode($t['configuraciones_flowchart_operator_position'], TRUE) : NULL;
            unset($t['configuraciones_flowchart_operator_position']);
            $op = array(
                'operator' . $t['configuraciones_id'] => array(
                    'data' => $t,
                    'top' => !is_null($posicion) ? $posicion['top'] : $top,
                    'left' => !is_null($posicion) ? $posicion['left'] : $left,
                    'properties' => array(
                        'title' => $t['tareas_nombre'],
                        'inputs' => array(
                            'input_1' => array(
                                'label' => 'Entrada'
                            )
                        ),
                        'outputs' => array(
                            'output_1' => array(
                                'label' => 'Salida'
                            ),
                        ),
                    ),
                )
            );
            $operators = array_merge($operators, $op);
            if (!is_null($idAnterior)) {
                $enlace = array(
                    $index => array(
                        'fromOperator' => 'operator' . $idAnterior,
                        'fromConnector' => 'output_1',
                        'toOperator' => 'operator' . $t['configuraciones_id'],
                        'toConnector' => 'input_1'
                    ),
                );
                $links = array_merge($links, $enlace);
            }
            $idAnterior = $t['configuraciones_id'];
            $strLen = strlen($t['tareas_nombre']);
            $s = $strLen > 20 ? $strLen * 8 : $strLen * 10;
            $left += $s;
            if ($left > 1000) {
                $top += 100;
                $left = 20;
            }
        }
        $result = array(
            'operators' => $operators,
            'links' => $links
        );
        return $result;
    }

    /**
     * Obtiene el nombre del campo dependiendo del ID de referencia
     * @param integer $idCampo Identificador del campo de la base de datos
     * @return string Nombre del campo de la base de datos
     */
    function get_campo($idCampo = NULL) {
        $return = "";
        if (!empty($idCampo)) {
            $strSQL = "SELECT * FROM campos WHERE campos_id = " . $idCampo . " LIMIT 1";
            $dbTL = conectar_timeline();
            $result = $dbTL->ejecutaQuery($strSQL);
            if (mysql_num_rows($result) > 0) {
                $r = mysql_fetch_assoc($result);
                if (!empty($r['campos_funcion'])) {
                    $return = str_replace("__CAMPO__", $r['campos_nombre'], $r['campos_funcion']);
                } else {
                    $return = $r['campos_nombre'];
                }
            }
        }
        return $return;
    }

    /*
     * Función que regresa el tipo de dato MYSQL de un campo específico
     * @param string $campoEjecucion Nombre del campo al cual se desea obtener su tipo
     * @return string Devuelve el tipo de campo MySQL (int, string, date, datetime, text,...)
     */

    function get_tipo_campo_mysql($campoEjecucion = NULL) {
        $return = NULL;
        $strSQL = "SELECT * FROM cat_auditoria c "
                . "INNER JOIN cat_auditoria_fechas f ON f.idAuditoria = c.idAuditoria "
                . "LIMIT 1";
        $dbCYSA = conectarBD();
        $result = $dbCYSA->ejecutaQuery($strSQL);
        if (mysql_num_rows($result) == 1) {
            $campos = mysql_num_fields($result);
            for ($i = 0; $i < $campos; $i++) {
                list($servidor, $basedatos, $campo) = explode(".", $campoEjecucion);
                if ($campo == mysql_field_name($result, $i)) {
                    $return = strtoupper(mysql_field_type($result, $i));
                }
            }
        }
        return $return;
    }

    /**
     * Función que determina el rango de fechas de la semana, según la fecha proporcionada
     * @param string $fecha Cadena con formato YYYY-MM-DD que especifica una fecha. De forma predeterminada es la fecha actual.
     * @param boolen $semanaInglesa Indica si se desea como fecha final el viernes o el domingo. De forma predeterminada se usa la semana inglesa (De lunes a viernes)
     * @return array Arreglo con los valores de FECHA_INICIO y FECHA_FIN que contienen las fechas de inicio y fin de la semana solicitada
     */
    function get_rango_de_semana($fecha = "now", $semanaInglesa = TRUE) {
        date_default_timezone_set(date_default_timezone_get());
        if (empty($fecha) || strtolower($fecha) === "now") {
            $fecha = date("Y-m-d");
        }
        $dt = strtotime($fecha);
        $res['fecha_inicio'] = date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
        if ($semanaInglesa) {
            $res['fecha_fin'] = date('N', $dt) == 5 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next friday', $dt));
        } else {
            $res['fecha_fin'] = date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
        }
        return $res;
    }

    /**
     * Esta función devuelve los documentos relacionados a un tarea (configuracion) de la linea de tiempo para su VoBo
     * @param integer $idConfiguracion Identificador de la configuracion
     * @param integer $idAuditoria [Optional] Identificador de la auditoria
     * @return array Devuelve la informacion correspondiente a los documentos relacionados a la configuracion de la linea de tiempo
     */
    function get_documentos_de_configuracion($idConfiguracion, $idAuditoria = NULL) {
        $return = array();
        $PREFIX = get_prefix();
        if (!empty($idConfiguracion)) {
            $strSQL = "SELECT cd.* " . (!empty($idAuditoria) ? ', dd.denDocto, d.*, cdv.* ' : '')
                    . "FROM " . $PREFIX . "timeline.configuraciones_documentos cd ";
            if (!empty($idAuditoria)) {
                $strSQL .= "LEFT JOIN " . $PREFIX . "cysa.documentos d ON d.idTipoDocto = cd.idTipoDocto AND d.idAuditoria = " . $idAuditoria . " "
                        . "LEFT JOIN " . $PREFIX . "cysa.cat_documentos dd ON dd.idTipoDocto = cd.idTipoDocto "
                        . "LEFT JOIN " . $PREFIX . "cysa.cat_documentos_versiones cdv ON cdv.idVersion = d.idVersion ";
            }
            $strSQL .= "WHERE cd.configuraciones_id = " . $idConfiguracion . " "
                    . "ORDER BY cd.idTipoDocto ASC";
            $dbTL = conectar_timeline();
            $result = $dbTL->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                while ($r = mysql_fetch_assoc($result)) {
                    array_push($return, $r);
                }
            }
        }
        return $return;
    }

    /**
     * Regresa solo las letras iniciales de los nombres y apellidos de un empleado
     * @param integer $idEmpleado Número de nomina del empleado
     * @return string Iniciales del empleado
     */
    function get_iniciales_de_empleado($idEmpleado) {
        $iniciales = $idEmpleado;
        $strSQL = "SELECT nombreF, apF, amF FROM cat_funcionario WHERE idEmpleado = " . $idEmpleado . " LIMIT 1";
        $dbCYSA = conectarBD();
        $result = $dbCYSA->ejecutaQuery($strSQL);
        if (is_resource($result) && mysql_num_rows($result) == 1) {
            $row = mysql_fetch_assoc($result);
            $iniciales = strtoupper(substr($row['nombreF'], 0, 1) . substr($row['apF'], 0, 1) . substr($row['amF'], 0, 1));
        }
        return $iniciales;
    }

    /**
     * Obtiene la información de un empleado
     * @param integer $idEmpleado Número de nómina del empleado
     * @return array Información del empleado
     */
    function get_empleado($idEmpleado) {
        $return = array();
        $strSQL = "SELECT * FROM cat_funcionario WHERE idEmpleado = " . $idEmpleado . " LIMIT 1";
        $dbCYSA = conectarBD();
        $result = $dbCYSA->ejecutaQuery($strSQL);
        if (is_resource($result) && mysql_num_rows($result) == 1) {
            $return = mysql_fetch_assoc($result);
        }
        return $return;
    }

    /**
     * Obtiene los Vistos Buenos de un documento, regresándolos en orden cronologico descendiente (del mas reciente al mas antiguo)
     * @param integer $idDocto Identificador del documento
     * @param integer $idEmpleado [Optional] Identificador del empleado
     * @return array Inforación del Visto Bueno de un documento
     */
    function get_vobos($idDocto, $idEmpleado = NULL, $orderBy = "DESC") {
        $return = array();
        $strSQLEmpleado = "";
        if (!empty($idEmpleado)) {
            if (is_array($idEmpleado)) {
                $strSQLEmpleado = " AND f.idEmpleado IN (" . implode(",", $idEmpleado) . ") ";
            } else {
                $strSQLEmpleado = " AND f.idEmpleado = " . $idEmpleado . " ";
            }
        }
        if (!empty($idDocto)) {
            $strSQL = "SELECT vbr.*, 
            CONCAT(nombreF, ' ', apF, ' ',amF) nombre_empleado, 
            CONCAT(SUBSTRING(nombreF,1, 1),SUBSTRING(apF,1,1),SUBSTRING(amF,1,1)) iniciales,
            vb.bVoBo, vb.idEmpleado
FROM vobos vb
INNER JOIN cat_funcionario f ON f.idEmpleado = vb.idEmpleado
LEFT JOIN vobos_revisiones vbr ON vb.idDocto = vbr.revisiones_idDocto AND vb.idEmpleado = vbr.revisiones_idEmpleado
WHERE vb.idDocto = " . $idDocto . " " . $strSQLEmpleado . " 
ORDER BY revisiones_fecha_creacion " . $orderBy;
            $dbCYSA = conectarBD();
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                while ($r = mysql_fetch_assoc($result)) {
                    array_push($return, $r);
                }
            }
        }
        return $return;
    }

    /**
     * Obtiene la información de la Unidad Administrativa a la cual se le esta realizado la auditoria a partir de un Documento
     * @param integer $idDocto Identificador del Documento
     * @return array Información de la Unidad Administrativa del documento
     */
    function get_ua_de_documento($idDocto) {
        $return = FALSE;
        if (!empty($idDocto)) {
            $strSQL = "SELECT * FROM " . TB_DOCTOS . " d WHERE idDocto = " . $idDocto . " ";
            $dbCYSA = conectarBD();
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) == 1) {
                $row = mysql_fetch_assoc($result);
                $idTipoDocto = intval($row['idTipoDocto']);
                switch ($idTipoDocto) {
                    case 1:
                        $idParrafo = 4; // 4 = Unidad Administrativa Auditada
                        break;
                    case 2:
                        $idParrafo = 16; // 16 = Unidad Administrativa Auditada
                        break;
                    case 9:
                        $idParrafo = SOL_INF_ID_UA; // 128
                        break;
                    case 10:
                        $idParrafo = NULL;
                        break;
                    case 11:
                        $idParrafo = 225; // ACEI_DIR_UA
                        break;
                    case 12:
                        $idParrafo = CITATORIO_ID_UA; // 178 ID de la Unidad Administrativa (Dirección)
                        break;
                    case 18:
                        $idParrafo = EDIDUA; // 196
                        break;
                    case 21:
                        $idParrafo = 224; // AA_DIR_UA
                        break;
                    case 25:
                        $idParrafo = 253; // AANP_UNIDAD
                        break;
                    default:
                        $idParrafo = NULL;
                }
                $PREFIX = get_prefix();
                $strSQL = "SELECT ad.* FROM " . TB_DOCTOS_DETALLE . " dd "
                        . "LEFT JOIN " . $PREFIX . "sac.ayunta_direccion ad ON ad.clv_dir = dd.valor "
                        . "WHERE dd.idDocto = " . $idDocto . " AND dd.idParrafo = " . $idParrafo . " LIMIT 1";
                $result = $dbCYSA->ejecutaQuery($strSQL);
                if (is_resource($result) && mysql_num_rows($result) == 1) {
                    $return = mysql_fetch_assoc($result);
                }
            } else {
                $return = "No se encontró el idDocto";
            }
        }
        return $return;
    }

    /**
     * Obtiene al coordinador o coordinadores de una auditoria
     * @param integer $idAuditoria Identificador de la auditoría
     * @return array Numero de empleado o empleados coordinadores de la auditoría
     */
    function get_coordinador_de_auditoria($idAuditoria) {
        $return = array();
        $PREFIX = get_prefix();
        $strSQL = "SELECT a.idEmpleado, a.area, f.nombreF, f.apF, f.amF, p.idPuesto, p.denPuesto
FROM cat_auditoria a
LEFT JOIN cat_funcionario f ON f.idEmpleado = a.idEmpleado
LEFT JOIN " . $PREFIX . "sac.ayunta_puesto p ON p.idPuesto = f.idPuesto
WHERE idAuditoria = " . $idAuditoria;

        $dbCYSA = conectarBD();
        $result = $dbCYSA->ejecutaQuery($strSQL);
        if (is_resource($result) && mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            // Verificamos que la auditoria pertenezca a Auditoria Interna y
            // que el auditor líder no sea Coordinador de la auditoria
            if (stripos($row['area'], "AI") !== FALSE && $row['idPuesto'] != COORDINADOR_AUDITORIA) {
                $grupos = get_grupos_de_empleado($row['idEmpleado']);
                foreach ($grupos as $idGrupo) {
                    $empleados = get_grupo($idGrupo, TRUE);
                    foreach ($empleados as $idEmpleado) {
                        array_push($return, $idEmpleado);
                    }
                }
                $return = array_unique($return);
            }
        }
        return $return;
    }

    /**
     * Regresa los indentificadores de grupo al que pertenece un empleado
     * @param integer $idEmpleado Identificador del empleado
     * @return array Arreglo que contiene los identificadores del grupo
     */
    function get_grupos_de_empleado($idEmpleado) {
        $return = array();
        if (!empty($idEmpleado)) {
            $PREFIX = get_prefix();
            $dbSAC = conectarBD($PREFIX . 'sac');
            $strSQL = "SELECT * FROM dcont_grupo_integrantes WHERE idEmpleado = " . $idEmpleado;
            $result = $dbSAC->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    array_push($return, $row['idGrupo']);
                }
            }
        }
        return $return;
    }

    /**
     * Regresa los número de empleado de todo el grupo al que pertenece un empleado
     * @param integer $idGrupo Identificador del grupo
     * @param boolean $soloCoordinadores Indica si se desea que solo regrese a los coordinadores del grupo (excluye a los empleados de tipo auditor)
     * @return array Arreglo con los número de empleado que pertenecen al grupo
     */
    function get_grupo($idGrupo, $soloCoordinadores = FALSE) {
        $return = array();
        if (!empty($idGrupo)) {
            $PREFIX = get_prefix();
            $dbSAC = conectarBD($PREFIX . 'sac');
            $inner = $where = "";
            if ($soloCoordinadores) {
                $inner = "INNER JOIN dcont_empleado e ON e.idEmpleado = gi.idEmpleado ";
                $where = "AND e.idPuesto = " . COORDINADOR_AUDITORIA . " AND e.clv_dir = " . CONTRALORIA_MUNICIPAL . " AND clv_subdir = " . AUDIT_INTERNA . " AND clv_depto = 2";
            }
            $strSQL = "SELECT gi.idEmpleado FROM dcont_grupo_integrantes gi " . $inner . " WHERE gi.idGrupo = " . $idGrupo . " " . $where;
            $result = $dbSAC->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    array_push($return, $row['idEmpleado']);
                }
            }
        }
        return $return;
    }

    /**
     * Esta función regresa la URL que permite desplegar el documento de forma correcta
     * @param integer $idTipoDocto Identificador del tipo de documento
     * @return string URL del tipo de documento para que al hacerle clic se visualice en otra ventana
     */
    function get_url_de_documento($idTipoDocto) {
        $url = "";
        $idTipoDocto = 0 - abs($idTipoDocto);
        switch ($idTipoDocto) {
            case SOL_INFORMACION:
            case ORDEN_ENTRADA:
            case CITATORIO:
                $url = "v_frmOficiosPRINT.php?idDocto";
                break;
            case ACTA_RESULTADOS:
            case ACTA_REVISION_PAR:
            case ACTA_RESULTADOS_IC:
            case ACTA_MONITOREO:
            case ACTA_MONITOREO_REVISION:
                $url = "v_frmActasImpresion.php?idDoctoARA";
                break;
            case SOLICITUD_AMPLIACION:
            case SOLICITUD_REPROGRAMACION:
                $url = "printDoctoHTML.php?idDocto";
                break;
            case AUTORIZACION_NP:
                $url = "v_frmgen_AANP.php?idAANP";
                break;
            default:
                $url = "";
                break;
        }
        return $url;
    }

    /**
     * Función para regresar las recomendaciones de un auditoria
     * @param integer $idAuditoria
     * @return array Regresa un arreglo con:<br>[0] => el status de las recomendaciones<br>[1] => los datos de las revisiones<br>[2] => La cantidad de recomendaciones por status
     */
    function get_recomendaciones($idAuditoria) {
        $datosAudit = getAuditoria($idAuditoria);
        if (!empty($datosAudit['idAuditoriaOrigen'])) {
            $observaciones = get_observaciones_propias_de_auditoria($idAuditoria);
        } else {
            $observaciones = getListaObservaciones($idAuditoria);
        }
        $estadosRecomendaciones = $numeroRecomendaciones = $revisiones = array();
        $aux = getEtapaAuditoria($idAuditoria);
        $etapaActual = $aux['etapa'];
        foreach ($observaciones as $obs) {
            $idObservacion = intval($obs['idObs']);
            $n = get_maxima_revision($idAuditoria);
            $numeroRevision = ($n < 2) ? $n : 2;
            if (!empty($datosAudit['idAuditoriaOrigen'])) {
                $idAuditoria = get_real_auditoria_origen($idAuditoria);
                $numeroRevision = get_maxima_revision($idAuditoria);
            }
            $numeroObservacion = intval($obs['numObs']);
            do {
                $revisiones = get_revision_N_de_observacion($idObservacion, $numeroRevision);
                $numeroRevision--;
            } while (empty($revisiones) && $numeroRevision > 0);
            if (isset($revisiones) && !is_null($revisiones) && !empty($revisiones)) {
                // Esta variable sirve para contar las recomendaciones en auditorias anteriores a 2013
                $activarContador = FALSE;
                if (count($revisiones) > 1) {
                    $activarContador = TRUE;
                }
                $contadorRecomendaciones = 0;
                foreach ($revisiones as $r) {
                    $status = intval($r['idEstado']);
                    $status = $status < 1 ? 1 : $status;
                    array_push($estadosRecomendaciones, $status);
                    if (!isset($numeroRecomendaciones[$status])) {
                        $numeroRecomendaciones[$status] = array();
                    }
                    $numRecomendacion = $obs['numObs'] . "." . $r['numRec'];
                    if ($activarContador) {
                        $contadorRecomendaciones++;
                        $numRecomendacion = $obs['numObs'] . "." . ($contadorRecomendaciones);
                    } elseif (empty($r['numRec'])) {
                        $numRecomendacion = $obs['numObs'];
                    }
                    array_push($numeroRecomendaciones[$status], $numRecomendacion);
                }
            }
        }
        return array($estadosRecomendaciones, $revisiones, $numeroRecomendaciones);
    }

    /**
     * Obtiene el listado de observaciones de una auditoria y agrega ademas la clasificación de la observación
     * @param integer $idAuditoria Identificador de la auditoría
     * @return array Listado de las observaciones
     */
    function get_observaciones($idAuditoria) {
        $idAuditoria = get_real_auditoria_origen($idAuditoria);
        $datosAudit = getAuditoria($idAuditoria);
        $observaciones = array();
        if ($datosAudit['bSinObservacionAP'] == 0) { // Si esta marcado diferente a SIN OBSERVACIONES, entonces procedemos
            $observaciones = getListaObservaciones($idAuditoria);
            foreach ($observaciones as $index => $obs) {
                if ($obs['denObs'] !== "SIN OBSERVACIONES") {
                    $status = get_clasificacion_de_observacion($obs['idObs']);
                    $observaciones[$index] = array_merge($observaciones[$index], $status);
                } else {
                    unset($observaciones[$index]);
                }
            }
        }
        return $observaciones;
    }

    /**
     * Devuelve el tipo de clasificación de una observación (Administrativa u Operativa)
     * @param integer $idObservacion Identificador de la observación
     * @return array Clasificación de la observacion
     */
    function get_clasificacion_de_observacion($idObservacion) {
        $strSQL = "SELECT DISTINCT(r.idClasificacion) idClasificacion, ctr.denClasificacion "
                . "FROM recomendacion r "
                . "INNER JOIN cat_tipo_recomendacion ctr ON ctr.idClasificacion = r.idClasificacion "
                . "WHERE idObservacion = " . $idObservacion . " "
                . "ORDER BY r.idClasificacion DESC "
                . "LIMIT 1";
        $dbCYSA = conectarBD();
        $result = $dbCYSA->ejecutaQuery($strSQL);
        $return = FALSE;
        if (is_resource($result) && mysql_num_rows($result) > 0) {
            $r = mysql_fetch_assoc($result);
            $return = array(
                'idClasificacion' => $r['idClasificacion'],
                'denClasificacion' => $r['denClasificacion']
            );
        }
        return $return;
    }

    /**
     * Obtiene toda la información referente al documento especificado
     * @param integer $idDocto Identificador del documento
     * @return array Regresa el información, detalles y el HTML del documento especificado. FALSO en caso de que no se encuentre el documento
     */
    function get_documento($idDocto) {
        $return = FALSE;
        if (!empty($idDocto)) {
            $dbCYSA = conectarBD();
            $strSQL = "SELECT d.*, cd.codigo, cd.denDocto, cd.descDocto, clasDocumento, dh.fechaCreacion AS 'fechaCreacionHTML' FROM documentos d "
                    . "INNER JOIN cat_documentos cd ON cd.idTipoDocto = d.idTipoDocto "
                    . "LEFT JOIN documentos_html dh ON dh.idDocto = d.idDocto "
                    . "WHERE d.idDocto = " . $idDocto . " LIMIT 1";
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                $return = mysql_fetch_assoc($result);
                $strSQL = "SELECT dd.idParrafo, cdd.denParrafo, cdd.descParrafo, valor FROM documentos_detalle dd "
                        . "INNER JOIN cat_documentos_detalle cdd ON cdd.idParrafo = dd.idParrafo "
                        . "WHERE idDocto = " . $idDocto;
                $result = $dbCYSA->ejecutaQuery($strSQL);
                if (is_resource($result) && mysql_num_rows($result) > 0) {
                    $return['detalles'] = array();
                    while ($r = mysql_fetch_assoc($result)) {
                        array_push($return['detalles'], $r);
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Devuelve la información referente al titulo académicod
     * @param integer $idTitulo Identificador del título
     * @return array Regresa un arreglo con la informaición asociada al titulo univeritario. En otro caso regresa FALSO.
     */
    function get_titulo_universitario($idTitulo) {
        $return = FALSE;
        if (!empty($idTitulo)) {
            $dbCYSA = conectarBD();
            $strSQL = "SELECT * FROM cat_titulos WHERE idTitulo = " . $idTitulo . " LIMIT 1";
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                $return = mysql_fetch_assoc($result);
            }
        }
        return $return;
    }

    /**
     * Esta función regresa el identificador de la auditoría de seguimiento de una auditoría
     * @param integer $idAuditoria Identificador de la auditoría
     * @return integer El identificador de la auditoría de seguimiento. Regresa NULL en caso contrario.
     */
    function get_id_auditoria_de_seguimiento($idAuditoria) {
        // Revisamos si tiene una auditoria de seguimiento
        $idAuditoriaSeguimiento = NULL;
        $dbCYSA = conectarBD();
        $strSQL = "SELECT idAuditoria, DATE(FROM_UNIXTIME(fechaIniReal)) fechaIniReal, DATE(FROM_UNIXTIME(fechaFinReal)) fechaFinReal FROM cat_auditoria WHERE idAuditoriaOrigen = " . $idAuditoria . " AND statusAudit > 0 LIMIT 1";
        $result = $dbCYSA->ejecutaQuery($strSQL);
        if (is_resource($result) && mysql_num_rows($result) == 1) {
            $r = mysql_fetch_assoc($result);
            $idAuditoriaSeguimiento = $r['idAuditoria'];
        }
        return $idAuditoriaSeguimiento;
    }

    function minusculas($texto) {
        return mb_strtolower($texto, 'ISO-8859-1');
    }

    /**
     * Obtiene el último VoBo de un documento de un empleado
     * @param integer $idDocto Identificador del documento
     * @param integer $idEmpleado Identificador del empleado
     * @return array Arreglo con la información del último visto bueno
     */
    function get_ultimo_vobo_revisiones_de_documento($idDocto, $idEmpleado) {
        $data = get_vobos($idDocto, $idEmpleado);
        $return = FALSE;
        if (!empty($data)) {
            $return = $data[0];
        }
        return $return;
    }

    /**
     * Función que regresa la fecha máxima para poder generar prorrogas de una auditoría
     * @param integer $idAuditoria Identificador de la auditoría
     * @return date Fecha límite en formato YYYY-MM-DD para generar una prórroga
     */
    function get_fecha_maxima_para_generar_prorroga($idAuditoria) {
        $return = NULL;
        if (!empty($idAuditoria)) {
            $dbCYSA = conectarBD();
            $strSQL = "SELECT fechaOEDRes, fechaOEDRev1, fechaOEDRev2 FROM " . CAT_AUDITORIAS . " WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                $aux = mysql_fetch_assoc($result);
                if (!empty($aux['fechaOEDRes'])) {
                    $max = getTotalHabiles_v2($aux['fechaOEDRes'], 20);
                    $return = $max;
                }
            }
        }
        return $return;
    }

    function get_etapa_de_ampliacion_de_docto($idDocto) {
        $return = NULL;
        if (!empty($idDocto)) {
            $dbCYSA = conectarBD();
            $strSQL = "SELECT valor FROM " . TB_DOCTOS_DETALLE . " WHERE idDocto = " . $idDocto . " AND idParrafo = " . SOL_AMP_ETAPA_AUDITORIA;
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                $aux = mysql_fetch_assoc($result);
                $return = $aux['valor'];
            }
        }
        return $return;
    }

    function has_seguimiento($idAuditoria) {
        $return = FALSE;
        if (!empty($idAuditoria)) {
            $strSQL = "SELECT idAuditoria FROM " . CAT_AUDITORIAS . " WHERE idAuditoriaOrigen=" . $idAuditoria . " AND statusAudit>0 LIMIT 1";
            $dbCYSA = conectarBD();
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                $aux = mysql_fetch_assoc($result);
                $return = $aux['idAuditoria'];
            }
        }
        return $return;
    }

    function has_concluida_sin_seguimiento($idAuditoria) {
        $return = FALSE;
        if (!empty($idAuditoria)) {
            $pendientes = "";
            $numRev = 6;
            while (empty($pendientes) && $numRev > 0) {
                $pendientes = getLstObs_Pendientes_NoSolv($idAuditoria, $numRev);
                $numRev--;
            }
            if (!empty($pendientes)) {
                $return = TRUE;
            }
        }
        return $return;
    }

    /**
     * Devuelve una cadena de texto de las recomendaciones relacionadas a la auditoría
     * @param integer $idAuditoria Identificador de la auditoría
     * @return string Cadena de texto de las recomendaciones
     */
    function get_texto_recomendaciones($idAuditoria) {
        if (empty($idAuditoria)) {
            return "";
        }
        $t = getAuditoria($idAuditoria);
        $valor = "Sin observaciones";
        if ($t['bSinObservacionAP'] == 0) {
            $aux = get_recomendaciones($idAuditoria);
            $estadosRecomendaciones = $aux[0];
            $revisiones = $aux[1];
            $numeroRecomendaciones = $aux[2];
            $recomendaciones = array_count_values($estadosRecomendaciones);
            $totalRecomendaciones = count($estadosRecomendaciones);
            $valores = array();
            $valor = "";
            if (intval($t['statusAudit']) > 1) {
                foreach ($recomendaciones as $status => $o) {
                    $plural = $o > 1 ? TRUE : FALSE;
                    if ($o === $recomendaciones) {
                        $aux = get_estado_observacion($status, $plural, "pasado");
                        $msg = "Se " . mb_strtoupper($aux['status']);
                        if ($status === OBSERVACION_NO_SOLVENTADA) {
                            $msg = "NO se ha SOLVENTADO";
                        }
                        $valores[] = $msg . ' la' . ($totalRecomendaciones > 1 ? 's' : '') . ' ' . ($totalRecomendaciones > 1 ? $totalRecomendaciones : '') . ' recomendaci' . ($totalRecomendaciones > 1 ? 'ones' : 'ón');
                    } else {
                        $aux = get_estado_observacion($status, TRUE, "presente");
                        $valores[] = mb_strtoupper($aux['status']) . ': ' . $o . ' de ' . $totalRecomendaciones . "\n(" . implode(", ", $numeroRecomendaciones[$status]) . ")";
                    }
                }
                $valor = implode("\n", $valores);
            } else {
                $mostrarClasificacionObservaciones = !empty($t['fechaAprovacion']);
                if (!empty($t['idAuditoriaOrigen'])) {
                    $mostrarClasificacionObservaciones = !empty($t['fechaAprovacionRev1']);
                }
                if ($mostrarClasificacionObservaciones) {
                    if ($totalRecomendaciones == 0) {
                        // Si no ha escrito recomendaciones, entonces mostramos las observaciones
                        $aux = array();
                        $observaciones = get_observaciones($idAuditoria);
                        foreach ($observaciones as $obs) {
                            $index = $obs['denClasificacion'];
                            if (!isset($aux[$index]))
                                $aux[$index] = 0;
                            $aux[$index] ++;
                        }
                        foreach ($aux as $key => $value) {
                            $valores[] = strtoupper($key . "S") . ": " . $value;
                        }
                        $valor = implode(chr(13) . chr(10), $valores);
                    } else {
                        foreach ($recomendaciones as $status => $o) {
                            $plural = $o > 1 ? TRUE : FALSE;
                            if ($o === $recomendaciones) {
                                $aux = get_estado_observacion($status, $plural, "pasado");
                                $msg = "Se " . mb_strtoupper($aux['status']);
                                if ($status === OBSERVACION_NO_SOLVENTADA) {
                                    $msg = "NO se ha SOLVENTADO";
                                }
                                $valores[] = $msg . ' la' . ($totalRecomendaciones > 1 ? 's' : '') . ' ' . ($totalRecomendaciones > 1 ? $totalRecomendaciones : '') . ' recomendaci' . ($totalRecomendaciones > 1 ? 'ones' : 'ón');
                            } else {
                                $aux = get_estado_observacion($status, TRUE, "presente");
                                $valores[] = mb_strtoupper($aux['status']) . ': ' . $o . ' de ' . $totalRecomendaciones . "\n(" . implode(", ", $numeroRecomendaciones[$status]) . ")";
                            }
                        }
                        $valor = implode("\n", $valores);
                    }
                }
            }
        }
        return $valor;
    }

    function get_fecha_oed($idAuditoria) {
        $return = NULL;
        if (!empty($idAuditoria)) {
            $strSQL = "SELECT fechaOEDRes, fechaOEDRev1, fechaOEDRev2 FROM " . CAT_AUDITORIAS . " WHERE idAuditoria = " . $idAuditoria . " LIMIT 1";
            $dbCYSA = conectarBD();
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                $row = mysql_fetch_assoc($result);
                $return = $row['fechaOEDRes'];
                if (!empty($row['fechaOEDRev2'])) {
                    $return = $row['fechaOEDRev2'];
                } elseif (!empty($row['fechaOEDRev1'])) {
                    $return = $row['fechaOEDRev1'];
                }
            }
        }
        return $return;
    }

    /**
     * Indica si la auditoría se es reservada
     * @param integer $idAuditoria Identificador de la Auditoría
     * @return boolean Devuelve TRUE para indicar que la auditoría es RESERVADA, FALSE para PÚBLICA
     */
    function is_reservada($idAuditoria) {
        $return = TRUE;
        $idAuditoria = get_real_auditoria_origen($idAuditoria);
        $datosAudit = getAuditoria($idAuditoria);
        // Si esta marcado sin observaciones, entonces queda pública
        if ($datosAudit['bSinObservacionAP'] == 1) {
            $return = FALSE;
        } else { // Revisamos si esta en PROCESO
            if ($datosAudit['statusAudit'] != 1) { // de lo contrario, hay que revisar sus observaciones
                $maxRevision = get_maxima_revision($idAuditoria);
                if ($maxRevision > 0) {
                    $strSQL = "SELECT rr.*
                    FROM cat_auditoria a
                    INNER JOIN observaciones o ON o.idAuditoria = a.idAuditoria AND o.bEliminada=0
                    INNER JOIN recomendacion r ON r.idObservacion = o.idObservacion
                    INNER JOIN revision_recomendacion rr ON rr.idRecomendacion = r.idRecomendacion AND rr.numRevision = " . $maxRevision . "
                    WHERE a.idAuditoria = " . $idAuditoria . " GROUP BY rr.idEstatus";
                    $dbCYSA = conectarBD();
                    $result = $dbCYSA->ejecutaQuery($strSQL);
                    if (is_resource($result) && mysql_num_rows($result) > 0) {
                        $recomendaciones = array();
                        while ($row = mysql_fetch_array($result)) {
                            $status = intval($row['idEstatus']);
                            $status = $status < 1 ? 1 : $status;
                            array_push($recomendaciones, $status);
                        }
                        $recomendaciones = array_count_values($recomendaciones);
                    }
                    if (isset($recomendaciones) && count($recomendaciones) == 1 && isset($recomendaciones[2])) {
                        $return = FALSE;
                    }
                } elseif ($maxRevision == 0) {
                    // significa que no tuvo observaciones, por lo tanto se pone como publica
                    $return = FALSE;
                }
            } // como esta en proceso, entonces es RESERVADA.
        }


        return $return;
    }

    /**
     * Esta función regresa la última revisión relacionada con el ciclo de vida de una auditoría (incluye seguimientos)
     * @param integer $idAuditoria Identificador de la auditoría
     * @return integer Devuelve el valor de la última revisión de un auditoría
     */
    function get_maxima_revision($idAuditoria) {
        $return = 0;
        $dbCYSA = conectarBD();
        $strSQL = "SELECT MAX(numRevision) AS maxima_revision
                FROM cat_auditoria a
                INNER JOIN observaciones o ON o.idAuditoria = a.idAuditoria AND o.bEliminada=0
                INNER JOIN recomendacion r ON r.idObservacion = o.idObservacion
                LEFT JOIN revision_recomendacion rr ON rr.idRecomendacion = r.idRecomendacion
                WHERE a.idAuditoria = " . $idAuditoria;
        $result = $dbCYSA->ejecutaQuery($strSQL);
        if (is_resource($result) && mysql_num_rows($result) > 0) {
            $aux = mysql_fetch_assoc($result);
            $return = intval($aux['maxima_revision']);
        }
        return $return;
    }

    /**
     * Esta función devuelve el identificador de la auditoría origen. Se entiende como auditoría origen cualquier auditoría de tipo AP, AE o IC.
     * @param integer $idAuditoria Indentificador de la auditoría
     * @return integer Devuelve el Identificador de auditoría AP/AE/IC origen
     */
    function get_real_auditoria_origen($idAuditoria) {
        $return = $idAuditoria;
        $datosAudit = getAuditoria($idAuditoria);
        if (!empty($datosAudit['idAuditoriaOrigen'])) {
            $return = $datosAudit['idAuditoriaOrigen'];
            $datosAudit = getAuditoria($datosAudit['idAuditoriaOrigen']);
            if (!empty($datosAudit['idAuditoriaOrigen'])) {
                $return = $datosAudit['idAuditoriaOrigen'];
                $datosAudit = getAuditoria($idAuditoria);
            }
        }
        return $return;
    }

    /**
     * Devuelve las observaciones propias de una auditoría, es decir, que si es un seguimiento, devuelve sólo las observaciones relacionadas
     * con el seguimiento; de lo contrario regresa todas las observaciones, excepto las eliminadas.
     * @param integer $idAuditoria Identificador de la auditoría
     * @return array Arreglo con las observaciones relacionadas a la auditoría
     */
    function get_observaciones_propias_de_auditoria($idAuditoria) {
        $return = array();
        $data = getAuditoria($idAuditoria);
        $n = get_maxima_revision($idAuditoria);
        $numeroRevision = ($n < 2) ? $n : 2; // Por default todas las auditorias empienza en la revision 1, pero usamos 2 por si acaso
        if (!empty($data['idAuditoriaOrigen'])) {
            $idAuditoria = get_real_auditoria_origen($idAuditoria);
            $numeroRevision = get_maxima_revision($idAuditoria);
        }
        do {
            $return = get_revision_N_de_auditoria($idAuditoria, $numeroRevision);
            $numeroRevision--;
        } while (empty($return) && $numeroRevision > 0);
        return $return;
    }

    function get_revision_N_de_auditoria($idAuditoria, $numeroRevision) {
        $return = array();
        if (!empty($idAuditoria) && !empty($numeroRevision)) {
            $strSQL = "SELECT o.*, r.numRecomendacion, o.idObservacion AS idObs, o.numObservacion AS numObs
FROM observaciones o
INNER JOIN recomendacion r ON r.idObservacion = o.idObservacion
INNER JOIN revision_recomendacion rr ON rr.idRecomendacion = r.idRecomendacion
WHERE o.bEliminada=0 AND o.idAuditoria = " . $idAuditoria . " AND rr.numRevision = " . $numeroRevision;
            $dbCYSA = conectarBD();
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    array_push($return, $row);
                }
            }
        }
        return $return;
    }

    function get_revision_N_de_observacion($idObservacion, $numeroRevision) {
        $return = array();
        if (!empty($idObservacion) && !empty($numeroRevision)) {
            $strSQL = "SELECT o.*, r.numRecomendacion, rr.*, o.idObservacion AS idObs, o.numObservacion AS numObs,
                    r.idRecomendacion AS 'idRec', rr.idEstatus AS 'idEstado', r.descRecomendacion AS 'descResc', r.idClasificacion AS 'idClasif', cer.denEstatus AS 'denEstado', rr.avance AS 'avanceRec', r.numRecomendacion AS 'numRec'
                    FROM observaciones o
                    INNER JOIN recomendacion r ON r.idObservacion = o.idObservacion
                    INNER JOIN revision_recomendacion rr ON rr.idRecomendacion = r.idRecomendacion
                    INNER JOIN cat_estatus_recomendacion cer ON cer.idEstatus = rr.idEstatus
                    WHERE o.bEliminada=0 AND o.idObservacion = " . $idObservacion . " AND rr.numRevision = " . $numeroRevision;
            $dbCYSA = conectarBD();
            $result = $dbCYSA->ejecutaQuery($strSQL);
            if (is_resource($result) && mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    array_push($return, $row);
                }
            } else {
//            $strSQL = "SELECT o.*, r.numRecomendacion, o.idObservacion AS idObs, o.numObservacion AS numObs, r.idRecomendacion AS 'idRec', r.idEstatus AS 'idEstado', r.descRecomendacion AS 'descResc', r.idClasificacion AS 'idClasif', cer.denEstatus AS 'denEstado',  r.numRecomendacion AS 'numRec'
//                        FROM observaciones o
//                        INNER JOIN recomendacion r ON r.idObservacion = o.idObservacion
//                        INNER JOIN cat_estatus_recomendacion cer ON cer.idEstatus = r.idEstatus
//                        WHERE o.bEliminada=0 AND o.idObservacion = " . $idObservacion;
//            echo $strSQL;
//            $result = $dbCYSA->ejecutaQuery($strSQL);
//            if (is_resource($result) && mysql_num_rows($result) > 0) {
//                while ($row = mysql_fetch_assoc($result)) {
//                    array_push($return, $row);
//                }
//            }
            }
        }
        return $return;
    }

    /**
     * Esta función indica si la auditoría se encuentra solventada
     * @param integer $idAuditoria Identificador de la auditoría
     * @return boolean Regresa TRUE cuando todas las recomendaciones se encuentran solventadas. FALSE en cualquier otro caso
     */
    function is_auditoria_solventada($idAuditoria) {
        $return = FALSE;
        if (!empty($idAuditoria)) {
            if (is_sin_observaciones($idAuditoria)) {
                $return = TRUE;
            } else {
                $recomendaciones = get_recomendaciones($idAuditoria);
                // En $recomendaciones[0] se almacenan los status de las recomendaciones
                if (isset($recomendaciones[0]) && count($recomendaciones[0]) > 0) {
                    $return = TRUE; // Inicializamos como si todas estuviesen solventadas
                    foreach ($recomendaciones[0] as $r) {
                        if ($r != 2) { // Si la recomendacion es diferente al valor 2 = SOLVENTADO
                            $return = FALSE; // Inicamos entonoces que la auditoria tiene recomendaciones NO SOLVENTADAS
                        }
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Función que indica si una auditoría esta marcada como SIN OBSERVACIONES
     * @param integer $idAuditoria Identificador de la auditoría
     * @return boolean Devuelve TRUE cuando la auditoría no tuvo observaciones. FALSE en cualquier otro caso.
     */
    function is_sin_observaciones($idAuditoria) {
        $return = FALSE;
        $idAuditoria = get_real_auditoria_origen($idAuditoria);
        $datosAudit = getAuditoria($idAuditoria);
        $observaciones = array();
        if ($datosAudit['bSinObservacionAP'] == 0) { // Si esta marcado diferente a SIN OBSERVACIONES, entonces procedemos
            $observaciones = getListaObservaciones($idAuditoria);
            foreach ($observaciones as $index => $obs) {
                if ($obs['denObs'] === "SIN OBSERVACIONES") {
                    $return = TRUE;
                }
            }
        } else {
            $return = TRUE;
        }
        return $return;
    }

}
