<div class="card1 no-bg">
    <div class="card-header no-bg b-a-0">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (isset($this->session->cysa[$this->module['id_field']])): ?>
            <ul id="tabMenuAuditoria" class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#informacion" role="tab">Información</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#documentos" role="tab">Documentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#acta_resultados" role="tab">Acta de Resultados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#control_auditoria" role="tab">Control de auditoría</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#herramientas" role="tab">Herramientras</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#reciclaje" role="tab">Reciclaje</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#timeline" role="tab">Línea de tiempo</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="informacion" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_informacion"); ?>
                </div>
                <div class="tab-pane" id="documentos" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_documentos"); ?>
                </div>
                <div class="tab-pane" id="acta_resultados" role="tabpanel">messages</div>
                <div class="tab-pane" id="control_auditoria" role="tabpanel">
                    <?php $this->load->view("auditoria/auditoria_view_tab_control_auditoria"); ?>
                </div>
                <div class="tab-pane" id="herramientras" role="tabpanel">settings</div>
                <div class="tab-pane" id="reciclaje" role="tabpanel">settings</div>
                <div class="tab-pane" id="timeline" role="tabpanel">settings</div>
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