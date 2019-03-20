<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <?php $this->load->view('auditoria/header_view', array('registros' => $registros)); ?>
    </div>
    <div class="card-block">
        <?php if (!empty($this->session->userdata(APP_NAMESPACE))): $auditorias_id = $this->session->userdata(APP_NAMESPACE)[$this->module['id_field']]; ?>
            
        <?php endif; ?>
    </div>
</div>
<script src="<?= base_url(); ?>resources/scripts/auditoria_view.js" type="text/javascript"></script>
