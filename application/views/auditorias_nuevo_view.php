<?php
if (isset($r)) {
    $r['fechaIniAudit'] = date("Y-m-d", $r['fechaIniAudit']);
    $r['fechaIniReal'] = date("Y-m-d", $r['fechaIniReal']);
    $r['fechaFinAudit'] = date("Y-m-d", $r['fechaFinAudit']);
    $r['fechaFinReal'] = date("Y-m-d", $r['fechaFinReal']);
    $subdirecciones = $this->Catalogos_model->getSubdirecciones($r['clv_dir']);
    $departamentos = $this->Catalogos_model->getDepartamentos($r['clv_dir'], $r['clv_subdir']);
}
?><div class="card">
    <div class="card-header no-bg1 b-a-0">
        <h3><?= $tituloModulo; ?></h3>
    </div>
    <div class="card-block">
        <div>
            <form id="myForm" method="post" action="<?= $urlAction; ?>" novalidate="novalidate">
                <fieldset class="form-group">
                    <label for="area" class="col-sm-2 form-control-label">Número de auditoría</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <select id="area" name="area" class="form-control">
                                <option value="0">Área</option>
				<?php foreach ($areas as $key => $a): ?><option value="<?= $key; ?>"<?= isset($r) && $r['area'] === $a ? ' selected="selected"' : ''; ?>><?= $a; ?></option><?php endforeach; ?>
                            </select>
                            <div class="input-group-addon">/</div>
                            <select id="tipo" name="tipo" class="form-control">
                                <option value="0">Tipo</option>
				<?php foreach ($tipos as $key => $t): ?><option value="<?= $key; ?>"<?= isset($r) && $r['tipo'] === $t ? ' selected="selected"' : ''; ?>><?= $t; ?></option><?php endforeach; ?>
                            </select>
                            <div class="input-group-addon">/</div>
                            <input type="text" id="consecutivo" name="consecutivo" class="form-control" value="<?= isset($r) ? $r['numero'] : ''; ?>">
                            <div class="input-group-addon">/</div>
                            <select id="anio" name="anio" class="form-control">
				<?php foreach ($anios as $a): ?><option value="<?= $a; ?>" <?= isset($r) ? ($r['anio'] == $a ? 'selected="selected"' : '') : ($a == date("Y") ? 'selected="selected"' : ''); ?>><?= $a; ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="esSegundoPeriodo" name="esSegundoPeriodo" value="1" <?= isset($r) && $r['segundoPeriodo'] ? 'checked="checked"' : ''; ?>> 2° Período
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="asignar" name="asignar" value="1"> Asignar el numero sugerido para la Auditoría.
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="ppa" class="col-xs-4 col-sm-2 form-control-label">Pertenece al PAA</label>
                    <div class="col-xs-8 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="ppa" name="ppa" value="1" <?= isset($r) && $r['auditProgramada'] ? 'checked="checked"' : ''; ?>>
                                <div class="btn btn-info btn-sm"><i class="fa fa-info"></i></div>
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="fipa" class="col-sm-2 form-control-label">Fecha de inicio</label>
                    <div class="col-sm-10">
                        <div class="input-group col-md-6 p-l-0 p-r-0">
                            <span class="input-group-addon">Programada</span>
                            <input type="text" class="form-control datepicker" id="fipa" value="<?= isset($r) ? $r['fechaIniAudit'] : ''; ?>">
                            <input type="hidden" id="fipa_alt" name="fipa" value="<?= isset($r) ? $r['fechaIniAudit'] : ''; ?>">
                        </div>
                        <div class="input-group col-md-6">
                            <span class="input-group-addon">Real</span>
                            <input type="text" class="form-control datepicker" id="firp" value="<?= isset($r) ? $r['fechaIniReal'] : ''; ?>">
                            <input type="hidden" id="firp_alt" name="firp" value="<?= isset($r) ? $r['fechaIniReal'] : ''; ?>">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="ffpa" class="col-sm-2 form-control-label">Fecha de terminación</label>
                    <div class="col-sm-10">
                        <div class="input-group col-md-6 p-l-0 p-r-0">
                            <span class="input-group-addon">Programada</span>
                            <input type="text" class="form-control datepicker" id="ffpa" value="<?= isset($r) ? $r['fechaFinAudit'] : ''; ?>">
                            <input type="hidden" id="ffpa_alt" name="ffpa" value="<?= isset($r) ? $r['fechaFinAudit'] : ''; ?>">
                        </div>
                        <div class="input-group col-md-6">
                            <span class="input-group-addon">Real</span>
                            <input type="text" class="form-control datepicker" id="ffrp" name="ffrp" value="<?= isset($r) ? $r['fechaFinReal'] : ''; ?>">
                            <input type="hidden" id="ffrp_alt" name="ffrp" value="<?= isset($r) ? $r['fechaFinReal'] : ''; ?>">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="direccion" class="col-sm-2 form-control-label">Dirección</label>
                    <div class="col-sm-10">
                        <select id="direccion" name="direccion" class="form-control">
                            <option value="0">SELECCIONAR</option>
			    <?php foreach ($direcciones as $d): ?><option value="<?= $d['clv_dir']; ?>"<?= isset($r) && $r['clv_dir'] == $d['clv_dir'] ? ' selected="selected"' : ''; ?>><?= $d['denDireccion']; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="subdireccion" class="col-sm-2 form-control-label">Subdirección</label>
                    <div class="col-sm-10">
                        <select id="subdireccion" name="subdireccion" class="form-control" <?= (isset($subdirecciones) && count($subdirecciones) > 0) ? '' : 'disabled="disabled"'; ?>>
			    <?php if (isset($subdirecciones) && count($subdirecciones) > 0): ?>
				<?php foreach ($subdirecciones as $s): ?><option value="<?= $s['clv_subdir']; ?>"<?= isset($r) && $r['clv_subdir'] == $s['clv_subdir'] ? ' selected="selected"' : ''; ?>><?= $s['denSubdireccion']; ?></option><?php endforeach; ?>
			    <?php endif; ?>
			</select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="departamento" class="col-sm-2 form-control-label">Departamento</label>
                    <div class="col-sm-10">
                        <select id="departamento" name="departamento" class="form-control" <?= (isset($departamentos) && count($departamentos) > 0) ? '' : 'disabled="disabled"'; ?>>
			    <?php if (isset($departamentos) && count($departamentos) > 0): ?>
				<?php foreach ($departamentos as $d): ?><option value="<?= $d['clv_depto']; ?>"<?= isset($r) && $r['clv_depto'] == $d['clv_depto'] ? ' selected="selected"' : ''; ?>><?= $d['denDepartamento']; ?></option><?php endforeach; ?>
			    <?php endif; ?>
			</select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="rubro" class="col-sm-2 form-control-label">Rubro</label>
                    <div class="col-sm-10">
                        <input type="text" id="rubro" name="rubro" class="form-control" value="<?= isset($r) ? $r['rubroAudit'] : ''; ?>">
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="objetivo" class="col-sm-2 form-control-label">Objetivo</label>
                    <div class="col-sm-10">
                        <textarea id="objetivo" name="objetivo" class="form-control" rows="5"><?= isset($r) ? $r['objetivoAudit'] : ''; ?></textarea>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditor_lider" class="col-sm-2 form-control-label">Auditor líder</label>
                    <div class="col-sm-10">
                        <select id="auditor_lider" name="auditor_lider" class="form-control">
                            <option value="0">SELECCIONAR</option>
			    <?php foreach ($auditores as $a): ?><option value="<?= $a['idEmpleado']; ?>"<?= isset($r) && $r['idEmpleado'] == $a['idEmpleado'] ? ' selected="selected"' : ''; ?>><?= $a['nombreCompleto']; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <div class="pull-xs-right col-sm-offset-2 col-sm-10">
                        <a href="<?= base_url() . $this->uri->segment(1); ?>" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary"><?= $etiquetaBoton; ?></button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<style>
    .input-group-addon:first-child {
        width: 120px;
        text-align: right;
    }
</style>
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/scripts/auditorias_nuevo_view.js" type="text/javascript"></script>