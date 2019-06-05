<div class="tab-pane" id="<?= isset($o, $o['observaciones_id']) ? 'observaciones_' . $o['observaciones_id'] : 'nueva_observacion_'; ?>" role="tabpanel">
    <ul class="nav nav-pills" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#<?= isset($o, $o['observaciones_id']) ? 'observacion_detalles_' . $o['observaciones_id'] : 'nueva_observacion_'; ?>" role="tab">
                Descripción
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#<?= isset($o, $o['observaciones_id']) ? 'observacion_solventacion_' . $o['observaciones_id'] : 'nueva_solventacion_'; ?>" role="tab">
                Etapa de solventación
            </a>
        </li>
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