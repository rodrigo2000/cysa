<div class="tab-pane" id="<?= isset($o, $o['observaciones_id']) ? 'observaciones_' . $o['observaciones_id'] : 'nueva_observaciones_'; ?>" role="tabpanel">
    <ul class="nav nav-pills" role="tablist">
        <li class="nav-item">
            <a class="nav-link tab-detalles <?= $auditoria['auditorias_is_sin_observaciones'] == 1 || $etapa_auditoria == AUDITORIA_ETAPA_AP ? 'active' : ''; ?>" data-toggle="tab" href="#<?= isset($o, $o['observaciones_id']) ? 'observacion_detalles_' . $o['observaciones_id'] : 'nueva_observacion_detalles_'; ?>" role="tab">
                Descripción
                <button class="btn btn-sm btn-info btn-tab-close imprimir" data-etapa="<?= AUDITORIA_ETAPA_AP; ?>"><i class="fa fa-print"></i></button>
            </a>
        </li>
        <?php if ($auditoria['auditorias_is_sin_observaciones'] == 0): ?>
            <li class="nav-item">
                <?php $disabled = ($etapa_auditoria == AUDITORIA_ETAPA_AP && $auditoria['auditorias_status_id'] == AUDITORIAS_STATUS_EN_PROCESO) ? 'disabled' : ''; ?>
                <a class="nav-link tab-solventacion <?= $etapa_auditoria > AUDITORIA_ETAPA_AP ? 'active' : ''; ?> <?= $disabled; ?>" data-toggle="tab" href="#<?= isset($o, $o['observaciones_id']) ? 'observacion_solventacion_' . $o['observaciones_id'] : 'nueva_observacion_solventacion_'; ?>" role="tab">
                    Etapa de solventación
                    <button class="btn btn-sm btn-info btn-tab-close imprimir <?= $disabled; ?>" data-etapa="<?= AUDITORIA_ETAPA_REV1 ?>"><i class="fa fa-print"></i></button>
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="tab-content">
        <?php
        $recomendaciones = array();
        if (isset($o, $o['observaciones_id'])) {
            $recomendaciones = $this->Recomendaciones_model->get_recomendaciones($o['observaciones_id'], TRUE);
        }
        $o['recomendaciones'] = $recomendaciones;
        $data = array('index' => $index, 'o' => $o);
        $this->load->view('templates/observacion_detalles_view', $data);
        $this->load->view('templates/observacion_solventacion_view', $data);
        ?>
    </div>
</div>