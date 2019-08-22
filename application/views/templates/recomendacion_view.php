<?php $css = array(NULL, 'danger', 'success', NULL, 'warning'); ?>
<?php $status_id = isset($rr, $rr['recomendaciones_staus_id']) ? intval($rr['recomendaciones_status_id']) : 0; ?>
<?php $empleado_id = isset($rr, $rr['recomendaciones_empleados_id']) ? intval($rr['recomendaciones_empleados_id']) : 0; ?>
<?php $clasificacion_id = isset($rr, $rr['recomendaciones_clasificaciones_id']) ? intval($rr['recomendaciones_clasificaciones_id']) : 0; ?>
<?php $status_id = isset($rr, $rr['recomendaciones_status_id']) ? intval($rr['recomendaciones_status_id']) : 0; ?>
<div id="<?= isset($rr, $rr['recomendaciones_id']) ? 'recomendacion_' . $rr['recomendaciones_id'] : 'nueva_recomendacion_' ?>" class="card XXXcard-inverse XXXcard-<?= $css[$status_id]; ?>">
    <form>
        <div class="card-header">
            <?php if ($etapa_auditoria == AUDITORIA_ETAPA_AP): ?>
                <a href="#" class="btn btn-primary btn-sm guardar-recomendacion pull-right" title="Guardar recomendación"><i class="fa fa-save"></i></a>
                <a href="#" class="btn btn-danger btn-sm eliminar-recomendacion pull-right m-r-1" title="Eliminar recomendación"><i class="fa fa-trash"></i></a>
            <?php endif; ?>
            <input type="hidden" class="recomendaciones_id" name="recomendaciones_id[]" value="<?= isset($rr, $rr['recomendaciones_id']) ? $rr['recomendaciones_id'] : 0 ?>">
            <input type="hidden" class="recomendaciones_observaciones_id" name="recomendaciones_observaciones_id[]" value="<?= isset($rr, $rr['recomendaciones_observaciones_id']) ? $rr['recomendaciones_observaciones_id'] : 0 ?>">
            <input type="hidden" class="recomendaciones_etapa_id" name="recomendaciones_etapa_id[]" value="<?= $etapa_auditoria; ?>">
            <h4>Recomendación <?= isset($rr, $rr['recomendaciones_numero']) ? $rr['recomendaciones_numero'] : 'nueva'; ?></h4>
        </div>
        <div class="card-block">
            <?php if ($etapa_auditoria == AUDITORIA_ETAPA_AP): ?>
                <textarea name="recomendaciones_descripcion[]" class="form-control autosize font-barlow text-justify"><?= isset($rr, $rr['recomendaciones_descripcion']) ? trim($rr['recomendaciones_descripcion']) : NULL; ?></textarea>
            <?php else: ?>
                <div class="text-2-html" style="max-height: 250px; overflow-y: scroll;"><?= isset($rr, $rr['recomendaciones_descripcion']) ? $rr['recomendaciones_descripcion'] : ''; ?></div>
            <?php endif; ?>
        </div>
        <?php if (isset($avance) && !empty($rr)): ?>
            <div class="card-block">
                <h4 class="card-title">
                    Avance
                    <?php if ($etapa_auditoria < AUDITORIA_ETAPA_FIN): ?>
                        <a href="#" class="btn btn-primary btn-sm guardar-recomendacion-avance pull-right" title="Guardar avance de la recomendación"><i class="fa fa-save"></i></a>
                    <?php endif; ?>
                </h4>
                <?php if ($etapa_auditoria < AUDITORIA_ETAPA_FIN): ?>
                    <textarea id="recomendaciones_avaces_descripcion_<?= isset($rr, $rr['recomendaciones_observaciones_id']) ? $rr['recomendaciones_observaciones_id'] : ''; ?>_<?= isset($rr, $rr['recomendaciones_id']) ? $rr['recomendaciones_id'] : ''; ?>_<?= $etapa_auditoria; ?>" name="recomendaciones_avaces_descripcion[]" class="editor_html"><?= isset($avance['recomendaciones_avances_descripcion']) ? $avance['recomendaciones_avances_descripcion'] : ''; ?></textarea>
                    <input type="hidden" class="recomendaciones_avances_numero_revision" name="recomendaciones_avances_numero_revision[]" value="<?= isset($avance['recomendaciones_avances_numero_revision']) ? $avance['recomendaciones_avances_numero_revision'] : 3; ?>">
                <?php else: ?>
                    <div class="font-barlow font-size-1rem" style="max-height: 250px; overflow-y: scroll;"><?= $avance['recomendaciones_avances_descripcion']; ?></div>
                <?php endif; ?>
                <?php if (!empty($avance)): ?>
                    <?php $empleado_id = intval($avance['recomendaciones_avances_empleados_id']); ?>
                    <?php $clasificacion_id = intval($avance['recomendaciones_avances_recomendaciones_clasificaciones_id']); ?>
                    <?php $status_id = intval($avance['recomendaciones_avances_recomendaciones_status_id']); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="card-footer">
            <div class="row align-middle">
                <div class="col-xs-12 col-sm-12 col-md-4 text-xs-center">
                    <b>Responsable:</b><br>
                    <?php $empleados_involucrados = $this->Auditoria_model->get_involucrados(); ?>
                    <select id="recomendaciones_empleados_id" name="recomendaciones_empleados_id[]" class="form-control" <?= ($etapa_auditoria >= AUDITORIA_ETAPA_FIN) ? 'disabled="disabled"' : ''; ?>>
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($empleados_involucrados as $e): ?>
                            <option value="<?= $e['empleados_id']; ?>" <?= $e['empleados_id'] == $empleado_id ? 'selected="selected"' : ''; ?>><?= $e['empleados_nombre_titulado_siglas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 text-xs-center">
                    <b>Clasificación:</b><br>
                    <?php $recomendaciones_clasificaciones = $this->Recomendaciones_clasificaciones_model->get_todos(); ?>
                    <select id="recomendaciones_clasificaciones_id" name="recomendaciones_clasificaciones_id[]" class="form-control" <?= ($etapa_auditoria >= AUDITORIA_ETAPA_FIN) ? 'disabled="disabled"' : ''; ?>>
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($recomendaciones_clasificaciones as $rc): ?>
                            <option value="<?= $rc['recomendaciones_clasificaciones_id']; ?>" <?= $rc['recomendaciones_clasificaciones_id'] == $clasificacion_id ? 'selected="selected"' : ''; ?>><?= $rc['recomendaciones_clasificaciones_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 text-xs-center">
                    <b>Status:</b><br>
                    <?php $recomendaciones_status = $this->Recomendaciones_status_model->get_todos(); ?>
                    <select id="recomendaciones_status_id" name="recomendaciones_status_id[]" class="form-control" <?= ($etapa_auditoria >= AUDITORIA_ETAPA_FIN) ? 'disabled="disabled"' : ''; ?>>
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($recomendaciones_status as $rs): ?>
                            <option value="<?= $rs['recomendaciones_status_id']; ?>" <?= $rs['recomendaciones_status_id'] == $status_id ? 'selected="selected"' : ''; ?>><?= $rs['recomendaciones_status_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>