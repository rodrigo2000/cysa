<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <a class="btn btn-sm btn-primary-outline pull-right m-l-1" href="<?= $this->module['new_url']; ?>"><i class="icon-plus"></i><?= $this->module['title_new']; ?></a>
        <h3><?= $this->module['title_list']; ?></h3>
    </div>
    <div class="card-block">
	<?php if (isset($registros) && count($registros) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover dataTable">
		    <colgroup>
			<col span="2">
			<col style="text-align: right; width: 100px;">
		    </colgroup>
                    <thead class="thead-inverse">
                        <tr>
			    <th>Etiqueta</th>
                            <th>Nombre del campo</th>
			    <!--<th>Función</th>-->
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
			<?php foreach ($registros as $r): ?>
			    <tr>
				<td><?= $r['campos_etiqueta']; ?></td>
				<td><?= $r['campos_nombre']; ?></td>
				<!--<td><?= $r['campos_funcion'];?></td>-->
				<td>
				    <a href="<?= $this->module['edit_url'] . "/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs btn-primary-outline"><i class="fa fa-pencil"></i></a>
				    <a href="<?= $this->module['delete_url'] . "/" . $r[$this->module['id_field']]; ?>" class="btn btn-xs btn-danger-outline"><i class="fa fa-trash"></i></a>
				</td>
			    </tr>
			<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
	<?php else: ?>
    	<h3 align="center" style="margin-top: 50px;">No se encontraron campos.</h3>
	<?php endif; ?>
    </div>
</div>
<script>
    var dataTableOrder = "asc";
    var dataTableFieldOrder = 0;
    var dataTableOrderTargets = [-1];
</script>