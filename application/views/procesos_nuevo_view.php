<?php
if (isset($r) && !is_array($r['procesos_tipo_auditoria']) && isset($r['procesos_tipo_auditoria'])) {
    $tiposAuditorias = explode(",", $r['procesos_tipo_auditoria']);
}
?><div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <form action="<?= $urlAction; ?>" id="procesos_form" method="post">
	    <div class="form-group row">
		<label for="procesos_nombre" class="col-xs-2 col-form-label">Nombre</label>
		<div class="col-xs-10">
		    <input id="procesos_nombre" name="procesos_nombre" class="form-control" type="text" value="<?= isset($r) ? $r['procesos_nombre'] : ''; ?>">
		    <?php echo form_error('procesos_nombre'); ?>
		</div>
	    </div>
	    <div class="form-group row">
		<label for="procesos_descripcion" class="col-xs-2 col-form-label">Descripcion</label>
		<div class="col-xs-10">
		    <input id="procesos_descripcion" name="procesos_descripcion" class="form-control" type="search" value="<?= isset($r) ? $r['procesos_descripcion'] : ''; ?>">
		    <?php echo form_error('procesos_descripcion'); ?>
		</div>
	    </div>
	    <div class="form-group row">
		<label for="procesos_version_iso" class="col-xs-2 col-form-label">Versión ISO</label>
		<div class="col-xs-10">
		    <input id="procesos_version_iso" name="procesos_version_iso" class="form-control" type="search" value="<?= isset($r) ? number_format($r['procesos_version_iso'], 1) : ''; ?>">
		    <?php echo form_error('procesos_version_iso'); ?>
		</div>
	    </div>
	    <div class="form-group row">
		<label for="tiposAuditorias" class="col-xs-2 col-form-label">Aplica a</label>
		<div class="col-xs-10">
		    <div id="tiposAuditorias" class="btn-group" data-toggle="buttons">
			<label class="btn btn-default<?= isset($r) && in_array('AP', $tiposAuditorias) ? ' active' : ''; ?>">AP</label>
			<label class="btn btn-default<?= isset($r) && in_array('AE', $tiposAuditorias) ? ' active' : ''; ?>">AE</label>
			<label class="btn btn-default<?= isset($r) && in_array('SA', $tiposAuditorias) ? ' active' : ''; ?>">SA</label>
			<label class="btn btn-default<?= isset($r) && in_array('CI', $tiposAuditorias) ? ' active' : ''; ?>">CI</label>
		    </div><br>
		    <?php echo form_error('procesos_tipo_auditoria'); ?>
		</div>
	    </div>
	    <div class="form-group row">
		<label class="control-label col-md-2"></label>
		<div class="col-md-7">
		    <a href="<?= base_url() . $this->uri->segment(1); ?>" class="btn btn-default">Cancelar</a>
		    <button class="btn btn-primary" type="submit"><?= $etiquetaBoton; ?></button>
		</div>
	    </div>
	    <input type="hidden" name="<?= $this->module['id_field']; ?>" value="<?= isset($r[$this->module['id_field']]) ? $r[$this->module['id_field']] : ''; ?>"> 
	    <input type="hidden" name="procesos_tipo_auditoria" id="procesos_tipo_auditoria" value="<?= isset($r) ? $r['procesos_tipo_auditoria'] : ''; ?>">
	</form>
    </div>
</div>
<script src="<?= base_url(); ?>resources/scripts/procesos_nuevo_view.js" type="text/javascript"></script>