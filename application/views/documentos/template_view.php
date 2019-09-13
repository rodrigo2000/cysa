<!-- DDslick -->
<script src="<?= base_url(); ?>resources/plugins/ddslick/jquery.ddslick.min.js" type="text/javascript"></script>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- Typeahead -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/typeahead.js/dist/typeahead.bundle4.js" type="text/javascript"></script>
<!-- xEditable -->
<link href="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css"/>
<!--<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-editable.min.js" type="text/javascript"></script>-->
<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/x-editable-bs4.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-datepicker.es.js" type="text/javascript"></script>
<!-- Noty -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/noty/js/noty/packaged/jquery.noty.packaged.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/noty/js/noty/packaged/noty-defaults.js" type="text/javascript"></script>
<!-- Personalizado -->
<script src="<?= base_url(); ?>resources/scripts/auditoria_view.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/scripts/auditorias_documentos_generico.js" type="text/javascript"></script>
<!-- Estilos para irmprimir -->
<link href="<?= base_url(); ?>resources/styles/oficios.css" rel="stylesheet" type="text/css"/>
<link href="<?= base_url(); ?>resources/styles/media_print.css" rel="stylesheet" type="text/css"/>
<?php if ($accion === "descargar"): ?>
    <link href="<?= APP_SAC_URL; ?>resources/styles/emular_impresora.css" rel="stylesheet" type="text/css"/>
    <script src="<?= APP_SAC_URL; ?>resources/scripts/emular_impresora.js" type="text/javascript"></script>
<?php endif; ?>
<link href="<?= APP_SAC_URL; ?>resources/styles/fuentes.css" rel="stylesheet" type="text/css"/>
<style>
    .btn, span[type=button].label {
        /* display: initial; /* Corrigen un problema de espaciado en los botones */
    }
</style>
<div class="card">
    <div class="card-header no-bg1 b-a-0 hidden-print">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (!empty($this->session->userdata(APP_NAMESPACE))) : ?>
            <?php echo validation_errors(); ?>
            <form id="frmOficios" name="frmOficios" class="<?= $documento_autorizado || $is_finalizada ? 'autorizado' : ''; ?><?= $accion === "descargar" ? ' impresion' : ''; ?>" method="post" action="<?= $urlAction; ?>">
                <div class="text-xs-center hidden-print oficio-menu-opciones">
                    <?php $this->load->view('documentos/menu_opciones'); ?>
                </div>
                <?php if ($is_finalizada || $documento_autorizado): ?>
                    <?php echo $this->Documentos_blob_model->get_html($auditoria['auditorias_id'], $documento['documentos_versiones_documentos_tipos_id']); ?>
                <?php else: // Aquí se carga la VISTA del documento ?>
                    <?php $this->load->view($vista); ?>
                <?php endif; ?>
                <div class="text-xs-center hidden-print oficio-menu-opciones">
                    <?php $this->load->view('documentos/menu_opciones'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>