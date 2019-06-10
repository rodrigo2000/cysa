<?php $css = array(NULL, 'danger', 'success', NULL, 'warning'); ?>
<?php $status_id = isset($rr, $rr['recomendaciones_staus_id']) ? intval($rr['recomendaciones_status_id']) : 0; ?>
<div id="<?= isset($rr, $rr['recomendaciones_id']) ? 'recomendacion_' . $rr['recomendaciones_id'] : 'nueva_recomendacion_' ?>" class="card XXXcard-inverse XXXcard-<?= $css[$status_id]; ?>">
    <form>
        <div class="card-header text-xs-right">
            <a href="#" class="btn btn-primary btn-sm guardar-recomendacion">Guardar</a>
            <a href="#" class="btn btn-danger btn-sm eliminar-recomendacion">Eliminar</a>
            <h4 class="pull-xs-left">Recomendación <?= isset($rr, $rr['recomendaciones_numero']) ? $rr['recomendaciones_numero'] : 'nueva'; ?></h4>
            <input type="hidden" class="recomendaciones_id" name="recomendaciones_id[]" value="<?= isset($rr, $rr['recomendaciones_id']) ? $rr['recomendaciones_id'] : 0 ?>">
            <input type="hidden" class="recomendaciones_observaciones_id" name="recomendaciones_observaciones_id[]" value="<?= isset($rr, $rr['recomendaciones_observaciones_id']) ? $rr['recomendaciones_observaciones_id'] : 0 ?>">
        </div>
        <div class="card-block">
            <textarea name="recomendaciones_descripcion[]" class="form-control autosize"><?= isset($rr, $rr['recomendaciones_descripcion']) ? trim($rr['recomendaciones_descripcion']) : NULL; ?></textarea>
        </div>
        <div class="card-footer">
            <div class="row align-middle">
                <div class="col-xs-12 col-sm-4 text-xs-center">
                    <b>Clasificación:</b><br>
                    <?php $recomendaciones_clasificaciones = $this->Recomendaciones_clasificaciones_model->get_todos(); ?>
                    <select id="recomendaciones_clasificaciones_id" name="recomendaciones_clasificaciones_id[]" class="form-control">
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($recomendaciones_clasificaciones as $rc): ?>
                            <option value="<?= $rc['recomendaciones_clasificaciones_id']; ?>" <?= isset($rr, $rr['recomendaciones_clasificaciones_id']) && $rc['recomendaciones_clasificaciones_id'] == $rr['recomendaciones_clasificaciones_id'] ? 'selected="selected"' : ''; ?>><?= $rc['recomendaciones_clasificaciones_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4 text-xs-center">
                    <b>Status:</b><br>
                    <?php $recomendaciones_status = $this->Recomendaciones_status_model->get_todos(); ?>
                    <select id="recomendaciones_status_id" name="recomendaciones_status_id[]" class="form-control">
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($recomendaciones_status as $rs): ?>
                            <option value="<?= $rs['recomendaciones_status_id']; ?>" <?= isset($rr, $rr['recomendaciones_status_id']) && $rs['recomendaciones_status_id'] == $rr['recomendaciones_status_id'] ? 'selected="selected"' : ''; ?>><?= $rs['recomendaciones_status_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-4 text-xs-center">
                    <b>Responsable:</b><br>
                    <?php $empleados_involucrados = $this->Auditoria_model->get_involucrados(); ?>
                    <select id="recomendaciones_empleados_id" name="recomendaciones_empleados_id[]" class="form-control">
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($empleados_involucrados as $e): ?>
                            <option value="<?= $e['empleados_id']; ?>" <?= isset($rr, $rr['recomendaciones_empleados_id']) && $e['empleados_id'] == $rr['recomendaciones_empleados_id'] ? 'selected="selected"' : ''; ?>><?= $e['empleados_nombre_titulado_siglas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>