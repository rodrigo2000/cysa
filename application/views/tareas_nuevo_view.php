<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <form action="<?= $urlAction; ?>" id="tareas_form" method="post">
	    <div class="form-group row">
		<label for="tareas_nombre" class="col-xs-2 col-form-label">Nombre</label>
		<div class="col-xs-10">
		    <input id="tareas_nombre" name="tareas_nombre" class="form-control" type="text" value="<?= isset($r) ? $r['tareas_nombre'] : ''; ?>">
		    <?php echo form_error('tareas_nombre'); ?>
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
	</form>
    </div>
</div>
<script src="<?= base_url(); ?>resources/scripts/tareas_nuevo_view.js" type="text/javascript"></script>