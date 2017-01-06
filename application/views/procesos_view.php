<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <a class="btn btn-sm btn-primary-outline pull-right m-l-1" href="<?= $this->module['new_url']; ?>"><i class="icon-plus"></i><?= $this->module['title_new']; ?></a>
        <h3><?= $this->module['title_list']; ?></h3>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover dataTable">
		<colgroup>
		    <col span="4">
		    <col style="width: 140px;">
		</colgroup>
                <thead class="thead-inverse">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Versión ISO</th>
                        <th>Aplica a</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
		    <?php foreach ($registros as $r): ?>
    		    <tr>
    			<td><?= $r['procesos_nombre']; ?></td>
    			<td><?= $r['procesos_descripcion']; ?></td>
    			<td><?= $r['procesos_version_iso']; ?></td>
    			<td><?= str_replace(",", ", ", $r['procesos_tipo_auditoria']); ?></td>
    			<td>
    			    <a href="<?= base_url() . $this->module['controller'] . "/nuevo_proceso_vigente/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs<?= $r['procesos_vigente'] == 1 ? ' btn-warning' : ' btn-default'; ?>"><i class="fa fa-star"></i></a>
    			    <a href="<?= $this->module['edit_url'] . "/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs btn-primary-outline"><i class="fa fa-pencil"></i></a>
    			    <a href="<?= $this->module['delete_url'] . "/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs btn-danger-outline"><i class="fa fa-trash"></i></a>
    			</td>
    		    </tr>
		    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>