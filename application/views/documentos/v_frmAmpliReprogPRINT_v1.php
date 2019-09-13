<?php
$aux = $this->Auditorias_tipos_model->get_uno($auditoria['auditorias_tipo']);
$tipo_auditoria = $aux['auditorias_tipos_nombre'] . " (" . $auditoria['auditorias_tipos_siglas'] . ")";
$aux = $this->Auditorias_areas_model->get_uno($auditoria['auditorias_area']);
$area_responsable = $aux['auditorias_areas_nombre'];
$aux = array(
    $auditoria['direcciones_nombre'],
    $auditoria['subdirecciones_nombre'],
    $auditoria['departamentos_nombre']
);
$unidad_administrativa = implode(" / ", $aux);
$amplia_reprog = array(
    'etapa' => AUDITORIA_ETAPA_AP
);
/*
 *
 * Falta verificar cómo obtener en qué etapa se encuentra la auditoría
 *
 */
$etapas = array('Resultados de Auditoría', 'Solventación de Observaciones', 'Segunda Solventación de Observaciones', 'Auditoría Finalizada');
$is_reprogramacion = TRUE;
$folios = array();
if ($documentos_tipos_id == TIPO_DOCUMENTO_AMPLIACION) {
    $is_reprogramacion = FALSE;
    $ampliaciones = $this->Auditoria_model->get_documentos($auditoria['auditorias_id'], TIPO_DOCUMENTO_AMPLIACION);
    foreach ($ampliaciones as $amplia) {
        if (isset($amplia['valores']) && isset($amplia['valores'][AMPLIA_REPROG_FOLIO])) {
            array_push($folios, $amplia['valores'][AMPLIA_REPROG_FOLIO]);
        }
    }
} else {
    $reprogramaciones = $this->Auditoria_model->get_documentos($auditoria['auditorias_id'], TIPO_DOCUMENTO_REPROGRAMACION);
    foreach ($reprogramaciones as $reprog) {
        if (isset($reprog['valores']) && isset($reprog['valores'][AMPLIA_REPROG_FOLIO])) {
            array_push($folios, $reprog['valores'][AMPLIA_REPROG_FOLIO]);
        }
    }
}
?>
<div id="oficio-hoja" class="acta <?= $documento_autorizado ? 'autorizado' : ''; ?>">
    <div class="watermark">PARA REVISIÓN</div>
    <table width="100%">
        <thead>
            <tr>
                <td>
                    <div class="row">
                        <div class="col-xs-3">
                            <img src="<?= APP_SAC_URL; ?>resources/images/logo-icon.png" alt=""/>
                        </div>
                        <div class="col-xs-6 text-xs-center">
                            <div style="font-size: 15pt; font-weight: bold;">AYUNTAMIENTO DE MÉRIDA</div>
                            <div><?= mb_strtoupper(LABEL_CONTRALORIA); ?></div>
                            <div>SOLICITUD DE <?= $is_reprogramacion ? 'REPROGRAMACIÓN' : 'AMPLIACIÓN'; ?></div>
                        </div>
                        <div class="col-xs-3"></div>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody id="oficio-body">
            <tr>
                <td>
                    <table class="table table-sm mismo-tamano-fuente-p table-borderless">
                        <tbody>
                            <tr>
                                <th width="300">Folio:</th>
                                <td>
                                    <?php $aux = isset($r[AMPLIA_REPROG_FOLIO]) ? $r[AMPLIA_REPROG_FOLIO] : count($folios) + 1; ?>
                                    <?= str_pad($aux, 3, "0", STR_PAD_LEFT); ?>
                                    <input type="hidden" name="constantes[<?= AMPLIA_REPROG_FOLIO; ?>]" value="<?= $aux; ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Fecha de solicitud:</th>
                                <td>
                                    <?php $aux = isset($r[AMPLIA_REPROG_FECHA_SOLICITUD]) ? $r[AMPLIA_REPROG_FECHA_SOLICITUD] : ahora(); ?>
                                    <?= mysqlDate2OnlyDate($aux); ?>
                                    <input type="hidden" name="constantes[<?= AMPLIA_REPROG_FECHA_SOLICITUD; ?>]" value="<?= $aux; ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Número de auditoría:</th>
                                <td>
                                    <?php $aux = empty($auditoria['auditorias_numero']) ? $auditoria['numero_auditoria'] : 'Sin asignar'; ?>
                                    <?= $aux; ?>
                                    <input type="hidden" name="constantes[<?= AMPLIA_REPROG_NUMERO_AUDITORIA; ?>]" value="<?= $aux; ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Etapa de auditoría:</th>
                                <td>
                                    <?= $etapas[$amplia_reprog['etapa']]; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Unidad Administrativa:</th>
                                <td>
                                    <?= $auditoria['direcciones_nombre']; ?>
                                    <input type="hidden" name="constantes[<?= AMPLIA_REPROG_UA_AUDITADA; ?>]" value="<?= $auditoria['direcciones_nombre']; ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Subdirección:</th>
                                <td>
                                    <?= $auditoria['subdirecciones_nombre']; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Área auditada:</th>
                                <td>
                                    <?= $auditoria['departamentos_nombre']; ?>
                                    <input type="hidden" name="constantes[<?= AMPLIA_REPROG_AREA_AUDITADA; ?>]" value="<?= $auditoria['departamentos_nombre']; ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>Área Responsable de Auditoría:</th>
                                <td>
                                    <?= $auditoria['auditorias_areas_nombre']; ?>
                                    <input type="hidden" name="constantes[<?= AMPLIA_REPROG_AREA_RESPONSABLE; ?>]" value="<?= $auditoria['departamentos_nombre']; ?>">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-sm table-bordered mismo-tamano-fuente-p">
                        <tbody>
                            <tr><th colspan="5">Actividad o motivo</th></tr>
                            <tr><td colspan="5"><?= span_editable($r, AMPLIA_REPROG_MOTIVO, NULL, NULL, NULL, TRUE) ?></td></tr>
                            <tr>
                                <th colspan="2" class="text-xs-center">Fecha Inicio</th>
                                <th colspan="2" class="text-xs-center">Fecha Final</th>
                                <th class="text-xs-center">Días de impacto</th>
                            </tr>
                            <tr>
                                <th class="text-xs-center">Programada</th>
                                <th class="text-xs-center">Real</th>
                                <th class="text-xs-center">Programada</th>
                                <th class="text-xs-center">Real</th>
                                <td class="text-xs-center v-align-middle" rowspan="2">
                                    <?php $aux = isset($r[AMPLIA_REPROG_DIAS_IMPACTO]) ? $r[AMPLIA_REPROG_DIAS_IMPACTO] : '0'; ?>
                                    <?php if ($documento_autorizado): ?>
                                        <?php echo $aux; ?>
                                    <?php else: ?>
                                        <?php if ($is_reprogramacion): ?>
                                            <?= $aux; ?>
                                        <?php else: ?>
                                            <input type="number" id="<?= AMPLIA_REPROG_DIAS_IMPACTO; ?>" name="constantes[<?= AMPLIA_REPROG_DIAS_IMPACTO; ?>]" value="<?= $aux; ?>" class="form-control text-xs-center" min="0" default-value="0">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-xs-center v-align-middle">
                                    <?php $aux = $auditoria['auditorias_fechas_inicio_programado']; ?>
                                    <?= mysqlDate2OnlyDate($aux); ?>
                                    <input type="hidden" name="constantes[<?= AMPLIA_REPROG_FECHA_INICIAL; ?>]" value="<?= $aux; ?>">
                                </td>
                                <td class="text-xs-center v-align-middle">
                                    <?php if ($is_reprogramacion): ?>
                                        <button class="btn btn-secondary btn-block component-daterangepicker" id="input_fecha_programada_terminacion" type="button" datepicker="fecha_programada_terminacion"><?= isset($r) && !empty($r['fecha_programada_terminacion']) && $r['fecha_programada_terminacion'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['fecha_programada_terminacion']) : '<i class="fa fa-calendar"></i>'; ?></button>
                                        <input type="hidden" id="fecha_programada_terminacion" name="fecha_programada_terminacion" value="<?= isset($r) && isset($r[AMPLIA_REPROG_FECHA_PROGRAMADA_TERMINACION]) && $r[AMPLIA_REPROG_FECHA_PROGRAMADA_TERMINACION] !== '0000-00-00' ? $r[AMPLIA_REPROG_FECHA_PROGRAMADA_TERMINACION] : ''; ?>">
                                    <?php else: ?>
                                        <?php $aux = $auditoria['auditorias_fechas_inicio_real']; ?>
                                        <?= mysqlDate2OnlyDate($aux); ?>
                                        <input type="hidden" name="constantes[<?= AMPLIA_REPROG_FECHA_INICIAL; ?>]" value="<?= $aux; ?>">
                                    <?php endif; ?>
                                </td>
                                <td class="text-xs-center v-align-middle">
                                    <?php $aux = $is_reprogramacion ? NULL : $auditoria['auditorias_fechas_fin_programado']; ?>
                                    <?= empty($aux) ? 'N/A' : mysqlDate2OnlyDate($aux); ?>
                                </td>
                                <td class="text-xs-center v-align-middle" class="col-xs-12" style="border-right:1px solid rgba(0,0,0,.1);">
                                    <?php $aux = $is_reprogramacion ? NULL : (!empty($r[AMPLIA_REPROG_FECHA_FINAL]) ? $r[AMPLIA_REPROG_FECHA_FINAL] : $auditoria['auditorias_fechas_fin_programado']); ?>
                                    <span id="actualizar<?= AMPLIA_REPROG_FECHA_FINAL; ?>"><?= empty($aux) ? 'N/A' : mysqlDate2OnlyDate($aux); ?></span>
                                    <input type="hidden" id="<?= AMPLIA_REPROG_FECHA_FINAL; ?>" name="constantes[<?= AMPLIA_REPROG_FECHA_FINAL; ?>]" value="<?= empty($aux) ? 'N/A' : $aux; ?>">
                                    <input type="hidden" id="fecha_final_original" value="<?= empty($aux) ? 'N/A' : $aux; ?>">
                                </td>
                            </tr>
                            <tr><th colspan="5">Observaciones</td></th>
                            <tr><td colspan="5"><?= span_editable($r, AMPLIA_REPROG_OBSERVACIONES, NULL, NULL, NULL, TRUE); ?></td></tr>
                        </tbody>
                    </table>
                    <?php if ($documento_autorizado): ?>
                        <div class="firmas row">
                            <div class="col-xs-4 col-print-4">
                                <?php $e = $this->Empleados_model->get_jefe($auditoria['auditorias_periodos_id'], $auditoria['auditorias_auditor_lider']); ?>
                                <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : SIN_ESPECIFICAR; ?>">
                                    <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : SIN_ESPECIFICAR; ?></div>
                                    <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : SIN_ESPECIFICAR; ?></div>
                                    <div class="firmas_empleado_cargo">Solicitó</div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-print-4">
                                <?php $e = $this->CYSA_model->get_subdirector_de_contraloria($auditoria['auditorias_periodos_id']); ?>
                                <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : SIN_ESPECIFICAR; ?>">
                                    <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : SIN_ESPECIFICAR; ?></div>
                                    <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : SIN_ESPECIFICAR; ?></div>
                                    <div class="firmas_empleado_cargo">Vo.Bo.</div>
                                </div>
                            </div>
                            <div class="col-xs-4 col-print-4-">
                                <?php $e = $this->CYSA_model->get_director_de_contraloria($auditoria['auditorias_periodos_id']); ?>
                                <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : SIN_ESPECIFICAR; ?>">
                                    <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : SIN_ESPECIFICAR; ?></div>
                                    <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : SIN_ESPECIFICAR; ?></div>
                                    <div class="firmas_empleado_cargo">Autorizó</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-xs-right">
                    <?= $documento['documentos_versiones_prefijo_iso'] . $documento['documentos_versiones_codigo_iso'] . " " . $documento['documentos_versiones_numero_iso']; ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="form-group row hidden-print">
    <div class="col-sm-12 text-xs-center">
        <a href="<?= base_url() . $this->uri->segment(1); ?>" class="btn btn-default">Cancelar</a>
        <?php if (!$documento_autorizado && $this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_MODIFICAR, APP_NAMESPACE, 'Documentos')): ?>
            <button type="button" class="btn btn-primary m-l-2 boton_guardar"><?= $etiquetaBoton; ?></button>
        <?php endif; ?>
        <input type="hidden" name="<?= $this->module['id_field'] ?>" value="<?= $id; ?>">
        <input type="hidden" name="documentos_id" id="documentos_id" value="<?= isset($documento['documentos_id']) && $accion === "modificar" ? $documento['documentos_id'] : 0; ?>">
        <input type="hidden" name="accion" id="accion" value="<?= $accion; ?>">
        <input type="hidden" name="documentos_tipos_id" id="documentos_tipos_id" value="<?= $documento['documentos_versiones_documentos_tipos_id']; ?>">
        <input type="hidden" name="documentos_versiones_id" id="documentos_versiones_id" value="<?= $documento['documentos_versiones_id']; ?>">
    </div>
</div>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.js" type="text/javascript"></script>
<!-- Personalizados -->
<script src="<?= base_url(); ?>resources/scripts/auditorias_documento_amplia_reprog.js" type="text/javascript"></script>