<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <a class="btn btn-sm btn-primary-outline pull-right m-l-1" href="<?= $this->module['new_url']; ?>"><i class="icon-plus"></i><?= $this->module['title_new']; ?></a>
        <h3><?= $this->module['title_list']; ?></h3>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <form id="filtros" class="form-inline m-b-1">
                <div class="form-group">
                    <label class="sr-only" for="direcciones_id">Dirección</label>
                    <select id="direcciones_id" name="direcciones_id" class="form-control" style="max-width: 250px;">
                        <option value="0">Todas las direcciones</option>
                        <?php foreach ($direcciones as $d): ?>
                            <option value="<?= $d['direcciones_id']; ?>"><?= $d['cc_etiqueta_direccion'] . " - " . $d['direcciones_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="auditorias_anio">Año</label>
                    <select id="auditorias_anio" name="auditorias_anio" class="form-control">
                        <?php foreach ($anios as $a): ?>
                            <option value="<?= $a; ?>"><?= $a; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="auditorias_area">Área</label>
                    <select id="auditorias_area" name="auditorias_area" class="form-control">
                        <option value="0" selected="selected">Área</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?= $a['auditorias_areas_id']; ?>"><?= $a['auditorias_areas_siglas']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="auditorias_tipo">Tipo</label>
                    <select id="auditorias_tipo" name="auditorias_tipo" class="form-control">
                        <option value="0" selected="selected">Tipo</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['auditorias_tipos_id']; ?>"><?= $t['auditorias_tipos_nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="auditorias_status_id">Status</label>
                    <select id="auditorias_status_id" name="auditorias_status_id" class="form-control">
                        <option value="0" selected="selected">Todos los status</option>
                        <option value="5">Sin iniciar</option>
                        <option value="2">En Proceso</option>
                        <option value="4">Reprogramada</option>
                        <option value="3">Finalizada</option>
                        <option value="1">Cancelada</option>
                        <option value="6">Sustituída</option>
                    </select>
                </div>
                <button id="btnBuscar" type="button" class="btn btn-primary"><i class="fa fa-search"></i></button>
            </form>
            <table id="tablaAuditorias" class="table table-sm table-hover table-striped dataTablePersonalizado">
                <thead class="thead-inverse">
                    <tr>
                        <th>No de Auditoría</th>
                        <th>Rubro / Auditor</th>
                        <th>Centro de costos</th>
                        <th width="130">Inicio</th>
                        <th width="130">Fin</th>
                        <th>Status</th>
                        <th width="100">&nbsp;</th>
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