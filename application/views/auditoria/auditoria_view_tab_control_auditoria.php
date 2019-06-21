<div class="row">
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Representante Designado</h3>
            <div class="card-block">
                <?php if ($auditoria['auditorias_status_id'] < AUDITORIAS_STATUS_FINALIZADA): ?>
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
                                    <?php if ($auditoria['auditorias_status_id'] < AUDITORIAS_STATUS_FINALIZADA): ?>
                                        <a href="#" id="btn-editar-enlace-designado"><i class="fa fa-edit"></i></a>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php if ($auditoria['auditorias_status_id'] < AUDITORIAS_STATUS_FINALIZADA): ?>
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
                                    $mostrar_bajas = $auditoria['auditorias_status_id'] > AUDITORIAS_STATUS_EN_PROCESO ? TRUE : FALSE;
                                    ?>
                                    <?php $empleados = $this->SAC_model->get_empleados_de_UA($periodos_id, $direcciones_id, $subdirecciones_id, $departamentos_id); ?>
                                    <select name="auditorias_enlace_designado" id="auditorias_enlace_designado" class="form-control empleados_dependiente" size="5">
                                        <?php foreach ($empleados as $e): ?>
                                            <option value="<?= $e['empleados_id']; ?>"><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button id="btn-asignar-enlace-designado" class="btn btn-primary w-200">Actualizar</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-sm-3 hidden-sm-down"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Involucrados en la auditoría</h3>
            <div class="card-block">
                <div class="row">
                    <div class="espaciado col-xs-12">
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
                        <?php
                        $periodos_id = intval($auditoria['auditorias_periodos_id']);
                        $direcciones_id = intval($auditoria['auditorias_direcciones_id']);
                        $subdirecciones_id = intval($auditoria['auditorias_subdirecciones_id']);
                        $departamentos_id = intval($auditoria['auditorias_departamentos_id']);
                        $mostrar_bajas = $auditoria['auditorias_status_id'] > AUDITORIAS_STATUS_EN_PROCESO ? TRUE : FALSE;
                        ?>
                        <?php $empleados = $this->SAC_model->get_empleados_de_UA($periodos_id, $direcciones_id, $subdirecciones_id, $departamentos_id); ?>
                        <?php $aux = array_column($auditoria['empleados_involucrados'], 'empleados_id'); ?>
                        <select name="empleados_involucrados" id="empleados_involucrados" class="form-control empleados_dependiente" size="5" grupo="auditorias" multiple="multiple" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                            <?php foreach ($empleados as $e): ?>
                                <?php if (!in_array($e['empleados_id'], $aux)): ?>
                                    <option value="<?= $e['empleados_id']; ?>"><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php foreach ($auditoria['empleados_involucrados'] as $e): ?>
                                <?php if (isset($e['empleados_id'])): ?>
                                    <option value="<?= $e['empleados_id']; ?>" selected="selected"><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!$is_finalizada): ?>
                        <div class="col-xs-12 text-xs-center m-t-1">
                            <button id="btn-actualizar-empleados-involucrados" class="btn btn-primary w-200">Actualizar involucrados</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php $empleados_contraloria = $this->SAC_model->get_auditores($mostrar_bajas); ?>
    <?php $equipo = array_column($auditoria['auditoria_equipo'], 'empleados_id'); ?>
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Equipo de auditoría</h3>
            <div class="card-block">
                <select name="equipo_auditoria" id="equipo_auditoria" class="form-control" multiple="multiple" rows="10" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                    <?php foreach ($empleados_contraloria as $e): ?>
                        <option value="<?= $e['empleados_id']; ?>" <?= in_array($e['empleados_id'], $equipo) ? 'selected="selected"' : ''; ?>><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$is_finalizada): ?>
                    <div class="text-xs-center m-t-1">
                        <button id="btn-actualizar-equipo-auditoria" class="btn btn-primary w-200">Actualizar equipo</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php $adicionales = array_column($auditoria['auditoria_equipo'], 'empleados_id'); ?>
    <div class="col-xs-12">
        <div class="card">
            <h3 class="card-header bg-info">Permisos adicionales</h3>
            <div class="card-block">
                <select name="permisos_adicionales" id="permisos_adicionales" class="form-control" multiple="multiple" rows="10" <?= $is_finalizada ? 'disabled="disabled"' : NULL; ?>>
                    <?php foreach ($empleados_contraloria as $e): ?>
                        <option value="<?= $e['empleados_id']; ?>" <?= in_array($e['empleados_id'], $adicionales) ? 'selected="selected"' : ''; ?>><?= $e['nombre_completo'] . (!empty($e['empleados_numero_empleado']) ? " (" . $e['empleados_numero_empleado'] . ")" : ''); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$is_finalizada): ?>
                    <div class="text-xs-center m-t-1">
                        <button id="btn-actualizar-permisos-adicionales" class="btn btn-primary w-200">Actualizar permisos</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>