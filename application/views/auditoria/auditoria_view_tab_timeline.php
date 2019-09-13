<?php
$etapasNombre = array(
    AUDITORIA_ETAPA_AP => 'Auditoría programada',
    AUDITORIA_ETAPA_REV1 => 'Solventación de Observaciones',
    AUDITORIA_ETAPA_REV2 => 'Segunda revisión'
);
define("ETAPA_AP", AUDITORIA_ETAPA_AP);
define("OBSERVACION_SOLVENTADA", OBSERVACIONES_STATUS_SOLVENTADA);
define("OBSERVACION_NO_SOLVENTADA", OBSERVACIONES_STATUS_NO_SOLVENTADA);
define("OBSERVACION_ATENDIDA", OBSERVACIONES_STATUS_ATENDIDA);
define("PREFIX", "");
$PREFIX = PREFIX;
$aux = $this->Auditoria_model->get_etapa();
$etapaAuditoria = 0;
switch ($aux['etapa']) {
    case AUDITORIA_ETAPA_FIN:
    case AUDITORIA_ETAPA_REV2:
    case AUDITORIA_ETAPA_REV1:
        $etapaAuditoria = AUDITORIA_ETAPA_REV1;
        break;
    case AUDITORIA_ETAPA_AP:
        $etapaAuditoria = AUDITORIA_ETAPA_AP;
        break;
}
$etapasArray = array(AUDITORIA_ETAPA_AP, AUDITORIA_ETAPA_REV1, AUDITORIA_ETAPA_REV2);

if (isset($recomendaciones)) {
    $dataRecomendaciones = array();
    $dataRecomendaciones = $this->Articulo70_model->get_uno($auditorias_id);
    if (is_array($dataRecomendaciones) && count($dataRecomendaciones) > 0) {
        $periodoFrom = explode("-", $dataRecomendaciones['articulo70_periodo_from']);
        $periodoTo = explode("-", $dataRecomendaciones['articulo70_periodo_to']);
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
<!-- CandleStick -->
<link href="<?= base_url(); ?>resources/plugins/jquery.candlestick/dist/candlestick.min.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/plugins/jquery.candlestick/dist/hammer.min.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/jquery.candlestick/dist/jquery.hammer.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/jquery.candlestick/dist/candlestick.min.js" type="text/javascript"></script>
<!-- FileUpload -->
<link href="<?= base_url(); ?>resources/plugins/blueimp-file-upload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/plugins/jquery.ui/ui/widget.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/blueimp-file-upload/js/jquery.iframe-transport.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/blueimp-file-upload/js/jquery.fileupload.js" type="text/javascript"></script>
<!-- DatePicker -->
<link href="<?= base_url(); ?>resources/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js" type="text/javascript"></script>
<span class="text-xs-right pull-right float-xs-right visible-print-block" style="margin-top:6px"><?= mysqlDate2OnlyDate(date("Y-m-d"), TRUE); ?></span>
<h3 class="visible-print-block"><strong><?= $auditoria['numero_auditoria']; ?></strong></h3>
<?php foreach ($etapas as $indexEtapa => $e): ?>
    <div id="etapa<?= $e['etapas_codigo']; ?>" class="etapas">
        <div style="height: 77px;">
            <p class="lead alert alert-info etapa-nombre text-xs-center"><strong><?= $e['etapas_nombre']; ?></strong></p>
        </div>
        <div class="timeline">
            <?php if ($e['etapas_codigo'] == AUDITORIA_ETAPA_AP): ?>
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
                <?php $mostrarBtnEditar = ($t['editar_fecha'] && $existeCampoEjecucion && $t['tareas_nombre'] != "Inicio de Revisión de Solventación" && $t['tareas_nombre'] != "Fin de Auditoría") || ($t['tareas_nombre'] == "Envío de Oficio de Orden de Auditoría" && $auditoria['statusAudit'] == AUDIT_PROCESO /*&& $_SESSION['usuario']->validaSubsistema('cysaRegAuditoria')*/); ?>
                <?php if (in_array($t['configuraciones_tareas_id'], array(20, 24, 27, 28))) $t['class'] = 'purple-darker'; ?>
                <?php if (in_array($t['configuraciones_tareas_id'], array(20, 27))) $t['icon'] = 'flag'; ?>
                <?php if (in_array($t['configuraciones_tareas_id'], array(24, 28))) $t['icon'] = "star"; ?>
                <?php // if ($t['configuraciones_tareas_id']==24 && $e['etapas_codigo'] > AUDITORIA_ETAPA_AP) $t['icon'] = "star_border"; ?>
                <div class="timeline-card" data-editable="<?= intval($t['configuraciones_orden_ejecucion']) < count($tareas) && $existeCampoEjecucion ? 'true' : 'false'; ?>">
                    <div class="timeline-icon bg-<?= $t['class']; ?> text-white">
                        <i class="material-icons"><?= $t['icon']; ?></i>
                    </div>
                    <section class="timeline-content">
                        <div class="timeline-body">
                            <div class="timeline-heading lead m-b-0">
                                <strong><?= $t['tareas_nombre']; ?><?= ($e['etapas_codigo'] == AUDITORIA_ETAPA_AP && $indexTarea == count($tareas) - 1) ? '<br>' . $auditoria['nombreAuditoria'] : ''; ?></strong>
                                <?php //if ($etapaAuditoria == $e['etapas_codigo']):         ?>
                                <input type="text" class="input_campo_ejecucion" name="fecha_ejecucion_alt" id="fecha_ejecucion_alt_<?= $t['configuraciones_id'] ?>" value="<?= isset($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_ejecucion'] > 0 ? $t['tareas_fecha_ejecucion'] : $t['tareas_fecha_programada']; ?>" data-tareas-nombre="<?= $t['tareas_nombre']; ?>" data-tipo-mysql="<?= isset($t['campo_ejecucion_real']) ? $this->Timeline_model->get_tipo_campo_mysql($t['campo_ejecucion_real']) : ''; ?>">
                                <a href="#" class="campo_ejecucion <?= ($t['tareas_nombre'] != "Inicio de Auditoría" && $mostrarBtnEditar) ? '' : 'hidden-xs-up'; ?>"
                                   data-campo-ejecucion="<?= isset($t['campo_ejecucion_real']) ? $t['campo_ejecucion_real'] : ''; ?>"
                                   data-tareas-fecha-programada="<?= $t['tareas_fecha_programada']; ?>"
                                   data-tareas-nombre="<?= $t['tareas_nombre']; ?>"
                                   data-configuraciones-id="<?= $t['configuraciones_id']; ?>"
                                   data-tareas-fecha-ejecucion="<?= isset($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_ejecucion'] > 0 ? $t['tareas_fecha_ejecucion'] : ''; ?>">
                                    <i class="fa fa-calendar-check-o"></i>
                                </a>
                                <?php //endif;      ?>
                                <?php if (!is_null($t['configuraciones_extra_button']) /* && $etapaAuditoria == $e['etapas_codigo'] */ && ($auditoria['auditorias_status_id'] == AUDITORIAS_STATUS_EN_PROCESO || $t['editar_fecha'])) eval('?>' . $t['configuraciones_extra_button']); ?>
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
                                        <span class="fecha_ejecucion_valor" id="<?= isset($t['campo_ejecucion_real']) ? str_replace(".", "-", $t['campo_ejecucion_real']) : ''; ?>"><?= isset($t['tareas_fecha_ejecucion']) && $t['tareas_fecha_ejecucion'] > 0 ? ($this->Timeline_model->get_tipo_campo_mysql($t['campo_ejecucion_real']) == "DATETIME" ? mysqlDate2Date($t['tareas_fecha_ejecucion'], FALSE) : mysqlDate2OnlyDate($t['tareas_fecha_ejecucion'], TRUE)) : ''; ?></span>
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
                                                            <?php if (!empty($vobo) && is_array($vobo)): // Hay informacion del ultimo vobo    ?>
                                                                <?php if ($this->session->userdata('empleados_id') == $auditoria['idLider']): // Cuando soy el lider de auditoria muestro los controles para entregar el documento ?>
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
                                                                <?php elseif ((intval($this->session->userdata('empleados_id')) == $idEmpleadoVoBo) || (is_array($idEmpleadoVoBo) && in_array($this->session->userdata('empleados_id'), $idEmpleadoVoBo))): ?>
                                                                    <input class="js-candlestick" type="checkbox" value="<?= isset($vobo['bVoBo']) ? strval($vobo['bVoBo']) : ''; ?>" name="idDocto_<?= $idDocto; ?>" id="idDocto_<?= $idDocto; ?>_1" data-idRevisiones="<?= $vobo['revisiones_id']; ?>" data-idDocto="<?= $d['idDocto'] ?>" data-idEmpleado="<?= $idEmpleadoVoBo; ?>" <?= $bloqueado ? 'disabled="true"' : ''; ?> data-valorInicial="<?= isset($vobo['bVoBo']) ? strval($vobo['bVoBo']) : 'reset'; ?>">
                                                                <?php else: ?>
                                                                    <i class="material-icons bg-<?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'success' : 'danger') : 'info'; ?> <?= empty($d['idDocto']) ? 'disabled' : ''; ?>"><?= isset($vobo['bVoBo']) ? ($vobo['bVoBo'] == 1 ? 'check' : 'clear') : 'help_outline'; ?></i>
                                                                <?php endif; ?>
                                                            <?php else : // sino hay informacion ?>
                                                                <?php if (isset($d['idDocto'])): // existe el idDocto?, mostramos el boton ENTREGAR ?>
                                                                    <?php if ((intval($this->session->userdata('empleados_id')) == $idEmpleadoVoBo) || (is_array($idEmpleadoVoBo) && in_array($this->session->userdata('empleados_id'), $idEmpleadoVoBo))): ?>
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
                                        $aux3 = agregar_dias($t['tareas_fecha_programada'], 4);
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
            <?php if ($e['etapas_codigo'] == AUDITORIA_ETAPA_AP): ?>
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


<?php if (!is_null($auditoria['auditorias_origen_id'])): ?>
    <?php $origen = getAuditoria($auditoria['auditorias_origen_id']); ?>
    <div class="row">
        <div class="col-sm-8 col-md-4 center-block" style="float:none; text-align: center;">
            <a href="#" class="btn btn-lg btn-info btn-block" onclick="return abrirTimeline('../controlador/timeline.php', '<?= $auditoria['auditorias_origen_id']; ?>');">Auditoría Origen<br><?= $origen['num']; ?></a>
        </div>
    </div>
<?php endif; ?>

<?php if ((isset($recomendaciones) && isset($recomendaciones[OBSERVACIONES_STATUS_SOLVENTADA]) && $recomendaciones[OBSERVACIONES_STATUS_SOLVENTADA] == $totalRecomendaciones) || ((isset($is_sin_observaciones) && $is_sin_observaciones == 1))): ?>
    <div class="modal fade" id="modalArticulo70" tabindex="-1" role="dialog" aria-labelledby="modalArticulo70" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                            <label for="periodo_from_label" class="col-xs-2 col-sm-1 col-form-label">Periodo</label>
                            <div class="col-xs-10 col-sm-6">
                                <div class="input-group">
                                    <input class="form-control text-xs-center form-control-success form-control-danger" type="text" value="<?= isset($dataRecomendaciones['articulo70_periodo_from']) ? ucfirst(getNombreDelMes($periodoFrom[1])) . "/" . $periodoFrom[0] : ''; ?>" id="periodo_from_label" data-hidden="periodo_from"<?= (isset($dataRecomendaciones['articulo70_bloqueado']) && $dataRecomendaciones['articulo70_bloqueado'] ? ' disabled' : '') ?>>
                                    <span class="input-group-addon" id="sizing-addon2">a</span>
                                    <input class="form-control text-xs-center form-control-success form-control-danger" type="text" value="<?= isset($dataRecomendaciones['articulo70_periodo_to']) ? ucfirst(getNombreDelMes($periodoTo[1])) . "/" . $periodoTo[0] : ''; ?>" id="periodo_to_label" data-hidden="periodo_to"<?= (isset($dataRecomendaciones['articulo70_bloqueado']) && $dataRecomendaciones['articulo70_bloqueado'] ? ' disabled' : '') ?>>
                                </div>
                                <input type="hidden" value="<?= isset($dataRecomendaciones['articulo70_periodo_from']) ? $dataRecomendaciones['articulo70_periodo_from'] : ''; ?>" name="periodo_from" id="periodo_from">
                                <input type="hidden" value="<?= isset($dataRecomendaciones['articulo70_periodo_to']) ? $dataRecomendaciones['articulo70_periodo_to'] : ''; ?>" name="periodo_to" id="periodo_to">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-12 col-sm-4">
                                <div class="col-xs-12 text-xs-center">
                                    <?php if (!isset($dataRecomendaciones['bloqueado']) || $dataRecomendaciones['bloqueado'] == 0): ?>
                                        <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                            <i class="material-icons">file_upload</i>
                                            <span>Seleccionar PDF de ARA...</span>
                                            <input id="fileuploadARAPDF" type="file" name="filesARAPDF" tipoDocto="ARA" app="PDF" accept="application/pdf">
                                        </div>
                                        <progress id="progressARAPDF" class="progress progress-info progress-animated hidden-xs-up" value="0" max="100"></progress>
                                        <div id="filesARAPDF" class="files"></div>
                                    <?php endif; ?>
                                    <?php $pathDestino = PATH_ARCHIVOS_ARTICULO_70; ?>
                                    <?php $nombreArchivo = str_replace("/", "-", $auditoria['numero_auditoria']) . " ARA.pdf"; ?>
                                    <div id="downloadFileARAPDF" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                        <a target="_blank" class="btn bg-red-lighter" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= $nombreArchivo; ?> <i class="fa fa-file-pdf-o"></i></a>
                                    </div>
                                    <br> <!-- ARA WORD ------------------------------------------------>
                                    <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                        <i class="material-icons">file_upload</i>
                                        <span>Seleccionar WORD del ARA</span>
                                        <input id="fileuploadARAWord" type="file" name="filesARAWord" tipoDocto="ARA" app="Word" accept=".doc,.docx">
                                    </div>
                                    <progress id="progressARAWord" class="progress progress-info progress-animated hidden-xs-up" value="0" max="100"></progress>
                                    <div id="filesARAWord" class="files"></div>
                                    <?php $nombreArchivo = str_replace("/", "-", $auditoria['numero_auditoria']) . " ARA.docx"; ?>
                                    <div id="downloadFileARAWord" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                        <a target="_blank" class="btn bg-info" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= $nombreArchivo; ?> <i class="fa fa-file-word-o"></i></a>
                                    </div>
                                    <div style="margin-top:10px;">
                                        <a href="../controlador/html2docx.php?idAuditoria=<?= $auditorias_id; ?>&tipoAuditoria=<?= $auditoria['auditorias_tipo']; ?>&idTipoDocto=<?= in_array($auditoria['auditorias_tipo'], array('AP', 'AE')) ? 3 : 6; ?>" target="_blank">Generar WORD del ARA</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="col-xs-12 text-xs-center">
                                    <?php if (intval($auditoria['auditorias_anio']) < 2018): ?>
                                        <?php if (!isset($dataRecomendaciones['bloqueado']) || $dataRecomendaciones['bloqueado'] == 0): ?>
                                            <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                                <i class="material-icons">file_upload</i>
                                                <span>Seleccionar PDF de OED...</span>
                                                <input id="fileuploadOEDPDF" type="file" name="filesOEDPDF" tipoDocto="OED" app="PDF" accept="application/pdf">
                                            </div>
                                            <progress id="progressOEDPDF" class="progress progress-info progress-animated hidden-xs-up" value = "0" max = "100"></progress>
                                            <div id="filesOEDPDF" class="files"></div>
                                        <?php endif; ?>
                                        <?php $nombreArchivo = str_replace("/", "-", $auditoria['numero_auditoria']) . " Envío de documentos.pdf"; ?>
                                        <div id="downloadFileOEDPDF" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                            <a target="_blank" class="btn bg-red-lighter" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= str_replace("/", "-", $auditoria['numero_auditoria']) . "<br>Envío de documentos.pdf"; ?> <i class="fa fa-file-pdf-o"></i></a>
                                        </div>
                                        <br> <!-- CO WORD ------------------------------------------------>
                                    <?php endif; ?>
                                    <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                        <i class="material-icons">file_upload</i>
                                        <span>Seleccionar WORD de Cédulas</span>
                                        <input id="fileuploadCOWord" type="file" name="filesCOWord" tipoDocto="CO" app="Word" accept=".doc,.docx">
                                    </div>
                                    <progress id="progressCOWord" class="progress progress-info progress-animated hidden-xs-up" value="0" max="100"></progress>
                                    <div id="filesCOWord" class="files"></div>
                                    <?php $nombreArchivo = str_replace("/", "-", $auditoria['numero_auditoria']) . " Cédulas de observación.docx"; ?>
                                    <div id="downloadFileCOWord" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                        <a target="_blank" class="btn bg-info" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= str_replace("/", "-", $auditoria['numero_auditoria']) . "<br>Cédulas de Observación"; ?> <i class="fa fa-file-word-o"></i></a>
                                    </div>
                                    <div style="margin-top:10px;">
                                        <a href="../controlador/html2docx.php?idAuditoria=<?= $auditorias_id; ?>&tipoAuditoria=<?= $auditoria['auditorias_tipo']; ?>&idTipoDocto=17" target="_blank">Generar WORD de las Cédulas de Observación</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="text-xs-center">
                                    <?php if (!isset($dataRecomendaciones['bloqueado']) || $dataRecomendaciones['bloqueado'] == 0): ?>
                                        <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                            <i class="material-icons">file_upload</i>
                                            <span>Seleccionar PDF de ARR...</span>
                                            <input id="fileuploadARRPDF" type="file" name="filesARRPDF" tipoDocto="ARR" app="PDF" accept="application/pdf">
                                        </div>
                                        <progress id="progressARRPDF" class="progress progress-info progress-animated hidden-xs-up" value="0" max="100"></progress>
                                        <div id="filesARRPDF" class="files"></div>
                                    <?php endif; ?>
                                    <?php $pathDestino = PATH_ARCHIVOS_ARTICULO_70; ?>
                                    <?php $nombreArchivo = str_replace("/", "-", $auditoria['numero_auditoria']) . " ARR.pdf"; ?>
                                    <div id="downloadFileARRPDF" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                        <a target="_blank" class="btn bg-red-lighter" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= $nombreArchivo; ?> <i class="fa fa-file-pdf-o"></i></a>
                                    </div>
                                    <br> <!-- ARR WORD ------------------------------------------------>
                                    <div class="btn btn-default btn-icon fileinput-button m-b-1">
                                        <i class="material-icons">file_upload</i>
                                        <span>Seleccionar WORD del ARR</span>
                                        <input id="fileuploadARRWord" type="file" name="filesARRWord" tipoDocto="ARR" app="Word" accept=".doc,.docx">
                                    </div>
                                    <progress id="progressARRWord" class="progress progress-info progress-animated hidden-xs-up" value="0" max="100"></progress>
                                    <div id="filesARRWord" class="files"></div>
                                    <?php $nombreArchivo = str_replace("/", "-", $auditoria['numero_auditoria']) . " ARR.docx"; ?>
                                    <div id="downloadFileARRWord" class="<?= file_exists($pathDestino . $nombreArchivo) === FALSE ? 'hidden-xs-up' : ''; ?>">
                                        <a target="_blank" class="btn bg-info" href="timeline_articulo70_descargar_pdf.php?f=<?= $nombreArchivo; ?>"><?= $nombreArchivo; ?> <i class="fa fa-file-word-o"></i></a>
                                    </div>
                                    <div style="margin-top:10px;">
                                        <a href="../controlador/html2docx.php?idAuditoria=<?= $auditorias_id; ?>&tipoAuditoria=<?= $auditoria['auditorias_tipo']; ?>&idTipoDocto=5" target="_blank">Generar WORD del ARR</a>
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

<style>
    .modal-lg { width: 90% !important;}
</style>
<!-- Timeline -->
<link href="<?= base_url(); ?>resources/styles/auditorias_view_tab_timeline.css" rel="stylesheet" type="text/css"/>
<link href="<?= base_url(); ?>resources/styles/timeline.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/scripts/timeline.js" type="text/javascript"></script>
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