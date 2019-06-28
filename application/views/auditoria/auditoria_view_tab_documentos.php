<?php if (!empty($this->session->userdata(APP_NAMESPACE))): $auditorias_id = $this->session->userdata(APP_NAMESPACE)[$this->module['id_field']]; ?>
    <div class="card-columns" id="todos-los-documentos">
        <div class="card">
            <div class="card-block">
                <h4 class="card-title">Documentación inicial</h4>
            </div>
            <div class="list-group">
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/OA"; ?>">Orden de Auditoría (OA)</a>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/AIA"; ?>">Acta de inicio de Auditoría (AIA)</a>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/AANP"; ?>">Autirozación de Auditoría No Programada (AANP)</a>
            </div>
        </div>
        <div class="card">
            <div class="card-block">
                <h4 class="card-title">Reprogramaciones y Ampliaciones</h4>
            </div>
            <div class="list-group">
                <?php $reprogramaciones = $this->Auditoria_model->get_documentos($auditoria['auditorias_id'], TIPO_DOCUMENTO_REPROGRAMACION); ?>
                <?php foreach ($reprogramaciones as $r): ?>
                    <?php if (isset($r['valores']) && !empty($r['valores'])): $folio = $r['valores'][15]; ?>
                        <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/" . TIPO_DOCUMENTO_REPROGRAMACION . "/" . $r['documentos_id']; ?>">Reprogramación <?= str_pad($folio, 3, "0", STR_PAD_LEFT); ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/REPROG/nuevo"; ?>">Nueva reprogramación</a>
                <?php $ampliaciones = $this->Auditoria_model->get_documentos($auditoria['auditorias_id'], TIPO_DOCUMENTO_AMPLIACION); ?>
                <?php foreach ($ampliaciones as $a): ?>
                    <?php if (isset($a['valores']) && !empty($a['valores'])): $folio = $a['valores'][2]; ?>
                        <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/" . TIPO_DOCUMENTO_AMPLIACION . "/" . $a['documentos_id']; ?>">Ampliación <?= str_pad($folio, 3, "0", STR_PAD_LEFT); ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/AMPLIA/nuevo"; ?>">Nueva ampliación</a>
            </div>
        </div>
        <div class="card">
            <div class="card-block">
                <h4 class="card-title">Documentación final</h4>
            </div>
            <div class="list-group">
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/OC"; ?>">Oficio de Citatorio (OC)</a>
                <?php if ($auditoria['auditorias_anio'] < 2018): ?>
                    <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/OED"; ?>">Oficio de Envío de Documentos (OED)</a>
                <?php endif; ?>
                <a class="list-group-item" href="<?= $this->module['url'] . "/portada"; ?>">Imprimir portada o guarda exterior</a>
            </div>
        </div>
        <div class="card">
            <div class="card-block">
                <h4 class="card-title">Actas</h4>
            </div>
            <div class="list-group">
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/ORP"; ?>">Oficio de Resolución de Prórroga (ORP)</a>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/OSI"; ?>">Oficio de Solicitud de Información (OSI)</a>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/RAP"; ?>">Oficio de Resolución de Ampliación de Plazo (RAP)</a>
            </div>
        </div>
        <div class="card">
            <div class="card-block">
                <h4 class="card-title">Oficios generales</h4>
            </div>
            <div class="list-group">
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/ARA"; ?>">Acta de Resultados (ARA/ARR)</a>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/ACEI"; ?>">Acta de Cierre de Entrega de Información (ACEI)</a>
                <a class="list-group-item" href="<?= $this->module['documentos_url'] . "/AA"; ?>">Autirozación Administrativa (AA)</a>
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- Personaliado -->
<link href="<?= APP_CYSA_URL; ?>resources/styles/auditorias_view_tab_documentos.css" rel="stylesheet" type="text/css"/>