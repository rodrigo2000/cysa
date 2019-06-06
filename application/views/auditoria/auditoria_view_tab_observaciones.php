<!--Nav tabs -->
<ul class = "nav nav-tabs" role = "tablist" id = "observaciones_menu">
    <?php foreach ($auditoria['observaciones'] as $o): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#observaciones_<?= $o['observaciones_id']; ?>" role="tab" title="<?= $o['observaciones_titulo']; ?>">
                Observación <?= $o['observaciones_numero']; ?>
                <button class="btn btn-sm btn-danger btn-tab-close eliminar-observacion" data-observaciones-id="<?= $o['observaciones_id']; ?>" type="button">&times;</button>
            </a>
        </li>
    <?php endforeach; ?>
    <li class="nav-item" id="tab-add-observacion">
        <a class="nav-link" onclick="javascript: return false;" data-toggle="tab" href="#" role="tab">
            <button class="btn btn-sm btn-success-outline btn-tab-add add-observacion">+ Agregar observación</button>
        </a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content" id="observaciones_auditoria">
    <?php foreach ($auditoria['observaciones'] as $index => $o): ?>
        <?php $data = array('index' => $index, 'o' => $o); ?>
        <?php $this->load->view('templates/observacion_view', $data); ?>
    <?php endforeach; ?>
</div>
<div id="template_nueva_observacion" class="hidden-xs-up">
    <?php $data = array('o' => array()); ?>
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