<?php

require_once('../../_funciones/validaSesion.php');
require_once('../../_funciones/configSAC.php');
require_once('../modelo/globalAuditoria.php');
include_once("timeline_funciones.php");

require_once('../modelo/globalAuditoria.php');
extract($_POST);

function post_request($url, $data = NULL) {
    $curl = curl_init();
    $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
    $headers = array(
        "Cache-Control: no-cache",
    );
    session_write_close();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_SAFE_UPLOAD, FALSE); // requerido a partir de PHP 5.6.0
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    //curl_setopt($curl, CURLOPT_HEADER, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_COOKIESESSION, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0.1');
    curl_setopt($curl, CURLOPT_COOKIE, $strCookie);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        $response = "Error: " . curl_error($curl);
    } else {
        curl_close($curl);
    }
    return $response;
}

$expresionRegular = '/(([A-z\d\-}]{1,})\.){2}([A-z\d-]){1,}/';
preg_match($expresionRegular, $campoEjecucion, $matches);
$strSQL = "";
$response = array();
$datosAudit = getAuditoria($idAuditoria);

if (count($matches) > 0) {
    list($basedatos, $tabla, $campo) = explode(".", $matches[0]);
    $dbCYSA = conectarBD();
    $strSQL = "SELECT * FROM cat_auditoria_fechas WHERE idAuditoria = " . $idAuditoria;
    $res = $dbCYSA->ejecutaQuery($strSQL);
    if (mysql_num_rows($res) == 0) {
        $strSQL = "INSERT INTO cat_auditoria_fechas VALUES(" . $idAuditoria . ")";
        $dbCYSA->ejecutaQuery($strSQL);
    }
    // En caso de ser la fecha de envio de documentos,
    // entonces calculamos la fecha de inicio de revisión de solventación programada (fechaIniRealRev1)
    if ($campo === "fechaOEDRes") {
        $f = explode(" ", $fechaAlt);
        $g = explode("CYSA", $_SERVER['HTTP_REFERER']);
        $data['fOEDRes'] = $f[0];
        $data['idAuditoria'] = $idAuditoria;
        $url = $g[0] . 'CYSA/modelo/auditoria.php?t=' . time();
        $request = post_request($url, $data);
        if ($request !== "Fecha del sello del Oficio de Orden de Entrada de Auditor&iacute;a actualizada" && $request !== "Fecha del Sello del Env&iacute;o de Documentos actualizada") {
            $response['campoEjecucion'] = "proto_cysa.cat_auditoria.fechaOEDRes";
            $response['class'] = "danger";
            $response['fecha'] = $fechaAlt;
            $response['icon'] = "close";
//            $response['message'] = mysqlDate2OnlyDate($fechaAlt, TRUE);
            $response['message'] = strip_tags($request);
            $response['success'] = FALSE;
            $result = $response;
            echo json_encode($result);
            die();
        }
        // Actualizamos EXPEDIENTES
        // Solo si no tuvo observaciones o todas fueron solventadas, entonces actulizamos
        $sinObservaciones = isset($datosAudit['bSinObservacionAP']) && $datosAudit['bSinObservacionAP'] == 1;
        $susRecomendacionesEstanTodasSolventadas = is_auditoria_solventada($idAuditoria);
        if ($sinObservaciones || $susRecomendacionesEstanTodasSolventadas) {
            // Actualizamos fecha de cierre y la fecha de desclasificación
            $DT_fecha = new DateTime($f[0]);
            $DT_fecha->modify('+1 day'); // Agregamos un día natural
            $fechaDesclasificacion = $DT_fecha->format("Y-m-d");
            // Ejecutamos query
            $strSQL = "UPDATE expedientes "
                    . "SET expedientes_fecha_cierre = '" . $f[0] . "', expedientes_fecha_desclasificacion = '" . $fechaDesclasificacion . "' "
                    . "WHERE expedientes_idAuditoria = " . $idAuditoria . " LIMIT 1";
            $dbExpedientes = conectarBD(DB_PREFIX . "expedientes");
            $dbExpedientes->ejecutaQuery($strSQL);
        }
    } elseif ($campo === "fechas_envios_osi") {
        // Actualizamos  fecha de apertura en el expediente
        $f = explode(" ", $fechaAlt);
        $strSQL = "UPDATE expedientes SET expedientes_fecha_apertura = '" . $f[0] . "' WHERE expedientes_idAuditoria = " . $idAuditoria . " LIMIT 1";
        $dbExpedientes = conectarBD(DB_PREFIX . "expedientes");
        $dbExpedientes->ejecutaQuery($strSQL);
    }
    // en caso de ser un citatorio, entonces actualizamos la hora en el documento de citatorio
    if (strpos($campo, 'fechaLectura') !== FALSE) {
        list($fecha, $hora) = explode(" ", $fechaAlt);
        list($h, $m, $s) = explode(":", $hora . ":00");
        include_once('../modelo/m_documentos.php');
        $etapa = getEtapaAuditoria($idAuditoria);
        if ($datosAudit['anio'] < 2018) {
            $listaOficiosCitatorio = getLstOficiosOC($idAuditoria, $etapa['etapa']);
            $listaOficiosCitatorioFiltro = getLstOficiosOC_filtro($idAuditoria, $etapa['etapa']);
        } else {
            $listaOficiosCitatorio = array();
            $strSQL = "SELECT idDocto, valor, bAprovado FROM " . TB_DOCTOS . " "
                    . "INNER JOIN " . TB_DOCTOS_DETALLE . " USING(idDocto) " 
                    . "WHERE idAuditoria = " . $idAuditoria . " AND idTipoDocto=" . abs(CITATORIO) . " "
                    . "AND bCancelado=0 AND idParrafo=" . CITATORIO_ID_UA . " "
                    . "ORDER BY fechaCreacion DESC ";
            $rs = $dbCYSA->ejecutaQuery($strSQL);
            if ($rs && mysql_num_rows($rs) > 0) {
                while ($r = mysql_fetch_assoc($rs)) {
                    $listaOficiosCitatorio[] = $r;
                }
            }
        }
        $valor = substr("00" . $h, -2) . ":" . $m;
        $idDocto = NULL;
        $strSQL = "SELECT * FROM documentos d WHERE d.idAuditoria = " . $idAuditoria . " AND d.idTipoDocto = " . ($etapa['etapa'] == ETAPA_AP ? 3 : 5) . " ORDER BY d.fechaCreacion DESC LIMIT 1";
        $res = $dbCYSA->ejecutaQuery($strSQL);
        if (is_resource($res) && mysql_num_rows($res) > 0) {
            $detallesARR = mysql_fetch_assoc($res);
            $idDocto = $detallesARR['idDocto'];
        }
        // Actualizamos ARA o ARR
        if (!is_null($idDocto)) {
            $idParrafo = 55;
            $strSQL = "INSERT INTO " . TB_DOCTOS_DETALLE . "(idDocto, idParrafo, valor) VALUES(" . $idDocto . "," . $idParrafo . ",'" . $valor . "') ON DUPLICATE KEY UPDATE valor='" . $valor . "'";
            $dbCYSA->ejecutaQuery($strSQL);
        }
        // Actualizamos citatorios
        if ($datosAudit['anio'] < 2018) {
            foreach ($listaOficiosCitatorio as $key => $oficio) {
                foreach ($listaOficiosCitatorioFiltro as $key2 => $filtro) {
                    if ($oficio['idDocto'] == $filtro['idDocto']) {
                        unset($listaOficiosCitatorio[$key]);
                    }
                }
            }
        }
        foreach ($listaOficiosCitatorio as $elemento) {
            $idDocto = $elemento['idDocto'];
            $idParrafo = 148; // Hora
            $strSQL = "INSERT INTO " . TB_DOCTOS_DETALLE . "(idDocto, idParrafo, valor) VALUES(" . $idDocto . "," . $idParrafo . ",'" . $valor . "') ON DUPLICATE KEY UPDATE valor='" . $valor . "'";
            $dbCYSA->ejecutaQuery($strSQL);
            $valorFecha = mysqlDate2Date($fecha);
            $idParrafo = 149; // Fecha
            $strSQL = "INSERT INTO " . TB_DOCTOS_DETALLE . "(idDocto, idParrafo, valor) VALUES(" . $idDocto . "," . $idParrafo . ",'" . $valorFecha . "') ON DUPLICATE KEY UPDATE valor='" . $valorFecha . "'";
            $dbCYSA->ejecutaQuery($strSQL);
        }
    }
    $strSQL = "UPDATE " . $basedatos . "." . $tabla . " SET " . $campo . " = '" . $fechaAlt . "' WHERE idAuditoria = " . $idAuditoria;
    $resultQuery = $dbCYSA->ejecutaQuery($strSQL);
    $result['success'] = $resultQuery;
    $result['message'] = (get_tipo_campo_mysql($matches[0]) === "DATETIME") ? mysqlDate2Date($fechaAlt, FALSE) : mysqlDate2OnlyDate($fechaAlt, TRUE);
    $result['fecha'] = $fechaAlt;
    $result['campoEjecucion'] = $campoEjecucion;
    // Si la tarea es diferente a la de actualizar la fecha en que se recibe
    // la información del área auditada
    if (isset($idConfiguraciones) && $idConfiguraciones != "00") {
        // Obtengo la fecha programada
        $fechaProgramada = get_fecha_programada_de_tarea($idAuditoria, $idConfiguraciones);
        // Verificamos si la fecha programa requiere re-programación
        $strSQL = "SELECT configuraciones_fecha_reprogramada FROM configuraciones WHERE configuraciones_id = " . $idConfiguraciones . " LIMIT 1";
        $dbTimeline = conectar_timeline();
        $res = $dbTimeline->ejecutaQuery($strSQL);
        if ($res && mysql_num_rows($res) > 0) {
            $tarea = mysql_fetch_array($res);
            $diferenciaReprogramacion = get_diferencia_de_reprogramacion($idAuditoria, $tarea['configuraciones_fecha_reprogramada']);
            // Calculamos la fecha reprogramada
            if ($diferenciaReprogramacion != 0) {
                $fechaProgramada = getTotalHabiles_v2($fechaProgramada, $diferenciaReprogramacion);
            }
        }
        // Esta validación sirve para mostrar correctamente el icono y color de la tarea,
        // ya que la tarea "Convocar revisión de avances con el área auditada" tiene como fecha
        // programa un intervalo de fechas, por lo tanto a la fecha programa establecida se le añaden 4 días
        // para que se considere el intervalo de tiempo
        if ($campo === "fechas_revision_avances_auditoria") {
            $fechaProgramada = getTotalHabiles_v2($fechaProgramada, 4);
        }
        $result['icon'] = get_icono_de_timeline(parseDatetime2Date($fechaProgramada), parseDatetime2Date($fechaAlt), $idAuditoria);
        $result['class'] = get_clase_de_timeline(parseDatetime2Date($fechaProgramada), parseDatetime2Date($fechaAlt));
        $diasHabiles = getDiasHabiles(parseDatetime2Date($fechaProgramada), parseDatetime2Date($fechaAlt));
        $plural = ($diasHabiles > 1 ? TRUE : FALSE);
        if ($diasHabiles > 0) {
            $result['message_retraso'] = 'Esta acci&oacute;n se realiz&oacute; con <strong class="text text-danger" data-toggle="tooltip" title="Se debi&oacute;realizar a m&aacute;s tardar el<br>' . mysqlDate2OnlyDate($fechaProgramada) . '">' . $diasHabiles . ' d&iacute;a' . ($plural ? 's' : '') . ' h&aacute;bil' . ($plural ? 'es' : '') . ' de atraso</strong>';
        }
    } else {
        $result['icon'] = "flag";
        $result['class'] = "purple-darker";
    }
} else {
    $result['success'] = FALSE;
    $result['message'] = "Error con el nombre del campo de ejecuci&oacute;n.";
}

echo json_encode($result);
