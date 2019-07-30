<?php
$is_asignar_consecutivo = FALSE;
if ($this->input->server('REQUEST_METHOD') === "POST" && $this->input->post('asignar_consecutivo') == 1) {
    $is_asignar_consecutivo = TRUE;
}
if ($accion === 'nuevo' && isset($r)) {
    $periodos_id = intval($r['auditorias_periodos_id']);
    $direcciones_id = intval($r['auditorias_direcciones_id']);
    $subdirecciones_id = intval($r['auditorias_subdirecciones_id']);
    $departamentos_id = intval($r['auditorias_departamentos_id']);
    $subdirecciones = $this->SAC_model->get_subdirecciones_de_direccion($periodos_id, $direcciones_id);
    $departamentos = $this->SAC_model->get_departamentos_de_subdireccion($periodos_id, $direcciones_id, $subdirecciones_id);
}
echo validation_errors();
?><div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <div>
            <form id="myForm" method="post" action="<?= $urlAction; ?>" novalidate="novalidate">
                <fieldset class="form-group">
                    <label for="area" class="col-sm-2 form-control-label">Número de auditoría</label>
                    <div class="col-sm-10">
                        <?php if (isset($r, $r['auditorias_fechas_sello_orden_entrada']) && empty($r['auditorias_fechas_sello_orden_entrada'])): ?>
                            <div class="input-group">
                                <select id="auditorias_area" name="auditorias_area" class="form-control">
                                    <option value="0">Área</option>
                                    <?php foreach ($areas as $a): ?>
                                        <option value="<?= $a['auditorias_areas_id']; ?>"<?= isset($r) && $r['auditorias_area'] === $a['auditorias_areas_id'] ? ' selected="selected"' : ''; ?>><?= $a['auditorias_areas_siglas']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-addon">/</div>
                                <select id="auditorias_tipo" name="auditorias_tipo" class="form-control">
                                    <option value="0">Tipo</option>
                                    <?php foreach ($tipos as $t): ?>
                                        <option value="<?= $t['auditorias_tipos_id']; ?>"<?= isset($r) && $r['auditorias_tipo'] === $t['auditorias_tipos_id'] ? ' selected="selected"' : ''; ?>><?= $t['auditorias_tipos_siglas'] . " (" . $t['auditorias_tipos_nombre'] . ")"; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-addon">/</div>
                                <input type="text" id="numero_auditoria" class="form-control" value="<?= isset($r) && intval($r['auditorias_numero']) > 0 ? $r['auditorias_numero'] : ''; ?>" placeholder="Número" <?= $is_asignar_consecutivo ? 'disabled="disabled"' : ''; ?>>
                                <input type="hidden" id="auditorias_numero" name="auditorias_numero" value="<?= isset($r) && intval($r['auditorias_numero']) > 0 ? $r['auditorias_numero'] : ''; ?>">
                                <div class="input-group-addon">/</div>
                                <select id="auditorias_anio" name="auditorias_anio" class="form-control">
                                    <?php foreach ($anios as $a): ?>
                                        <option value="<?= $a; ?>" <?= isset($r) ? ($r['auditorias_anio'] == $a ? 'selected="selected"' : '') : ($a == date("Y") ? 'selected="selected"' : ''); ?>><?= $a; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?= form_error('auditorias_area'); ?>
                            <?= form_error('auditorias_tipo'); ?>
                            <?= form_error('auditorias_numero'); ?>
                        <?php else: ?>
                            <span class="lead"><?= ($r['numero_auditoria']); ?></span>
                            <input type="hidden" name="auditorias_area" value="<?= $r['auditorias_area']; ?>">
                            <input type="hidden" name="auditorias_tipo" value="<?= $r['auditorias_tipo']; ?>">
                            <input type="hidden" name="auditorias_numero" value="<?= $r['auditorias_numero']; ?>">
                            <input type="hidden" name="auditorias_anio" value="<?= $r['auditorias_anio']; ?>">
                        <?php endif; ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <div class="col-sm-offset-2 col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="auditorias_segundo_periodo" name="auditorias_segundo_periodo" value="1" <?= isset($r) && $r['auditorias_segundo_periodo'] == 1 ? 'checked="checked"' : ''; ?>> 2° Período
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="asignar_consecutivo" name="asignar_consecutivo" value="1" <?= $is_asignar_consecutivo ? 'checked="checked"' : ''; ?>> Asignar el numero sugerido para la Auditoría.
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="auditorias_is_programada" name="auditorias_is_programada" value="1" <?= isset($r) && $r['auditorias_is_programada'] == 1 ? 'checked="checked"' : ''; ?>>
                                Pertenece al PAA
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="fipa" class="col-sm-2 form-control-label">Fecha de inicio</label>
                    <div class="col-sm-10">
                        <div class="input-group col-xs-12 col-md-6 p-l-0">
                            <span class="input-group-addon">Programada</span>
                            <button class="btn btn-secondary btn-block component-daterangepicker" id="input_auditorias_fechas_inicio_programado" type="button" datepicker="auditorias_fechas_inicio_programado"><?= isset($r) && !empty($r['auditorias_fechas_inicio_programado']) && $r['auditorias_fechas_inicio_programado'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['auditorias_fechas_inicio_programado']) : '<i class="fa fa-calendar"></i>'; ?></button>
                            <input type="hidden" id="auditorias_fechas_inicio_programado" name="auditorias_fechas_inicio_programado" value="<?= isset($r) && $r['auditorias_fechas_inicio_programado'] !== '0000-00-00' ? $r['auditorias_fechas_inicio_programado'] : ''; ?>">
                            <?= form_error('auditorias_fechas_inicio_programado'); ?>
                        </div>
                        <div class="input-group col-xs-12 col-md-6 p-l-0">
                            <span class="input-group-addon">Real</span>
                            <button class="btn btn-secondary btn-block component-daterangepicker" id="input_auditorias_fechas_inicio_real" type="button" datepicker="auditorias_fechas_inicio_real"><?= isset($r) && !empty($r['auditorias_fechas_inicio_real']) && $r['auditorias_fechas_inicio_real'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['auditorias_fechas_inicio_real']) : '<i class="fa fa-calendar"></i>'; ?></button>
                            <input type="hidden" id="auditorias_fechas_inicio_real" name="auditorias_fechas_inicio_real" value="<?= isset($r) && $r['auditorias_fechas_inicio_real'] !== '0000-00-00' ? $r['auditorias_fechas_inicio_real'] : ''; ?>">
                            <?= form_error('auditorias_fechas_inicio_real'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="ffpa" class="col-sm-2 form-control-label">Fecha de terminación</label>
                    <div class="col-sm-10">
                        <div class="input-group col-xs-12 col-md-6 p-l-0">
                            <span class="input-group-addon">Programada</span>
                            <button class="btn btn-secondary btn-block component-daterangepicker" id="input_auditorias_fechas_fin_programado" type="button" datepicker="auditorias_fechas_fin_programado"><?= isset($r) && !empty($r['auditorias_fechas_fin_programado']) && $r['auditorias_fechas_fin_programado'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['auditorias_fechas_fin_programado']) : '<i class="fa fa-calendar"></i>'; ?></button>
                            <input type="hidden" id="auditorias_fechas_fin_programado" name="auditorias_fechas_fin_programado" value="<?= isset($r) && $r['auditorias_fechas_fin_programado'] !== '0000-00-00' ? $r['auditorias_fechas_fin_programado'] : ''; ?>">
                            <?= form_error('auditorias_fechas_fin_programado'); ?>
                        </div>
                        <div class="input-group col-xs-12 col-md-6 p-l-0">
                            <span class="input-group-addon">Real</span>
                            <button class="btn btn-secondary btn-block component-daterangepicker" id="input_auditorias_fechas_fin_real" type="button" datepicker="auditorias_fechas_fin_real"><?= isset($r) && !empty($r['auditorias_fechas_fin_real']) && $r['auditorias_fechas_fin_real'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['auditorias_fechas_fin_real']) : '<i class="fa fa-calendar"></i>'; ?></button>
                            <input type="hidden" id="auditorias_fechas_fin_real" name="auditorias_fechas_fin_real" value="<?= isset($r) && $r['auditorias_fechas_fin_real'] !== '0000-00-00' ? $r['auditorias_fechas_fin_real'] : ''; ?>">
                            <?= form_error('auditorias_fechas_fin_real'); ?>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditorias_periodos_id" class="col-xs-12 col-sm-2 col-md-2 control-label">Período</label>
                    <div class="col-sm-10 col-md-6">
                        <select id="auditorias_periodos_id" name="auditorias_periodos_id" class="form-control periodos_dependiente">
                            <option value="0" selected="selected">SELECCIONE UNO</option>
                            <?php foreach ($periodos as $p): ?>
                                <option value="<?= $p['periodos_id'] ?>" <?= (isset($r) && isset($r['auditorias_periodos_id']) && $r['auditorias_periodos_id'] == $p['periodos_id']) || ($accion === 'nuevo' && $p['periodos_id'] == $periodo_actual['periodos_id']) ? 'selected="selected"' : ''; ?>>Período <?= $p['periodos_id'] . " (" . mysqlDate2Date($p['periodos_fecha_inicio']) . " al " . mysqlDate2Date($p['periodos_fecha_fin']) . ")"; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('auditorias_periodos_id'); ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditorias_direcciones_id" class="col-xs-12 col-sm-2 col-md-2 form-control-label">Dirección</label>
                    <div class="col-sm-10 col-md-6">
                        <select id="auditorias_direcciones_id" name="auditorias_direcciones_id" class="form-control direcciones_dependiente" <?= $accion === 'nuevo' && count($direcciones) == 0 ? 'disabled' : NULL; ?>>
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($direcciones as $d): ?>
                                <option value="<?= $d['direcciones_id']; ?>"<?= isset($r) && $r['auditorias_direcciones_id'] == $d['direcciones_id'] ? ' selected="selected"' : ''; ?>><?= $d['direcciones_nombre_cc']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('auditorias_direcciones_id'); ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditorias_subdirecciones_id" class="col-xs-12 col-sm-2 col-md-2 form-control-label">Subdirección</label>
                    <div class="col-sm-10 col-md-6">
                        <select id="auditorias_subdirecciones_id" name="auditorias_subdirecciones_id" class="form-control subdirecciones_dependiente" <?= $accion === 'nuevo' && count($subdirecciones) == 0 ? 'disabled' : NULL; ?>>
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($subdirecciones as $s): ?>
                                <option value="<?= $s['subdirecciones_id']; ?>"<?= isset($r) && $r['auditorias_subdirecciones_id'] == $s['subdirecciones_id'] ? ' selected="selected"' : ''; ?>><?= $s['subdirecciones_nombre_cc']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('auditorias_subdirecciones_id'); ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditorias_departamentos_id" class="col-xs-12 col-sm-2 col-md-2 form-control-label">Departamento</label>
                    <div class="col-sm-10 col-md-6">
                        <select id="auditorias_departamentos_id" name="auditorias_departamentos_id" class="form-control departamentos_dependiente" <?= $accion === 'nuevo' && count($departamentos) == 0 ? 'disabled' : NULL; ?>>
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($departamentos as $d): ?>
                                <option value="<?= $d['departamentos_id']; ?>"<?= isset($r) && $r['auditorias_departamentos_id'] == $d['departamentos_id'] ? ' selected="selected"' : ''; ?>><?= $d['departamentos_nombre_cc']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('auditorias_departamentos_id'); ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditorias_rubro" class="col-sm-2 form-control-label">Rubro</label>
                    <div class="col-sm-10">
                        <input type="text" id="auditorias_rubro" name="auditorias_rubro" class="form-control" value="<?= isset($r) ? $r['auditorias_rubro'] : ''; ?>">
                        <?= form_error('auditorias_rubro'); ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditorias_objetivo" class="col-sm-2 form-control-label">Objetivo</label>
                    <div class="col-sm-10">
                        <textarea id="auditorias_objetivo" name="auditorias_objetivo" class="form-control" rows="5"><?= isset($r) ? $r['auditorias_objetivo'] : ''; ?></textarea>
                        <?= form_error('auditorias_objetivo'); ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditorias_auditor_lider" class="col-sm-2 form-control-label">Auditor líder</label>
                    <div class="col-sm-10">
                        <select id="auditorias_auditor_lider" name="auditorias_auditor_lider" class="form-control">
                            <option value="0">SELECCIONAR</option>
                            <?php $depto = NULL; ?>
                            <?php foreach ($auditores as $a): ?>
                                <?php if ($depto != $a['departamentos_nombre']): $depto = $a['departamentos_nombre']; ?>
                                    <optgroup label="<?= $a['departamentos_nombre']; ?>">
                                    <?php endif; ?>
                                    <option value="<?= $a['empleados_id']; ?>"<?= isset($r) && $r['auditorias_auditor_lider'] == $a['empleados_id'] ? ' selected="selected"' : ''; ?>><?= $a['empleados_nombre'] . " " . $a['empleados_apellido_paterno'] . " " . $a['empleados_apellido_materno'] . " (" . number_format($a['empleados_numero_empleado']) . ")"; ?></option>
                                    <?php if ($depto != $a['departamentos_nombre']): ?>
                                    </optgroup>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('auditorias_auditor_lider'); ?>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <div class="pull-xs-right col-sm-offset-2 col-sm-10">
                        <a href="<?= base_url() . $this->uri->segment(1); ?>" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><?= $etiquetaBoton; ?></button>
                        <input type="hidden" name="accion" value="<?= $accion; ?>">
                        <input type="hidden" name="<?= $this->module['id_field']; ?>" value="<?= isset($r, $r[$this->module['id_field']]) ? $r[$this->module['id_field']] : ''; ?>">
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<style>
    .input-group-addon:first-child {
        width: 120px;
        text-align: right;
    }
</style>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.js" type="text/javascript"></script>
<!-- Personalizado -->
<script src="<?= base_url(); ?>resources/scripts/auditorias_nuevo_view.js" type="text/javascript"></script>
<link href="<?= base_url(); ?>resources/styles/auditorias_nuevo_view.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/scripts/select_dependientes.js" type="text/javascript"></script>