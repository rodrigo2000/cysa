<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <a class="btn btn-sm btn-primary-outline pull-right m-l-1" href="<?= $this->module['new_url']; ?>"><i class="icon-plus"></i><?= $this->module['title_new']; ?></a>
        <h3><?= $this->module['title_list']; ?></h3>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <form class="form-inline m-b-1">
                <div class="form-group">
                    <label class="sr-only" for="clv_dir">Dirección</label>
                    <select id="clv_dir" name="clv_dir" class="form-control">
                        <option value="0">Todas las direcciones</option>
                        <?php foreach ($direcciones as $d): ?>
                            <option value="<?= $d['clv_dir']; ?>"><?= $d['denDireccion']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="anio">Año</label>
                    <select id="anio" name="anio" class="form-control">
                        <?php foreach ($anios as $a): ?>
                            <option value="<?= $a; ?>"><?= $a; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="idArea">Área</label>
                    <select id="idArea" name="idArea" class="form-control">
                        <option value="0" selected="selected">Área</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?= $a; ?>"><?= $a; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="idTipo">Tipo</label>
                    <select id="idTipo" name="idTipo" class="form-control">
                        <option value="0" selected="selected">Tipo</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t; ?>"><?= $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="idStatus">Status</label>
                    <select id="idStatus" name="idStatus" class="form-control">
                        <option value="0" selected="selected">Status</option>
                        <option value="2">En Proceso</option>
                        <option value="4">Reprogramada</option>
                        <option value="3">Finalizada</option>
                        <option value="1">Cancelada</option>
                    </select>
                </div>
                <button id="btnBuscar" type="button" class="btn btn-primary"><i class="fa fa-search"></i></button>
            </form>
            <table id="tablaAuditorias" class="table table-sm table-hover table-striped dataTablePersonalizado">
		<col span="6">
		<col style="width: 80px;">
                <thead class="thead-inverse">
                    <tr>
                        <th>No de Auditoría</th>
                        <!--<th>Rubro</th>-->
                        <th>Dirección/Subdirección</th>
                        <th>Fecha Fin Programada</th>
                        <th>Fecha Fin Real (1° Etapa)</th>
                        <th>Fecha Aprobación</th>
                        <th>Estado</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?= base_url(); ?>resources/scripts/auditorias_view.js" type="text/javascript"></script>
<script>
    var dataTableOrder = "asc";
    var dataTableFieldOrder = 0;
    var dataTableOrderTargets = [-1];
</script>
<style>
    .table thead tr th, .table td {
	vertical-align: middle;
	text-align: center;
    }
</style>