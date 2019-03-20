<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
		<form class="form-horizontal" novalidate="novalidate" action="<?= $urlAction; ?>" method="post">
            <div class="form-group row">
                <label for="catalogo_categorias_servicios_nombre" class="col-sm-3 control-label">Nombre</label>
                <div class="col-sm-6 col-md-6">
                    <input type="text" id="catalogo_categorias_servicios_nombre" name="catalogo_categorias_servicios_nombre" class="form-control" value="<?= isset($r) ? $r['catalogo_categorias_servicios_nombre'] : ''; ?>">
                    <?= form_error('catalogo_categorias_servicios_nombre'); ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="text-xs-center">
                    <a href="<?= $this->module['cancel_url']; ?>" class="btn btn-default">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><?= $etiquetaBoton; ?></button>
                    <input type="hidden" name="accion" value="<?= $accion; ?>">
                    <input type="hidden" name="<?= $this->module['id_field']; ?>" value="<?= isset($r) && $r[$this->module['id_field']] ? $r[$this->module['id_field']] : ''; ?>">
                </div>
            </div>
        </form>
    </div>
</div>