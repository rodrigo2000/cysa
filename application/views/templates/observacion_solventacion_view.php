<div class="tab-pane panel-solventacion <?= $etapa_auditoria > AUDITORIA_ETAPA_AP ? 'active' : ''; ?>" id="observacion_solventacion_<?= isset($o, $o['observaciones_id']) ? $o['observaciones_id'] : NULL; ?>" role="tabpanel">
    <h5 class="m-b-1"><?= $o['observaciones_titulo']; ?></h5>
    <input type="hidden" class="observaciones_id" name="observaciones_id[]" value="<?= isset($o['observaciones_id']) ? $o['observaciones_id'] : 0; ?>">
    <div id="recomendaciones" class="m-t-2">
        <?php if (isset($o['recomendaciones'])): ?>
            <?php foreach ($o['recomendaciones'] as $rr): $numero_revision = 1; ?>
                <?php $avances = $this->Recomendaciones_model->get_avances_de_recomendacion($rr['recomendaciones_id'], $numero_revision); ?>
                <?php $avance = (isset($avances[0]) && !empty($avances[0])) ? $avances[0] : array(); ?>
                <?php $data_recomendaciones = array('rr' => $rr, 'avance' => $avance); ?>
                <?php $this->load->view('templates/recomendacion_view', $data_recomendaciones); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>