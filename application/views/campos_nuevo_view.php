<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <form action="<?= $urlAction; ?>" id="campos_form" method="post">
	    <div class="form-group row">
		<label for="campos_etiqueta" class="col-xs-2 col-form-label">Etiqueta</label>
		<div class="col-xs-10">
		    <input id="campos_etiqueta" name="campos_etiqueta" class="form-control" type="text" value="<?= isset($r) ? $r['campos_etiqueta'] : ''; ?>">
		    <?php echo form_error('campos_etiqueta'); ?>
		</div>
	    </div>
	    <div class="form-group row">
		<label for="campos_nombre" class="col-xs-2 col-form-label">Nombre</label>
		<div class="col-xs-10">
		    <input id="campos_nombre" name="campos_nombre" class="form-control" type="text" value="<?= isset($r) ? $r['campos_nombre'] : ''; ?>" placeholder="basedatos.tabla.campo">
		    <?php echo form_error('campos_nombre'); ?>
		</div>
	    </div>
	    <div class="form-group row">
		<label for="campos_funcion" class="col-xs-2 col-form-label">Función</label>
		<div class="col-xs-10">
		    <input id="campos_funcion" name="campos_funcion" class="form-control" type="text" value="<?= isset($r) ? $r['campos_funcion'] : ''; ?>" placeholder="Ninguna">
		    <p class="text-muted">Algunos campos tiene un tipo de dato diferente a DATE. Esta función sirve para convertir el valor del campo en formato DATE de MySQL.</p>
		    <?php echo form_error('campos_funcion'); ?>
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