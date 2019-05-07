<div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <div class="table-responsive">
            <form class="form-horizontal" novalidate="novalidate" action="<?= $urlAction; ?>" method="post">
                <div class="form-group row">
                    <label for="expedientes_fecha_apertura" class="col-xs-12 col-sm-3 col-md-2 control-label">Fecha de ingreso</label>
                    <div class="col-xs-12 col-sm-9 col-md-4 m-b-xs">
                        <button class="btn btn-secondary btn-block component-daterangepicker" id="input_expedientes_fecha_apertura" type="button" datepicker="expedientes_fecha_apertura"><?= isset($r) && !empty($r['expedientes_fecha_apertura']) && $r['expedientes_fecha_apertura'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['expedientes_fecha_apertura']) : '<i class="fa fa-calendar"></i>'; ?></button>
                        <input type="hidden" id="expedientes_fecha_apertura" name="expedientes_fecha_apertura" value="<?= isset($r) && isset($r['expedientes_fecha_apertura']) && $r['expedientes_fecha_apertura'] !== '0000-00-00' ? $r['expedientes_fecha_apertura'] : ''; ?>">
                        <?= form_error('expedientes_fecha_apertura'); ?>
                    </div>
                    <label for="expedientes_fecha_cierre" class="col-xs-12 col-sm-3 col-md-2 control-label">Fecha de cierre</label>
                    <div class="col-xs-12 col-sm-9 col-md-4">
                        <button class="btn btn-secondary btn-block component-daterangepicker" id="input_expedientes_fecha_cierre" type="button" datepicker="expedientes_fecha_cierre"><?= isset($r) && !empty($r['expedientes_fecha_cierre']) && $r['expedientes_fecha_cierre'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['expedientes_fecha_cierre']) : '<i class="fa fa-calendar"></i>'; ?></button>
                        <input type="hidden" id="expedientes_fecha_cierre" name="expedientes_fecha_cierre" value="<?= isset($r) && isset($r['expedientes_fecha_cierre']) && $r['expedientes_fecha_cierre'] !== '0000-00-00' ? $r['expedientes_fecha_cierre'] : ''; ?>">
                        <?= form_error('expedientes_fecha_cierre'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="expedientes_fecha_desclasificacion" class="col-xs-12 col-sm-3 col-md-2 control-label">Desclasificación</label>
                    <div class="col-xs-12 col-sm-9 col-md-4 m-b-xs">
                        <button class="btn btn-secondary btn-block component-daterangepicker" id="input_expedientes_fecha_desclasificacion" type="button" datepicker="expedientes_fecha_desclasificacion"><?= isset($r) && !empty($r['expedientes_fecha_desclasificacion']) && $r['expedientes_fecha_desclasificacion'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['expedientes_fecha_desclasificacion']) : '<i class="fa fa-calendar"></i>'; ?></button>
                        <input type="hidden" id="expedientes_fecha_desclasificacion" name="expedientes_fecha_desclasificacion" value="<?= isset($r) && isset($r['expedientes_fecha_desclasificacion']) && $r['expedientes_fecha_desclasificacion'] !== '0000-00-00' ? $r['expedientes_fecha_desclasificacion'] : ''; ?>">
                        <?= form_error('expedientes_fecha_desclasificacion'); ?>
                    </div>
                    <label for="expedientes_cantidad_cd" class="col-xs-6 col-sm-3 col-md-2 control-label">Cantidad de CDs</label>
                    <div class="col-xs-12 col-sm-2 col-md-1 m-b-xs">
                        <input type="number" id="" name="expedientes_cantidad_cd" class="form-control" value="<?= isset($r) && isset($r['expedientes_cantidad_cd']) ? $r['expedientes_cantidad_cd'] : ''; ?>" min="0">
                        <?= form_error('expedientes_cantidad_cd'); ?>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-1">
                        <input class="form-check-input" type="checkbox" name="expedientes_isPPR" id="empleados_genero" data-labelauty="¿PPR?" value="1" <?= isset($r) && isset($r['expedientes_isPPR']) && $r['expedientes_isPPR'] == 1 ? 'checked="checked"' : ''; ?>>
                        <?= form_error("expedientes_is_ppr"); ?>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-2">
                        <input class="form-check-input" type="checkbox" name="expedientes_isReservada" id="expedientes_is_reservada" data-labelauty="¿Reservada?" value="1" <?= isset($r) && isset($r['expedientes_isReservada']) && $r['expedientes_isReservada'] == 1 ? 'checked="checked"' : ''; ?>>
                        <?= form_error("expedientes_is_reservada"); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="empleados_fecha_ingreso" class="col-xs-12 col-sm-4 col-sm-3 col-md-2 control-label">Número de fojas</label>
                    <div class="col-xs-12 col-sm-8 col-md-4">
                        <div id="tomos">
                            <?php if (isset($r) && isset($r['expedientes_numero_fojas']) && !empty($r['expedientes_numero_fojas'])): ?>
                                <?php $tomos = explode(",", $r['expedientes_numero_fojas']); ?>
                                <?php foreach ($tomos as $index => $t): ?>
                                    <div class="input-group">
                                        <span class="input-group-addon">Tomo <?= $index + 1; ?></span>
                                        <input type="number" name="numero_fojas[]" class="form-control text-xs-center" size="4" value="<?= $t; ?>">
                                        <span class="input-group-btn">
                                            <a href="#" class="btn btn-danger btnEliminarTomo"><i class="fa fa-remove"></i></a>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs-center">
                            <a id="btnAgregarTomo" href="#" class="btn btn-sm btn-info">Agregar tomo</a>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="text-xs-center">
                        <a href="<?= $this->module['cancel_url']; ?>" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><?= $etiquetaBoton; ?></button>
                        <a class="btn btn-default <?= isset($r) && empty($r['expedientes_id']) ? 'hidden-xs-up' : ''; ?>" href="<?= $this->module['url'] . "/imprimir_portada"; ?>" target="_imprimir_portada">Descargar portada en Word</a>
                        <input type="hidden" name="accion" value="<?= $accion; ?>">
                        <input type="hidden" name="<?= $this->module['id_field']; ?>" value="<?= isset($r) && isset($r[$this->module['id_field']]) ? $r[$this->module['id_field']] : ''; ?>">
                        <input type="hidden" name="expedientes_id" value="<?= isset($r) && isset($r['expedientes_id']) ? $r['expedientes_id'] : 0; ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Labelauty -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.js" type="text/javascript"></script>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.js" type="text/javascript"></script>
<!-- Personalizado -->
<link href="<?= base_url(); ?>resources/styles/auditorias_portada_view.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url(); ?>resources/scripts/auditorias_portada_view.js" type="text/javascript"></script>