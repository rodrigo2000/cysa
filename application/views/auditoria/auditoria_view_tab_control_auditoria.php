<div class="row">
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Representante Designado</h3>
            <div class="card-block">
                <?php if (!$is_finalizada): ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <p>
                                <input type="checkbox" class="labelautyfy" name="auditorias_representante_designado" id="auditorias_representante_designado" data-labelauty="Omitir representante designado" <?= empty($auditoria['enlace_designado']) ? 'checked="checked"' : ''; ?>>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-sm-3 hidden-sm-down"></div>
                    <div class="col-xs-12 col-sm-6 text-xs-center">
                        <div id="show-hide-label-enlace-designado" <?= empty($auditoria['enlace_designado']) ? 'style="display:none"' : ''; ?>>
                            <div class="col-xs-12">
                                <p align="center" class="lead">
                                    <span id="label-enlace-designado"><?= !empty($auditoria['enlace_designado']) ? $auditoria['enlace_designado']['empleados_nombre_titulado_siglas'] : 'SELECCIONAR'; ?></span>
                                    <?php if (in_array($auditoria['auditorias_status_id'], array(AUDITORIAS_STATUS_EN_PROCESO, AUDITORIAS_STATUS_SIN_INICIAR))): ?>
                                        <a href="#" id="btn-editar-enlace-designado"><i class="fa fa-edit"></i></a>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if (!$is_finalizada): ?>
                                <div id="show-hide-asignar-enlace-designado" class="espaciado" style="display:none">
                                    <select id="cc_periodos_id" name="cc_periodos_id" class="form-control periodos_dependiente hidden-xs-up">
                                        <option value="0" selected="selected">SELECCIONE PERÍODO</option>
                                        <?php foreach ($periodos as $p): ?>
                                            <option value="<?= $p['periodos_id'] ?>" <?= isset($auditoria) && isset($auditoria['cc_periodos_id']) && $auditoria['cc_periodos_id'] == $p['periodos_id'] ? 'selected="selected"' : ''; ?>>Período <?= $p['periodos_id'] . " (" . mysqlDate2Date($p['periodos_fecha_inicio']) . " al " . mysqlDate2Date($p['periodos_fecha_fin']) . ")"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select id="cc_direcciones_id" name="cc_direcciones_id" class="form-control direcciones_dependiente hidden-xs-up">
                                        <option value="0" selected="selected">SELECCIONE DIRECCIÓN</option>
                                        <?php foreach ($direcciones as $d): ?>
                                            <option value="<?= $d['direcciones_id'] ?>" <?= isset($auditoria) && isset($auditoria['cc_direcciones_id']) && $auditoria['cc_direcciones_id'] == $d['direcciones_id'] ? 'selected="selected"' : ''; ?>><?= sprintf("%02d", $d['cc_etiqueta_direccion']) . " - " . $d['direcciones_nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="auditorias_subdirecciones_id" id="auditorias_subdirecciones_id" class="form-control subdirecciones_dependiente">
                                        <option>SELECCIONAR SUBDIRECCIÓN</option>
                                        <?php foreach ($subdirecciones as $s): ?>
                                            <option value="<?= $s['subdirecciones_id']; ?>" <?= $s['subdirecciones_id'] == $auditoria['auditorias_subdirecciones_id'] ? 'selected="selected"' : ''; ?>><?= sprintf("%02d", $s['cc_etiqueta_direccion']) . "." . sprintf("%02d", $s['cc_etiqueta_subdireccion']) . " - " . $s['subdirecciones_nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="auditorias_departamentos_id" id="auditorias_departamentos_id" class="form-control departamentos_dependiente">
                                        <option>SELECCIONE DEPARTAMENTO</option>
                                        <?php foreach ($departamentos as $d): ?>
                                            <option value="<?= $d['departamentos_id']; ?>" <?= $d['departamentos_id'] == $auditoria['auditorias_departamentos_id'] ? 'selected="selected"' : ''; ?>><?= sprintf("%02d", $d['cc_etiqueta_direccion']) . "." . sprintf("%02d", $d['cc_etiqueta_subdireccion']) . "." . sprintf("%02d", $d['cc_etiqueta_departamento']) . " - " . $d['departamentos_nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php
                                    $periodos_id = intval($auditoria['auditorias_periodos_id']);
                                    $direcciones_id = intval($auditoria['auditorias_direcciones_id']);
                                    $subdirecciones_id = intval($auditoria['auditorias_subdirecciones_id']);
                                    $departamentos_id = intval($auditoria['auditorias_departamentos_id']);
                                    $empleados = $this->SAC_model->get_empleados_de_UA($periodos_id, $direcciones_id, $subdirecciones_id, $departamentos_id);
                                    ?>
                                    <select name="auditorias_enlace_designado" id="auditorias_enlace_designado" class="form-control empleados_dependiente" size="5">
                                        <?php foreach ($empleados as $e): ?>
                                            <option value="<?= $e['empleados_id']; ?>"><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-default w-200 m-b-1" onclick="$('#show-hide-asignar-enlace-designado').slideUp('slow');">Cancelar</button>
                                    <button id="btn-asignar-enlace-designado" class="btn btn-primary w-200 m-b-1">Actualizar</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-sm-3 hidden-sm-down"></div>
                </div>
                <?php if (!$is_finalizada && !empty($auditoria['auditorias_folio_oficio_representante_designado']) && !empty($auditoria['auditorias_folio_oficio_representante_designado'])): ?>
                    <div class="row m-t-1">
                        <div class="col-sm-6 text-xs-center m-b-1">
                            <?php if (!$is_finalizada): ?>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon3">Folio de oficio</span>
                                    <input type="text" name="auditorias_folio_oficio_representante_designado" id="auditorias_folio_oficio_representante_designado" class="form-control text-xs-center" value="<?= isset($auditoria) ? $auditoria['auditorias_folio_oficio_representante_designado'] : ''; ?>">
                                    <span class="input-group-btn">
                                        <a class="btn btn-default actualizar_campo" type="button" data-campo="auditorias_folio_oficio_representante_designado">Actualizar</a>
                                    </span>
                                </div>
                            <?php else: ?>
                                <p class="lead">Folio de oficio: <?= isset($auditoria) && !empty($auditoria['auditorias_folio_oficio_representante_designado']) ? $auditoria['auditorias_folio_oficio_representante_designado'] : 'NO ESPECIFICADO'; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6 text-xs-center">
                            <?php if (!$is_finalizada): ?>
                                <div class="input-group">
                                    <span class="input-group-addon">Fecha de sello</span>
                                    <button class="btn btn-secondary btn-block component-daterangepicker" id="input_auditorias_fechas_sello_oficio_representante_designado" type="button" datepicker="auditorias_fechas_sello_oficio_representante_designado"><?= isset($auditoria) && !empty($auditoria['auditorias_fechas_sello_oficio_representante_designado']) && $auditoria['auditorias_fechas_sello_oficio_representante_designado'] !== '0000-00-00' ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_sello_oficio_representante_designado']) : '<i class="fa fa-calendar"></i>'; ?></button>
                                    <input type="hidden" id="auditorias_fechas_sello_oficio_representante_designado" name="auditorias_fechas_sello_oficio_representante_designado" value="<?= isset($auditoria) && $auditoria['auditorias_fechas_sello_oficio_representante_designado'] !== '0000-00-00' ? $auditoria['auditorias_fechas_sello_oficio_representante_designado'] : ''; ?>">
                                    <span class="input-group-btn">
                                        <a class="btn btn-default actualizar_campo" id="mio" type="button" data-campo="auditorias_fechas_sello_oficio_representante_designado">Actualizar</a>
                                    </span>
                                </div>
                            <?php else: ?>
                                <p class="lead">Fecha de sello: <?= isset($auditoria) && !empty($auditoria['auditorias_fechas_sello_oficio_representante_designado']) && $auditoria['auditorias_fechas_sello_oficio_representante_designado'] !== '0000-00-00' ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_sello_oficio_representante_designado']) : 'NO ESPECIFICADO'; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Involucrados en la auditoría</h3>
            <div class="card-block">
                <div class="row">
                    <div class="espaciado col-xs-12">
                        <?php if (!$is_finalizada): ?>
                            <select name="auditorias_periodos_id" id="auditorias_periodos_id" class="form-control periodos_dependiente" grupo="auditorias" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                                <option value="0" selected="selected">SELECCIONE PERÍODO</option>
                                <?php foreach ($periodos as $p): ?>
                                    <option value="<?= $p['periodos_id'] ?>" <?= isset($auditoria) && isset($auditoria['cc_periodos_id']) && $auditoria['cc_periodos_id'] == $p['periodos_id'] ? 'selected="selected"' : ''; ?>>Período <?= $p['periodos_id'] . " (" . mysqlDate2Date($p['periodos_fecha_inicio']) . " al " . mysqlDate2Date($p['periodos_fecha_fin']) . ")"; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php $direcciones = $this->SAC_model->get_cc_direcciones($auditoria['auditorias_periodos_id']); ?>
                            <select name="auditorias_direcciones_id" id="auditorias_direcciones_id" class="form-control direcciones_dependiente" grupo="auditorias" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                                <?php foreach ($direcciones as $d): ?>
                                    <option value="<?= $d['direcciones_id']; ?>" <?= $d['direcciones_id'] == $auditoria['auditorias_direcciones_id'] ? 'selected="selected"' : ''; ?>><?= sprintf("%02d", $d['cc_etiqueta_direccion']) . " - " . $d['direcciones_nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php $subdirecciones = $this->SAC_model->get_cc_subdirecciones($auditoria['auditorias_direcciones_id']); ?>
                            <select name="auditorias_subdirecciones_id" id="auditorias_subdirecciones_id" class="form-control subdirecciones_dependiente" grupo="auditorias" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                                <option>SELECCIONAR SUBDIRECCIÓN</option>
                                <?php foreach ($subdirecciones as $s): ?>
                                    <option value="<?= $s['subdirecciones_id']; ?>" <?= $s['subdirecciones_id'] == $auditoria['auditorias_subdirecciones_id'] ? 'selected="selected"' : ''; ?>><?= sprintf("%02d", $d['cc_etiqueta_direccion']) . "." . sprintf("%02d", $s['cc_etiqueta_subdireccion']) . "." . sprintf("%02d", $s['cc_etiqueta_departamento']) . " - " . $s['subdirecciones_nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php $departamentos = $this->SAC_model->get_cc_departamentos($auditoria['auditorias_direcciones_id'], $auditoria['auditorias_subdirecciones_id']); ?>
                            <select name="auditorias_departamentos_id" id="auditorias_departamentos_id" class="form-control departamentos_dependiente" grupo="auditorias" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                                <option>SELECCIONE DEPARTAMENTO</option>
                                <?php foreach ($departamentos as $d): ?>
                                    <option value="<?= $d['departamentos_id']; ?>" <?= $d['departamentos_id'] == $auditoria['auditorias_departamentos_id'] ? 'selected="selected"' : ''; ?>><?= sprintf("%02d", $d['cc_etiqueta_direccion']) . "." . sprintf("%02d", $d['cc_etiqueta_subdireccion']) . "." . sprintf("%02d", $d['cc_etiqueta_departamento']) . " - " . $d['departamentos_nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php $involucrados = array_column($auditoria['empleados_involucrados'], 'empleados_id'); ?>
                            <select name="empleados_involucrados" id="empleados_involucrados" class="form-control empleados_dependiente dual-list" size="5" grupo="auditorias" multiple="multiple" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                                <?php foreach ($empleados as $e): ?>
                                    <?php if (!in_array($e['empleados_id'], $involucrados)): ?>
                                        <option value="<?= $e['empleados_id']; ?>"><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_id'] . ")" : ''); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php foreach ($auditoria['empleados_involucrados'] as $e): ?>
                                    <?php if (isset($e['empleados_id'])): ?>
                                        <option value="<?= $e['empleados_id']; ?>" selected="selected"><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_id'] . ")" : ''); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <?php foreach ($auditoria['empleados_involucrados'] as $e): ?>
                                <?php if (isset($e['empleados_id'])): ?>
                                    <div><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (!$is_finalizada): ?>
                        <div class="col-xs-12 text-xs-center m-t-1">
                            <button id="btn-actualizar-empleados-involucrados" class="btn btn-primary" style="width: 220px;">Actualizar involucrados</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php $mostrar_bajas = $is_finalizada; ?>
    <?php $empleados_contraloria = $this->SAC_model->get_auditores($mostrar_bajas); ?>
    <?php $equipo = array_column($auditoria['auditoria_equipo'], 'empleados_id'); ?>
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Equipo de auditoría</h3>
            <div class="card-block">
                <?php if (!$is_finalizada): ?>
                    <select name="equipo_auditoria" id="equipo_auditoria" class="form-control" multiple="multiple" rows="10" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                        <?php foreach ($empleados_contraloria as $e): ?>
                            <option value="<?= $e['empleados_id']; ?>" <?= in_array($e['empleados_id'], $equipo) ? 'selected="selected"' : ''; ?>><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <div class="row">
                        <?php if (count($empleados_contraloria) > 0): ?>
                            <?php foreach ($empleados_contraloria as $e): ?>
                                <?php if (in_array($e['empleados_id'], $equipo)) : ?>
                                    <div class="col-xs-4 col-sm-2 text-xs-center" style="min-height: 180px;">
                                        <img class="img-thumbnail img-circle" src="<?= APP_SAC_URL; ?>resources/images/avatar.jpg">
                                        <?= $e['empleados_nombre_titulado'] . (!empty($e['empleados_numero_empleado']) ? "<br>(#" . number_format($e['empleados_numero_empleado'], 0) . ")" : ''); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="lead">NINGUNO</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (!$is_finalizada): ?>
                    <div class="text-xs-center m-t-1">
                        <button id="btn-actualizar-equipo-auditoria" class="btn btn-primary w-200">Actualizar equipo</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php $adicionales = array_column($auditoria['auditoria_permisos_adicionales'], 'empleados_id'); ?>
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Permisos adicionales</h3>
            <div class="card-block">
                <?php if (!$is_finalizada): ?>
                    <select name="permisos_adicionales" id="permisos_adicionales" class="form-control" multiple="multiple" rows="10" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                        <?php foreach ($empleados_contraloria as $e): ?>
                            <option value="<?= $e['empleados_id']; ?>" <?= in_array($e['empleados_id'], $adicionales) ? 'selected="selected"' : ''; ?>><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <?php if (!empty($adicionales)): ?>
                        <?php foreach ($empleados_contraloria as $e): ?>
                            <?php if (in_array($e['empleados_id'], $adicionales)): ?>
                                <div><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="lead">NINGUNO</p>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (!$is_finalizada): ?>
                    <div class="text-xs-center m-t-1">
                        <button id="btn-actualizar-permisos-adicionales" class="btn btn-primary w-200">Actualizar permisos</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- Personalizado -->
<script src="<?= base_url(); ?>resources/scripts/auditorias_view_tab_control_auditoria.js" type="text/javascript"></script>