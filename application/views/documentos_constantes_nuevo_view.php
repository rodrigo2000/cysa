<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <form class="form-horizontal" novalidate="novalidate" action="<?= $urlAction; ?>" method="post">
            <div class="form-group row">
                <label for="documentos_constantes_documentos_tipos_id" class="col-sm-3 control-label">Tipo de documento</label>
                <div class="col-sm-6 col-md-6">
                    <select name="documentos_constantes_documentos_tipos_id" id="documentos_constantes_documentos_tipos_id" class="form-control">
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($documentos_tipos as $dt): ?>
                            <option value="<?= $dt['documentos_tipos_id']; ?>" <?= isset($r) && $r['documentos_constantes_documentos_tipos_id'] == $dt['documentos_tipos_id'] ? 'selected="selected"' : ''; ?>><?= $dt['documentos_tipos_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('documentos_constantes_documentos_tipos_id'); ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="documentos_constantes_nombre" class="col-sm-3 control-label">Nombre</label>
                <div class="col-sm-6 col-md-6">
                    <input type="text" id="documentos_constantes_nombre" name="documentos_constantes_nombre" class="form-control" value="<?= isset($r) ? $r['documentos_constantes_nombre'] : ''; ?>" maxlength="100">
                    <?= form_error('documentos_constantes_nombre'); ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="documentos_constantes_descripcion" class="col-sm-3 control-label">Descripción</label>
                <div class="col-sm-6 col-md-6">
                    <textarea name="documentos_constantes_descripcion" id="documentos_constantes_descripcion" class="form-control"><?= isset($r) ? $r['documentos_constantes_descripcion'] : ''; ?></textarea>
                    <?= form_error('documentos_constantes_descripcion'); ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="text-xs-center">
                    <a href="<?= $this->module['cancel_url']; ?>" class="btn btn-default">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><?= $etiquetaBoton; ?></button>
                    <input type="hidden" name="accion" value="<?= $accion; ?>">
                    <input type="hidden" name="<?= $this->module['id_field']; ?>" value="<?= isset($r) && isset($r[$this->module['id_field']]) ? $r[$this->module['id_field']] : ''; ?>">
                </div>
            </div>
        </form>
    </div>
</div>
