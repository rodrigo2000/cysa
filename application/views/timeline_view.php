<?php
include_once('timeline_funciones.php');
$etapasNombre = array(
    ETAPA_AP => 'Auditoría programada',
    ETAPA_RV1 => 'Solventación de Observaciones',
    ETAPA_RV2 => 'Segunda revisión'
);

$etapaAuditoria = 0;
switch ($aux['etapa']) {
    case ETAPA_FIN:
    case ETAPA_RV2:
    case ETAPA_RV1:
        $etapaAuditoria = ETAPA_RV1;
        break;
    case ETAPA_AP:
        $etapaAuditoria = ETAPA_AP;
        break;
}
$etapasArray = array(ETAPA_AP, ETAPA_RV1, ETAPA_RV2);
$PREFIX = get_prefix();
$datosAudit = getAuditoria($auditorias_id);
if (isset($recomendaciones)) {
    $dataRecomendaciones = array();
    $strSQL = "SELECT * FROM cat_auditoria_articulo70 WHERE idAuditoria = " . $auditorias_id . " LIMIT 1";
    $dbCYSA = conectarBD();
    $result = $dbCYSA->ejecutaQuery($strSQL);
    if (is_resource($result) && mysql_num_rows($result) == 1) {
        $dataRecomendaciones = mysql_fetch_assoc($result);
        $periodoFrom = explode("-", $dataRecomendaciones['periodo_from']);
        $periodoTo = explode("-", $dataRecomendaciones['periodo_to']);
    }
}
$auditoriasEnProceso = array();
$auditoriasFinalizadas = array();
$query = 'SELECT DISTINCT anio FROM ' . BD_CYSA . '.' . CAT_AUDITORIAS . ' WHERE anio IS NOT NULL AND statusAudit = 1 ORDER BY anio DESC';
$result = mysql_query($query);
if (is_resource($result) && mysql_num_rows($result) > 0) {
    while ($r = mysql_fetch_assoc($result)) {
        array_push($auditoriasEnProceso, 0 - $r['anio']);
    }
}
$query = 'SELECT DISTINCT anio FROM ' . BD_CYSA . '.' . CAT_AUDITORIAS . ' WHERE anio IS NOT NULL AND statusAudit NOT IN(0,1) ORDER BY anio DESC';
$result = mysql_query($query);
if (is_resource($result) && mysql_num_rows($result) > 0) {
    while ($r = mysql_fetch_assoc($result)) {
        array_push($auditoriasFinalizadas, $r['anio']);
    }
}
if (!isset($_POST['anio'])) {
    if ($datosAudit['statusAudit'] == 1) {
        $_POST['anio'] = 0 - $datosAudit['anio'];
    } else {
        $_POST['anio'] = $datosAudit['anio'];
    }
}
$documentos_id4Tag = 0;
// Verificamos si aún puede agregar prórrogas
$permitirAgregarProrroga = TRUE;
if (isset($auditoria['prorrogas_fecha_maxima_para_generarlas']) && !empty($auditoria['prorrogas_fecha_maxima_para_generarlas'])) {
    $prorrogas_fecha_maxima_para_generarlas = new DateTime($auditoria['prorrogas_fecha_maxima_para_generarlas']);
    $hoy = new DateTime("now");
    if ($prorrogas_fecha_maxima_para_generarlas < $hoy) {
        $permitirAgregarProrroga = FALSE;
    }
}
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1">
        <link rel="icon" href="../../timeline/images/ico/32x32.png" type="image/png">
        <title><?= $auditoria['nombreAuditoria']; ?></title>
        <link href="../../timeline/styles/app.min.css" rel="stylesheet" type="text/css" media="screen"/>
        <link href="../../timeline/styles/app.min.print.css" rel="stylesheet" type="text/css" media="print"/>
        <link href="../../timeline/styles/app_print.css" rel="stylesheet" type="text/css" media="print"/>
        <link href="../../timeline/styles/personalizados.css" rel="stylesheet" type="text/css"/>
        <link href="../../timeline/styles/personalizados_cysa.css" rel="stylesheet" type="text/css"/>
        <script src="../../_js/timeline/jquery-3.1.0.min.js" type="text/javascript"></script>
        <!-- SweetAlert 2 -->
        <script src="../../timeline/plugins/sweetalert2/es6-promise.min.js" type="text/javascript"></script>
        <link href="../../timeline/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/sweetalert2/sweetalert2.js" type="text/javascript"></script>
        <!--<script src="../../timeline/plugins/sweetalert2/sweetalert2.common.js" type="text/javascript"></script>-->
        <!-- Promise.finally support -->
        <!--<script src="../../timeline/plugins/finally.js" type="text/javascript"></script>-->
        <!-- Bootrstrap -->
        <script src="../../timeline/plugins/app.min.js" type="text/javascript"></script>
        <!-- Tether 1.3.3 -->
        <link href="../../timeline/plugins/tether-1.3.3/css/tether.min.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/tether-1.3.3/js/tether.min.js" type="text/javascript"></script>
        <!-- DatePicker -->
        <link href="../../timeline/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js" type="text/javascript"></script>
        <!-- DateRangePicker -->
        <script src="../../timeline/plugins/moment/min/moment.min.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/moment/locale/es.js" type="text/javascript"></script>
        <link href="../../timeline/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- Bootbox.js -->
        <script src="../../timeline/plugins/bootstrap-bootbox/bootbox.min.js" type="text/javascript"></script>
        <!-- Notifications -->
        <script src="../../timeline/plugins/noty/jquery.noty.packaged.min.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/noty/noty-defaults.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/noty/notifications.js" type="text/javascript"></script>
        <!-- Loader.css -->
        <link href="../../timeline/styles/loaders.css" rel="stylesheet" type="text/css">
        <script>
            var idAuditoria = <?= $auditorias_id; ?>;
            moment.locale('es'); // change the global locale to Spanish
        </script>
        <script src="../../timeline/timeline_view.js" type="text/javascript"></script>
        <link href="../../timeline/styles/timeline.css" rel="stylesheet" type="text/css"/>
        <!-- FileUpload -->
        <link href="../../timeline/plugins/blueimp-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/jquery.ui/ui/widget.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/blueimp-file-upload/js/jquery.iframe-transport.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/blueimp-file-upload/js/jquery.fileupload.js" type="text/javascript"></script>
        <!-- Animacion del check -->
        <link href="../../timeline/styles/check_animation.css" rel="stylesheet" type="text/css"/>
        <!-- Tags Input -->
        <link href="../../timeline/plugins/jquery.tagsinput/src/jquery.tagsinput.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/jquery.tagsinput/src/jquery.tagsinput.js" type="text/javascript"></script>
        <!-- CandleStick -->
        <link href="../../timeline/plugins/jquery.candlestick/dist/candlestick.min.css" rel="stylesheet" type="text/css"/>
        <script src="../../timeline/plugins/jquery.candlestick/dist/hammer.min.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/jquery.candlestick/dist/jquery.hammer.js" type="text/javascript"></script>
        <script src="../../timeline/plugins/jquery.candlestick/dist/candlestick.min.js" type="text/javascript"></script>
        <!-- Noty -->
        <script src="../../timeline/plugins/noty/js/noty/packaged/jquery.noty.packaged.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="app">
            <div class="main-panel">
                <nav class="navbar navbar-light bg-faded navbar-fixed-top">
                    <!--<button class="navbar-toggler hidden-sm-up pull-xs-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">&#9776;</button>-->
                    <div class="col-xs-8 col-md-6">
                        <form class="form-inline" name="frmCambiarAuditoria" id="frmCambiarAuditoria" method="post" action="timeline.php">
                            <select name="anio" id="selectAnio" class="form-control">
                                <?php if (count($auditoriasEnProceso) > 0): ?>
                                    <optgroup label="EN PROCESO">
                                        <?php foreach ($auditoriasEnProceso as $a): ?><option value="<?= $a; ?>" <?= $a == $_POST['anio'] ? 'selected="selected"' : ''; ?>><?= abs($a); ?></option><?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                                <?php if (count($auditoriasFinalizadas) > 0): ?>
                                    <optgroup label="FINALIZADAS">
                                        <?php foreach ($auditoriasFinalizadas as $a): ?><option value="<?= $a; ?>" <?= $a == $_POST['anio'] ? 'selected="selected"' : ''; ?>><?= $a; ?></option><?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                            <select name="id" id="selectAuditoria" class="form-control"></select>
                            <a href="#" id="datosAuditoria" class="btn btn-info btn-xs" title="Objetivo de la auditoría" data-toggle="tooltip" data-placement="bottom" nombreAuditoria="<?= $auditoria['nombreAuditoria']; ?>"><i class="material-icons" style="padding-top:5px;">my_location</i></a>
                        </form>
                    </div>
                    <!--<div class="collapse navbar-toggleable-xs" id="navbarResponsive">-->
                    <ul class="nav navbar-nav pull-xs-right">
                        <li class="nav-item active hidden-sm-down">
                            <span class="nav-item nav-link" href="#">Hoy es <?= mysqlDate2OnlyDate(date("Y-m-d"), TRUE); ?><span class="sr-only">(current)</span></span>
                        </li>
                        <li class="nav-item active hidden-md-up">
                            <span class="nav-item nav-link" href="#"><?= date("d") . "/" . getNombreDelMes(date("m")) . "/" . date("Y"); ?><span class="sr-only">(current)</span></span>
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Menú <span class="caret"></span></a>
                            <div id="menu" class="dropdown-menu" role="menu">
                                <h6 class="dropdown-header">Reportes</h6>
                                <a href="timeline_reporte_programa_auditorias.php" class="dropdown-item">Programa de Actividades</a>
                                <a href="timeline_reporte_programa_actividades_detallado.php" class="dropdown-item">Programa de Actividades Detallado</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Exportar</h6>
                                <a class="dropdown-item menuExportar" data-formato="xlsx"><i class="fa fa-file-excel-o fa-lg"></i> Excel 2007-2016 (XLSX)</a>
                                <a class="dropdown-item menuExportar" data-formato="xls"><i class="fa fa-file-excel-o fa-lg"></i> Excel 97-2003 (XLS)</a>
                                <a class="dropdown-item menuExportar" data-formato="pdf"><i class="fa fa-file-pdf-o fa-lg"></i> PDF</a>
                                <?php if (isset($pnc) && count($pnc) > 0): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Producto No Conforme</h6>
                                    <?php foreach ($pnc as $key => $p): ?>
                                        <a class="dropdown-item menuPNC" href="#" data-id-pnc="<?= $key; ?>" data-id-etapa="<?= $etapasArray[$p['idEtapa'] - 1]; ?>">PNC del <?= mysqlDate2OnlyDate($p['fecha']); ?></a>
                                    <?php endforeach; ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">PNC por etapa</h6>
                                    <?php for ($e = ETAPA_AP; $e <= $etapaAuditoria; $e++): ?>
                                        <a class="dropdown-item" href="../vista/v_repAuditPNC.php?idAudit=<?= $auditorias_id; ?>&etapa=<?= $e; ?>" target="_blank"><i class="fa fa-print fa-lg"></i> <?= $etapasNombre[$e]; ?></a>
                                    <?php endfor; ?>
                                <?php endif; ?>
                                <?php if (isset($ampliaciones) && count($ampliaciones) > 0): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Ampliaciones</h6>
                                    <?php foreach ($ampliaciones as $a): ?>
                                        <a class="dropdown-item menuAmpliaciones" href="#" data-idDocto="<?= $a['idDocto']; ?>"><i class="material-icons pull-xs-right">print</i> Ampliación <?= substr("00" . $a['valor'], -3); ?></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (isset($reprogramaciones) && count($reprogramaciones) > 0): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Reprogramaciones</h6>
                                    <?php foreach ($reprogramaciones as $a): ?>
                                        <a class="dropdown-item menuReprogramaciones" href="#" data-idDocto="<?= $a['idDocto']; ?>"><i class="material-icons pull-xs-right">print</i> Reprogramación <?= substr("00" . $a['valor'], -3); ?></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if (isset($auditoria['prorrogas']) && count($auditoria['prorrogas']) > 0): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Prórrogas</h6>
                                    <?php foreach ($auditoria['prorrogas'] as $p): ?>
                                        <a class="dropdown-item menuProrrogas" href="../vista/printDoctoHTML.php?idDocto=<?= $p['idDocto']; ?>" target="_blank"><i class="material-icons pull-xs-right">print</i> <?= $p['prorroga_detalles'][29] . "/" . $p['prorroga_detalles'][30] . "/" . $p['prorroga_detalles'][31]; ?></a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item timeline-toggle" data-value="centered"><i class="material-icons pull-xs-right">check</i> Vista CENTRADA</a>
                                <a href="#" class="dropdown-item timeline-toggle" data-value="stacked"> Vista APILADA</a>
                                <a href="#" class="dropdown-item menuIconografia">Iconografía</a>
                                <?php if (in_array($_SESSION['usuario']->getIdEmpleado(), array(15902, 10520, 17319))): ?>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Configuración</h6>
                                    <a href="../vista/timeline_configuraciones_flow_view.php?procesos_id=3" target="_blank" class="dropdown-item"> Configuración Visual</a>
                                    <a href="../vista/timeline_configuraciones_view.php?procesos_id=<?= $procesos_id; ?>" target="_blank" class="dropdown-item"> Configuración Avanzada</a>
                                    <a href="http://172.16.99.71/Contraloria_v2.0/sac/Catalogos2/index/<?= $_SESSION['usuario']->getUsr(); ?>" target="_blank" class="dropdown-item"> Configuración de catálogos</a>
                                <?php endif; ?>
                                <div class="dropdown-divider"></div>
                                <a href="../../timeline/documentacion/manual_ayuda.pdf" target="_blank" class="dropdown-item">Manual de Ayuda</a>
                            </div>
                        </li>
                    </ul>
                    <!--</div>-->
                </nav>
                <div class="main-content">
                    <div class="content-view">
                        <span class="text-xs-right pull-right float-xs-right visible-print-block" style="margin-top:6px"><?= mysqlDate2OnlyDate(date("Y-m-d"), TRUE); ?></span>
                        <h3 class="visible-print-block"><strong><?= $auditoria['nombreAuditoria']; ?></strong></h3>
                        <?php foreach ($etapas as $indexEtapa => $e): ?>
                            <div id="etapa<?= $e['etapas_codigo']; ?>" class="etapas">
                                <div style="height: 77px;">
                                    <p class="lead alert alert-info etapa-nombre text-xs-center"><strong><?= $e['etapas_nombre']; ?></strong></p>
                                </div>
                                <div class="timeline">
                                    <?php if ($e['etapas_codigo'] == ETAPA_AP): ?>
                                        <?php if ((isset($auditoria['prorrogas']) && count($auditoria['prorrogas']) > 0) || $auditoria['statusAudit'] == AUDIT_PROCESO): ?>
                                            <div class="timeline-card">
                                                <div class="timeline-icon bg-default text-white">
                                                    <i class="material-icons">more_horiz</i>
                                                </div>
                                                <section class="timeline-content">
                                                    <div class="timeline-body">
                                                        <div class="timeline-heading lead m-b-0">
                                                            <strong>Proceso de resolución de prórroga</strong>
                                                        </div>
                                                        <p class="m-b-0">
                                                            <?php if (isset($auditoria['prorrogas']) && count($auditoria['prorrogas']) > 0): ?>
                                                                <?php foreach ($auditoria['prorrogas'] as $p): ?>
                                                                    <a class="btn btn-info btn-block btn-icon m-r-xs" href="../vista/printDoctoHTML.php?idDocto=<?= $p['idDocto']; ?>" target="_blank">
                                                                        <i class="material-icons">print</i>
                                                                        <span><?= $p['prorroga_detalles'][29] . "/" . $p['prorroga_detalles'][30] . "/" . $p['prorroga_detalles'][31]; ?></span>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            <?php elseif ($permitirAgregarProrroga): ?>
                                                                <a id="prorrogas" class="btn btn-success-outline btn-block btn-icon m-r-xs"> <i class="material-icons">add</i>Agregar prórroga</a>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                    <div class="timeline-date">

                                                    </div>
                                                </section>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (isset($ampliaciones_por_etapa[$e['etapas_codigo']]) && count($ampliaciones_por_etapa[$e['etapas_codigo']]) > 0 && ((isset($ampliaciones) && count($ampliaciones) > 0) || $auditoria['statusAudit'] == AUDIT_PROCESO)): ?>
                                        <div class="timeline-card">
                                            <div class="timeline-icon bg-default text-white">
                                                <i class="material-icons">more_horiz</i>
                                            </div>
                                            <section class="timeline-content">
                                                <div class="timeline-body">
                                                    <div class="timeline-heading lead m-b-0">
                                                        <strong>Ampliaciones</strong>
                                                    </div>
                                                    <p class="m-b-0">
                                                        <?php if (isset($ampliaciones) && count($ampliaciones) > 0): ?>
                                                            <?php foreach ($ampliaciones as $a): ?>
                                                                <?php if ($a['etapas_codigo'] == $e['etapas_codigo']): ?>
                                                                    <a class="menuAmpliaciones btn btn-info btn-block btn-icon m-r-xs" href="../vista/printDoctoHTML.php?idDocto=<?= $a['idDocto']; ?>" target="_blank">
                                                                        <i class="material-icons">print</i>
                                                                        Ampliación <?= substr("00" . $a['valor'], -3); ?>
                                                                    </a>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                        <?php if ($auditoria['statusAudit'] == AUDIT_PROCESO && $e['etapas_codigo'] == $etapaAuditoria): ?>
                                                            <a id="ampliaciones" class="btn btn-success-outline btn-block btn-icon m-r-xs"><i class="material-icons">add</i> Agregar ampliación</a>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <div class="timeline-date">

                                                </div>
                                            </section>
                                        </div>
                                    <?php endif; ?>
                                    <!-- Recorremos todas las tareas -->
                                    <?php $tareas = $e['tareas']; ?>
                                    <?php $indexTarea = 0; ?>
                                    <?php foreach ($tareas as $t): ?>
                                        <?php $plural = (isset($t['diferencia_dias_habiles']) && abs($t['diferencia_dias_habiles']) > 1 ? TRUE : FALSE); ?>
                                        <?php $existeCampoEjecucion = isset($t['campo_ejecucion_real']) && !empty($t['campo_ejecucion_real']); ?>
                                        <?php // $t['editar_fecha'] = ($auditoria['statusAudit'] == AUDIT_PROCESO && $t['tareas_nombre'] !== "Envío de Oficio de Orden de Auditoría" ? TRUE : $t['editar_fecha']); // Temporalmente ?>
                                        <?php $mostrarBtnEditar = ($t['editar_fecha'] && $existeCampoEjecucion && $t['tareas_nombre'] != "Inicio de Revisión de Solventación" && $t['tareas_nombre'] != "Fin de Auditoría") || ($t['tareas_nombre'] == "Envío de Oficio de Orden de Auditoría" && $auditoria['statusAudit'] == AUDIT_PROCESO && $_SESSION['usuario']->validaSubsistema('cysaRegAuditoria')); ?>
                                        <?php if (in_array($t['configuraciones_tareas_id'], array(20, 24, 27, 28))) $t['class'] = 'purple-darker'; ?>
                                        <?php if (in_array($t['configuraciones_tareas_id'], array(20, 27))) $t['icon'] = 'flag'; ?>
                                        <?php if (in_array($t['configuraciones_tareas_id'], array(24, 28))) $t['icon'] = "star"; ?>
                                        <?php // if ($t['configuraciones_tareas_id']==24 && $e['etapas_codigo'] > ETAPA_AP) $t['icon'] = "star_border"; ?>
                                        <div class="timeline-card" data-editable="<?= intval($t['configuraciones_orden_ejecucion']) < count($tareas) && $existeCampoEjecucion ? 'true' : 'false'; ?>">
                                            <div class="timeline-icon bg-<?= $t['class']; ?> text-white">
                                                <i class="material-icons"><?= $t['icon']; ?></i>
                                            </div>
                                            <section class="timeline-content">
                                                <div class="timeline-body">
                                                    <div class="timeline-heading lead m-b-0">
                                                        <strong><?= $t['tareas_nombre']; ?><?= ($e['etapas_codigo'] == ETAPA_AP && $indexTarea == count($tareas) - 1) ? '<br>' . $auditoria['nombreAuditoria'] : ''; ?></strong>
                                                        <?php //if ($etapaAuditoria == $e['etapas_codigo']):        ?>
                                                        <input type="text" class="input_campo_ejecucion" name="fecha_ejecucion_alt" id="fecha_ejecucion_alt_<?= $t['configuraciones_id'] ?>" value="<?= isset($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_ejecucion'] > 0 ? $t['tareas_fecha_ejecucion'] : $t['tareas_fecha_programada']; ?>" data-tareas-nombre="<?= $t['tareas_nombre']; ?>" data-tipo-mysql="<?= isset($t['campo_ejecucion_real']) ? get_tipo_campo_mysql($t['campo_ejecucion_real']) : ''; ?>">
                                                        <a href="#" class="campo_ejecucion <?= ($t['tareas_nombre'] != "Inicio de Auditoría" && $mostrarBtnEditar) ? '' : 'hidden-xs-up'; ?>"
                                                           data-campo-ejecucion="<?= isset($t['campo_ejecucion_real']) ? $t['campo_ejecucion_real'] : ''; ?>"
                                                           data-tareas-fecha-programada="<?= $t['tareas_fecha_programada']; ?>"
                                                           data-tareas-nombre="<?= $t['tareas_nombre']; ?>"
                                                           data-configuraciones-id="<?= $t['configuraciones_id']; ?>"
                                                           data-tareas-fecha-ejecucion="<?= isset($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_ejecucion'] > 0 ? $t['tareas_fecha_ejecucion'] : ''; ?>">
                                                            <i class="fa fa-calendar-check-o"></i>
                                                        </a>
                                                        <?php //endif;     ?>
                                                        <?php if (!is_null($t['configuraciones_extra_button']) /* && $etapaAuditoria == $e['etapas_codigo'] */ && ($datosAudit['statusAudit'] == AUDIT_PROCESO || $t['editar_fecha'])) eval('?>' . $t['configuraciones_extra_button']); ?>
                                                        <?php if (FALSE && isset($t['documentos'])): $documentos_id4Tag = rand(); ?><a class="btnMyCollapse" style="float:right; padding-top: 5px;" href="#collapseExample_<?= $documentos_id4Tag; ?>" data-toggle="collapse" id="padre<?= $documentos_id4Tag; ?>" aria-expanded="false" aria-controls="collapseExample"><i class="material-icons" title="Ver Vo.Bo. de documentos">arrow_drop_up</i></a><?php endif; ?>
                                                    </div>
                                                    <?php if ($indexTarea != count($tareas) - 1): ?>
                                                        <?php if (isset($t['diferencia_dias_habiles']) && intval($t['diferencia_dias_habiles']) > 0): ?>
                                                            <p class="message-retraso m-b-0">Esta acción se ejecutó con
                                                                <strong class="text text-danger" data-toggle="tooltip" title="Se debió realizar a más tardar el<br><?= mysqlDate2OnlyDate($t['tareas_fecha_programada']) ?>">
                                                                    <?= $t['diferencia_dias_habiles']; ?> día<?= $plural ? 's' : ''; ?> hábil<?= $plural ? 'es' : ''; ?> de atraso
                                                                </strong>
                                                            </p>
                                                        <?php endif; ?>
                                                        <p class="m-b-0<?= !$existeCampoEjecucion || empty($t['tareas_fecha_ejecucion']) ? ' hidden-xs-up' : ''; ?>">
                                                            <?php if ($indexTarea > 0): ?>
                                                                <strong><?= (!empty($t['configuraciones_fecha_ejecucion_label'])) ? $t['configuraciones_fecha_ejecucion_label'] : 'Fecha Real de Ejecución'; ?>:</strong>
                                                                <span class="fecha_ejecucion_valor" id="<?= isset($t['campo_ejecucion_real']) ? str_replace(".", "-", $t['campo_ejecucion_real']) : ''; ?>"><?= isset($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_ejecucion'] > 0 ? (get_tipo_campo_mysql($t['campo_ejecucion_real']) == "DATETIME" ? mysqlDate2Date($t['tareas_fecha_ejecucion'], FALSE) : mysqlDate2OnlyDate($t['tareas_fecha_ejecucion'], TRUE)) : ''; ?></span>
                                                            <?php endif; ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <?php if (!is_null($t['configuraciones_descripcion'])) eval('?>' . $t['configuraciones_descripcion']); ?>
                                                    <?php if (FALSE && isset($t['documentos'])): $idDoctoAux = 1; ?>
                                                        <div class="collapse in" id="collapseExample_<?= $documentos_id4Tag; ?>" data-padre="<?= $documentos_id4Tag; ?>">
                                                            <table class="table table-sm documentos_vobos">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Documento</th>
                                                                        <?php foreach ($t['titulares_vobos'] as $index => $d): $empleado = is_array($d) ? '-' : get_empleado($d); ?>
                                                                            <th><?= Capitalizar($index); ?><br><span title="<?= is_array($d) ? '-' : $empleado['nombreF'] . " " . $empleado['apF'] . " " . $empleado['amF']; ?>"><?= is_array($d) ? '-' : get_iniciales_de_empleado($d); ?></span></th>
                                                                        <?php endforeach; ?>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($t['documentos'] as $d) : ?>
                                                                        <?php $idDocto = !empty($d['idDocto']) ? $d['idDocto'] : $idDoctoAux++; ?>
                                                                        <tr>
                                                                            <?php $UA = get_ua_de_documento($d['idDocto']); ?>
                                                                            <?php $vobos = get_vobos($d['idDocto']); ?>
                                                                            <td class="align-middle">
                                                                                <?php if (!empty($d['idDocto'])): ?>
                                                                                    <a href="../vista/<?= get_url_de_documento($d['idTipoDocto']); ?>=<?= $d['idDocto']; ?>&idVersion=<?= $d['idVersion']; ?>&etapa=<?= $e['etapas_codigo']; ?>&idTipoDocto=<?= $d['idTipoDocto']; ?><?= !empty($vobos) ? '&docAut=1' : ''; ?>" target="_blank" title="Visualizar"><?= $d['denDocto']; ?></a><?= (is_array($UA) && isset($UA['denDireccion']) ? ' <span class="label label-radius label-info">' . Capitalizar(minusculas($UA['denDireccion'])) . "</span>" : ''); ?>
                                                                                <?php else: ?>
                                                                                    <?= $d['denDocto'] . (is_array($UA) && isset($UA['denDireccion']) ? ' <span class="label label-radius label-info">' . Capitalizar($UA['denDireccion']) . "</span>" : ''); ?>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <?php
                                                                            // Arreglo que contiene los VoBos que se han realizado a un documento por un empleado
                                                                            $vobos = array();
                                                                            foreach ($t['titulares_vobos'] as $idEmpleadoVoBo) {
                                                                                if (is_array($idEmpleadoVoBo)) {
                                                                                    $coordinadorVoBo = array('idEmpleado' => 0); // Para evitar el NOTICE al hacer la asignación despues del foreach
                                                                                    foreach ($idEmpleadoVoBo as $idEmpleadoVoBoAux) {
                                                                                        $aux = get_vobos($d['idDocto'], $idEmpleadoVoBoAux);
                                                                                        if (!empty($aux)) {
                                                                                            $coordinadorVoBo = $aux[0];
                                                                                        }
                                                                                    }
                                                                                    $vobos[$coordinadorVoBo['idEmpleado']] = $coordinadorVoBo;
                                                                                } else {
                                                                                    $vobos[$idEmpleadoVoBo] = get_vobos($d['idDocto'], $idEmpleadoVoBo);
                                                                                }
                                                                            }

                                                                            // idEmpleados que pueden dar autorizacion de VoBo al documento
                                                                            $autorizadores = array_keys($vobos);
                                                                            foreach ($autorizadores as $index => $idEmpleadoVoBo):
                                                                                $vobo = isset($vobos[$idEmpleadoVoBo][0]) ? $vobos[$idEmpleadoVoBo][0] : $vobos[$idEmpleadoVoBo];
                                                                                // Bloqueamos el control cuando:
                                                                                // 1) El idDocto no exista, es decir, que no se ha creado el documento
                                                                                // 2) Cuando mi superior haya dado el VoBo antes que yo lo haya dado
                                                                                $bloqueado = (empty($d['idDocto']) || isset($autorizadores[$index + 1]) && isset($vobos[$autorizadores[$index + 1]]['bVoBo']) && $vobos[$autorizadores[$index + 1]]['bVoBo'] != "");
                                                                                $bloqueado = empty($d['idDocto']);
                                                                                $vobo = get_ultimo_vobo_revisiones_de_documento($d['idDocto'], $idEmpleadoVoBo);
                                                                                ?>
                                                                                <td align="center" class="align-middle" title="<?= isset($vobo['iniciales']) ? $vobo['iniciales'] : ''; ?>">
                                                                                    <?php if (!empty($vobo) && is_array($vobo)): // Hay informacion del ultimo vobo   ?>
                                                                                        <?php if ($_SESSION['usuario']->getIdEmpleado() == $datosAudit['idLider']): // Cuando soy el lider de auditoria muestro los controles para entregar el documento ?>
                                                                                            <?php if (!is_null($vobo['revisiones_fecha_entrega']) && is_null($vobo['revisiones_fecha_devolucion'])): ?>
                                                                                                Entregado
                                                                                            <?php elseif (is_null($vobo['revisiones_fecha_entrega']) && !is_null($vobo['revisiones_fecha_devolucion'])): ?>
                                                                                                <i class="material-icons bg-<?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'success' : 'danger') : 'info'; ?> <?= empty($d['idDocto']) ? 'disabled' : ''; ?>"><?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'check' : 'clear') : 'help_outline'; ?></i>
                                                                                                <?php if (intval($vobo['bVoBo']) != 1): ?>
                                                                                                    <br><a href="#" class="btn btn-secondary btn-xs entregar" data-idDocto="<?= $d['idDocto'] ?>" data-idEmpleado="<?= $idEmpleadoVoBo; ?>"><?= !is_null($vobo['bVoBo']) ? 'Re-' : ''; ?>Entregar</a>
                                                                                                <?php endif; ?>
                                                                                            <?php else: ?>
                                                                                                <?php if (!is_null($vobo['bVoBo'])): ?>
                                                                                                    <i class="material-icons bg-<?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'success' : 'danger') : 'info'; ?> <?= empty($d['idDocto']) ? 'disabled' : ''; ?>"><?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'check' : 'clear') : 'help_outline'; ?></i>
                                                                                                <?php endif; ?>
                                                                                                <?php if (intval($vobo['bVoBo']) != 1): ?>
                                                                                                    <a href="#" class="btn btn-secondary btn-xs entregar" data-idDocto="<?= $d['idDocto'] ?>" data-idEmpleado="<?= $idEmpleadoVoBo; ?>"><?= !is_null($vobo['bVoBo']) ? 'Re-' : ''; ?>Entregar</a>
                                                                                                <?php endif; ?>
                                                                                            <?php endif; ?>
                                                                                        <?php elseif ((intval($_SESSION['usuario']->getIdEmpleado()) == $idEmpleadoVoBo) || (is_array($idEmpleadoVoBo) && in_array($_SESSION['usuario']->getIdEmpleado(), $idEmpleadoVoBo))): ?>
                                                                                            <input class="js-candlestick" type="checkbox" value="<?= isset($vobo['bVoBo']) ? strval($vobo['bVoBo']) : ''; ?>" name="idDocto_<?= $idDocto; ?>" id="idDocto_<?= $idDocto; ?>_1" data-idRevisiones="<?= $vobo['revisiones_id']; ?>" data-idDocto="<?= $d['idDocto'] ?>" data-idEmpleado="<?= $idEmpleadoVoBo; ?>" <?= $bloqueado ? 'disabled="true"' : ''; ?> data-valorInicial="<?= isset($vobo['bVoBo']) ? strval($vobo['bVoBo']) : 'reset'; ?>">
                                                                                        <?php else: ?>
                                                                                            <i class="material-icons bg-<?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'success' : 'danger') : 'info'; ?> <?= empty($d['idDocto']) ? 'disabled' : ''; ?>"><?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'check' : 'clear') : 'help_outline'; ?></i>
                                                                                        <?php endif; ?>
                                                                                    <?php else : // sino hay informacion ?>
                                                                                        <?php if (isset($d['idDocto'])): // existe el idDocto?, mostramos el boton ENTREGAR ?>
                                                                                            <?php if ((intval($_SESSION['usuario']->getIdEmpleado()) == $idEmpleadoVoBo) || (is_array($idEmpleadoVoBo) && in_array($_SESSION['usuario']->getIdEmpleado(), $idEmpleadoVoBo))): ?>
                                                                                                <input class="js-candlestick" type="checkbox" value="<?= isset($vobo['bVoBo']) ? strval($vobo['bVoBo']) : ''; ?>" name="idDocto_<?= $idDocto; ?>" id="idDocto_<?= $idDocto; ?>_1" data-idRevisiones="<?= $vobo['revisiones_id']; ?>" data-idDocto="<?= $d['idDocto'] ?>" data-idEmpleado="<?= $idEmpleadoVoBo; ?>" <?= $bloqueado ? 'disabled="true"' : ''; ?> data-valorInicial="<?= isset($vobo['bVoBo']) ? strval($vobo['bVoBo']) : 'reset'; ?>">
                                                                                            <?php else: ?>
                                                                                                <a href="#" class="btn btn-secondary btn-xs entregar" data-idDocto="<?= $d['idDocto'] ?>" data-idEmpleado="<?= $idEmpleadoVoBo; ?>">Entregar</a>
                                                                                            <?php endif; ?>
                                                                                        <?php else: // sino existe el idDocto, entonces mostramos el icono azul en desactivado ?>
                                                                                            <i class="material-icons bg-info disabled">help_outline</i>
                                                                                        <?php endif; ?>
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            <?php endforeach; ?>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td colspan="4" class="text-xs-center">
                                                                            <a href="timeline_reporte_control_revision.php?idAuditoria=<?= $auditorias_id; ?>&idConfiguracion=<?= $t['configuraciones_id']; ?>" class="btn btn-success-outline btn-sm m-t-1 m-b-0">Descargar Control de Documentos Entregados para Revisión</a>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php $aux = new DateTime($t['tareas_fecha_programada']); ?>
                                                <div class="timeline-date">
                                                    <?php if (isset($t['tareas_fecha_reprogramada']) && $t['configuraciones_mostrar_fecha_programada'] == 1): ?>
                                                        <div id="fecha-reprogramada">
                                                            <?php $aux2 = new DateTime($t['tareas_fecha_reprogramada']); ?>
                                                            <?php echo getNombreDelDia($aux2->format('w')) . ", " . mysqlDate2OnlyDate($aux2->format("Y-m-d")); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div id="fecha-programada" <?= (isset($t['tareas_fecha_reprogramada'])) ? 'class="tachado"' : ''; ?>>
                                                        <?php if ($t['configuraciones_mostrar_fecha_programada'] == 1 || (isset($t['campo_ejecucion_real']) && $t['campo_programada_real'] != $t['campo_ejecucion_real'])): ?>
                                                            <?php if (!is_null($t['tareas_fecha_programada']) && trim($t['tareas_nombre']) === "Convocar revisión de avances con el área auditada"): ?>Del <?php endif; ?>
                                                            <?php if (!is_null($t['tareas_fecha_programada'])): ?><span class="timeline-date-day"><?= getNombreDelDia($aux->format('w')) . ", "; ?></span><?php endif; ?>
                                                            <?= is_null($t['tareas_fecha_programada']) ? 'SIN ESPECIFICAR' : mysqlDate2OnlyDate($t['tareas_fecha_programada']); ?><?php
                                                            if (!is_null($t['tareas_fecha_programada']) && trim($t['tareas_nombre']) === "Convocar revisión de avances con el área auditada") {
                                                                $aux3 = getTotalHabiles_v2($t['tareas_fecha_programada'], 4);
                                                                $aux2 = new DateTime($aux3);
                                                                echo '<br>al <span class="timeline-date-day">' . getNombreDelDia($aux2->format('w')) . ", </span>" . mysqlDate2OnlyDate($aux2->format("Y-m-d"));
                                                            }
                                                            ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <?php $indexTarea++; ?>
                                    <?php endforeach; ?>
                                    <!-- Fin del recordio de todas las tareas -->
                                    <?php if ($e['etapas_codigo'] == ETAPA_AP): ?>
                                        <?php if ((isset($reprogramaciones) && $reprogramaciones > 0) || empty($auditoria['fechaSelloOEA'])): ?>
                                            <div class="timeline-card">
                                                <div class="timeline-icon bg-default text-white">
                                                    <i class="material-icons">more_horiz</i>
                                                </div>
                                                <section class="timeline-content">
                                                    <div class="timeline-body">
                                                        <div class="timeline-heading lead m-b-0">
                                                            <strong>Reprogramaciones</strong>
                                                        </div>
                                                        <p class="m-b-0">
                                                            <?php if (isset($reprogramaciones) && $reprogramaciones > 0): ?>
                                                                <?php foreach ($reprogramaciones as $a): ?>
                                                                    <a class="menuReprogramaciones btn btn-info btn-block btn-icon m-r-xs" href="../vista/printDoctoHTML.php?idDocto=<?= $a['idDocto']; ?>" target="_blank">
                                                                        <i class="material-icons">print</i>
                                                                        Reprogramación <?= substr("00" . $a['valor'], -3); ?>
                                                                    </a>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                            <?php if (empty($auditoria['fechaSelloOEA'])): ?>
                                                                <a id="reprogramaciones" class="btn btn-success-outline btn-block btn-icon m-r-xs"><i class="material-icons">add</i> Agregar reprogramación</a>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                    <div class="timeline-date">

                                                    </div>
                                                </section>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!is_null($datosAudit['idAuditoriaOrigen'])): ?>
                        <?php $origen = getAuditoria($datosAudit['idAuditoriaOrigen']); ?>
                        <div class="row">
                            <div class="col-sm-8 col-md-4 center-block" style="float:none; text-align: center;">
                                <a href="#" class="btn btn-lg btn-info btn-block" onclick="return abrirTimeline('../controlador/timeline.php', '<?= $datosAudit['idAuditoriaOrigen']; ?>');">Auditoría Origen<br><?= $origen['num']; ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if ((isset($recomendaciones) && isset($recomendaciones[OBSERVACION_SOLVENTADA]) && $recomendaciones[OBSERVACION_SOLVENTADA] == $totalRecomendaciones) || ((isset($is_sin_observaciones) && $is_sin_observaciones == 1))): ?>
            <div class="modal fade" id="modalArticulo70" tabindex="-1" role="dialog" aria-labelledby="modalArticulo70" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" style="float:left;">LGT Art&iacute;culo 70 Fracci&oacute;n XXIV</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="frmArticulo70" id="frmArticulo70" action="../controlador/timeline_articulo70.php">
                                <div class="form-group row">
                                    <label for="periodo_from_label" class="col-xs-2 col-form-label">Periodo</label>
                                    <div class="col-xs-10">
                                        <div class="input-group">
                                            <input class="form-control text-xs-center form-control-success form-control-danger" type="text" value="<?= isset($dataRecomendaciones['periodo_from']) ? ucfirst(getNombreDelMes($periodoFrom[1])) . "/" . $periodoFrom[0] : ''; ?>" id="periodo_from_label" data-hidden="periodo_from"<?= (isset($dataRecomendaciones['bloqueado']) && $dataRecomendaciones['bloqueado'] ? ' disabled' : '') ?>>
                                            <span class="input-group-addon" id="sizing-addon2">a</span>
                                            <input class="form-control text-xs-center form-control-success form-control-danger" type="text" value="<?= isset($dataRecomendaciones['periodo_to']) ? ucfirst(getNombreDelMes($periodoTo[1])) . "/" . $periodoTo[0] : ''; ?>" id="periodo_to_label" data-hidden="periodo_to"<?= (isset($dataRecomendaciones['bloqueado']) && $dataRecomendaciones['bloqueado'] ? ' disabled' : '') ?>>
                                        </div>
                                        <input type="hidden" value="<?= isset($dataRecomendaciones['periodo_from']) ? $dataRecomendaciones['periodo_from'] : ''; ?>" name="periodo_from" id="periodo_from">
                                        <input type="hidden" value="<?= isset($dataRecomendaciones['periodo_to']) ? $dataRecomendaciones['periodo_to'] : ''; ?>" name="periodo_to" id="periodo_to">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="col-xs-12 text-xs-center">
                                            <?php if (!isset($dataRecomendaciones['bloqueado']) || $dataRecomendaciones['bloqueado'] == 0): ?>
                                                <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                                    <i class="material-icons">file_upload</i>
                                                    <span>Seleccionar PDF de ARR...</span>
                                                    <input id="fileuploadARR" type="file" name="filesARR" accept="application/pdf">
                                                </div>
                                                <progress id="progressARR" class="progress progress-info progress-animated hidden-xs-up" value="0" max="100"></progress>
                                                <div id="filesARR" class="files"></div>
                                            <?php endif; ?>
                                            <?php $pathDestino = PATH_ARCHIVOS_ARTICULO_70; ?>
                                            <?php $nombreArchivo = str_replace("/", "-", $datosAudit['num']) . " ARR.pdf"; ?>
                                            <div id="downloadFileOED" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                                <a target="_blank" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= $nombreArchivo; ?></a>
                                            </div>
                                            <br>
                                            <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                                <i class="material-icons">file_upload</i>
                                                <span>Seleccionar WORD del ARR</span>
                                                <input id="fileuploadARRWord" type="file" name="filesARRWord" accept=".doc,.docx">
                                            </div>
                                            <progress id="progressWord" class="progress progress-info progress-animated hidden-xs-up" value="0" max="100"></progress>
                                            <div id="filesARR" class="files"></div>
                                            <?php $nombreArchivo = str_replace("/", "-", $datosAudit['num']) . " ARR.docx"; ?>
                                            <div id="downloadFileWord" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                                <a target="_blank" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= $nombreArchivo; ?></a>
                                            </div>
                                            <div style="margin-top:10px;">
                                                <a href="../controlador/html2docx.php?idAuditoria=<?= $auditorias_id; ?>&tipoAuditoria=<?= $datosAudit['tipo']; ?>" target="_blank">Generar WORD</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "col-xs-12 col-sm-6">
                                        <div class = "col-xs-12 text-xs-center">
                                            <?php if (!isset($dataRecomendaciones['bloqueado']) || $dataRecomendaciones['bloqueado'] == 0): ?>
                                                <div class = "btn btn-default btn-icon fileinput-button m-b-1">
                                                    <i class = "material-icons">file_upload</i>
                                                    <span>Seleccionar PDF de OED...</span>
                                                    <input id = "fileuploadOED" type = "file" name = "filesOED" accept = "application/pdf">
                                                </div>
                                                <progress id = "progressOED" class = "progress progress-info progress-animated hidden-xs-up" value = "0" max = "100"></progress>
                                                <div id = "filesOED" class = "files"></div>
                                            <?php endif; ?>
                                            <?php $nombreArchivo = str_replace("/", "-", $datosAudit['num']) . " Envío de documentos.pdf"; ?>
                                            <div id="downloadFileOED" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                                <a target="_blank" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= str_replace("/", "-", $datosAudit['num']) . "<br>Envío de documentos.pdf"; ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="idAuditoria" id="idAuditoria" value="<?= $auditorias_id; ?>">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary <?= (isset($dataRecomendaciones['bloqueado']) && $dataRecomendaciones['bloqueado'] ? 'hidden-xs-up' : '') ?>">Guardar cambios</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </body>
</html>
<script>
    $(document).ready(function () {
        $(document).on('hidden.bs.collapse', 'div.collapse', function () {
            var id = "padre" + $(this).attr('data-padre');
            $("i.material-icons", "#" + id).html('arrow_drop_down');
            corregirLineaCentralDeTimeLine()
        }).on('shown.bs.collapse', 'div.collapse', function () {
            var id = "padre" + $(this).attr('data-padre');
            $("i.material-icons", "#" + id).html('arrow_drop_up');
            corregirLineaCentralDeTimeLine();
        });
        $("div.collapse", ".timeline").each(function (index, element) {
            $("#" + element.id).collapse('hide');
        });
    });
</script>