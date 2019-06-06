<div class="tab-pane active panel-detalles" id="observacion_detalles_<?= isset($o, $o['observaciones_id']) ? $o['observaciones_id'] : NULL; ?>" role="tabpanel">
    <form>
        <input type="text" name="observaciones_titulo[]" class="form-control m-b-2" placeholder="Título de la observación" value="<?= isset($o['observaciones_titulo']) ? $o['observaciones_titulo'] : NULL; ?>">
        <textarea name="observaciones_descripcion[]" class="editor_html"><?= isset($o['observaciones_descripcion']) ? $o['observaciones_descripcion'] : NULL; ?></textarea>
        <input type="hidden" class="observaciones_id" name="observaciones_id[]" value="<?= isset($o['observaciones_id']) ? $o['observaciones_id'] : 0; ?>">
        <input type="hidden" name="observaciones_numero[]" value="<?= isset($o['observaciones_numero']) ? $o['observaciones_numero'] : 0; ?>">
        <div class="row text-xs-center m-t-1">
            <a href="<?= base_url() . $this->module['controller'] . "/guardar"; ?>" class="btn btn-primary guardar-observacion">Guardar observación</a>
        </div>
    </form>
    <div id="recomendaciones" class="m-t-2">
        <?php if (isset($o['recomendaciones'])): ?>
            <?php foreach ($o['recomendaciones'] as $rr): ?>
                <?php $data_recomendaciones = array('rr' => $rr); ?>
                <?php $this->load->view('templates/recomendacion_view', $data_recomendaciones); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="text-xs-center">
        <a href="#" class="btn btn-default btn-sm add-recomendacion">
            Agregar recomendación
        </a>
    </div>
</div>