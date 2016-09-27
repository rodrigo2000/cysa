<div class="row m-b-1">
    <div class="col-xs-12">
        <form class="form-inline pull-sm-right">
            <div class="form-group">
                <label for="anio">Año de la auditoría</label>
                <select id="anio" class="form-control">
                    <option value="2016">2016</option>
                </select>
            </div>
            <div class="form-group">
                <label for="idAuditoria">Auditoría</label>
                <select id="idAuditoria" class="form-control">
                    <option value="0">Selecciona</option>
                </select>
            </div>
        </form>
    </div>
</div>
<div class="fullcalendar"></div>

<!-- Modal -->
<div class="modal fade bd-example-modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Nuevo evento</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <fieldset class="form-group row">
                        <label for="exampleInputEmail1" class="col-sm-2">Descripción</label>
                        <div class="col-sm-10">
                            <textarea class="form-control"></textarea>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label for="exampleInputPassword1" class="col-sm-2">Auditoría</label>
                        <div class="col-sm-10">
                            <select class="form-control">
                                <?php foreach ($auditorias as $a) : ?>
                                    <option value="<?= $a['id']; ?>"><?= empty($a['num']) ? $a['rubro'] : $a['num']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label for="exampleInputPassword1" class="col-sm-2">Equipo</label>
                        <div class="col-sm-10">
                            <select class="form-control" multiple="multiple">
                                <?php foreach ($auditores as $a) : ?>
                                    <option value="<?= $a['idEmpleado']; ?>"><?= $a['nombreCompleto']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label for="exampleInputPassword1" class="col-sm-2">Prioridad</label>
                        <div class="col-sm-10">
                            <select class="form-control">
                                <option>Normal</option>
                                <option>Urgente</option>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset class="form-group row">
                        <label for="exampleInputPassword1" class="col-sm-2">Periodo de tiempo</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <span class="input-group-addon" style="padding-bottom: 3px;"><i class="material-icons">date_range</i></span>
                                <input type="text" id="rango" name="rango" class="form-control drp" value="" placeholder="Seleccione el rango de fechas">
                                <input type="hidden" id="rango_inicio" name="rango_inicio" value="">
                                <input type="hidden" id="rango_final" name="rango_final" value="">
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Fullcalendar -->
<link  href="<?= APP_SAC_URL; ?>resources/plugins/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/moment.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/fullcalendar/dist/fullcalendar.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/fullcalendar/dist/gcal.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/fullcalendar/lang/es.js" type="text/javascript"></script>

<!-- Date Range Picker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>

<script  src="<?= base_url(); ?>resources/scripts/dashboard_view.js" type="text/javascript"></script>