<div class="text-xs-center m-b-2">
    <h1 class="m-b-1"><?= $auditoria['nombreAuditoria']; ?></h1>
    <div class="btn-group btn-group-sm timeline-toggle" data-toggle="buttons">
        <label class="btn btn-default active">
            <input type="radio" name="timelineType" id="centered" value="centered" autocomplete="off" checked="checked"> Centrado
        </label>
        <label class="btn btn-default">
            <input type="radio" name="timelineType" id="stacked" value="stacked" autocomplete="off"> Apilado
        </label>
    </div>
    <!--    <div class="btn-group btn-group-sm" data-toggle="buttons">
            <button id="btnShowHiddenCards" class="btn btn-sm text-xs-center">Mostrar tareas pendientes</button>
        </div>-->
</div>
<div class="timeline">
    <div class="timeline-card">
        <div class="timeline-icon <?= is_null($auditoria['fechaFinReal']) ? 'bg-default' : 'bg-purple-darker'; ?>">
            <i class="material-icons">star</i>
        </div>
        <section class="timeline-content">
            <div class="timeline-body">
                <div class="timeline-heading lead<?= (empty($auditoria['fechaFinReal']) || $auditoria['fechaFinReal'] == $auditoria['fechaFinAudit']) ? ' m-b-0' : ''; ?>"><strong>Fin de Auditoría</strong></div>
                <?php $plural = (abs($auditoria['reprogramacion_fin_dias_habiles']) > 1 ? TRUE : FALSE); ?>
                <?php if (intval($auditoria['reprogramacion_fin_dias_habiles']) > 0 && !is_null($auditoria['fechaFinReal'])): ?>
                    <p>Se finalizó <strong class="text text-danger" data-toggle="tooltip" title="Fecha programada<br><?= mysqlDate2OnlyDate($auditoria['fechaFinAudit'], TRUE); ?>"><?= abs($auditoria['reprogramacion_fin_dias_habiles']); ?> día<?= $plural ? 's' : ''; ?> hábil<?= $plural ? 'es' : ''; ?> después</strong> de la fecha programada</p>
                <?php endif; ?>
                <?php if (intval($auditoria['reprogramacion_fin_dias_habiles']) < 0 && !is_null($auditoria['fechaFinReal'])): ?>
                    <p>Se finalizó <strong class="text text-success" data-toggle="tooltip" title="Fecha programada<br><?= mysqlDate2OnlyDate($auditoria['fechaFinAudit'], TRUE); ?>"><?= abs($auditoria['reprogramacion_fin_dias_habiles']); ?> día<?= $plural ? 's' : ''; ?> hábil<?= $plural ? 'es' : ''; ?> antes</strong> de la fecha programada</p>
                <?php endif; ?>
                <?php if (!empty($auditoria['fechaFinReal']) && $auditoria['fechaFinReal'] != $auditoria['fechaFinAudit']): ?>
                    <p>
                        <strong>Fecha Real:</strong> <?= mysqlDate2OnlyDate($auditoria['fechaFinReal'], TRUE); ?><br>
                        <strong>Fecha Programada:</strong> <?= mysqlDate2OnlyDate($auditoria['fechaFinAudit'], TRUE); ?><br>
                        <!--Diferencia Días Hábiles: <?= $auditoria['reprogramacion_fin_dias_habiles']; ?><br>-->
                        <!--Diferencia Días Naturales: <?= $auditoria['reprogramacion_fin_dias_naturales']; ?>-->
                    </p>
                <?php endif; ?>
            </div>
            <?php $aux = new DateTime($auditoria['fechaFinAudit']); ?>
            <div class="timeline-date"><span class="timeline-date-day"><?= getNombreDelDia($aux->format('w')) . ", "; ?></span><?= mysqlDate2OnlyDate($auditoria['fechaFinAudit']); ?></div>
        </section>
    </div>
    <!--    <div class="timeline-card">
            <div class="timeline-icon bg-info">
                <i class="material-icons">add</i>
            </div>
            <section class="timeline-content">
                <div class="timeline-body">
                    <button id="btnShowHiddenCards" class="btn btn-sm text-xs-center">Mostrar tareas pendientes</button>
                </div>
                <div class="timeline-date">31 de Diciembre de 2016</div>
            </section>
        </div>
        <div id="tareas_pendientes"></div>-->
    <?php foreach ($etapas as $e): ?>
        <?php foreach ($tareas as $t): ?>
            <?php if ($t['tareas_etapas_id'] == $e['etapas_id']): ?>
                <?php $plural = (isset($t['diferencia_dias_habiles']) && abs($t['diferencia_dias_habiles']) > 1 ? TRUE : FALSE); ?>
                <div class="timeline-card">
                    <div class="timeline-icon bg-<?= $t['class']; ?> text-white">
                        <i class="material-icons"><?= $t['icon']; ?></i>
                    </div>
                    <section class="timeline-content">
                        <div class="timeline-body">
                            <div class="timeline-heading lead<?= !isset($t['tareas_fecha_ejecucion']) || empty($t['tareas_fecha_ejecucion']) || $t['tareas_fecha_programada'] == $t['tareas_fecha_ejecucion'] ? ' m-b-0' : ''; ?>"><strong><?= $t['tareas_nombre']; ?></strong></div>
                            <?php if (isset($t['diferencia_dias_habiles']) && intval($t['diferencia_dias_habiles']) > 0): ?><p>Esta acción se ejecutó con <strong class="text text-danger" data-toggle="tooltip" title="Se debió realizar a más tardar el<br><?= mysqlDate2OnlyDate($t['tareas_fecha_programada']) ?>"><?= $t['diferencia_dias_habiles']; ?> día<?= $plural ? 's' : ''; ?> hábil<?= $plural ? 'es' : ''; ?> de atraso</strong></p><?php endif; ?>
                            <?php if (isset($t['tareas_fecha_ejecucion']) && !empty($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_programada'] != $t['tareas_fecha_ejecucion']) : ?>
                                <p>
                                    <strong><?= (!empty($t['tareas_fecha_ejecucion_label'])) ? $t['tareas_fecha_ejecucion_label'] : 'Fecha Real de Ejecución'; ?>:</strong> <?= isset($t['tareas_fecha_ejecucion']) ? mysqlDate2OnlyDate($t['tareas_fecha_ejecucion'], TRUE) : ''; ?><br>
                                    <strong><?= (!empty($t['tareas_fecha_programada_label'])) ? $t['tareas_fecha_programada_label'] : 'Fecha Límite Programada'; ?>:</strong> <?= isset($t['tareas_fecha_programada']) ? mysqlDate2OnlyDate($t['tareas_fecha_programada'], TRUE) : ''; ?>
                                    <!--Diferencia Días Hábiles: <?= isset($t['diferencia_dias_habiles']) ? $t['diferencia_dias_habiles'] : ''; ?><br>
                                    Diferencia Días Naturales: <?= isset($t['diferencia_dias_naturales']) ? $t['diferencia_dias_naturales'] : ''; ?>-->
                                </p>
                            <?php endif; ?>
                            <?php if (isset($t['tareas_fecha_programada']) && isset($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_ejecucion'] > $t['tareas_fecha_programada']): ?>
                                <button type="button" class="btn btn-primary btn-icon m-r-xs m-b-xs btn-sm productoNoConforme"><i class="material-icons">add</i><span>Producto No Conforme</span></button>
                            <?php endif; ?>
                        </div>
                        <?php $aux = new DateTime($t['tareas_fecha_programada']); ?>
                        <div class="timeline-date">
                            <?php if (!is_null($t['tareas_fecha_programada'])): ?><span class="timeline-date-day"><?= getNombreDelDia($aux->format('w')) . ", "; ?></span><?php endif; ?>
                            <?= is_null($t['tareas_fecha_programada']) ? 'SIN ESPECIFICAR' : mysqlDate2OnlyDate($t['tareas_fecha_programada']); ?>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <div class="timeline-card">
        <div class="timeline-icon bg-purple-darker">
            <i class="material-icons">flag</i>
        </div>
        <section class="timeline-content">
            <div class="timeline-body">
                <div class="timeline-heading lead"><strong>Inicio de Auditoría<br><?= $auditoria['nombreAuditoria']; ?></strong></div>
                <?php $plural = (abs($auditoria['reprogramacion_inicio_dias_habiles']) > 1 ? TRUE : FALSE); ?>
                <?php if (intval($auditoria['reprogramacion_inicio_dias_habiles']) > 0): ?>
                    <p>Se inició <strong class="text text-danger" data-toggle="tooltip" title="Fecha programada<br><?= mysqlDate2OnlyDate($auditoria['fechaIniAudit'], TRUE); ?>"><?= abs($auditoria['reprogramacion_inicio_dias_habiles']); ?> día<?= $plural ? 's' : ''; ?> hábil<?= $plural ? 'es' : ''; ?> después</strong> de la fecha programada</p>
                <?php endif; ?>
                <?php if (intval($auditoria['reprogramacion_inicio_dias_habiles']) < 0): ?>
                    <p>Se inició <strong class="text text-success" data-toggle="tooltip" title="Fecha programada<br><?= mysqlDate2OnlyDate($auditoria['fechaIniAudit'], TRUE); ?>"><?= abs($auditoria['reprogramacion_inicio_dias_habiles']); ?> día<?= $plural ? 's' : ''; ?> hábil<?= $plural ? 'es' : ''; ?> antes</strong> de la fecha programada</p>
                <?php endif; ?>
                <p>
                    <strong>Auditor Líder:</strong> <?= $auditoria['lider']; ?><br>
                    <?php if (isset($auditoria['equipo']) && count($auditoria['equipo']) > 0): ?>
                        <strong>Equipo: </strong><?= implode(", ", $auditoria['equipo']); ?><br>
                    <?php endif; ?>
                    <br>
                    <?php if ($auditoria['fechaIniReal'] != $auditoria['fechaIniAudit']): ?>
                        <strong>Fecha Inicio Reprogramada:</strong> <?= mysqlDate2OnlyDate($auditoria['fechaIniReal'], TRUE); ?><br>
                    <?php endif; ?>
                    <?php if (!empty($auditoria['fechaFinReal']) && $auditoria['fechaFinReal'] != $auditoria['fechaFinAudit']): ?>
                        <strong>Fecha Fin Reprogramada:</strong> <?= mysqlDate2OnlyDate($auditoria['fechaFinReal'], TRUE); ?><br>
                    <?php endif; ?>
                    <br>
                    <strong>Fecha Inicio Programada:</strong> <?= mysqlDate2OnlyDate($auditoria['fechaIniAudit'], TRUE); ?><br>
                    <strong>Fecha Fin Programada:</strong> <?= mysqlDate2OnlyDate($auditoria['fechaFinAudit'], TRUE); ?><br>
                    <!--Diferencia Días Hábiles: <?= $auditoria['reprogramacion_inicio_dias_habiles']; ?><br>
                    Diferencia Días Naturales: <?= $auditoria['reprogramacion_inicio_dias_naturales']; ?><br>-->
                </p>
            </div>
            <?php $aux = new DateTime($auditoria['fechaIniReal']); ?>
            <!--<div class="timeline-date"><span class="timeline-date-day"><?= getNombreDelDia($aux->format('w')) . ", "; ?></span><?= mysqlDate2OnlyDate($auditoria['fechaIniReal']); ?></div>-->
        </section>
    </div>
</div>
<style>
    .dl-horizontal { margin-left: 0px !important; }
    .list-group-item { box-shadow: none; }
    div#tareas_pendientes {
        display:none;
        border: 1px dotted #C5C5EC;
        background-color: #ececec;
        /*background-image: url(<?= base_url(); ?>resources/images/marca_de_agua_tareas_pendientes.png);*/
        padding-top: 20px;
        margin-top: -20px;
    }
    .tooltip-inner {
        max-width:none;
    }
    .stacked .timeline-date-day { display: none; }
    @media(max-width: 991px){
        .timeline-date-day { display: none; }
    }
</style>
<link href="<?= base_url(); ?>resources/styles/personalizados_cysa.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-bootbox/bootbox.min.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/scripts/timeline_view.js" type="text/javascript"></script>