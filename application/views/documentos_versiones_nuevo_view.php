<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <form class="form-horizontal" novalidate="novalidate" action="<?= $urlAction; ?>" method="post">
            <div class="form-group row">
                <label for="documentos_versiones_documentos_tipos_id" class="col-sm-3 control-label">Tipo de documento</label>
                <div class="col-sm-6 col-md-6">
                    <select name="documentos_versiones_documentos_tipos_id" id="documentos_versiones_documentos_tipos_id" class="form-control">
                        <option value="0">SELECCIONE</option>
                        <?php foreach ($documentos_tipos as $dt): ?>
                            <option value="<?= $dt['documentos_tipos_id']; ?>" <?= isset($r) && $r['documentos_versiones_documentos_tipos_id'] == $dt['documentos_tipos_id'] ? 'selected="selected"' : ''; ?>><?= $dt['documentos_tipos_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('documentos_versiones_documentos_tipos_id'); ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="catalogo_categorias_servicios_nombre" class="col-sm-3 control-label">ISO</label>
                <div class="col-sm-6 col-md-6">
                    <div class="input-group">
                        <input type="text" name="documentos_versiones_prefijo_iso" class="form-control" value="<?= isset($r) ? $r['documentos_versiones_prefijo_iso'] : ''; ?>" placeholder="Prefijo">
                        <span class="input-group-addon">😁</span>
                        <input type="text" name="documentos_versiones_prefijo_iso" class="form-control" value="<?= isset($r) ? $r['documentos_versiones_codigo_iso'] : ''; ?>" placeholder="Código">
                        <span class="input-group-addon">&para;</span>
                        <input type="text" name="documentos_versiones_numero_iso" class="form-control" value="<?= isset($r) ? $r['documentos_versiones_numero_iso'] : ''; ?>" placeholder="Número">
                    </div>
                    <?= form_error('catalogo_categorias_servicios_nombre'); ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="documentos_versiones_archivo_registro" class="col-sm-3 control-label">Archivo de registro</label>
                <div class="col-sm-6 col-md-6">
                    <input name="documentos_versiones_archivo_registro" id="documentos_versiones_archivo_registro" class="form-control" value="<?= isset($r) ? $r['documentos_versiones_archivo_registro'] : ''; ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="documentos_versiones_archivo_impresion" class="col-sm-3 control-label">Archivo de impresión</label>
                <div class="col-sm-6 col-md-6">
                    <input name="documentos_versiones_archivo_impresion" id="documentos_versiones_archivo_impresion" class="form-control" value="<?= isset($r) ? $r['documentos_versiones_archivo_impresion'] : ''; ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="documentos_versiones_is_vigente" class="col-sm-3 control-label">¿Es vigente?</label>
                <div class="col-sm-6 col-md-6">
                    <input type="checkbox" name="documentos_versiones_is_vigente" id="documentos_versiones_is_vigente" value="1" <?= isset($r) && $r['documentos_versiones_is_vigente'] == 1 ? 'checked="checked"' : ''; ?>>
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