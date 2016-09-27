<?php
if (isset($documentos)) {
    $doctosTitulos = array("", "Documentos ISO", "Otros");
    $doctos = array(
        array(), array(), array()
    );
    foreach ($documentos as $d) {
        $doctos[intval($d['clasDocumento'])][] = array(
            "descripcion" => $d['denDocto'],
            "id" => $d['idTipoDocto']
        );
    }
}

$auditorias = array(
    array(
        'id' => 1,
        'nombre' => 'Auditoria 1'
    )
        )
?>
<div class="card">
    <?php if (!$modal): ?>
        <div class="card-header no-bg1 b-a-0">
            <h3><?= $tituloModulo; ?></h3>
        </div>
    <?php endif; ?>
    <div class="card-block">
        <div>
            <form id="myForm" method="post" action="<?= $urlAction; ?>" novalidate="novalidate">
                <fieldset class="form-group">
                    <label for="area" class="col-sm-2 form-control-label">Fecha de registro</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?= mysqlDate2OnlyDate(ahora(), FALSE); ?></p>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="area" class="col-sm-2 form-control-label">Auditorías relacionadas</label>
                    <div class="col-sm-10">
                        <div>
                            <input id="auditorias_relacionadas" name="auditorias_relacionadas" class="to-labelauty" type="checkbox" data-labelauty="No aplica|Sí aplica" checked="checked">
                        </div>
                    </div>
                </fieldset>
                <fieldset id="auditorias_container" class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <select id="idAuditoria" name="idAuditoria" class="form-control" multiple="multiple">
                            <!--<option value="0">SELECCIONAR</option>-->
                            <?php foreach ($auditorias as $a): ?><option value="<?= $a['id']; ?>"><?= $a['nombre']; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="etapa" class="col-sm-2 form-control-label">Etapa</label>
                    <div class="col-sm-10">
                        <select id="etapa" name="etapa" class="form-control">
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($etapas as $e): ?><option value="<?= $e['id_Proceso']; ?>"><?= $e['Etapa']; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="ppa" class="col-xs-4 col-sm-2 form-control-label">Subdirección</label>
                    <div class="col-sm-10">
                        <select id="subdireccion" name="subdireccion" class="form-control">
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($subdirecciones as $s): ?><option value="<?= $s['clv_subdir']; ?>"><?= $s['denSubdireccion'] ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="area" class="col-sm-2 form-control-label">Área</label>
                    <div class="col-sm-10">
                        <select id="area" name="area" class="form-control">
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($areas as $a): ?><option value="<?= $a['clv_depto']; ?>"><?= $a['denDepartamento'] ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="ffpa" class="col-sm-2 form-control-label">Tipo de inclumplimiento</label>
                    <div class="col-sm-10">
                        <div class="radio-inline">
                            <label>
                                <input type="radio" id="formato" name="tipo_inclumplimiento" value="formato" checked="checked"> Formato
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" id="proceso" name="tipo_inclumplimiento" value="proceso"> Proceso
                            </label>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="idMotivoIncumplimiento" class="col-sm-2 form-control-label">Motivo de incumplimiento</label>
                    <div class="col-sm-10">
                        <select id="idMotivoIncumplimiento" name="idMotivoIncumplimiento" class="form-control">
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($motivosIncumplimiento as $m): ?><option value="<?= $m['id_motivos']; ?>"><?= $m['motivos']; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="descripcion" class="col-sm-2 form-control-label">Descripción</label>
                    <div class="col-sm-10">
                        <textarea id="descripcion" name="descripcion" rows="5" class="form-control"></textarea>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="justificacion" class="col-sm-2 form-control-label">Justificación</label>
                    <div class="col-sm-10">
                        <textarea id="justificacion" name="justificacion" rows="5" class="form-control"></textarea>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="idClasificacionAccion" class="col-sm-2 form-control-label">Clasificación de la acción</label>
                    <div class="col-sm-10">
                        <select id="idClasificacionAccion" name="idClasificacionAccion" class="form-control">
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($acciones as $a): ?><option value="<?= $a['id_Accion']; ?>"><?= $a['accion']; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="accionRealizar" class="col-sm-2 form-control-label">Accion a realizar</label>
                    <div class="col-sm-10">
                        <textarea id="accionRealizar" name="accionRealizar" rows="5" class="form-control"></textarea>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="auditor_lider" class="col-sm-2 form-control-label">Responsable de evidencia</label>
                    <div class="col-sm-10">
                        <select id="auditor_lider" name="auditor_lider" class="form-control">
                            <option value="0">SELECCIONAR</option>
                            <?php foreach ($responsablesAutorizan as $key => $a): ?><option value="<?= $key; ?>"><?= $a; ?></option><?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="idDocumento" class="col-sm-2 form-control-label">Documento de evidencia</label>
                    <div class="col-sm-10">
                        <select id="idDocumento" name="idDocumento" class="multiselect-optgroup" multiple="multiple">
                            <!--<option value="0">SELECCIONAR</option>-->
                            <?php foreach ($doctos as $key => $tipos): if (count($tipos) > 0): ?>
                                    <optgroup label="<?= $doctosTitulos[$key]; ?>">
                                        <?php foreach ($tipos as $d): ?>
                                            <option value="<?= $d['id']; ?>"><?= $d['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <?php
                                endif;
                            endforeach;
                            ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="objetivo" class="col-sm-2 form-control-label">Detalle de evidencia</label>
                    <div class="col-sm-10">
                        <textarea id="objetivo" name="objetivo" rows="5" class="form-control"></textarea>
                    </div>
                </fieldset>
                <fieldset class="form-group">
                    <label for="objetivo" class="col-sm-2 form-control-label">Persona que registró</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?= $this->session->nombre_usuario; ?></p>
                    </div>
                </fieldset>
                <?php if (!$modal): ?>
                    <fieldset class="form-group">
                        <div class="pull-xs-right col-sm-offset-2 col-sm-10">
                            <a href="<?= base_url() . $this->uri->segment(1); ?>" class="btn btn-default">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><?= $etiquetaBoton; ?></button>
                        </div>
                    </fieldset>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<link href="<?= APP_SAC_URL; ?>resources/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/multiselect/js/jquery.multi-select.js" type="text/javascript"></script>
<link href="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/jquery-labelauty/source/jquery-labelauty.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/scripts/productos_nuevo_view.js" type="text/javascript"></script>