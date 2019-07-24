<div class="tab-pane panel-detalles <?= $etapa_auditoria == AUDITORIA_ETAPA_AP ? 'active' : ''; ?>" id="observacion_detalles_<?= isset($o, $o['observaciones_id']) ? $o['observaciones_id'] : NULL; ?>" role="tabpanel">
    <?php if ($etapa_auditoria == AUDITORIA_ETAPA_AP): ?>
        <form>
            <input type="text" name="observaciones_titulo[]" class="form-control m-b-2" placeholder="Título de la observación" value="<?= isset($o['observaciones_titulo']) ? $o['observaciones_titulo'] : NULL; ?>">
            <textarea name="observaciones_descripcion[]" class="editor_html"><?= isset($o['observaciones_descripcion']) ? $o['observaciones_descripcion'] : NULL; ?></textarea>
            <input type="hidden" name="observaciones_numero[]" value="<?= isset($o['observaciones_numero']) ? $o['observaciones_numero'] : 0; ?>">
            <div class="row text-xs-center m-t-1">
                <a href="<?= base_url() . $this->module['controller'] . "/guardar"; ?>" class="btn btn-primary guardar-observacion">Guardar observación</a>
            </div>
        </form>
    <?php else: ?>
        <h5 class="m-b-1"><?= isset($o, $o['observaciones_titulo']) ? $o['observaciones_titulo'] : ''; ?></h5>
        <div class="font-barlow font-size-1rem" style="height: 100px; overflow-y: scroll;"><?= isset($o, $o['observaciones_descripcion']) ? $o['observaciones_descripcion'] : ''; ?></div>
    <?php endif; ?>
    <input type="hidden" class="observaciones_id" name="observaciones_id[]" value="<?= isset($o, $o['observaciones_id']) ? $o['observaciones_id'] : 0; ?>">
    <div id="recomendaciones" class="m-t-2">
        <?php if (isset($o['recomendaciones'])): ?>
            <?php foreach ($o['recomendaciones'] as $rr): ?>
                <?php $data_recomendaciones = array('rr' => $rr); ?>
                <?php $this->load->view('templates/recomendacion_view', $data_recomendaciones); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if ($etapa_auditoria == AUDITORIA_ETAPA_AP): ?>
        <div class="text-xs-center <?= isset($o, $o['observaciones_id']) && $o['observaciones_id'] > 0 ? '' : 'hidden-xs-up'; ?>">
            <a href="#" class="btn btn-default btn-sm add-recomendacion" data-observaciones-id="<?= isset($o, $o['observaciones_id']) && $o['observaciones_id'] > 0 ? $o['observaciones_id'] : ''; ?>">
                Agregar recomendación
            </a>
        </div>
    <?php endif; ?>
</div>