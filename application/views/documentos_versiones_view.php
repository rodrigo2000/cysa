<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <?php if ($this->{$this->module['controller'] . "_model"}->puedo_insertar()): ?>
            <a class="btn btn-sm btn-primary-outline pull-right m-l-1" href="<?= $this->module['new_url']; ?>"><i class="icon-plus"></i><?= $this->module['title_new']; ?></a>
        <?php endif; ?>
        <h3><?= $this->module['title_list']; ?></h3>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm table-striped table-hover dataTable">
                <thead class="thead-inverse">
                    <tr>
                        <th width="10">🆔</th>
                        <th>Tipo de documento</th>
                        <th>Número ISO</th>
                        <th>Prefijo ISO</th>
                        <th>Código ISO</th>
                        <th>¿Vigente?</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $r): ?>
                        <tr>
                            <td class="text-xs-center align-middle"><?= $r[$this->module['id_field']] ?></td>
                            <td class="text-xs-center align-middle"><?= $r['documentos_tipos_nombre']; ?></td>
                            <td class="text-xs-center align-middle"><?= $r['documentos_versiones_numero_iso']; ?></td>
                            <td class="text-xs-center align-middle"><?= $r['documentos_versiones_prefijo_iso']; ?></td>
                            <td class="text-xs-center align-middle"><?= $r['documentos_versiones_codigo_iso']; ?></td>
                            <td class="text-xs-center align-middle"><?= $r['documentos_versiones_is_vigente'] == 1 ? 'Sí' : 'No'; ?></td>
                            <td width="100" class="text-xs-center align-middle">
                                <?php if ($this->{$this->module['controller'] . "_model"}->puedo_modificar()): ?>
                                    <a href="<?= $this->module['edit_url'] . "/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs btn-info" title="<?= $this->module['title_edit']; ?>"><i class="fa fa-pencil"></i></a>
                                <?php endif; ?>
                                <?php if ($this->{$this->module['controller'] . "_model"}->puedo_eliminar()): ?>
                                    <a href="<?= $this->module['delete_url'] . "/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs btn-danger" title="<?= $this->module['title_delete']; ?>"><i class="fa fa-trash"></i></a>
                                <?php endif; ?>
                                <?php if ($this->{$this->module['controller'] . "_model"}->puedo_destruir()): ?>
                                    <a href="<?= $this->module['destroy_url'] . "/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs btn-danger" title="<?= $this->module['title_destroy']; ?>"><i class="fa fa-remove"></i></a>
                                    <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>