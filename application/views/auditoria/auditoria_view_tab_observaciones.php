<link href="<?= APP_SAC_URL; ?>resources/styles/fuentes.css" rel="stylesheet" type="text/css"/>
<!--Nav tabs -->
<ul class="nav nav-tabs" role="tablist" id="observaciones_menu">
    <?php foreach ($auditoria['observaciones'] as $o): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#observaciones_<?= $o['observaciones_id']; ?>" role="tab" title="<?= $o['observaciones_titulo']; ?>">
                <span>Observación <?= $o['observaciones_numero']; ?></span>
                <?php if ($etapa_auditoria == AUDITORIA_ETAPA_AP): ?>
                    <button class="btn btn-sm btn-danger btn-tab-close eliminar-observacion" data-observaciones-id="<?= $o['observaciones_id']; ?>" type="button" title="Eliminar observación">&times;</button>
                <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
    <?php if ($etapa_auditoria == AUDITORIA_ETAPA_AP): ?>
        <li class="nav-item tab-no-hover" id="tab-add-observacion">
            <a class="nav-link" data-toggle="tab" href="#" role="tab" style="padding: .75rem 0;">
                <button class="btn btn-sm btn-success-outline btn-tab-add add-observacion"><i class="fa fa-plus" title="Agregar observación"></i></button>
            </a>
        </li>
    <?php endif; ?>
    <?php if (count($auditoria['observaciones']) > 1): ?>
        <li class="nav-item tab-no-hover">
            <a class="nav-link" data-toggle="tab" href="" style="padding: .75rem 0;">
                <button class="btn btn-sm btn-info-outline btn-tab-close imprimir imprimir-todas" data-etapa="-1" title="Imprimir todas las observaciones"><i class="fa fa-print"></i></button>
            </a>
        </li>
    <?php endif; ?>
</ul>

<!-- Tab panes -->
<div class="tab-content" id="observaciones_auditoria">
    <?php foreach ($auditoria['observaciones'] as $index => $o): ?>
        <?php $data = array('index' => $index, 'o' => $o); ?>
        <?php $this->load->view('templates/observacion_view', $data); ?>
    <?php endforeach; ?>
</div>
<div id="template_nueva_observacion" class="hidden-xs-up">
    <?php $data = array('index' => NULL, 'o' => array()); ?>
    <?php $this->load->view('templates/observacion_view', $data); ?>
</div>
<div id="template_nueva_recomendacion" class="hidden-xs-up">
    <?php $data = array('rr' => array()); ?>
    <?php $this->load->view('templates/recomendacion_view', $data); ?>
</div>

<!-- Tinymce -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/tinymce/js/tinymce/jquery.tinymce.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/tinymce/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/tinymce/js/tinymce/langs/es_MX.js" type="text/javascript"></script>

<!-- Autosize -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/autosize/dist/autosize.min.js" type="text/javascript"></script>

<!-- Personalizado -->
<link href="<?= base_url(); ?>resources/styles/auditorias_view_tab_observaciones.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/scripts/auditorias_view_tab_observaciones.js" type="text/javascript"></script>