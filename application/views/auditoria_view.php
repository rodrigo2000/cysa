<div class="card1 no-bg">
    <div class="card-header no-bg b-a-0">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (isset($this->session->cysa[$this->module['id_field']])): ?>
            <ul id="tab-menu-auditoria" class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-informacion" role="tab">Información</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-documentos" role="tab">Documentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-acta_resultados" role="tab">Acta de Resultados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-observaciones" role="tab">Observaciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-control_auditoria" role="tab">Control de auditoría</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-herramientas" role="tab">Herramientras</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-reciclaje" role="tab">Reciclaje</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-timeline" role="tab">Línea de tiempo</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane" id="tab-informacion" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_informacion"); ?>
                </div>
                <div class="tab-pane" id="tab-documentos" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_documentos"); ?>
                </div>
                <div class="tab-pane" id="tab-acta_resultados" role="tabpanel">
                    ARA
                </div>
                <div class="tab-pane active" id="tab-observaciones">
                    <?php $this->load->view("auditoria/auditoria_view_tab_observaciones"); ?>
                </div>
                <div class="tab-pane" id="tab-control_auditoria" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_control_auditoria"); ?>
                </div>
                <div class="tab-pane" id="tab-herramientras" role="tabpanel">settings</div>
                <div class="tab-pane" id="tab-reciclaje" role="tabpanel">settings</div>
                <div class="tab-pane" id="tab-timeline" role="tabpanel">settings</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Labelauty -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.js" type="text/javascript"></script>
<!-- MultiSelect -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/multiselect/js/jquery.multi-select.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/multiselect/js/jquery.quicksearch.js" type="text/javascript"></script>
<!-- Personalizados -->
<script src="<?= base_url(); ?>resources/scripts/select_dependientes.js" type="text/javascript"></script>
<link href="<?= base_url(); ?>resources/styles/auditoria_view.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/scripts/auditoria_view.js" type="text/javascript"></script>