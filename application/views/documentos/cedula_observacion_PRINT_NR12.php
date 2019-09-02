<!-- DDslick -->
<script src="<?= base_url(); ?>resources/plugins/ddslick/jquery.ddslick.min.js" type="text/javascript"></script>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- Typeahead -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/typeahead.js/dist/typeahead.bundle4.js" type="text/javascript"></script>
<!-- xEditable -->
<link href="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css"/>
<!--<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-editable.min.js" type="text/javascript"></script>-->
<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/x-editable-bs4.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-datepicker.es.js" type="text/javascript"></script>
<!-- Personalizado -->
<script src="<?= base_url(); ?>resources/scripts/auditoria_view.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/scripts/auditorias_documentos_generico.js" type="text/javascript"></script>
<link href="<?= base_url(); ?>resources/styles/oficios.css" rel="stylesheet" type="text/css"/>
<link href="<?= base_url(); ?>resources/styles/media_print.css" rel="stylesheet" type="text/css"/>
<?php if ($accion === "descargar"): ?>
    <link href="<?= APP_SAC_URL; ?>resources/styles/emular_impresora.css" rel="stylesheet" type="text/css"/>
    <script src="<?= APP_SAC_URL; ?>resources/scripts/emular_impresora.js" type="text/javascript"></script>
<?php endif; ?>
<link href="<?= APP_SAC_URL; ?>resources/styles/fuentes.css" rel="stylesheet" type="text/css"/>
<div class="card">
    <div class="card-header no-bg1 b-a-0 hidden-print">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (!empty($this->session->userdata(APP_NAMESPACE))) : ?>
            <?php echo validation_errors(); ?>
            <form id="frmOficios" name="frmOficios" class="acta <?= $documento_autorizado ? 'autorizado' : ''; ?><?= $accion === "descargar" ? ' impresion' : ''; ?>" method="post" action="<?= $urlAction; ?>">
                <div id="oficio-hoja" class="acta <?= $documento_autorizado ? 'autorizado' : ''; ?>">
                    <?php $r = isset($documento['valores']) && !empty($documento['valores']) && $accion !== "nuevo" ? $documento['valores'] : NULL; ?>
                    <div class="watermark">PARA REVISIÓN</div>
                    <table width="100%">
                        <tbody id="oficio-body">
                            <tr>
                                <td>
                                    <?php foreach ($observaciones as $o): ?>
                                        <table class="table-observaciones">
                                            <thead>
                                                <tr>
                                                    <td>
                                                        <div class="row bg-white">
                                                            <div class="col-xs-3">
                                                                <img src="<?= APP_SAC_URL; ?>resources/images/logo-icon.png" alt=""/>
                                                            </div>
                                                            <div class="col-xs-6 text-xs-center">
                                                                <div style="font-size: 15pt; font-weight: bold;">AYUNTAMIENTO DE MÉRIDA</div>
                                                                <div><?= LABEL_CONTRALORIA; ?></div>
                                                                <br>
                                                                <div>CÉDULA DE OBSERVACIÓN</div>
                                                            </div>
                                                            <div class="col-xs-3"></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p class="observacion-bg-default-light">
                                                            <span class="">UNIDAD ADMINISTRATIVA AUDITADA</span>
                                                            <span class="pull-xs-right"><?= !empty($auditoria['auditorias_fechas_lectura']) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_lectura']) : 'LECTURA PENDIENTE'; ?></span>
                                                        </p>
                                                        <div class="row">
                                                            <dl>
                                                                <dt class="col-sm-3">Unidad Administrativa:</dt>
                                                                <dd class="col-sm-9"><?= $auditoria['direcciones_nombre']; ?></dd>
                                                                <dt class="col-sm-3">Subdirección:</dt>
                                                                <dd class="col-sm-9"><?= $auditoria['subdirecciones_nombre']; ?></dd>
                                                                <dt class="col-sm-3">Departamento:</dt>
                                                                <dd class="col-sm-9"><?= $auditoria['departamentos_nombre']; ?></dd>
                                                                <dt class="col-sm-3">Objetivo a revisar:</dt>
                                                                <dd class="col-sm-9"><?= ucfirst($auditoria['auditorias_objetivo']); ?></dd>
                                                                <dt class="col-sm-3">No. de Auditoría:</dt>
                                                                <dd class="col-sm-9"><?= $auditoria['numero_auditoria']; ?></dd>
                                                            </dl>
                                                        </div>
                                                        <div>
                                                            <p class="observacion-bg-default-light">Observación <?= $o['observaciones_numero']; ?>: <?= $o['observaciones_titulo']; ?></p>
                                                            <div class="observacion-cuadro-descripcion"><?= $o['observaciones_descripcion']; ?></div>
                                                            <?php $involucrados = array(); ?>
                                                            <?php if (isset($o['recomendaciones'])): ?>
                                                                <?php foreach ($o['recomendaciones'] as $r): array_push($involucrados, $r['recomendaciones_empleados_id']); ?>
                                                                    <div class="recomendacion">
                                                                        <p class="recomendaciones-bg-default-light">Recomendación <?= $r['recomendaciones_numero']; ?></p>
                                                                        <p class="recomendacion-descripcion"><?= $r['recomendaciones_descripcion']; ?></p>
                                                                        <p class="recomendacion-clasificacion"><strong>Clasificación:</strong> <?= !empty($r['recomendaciones_clasificaciones_nombre']) ? $r['recomendaciones_clasificaciones_nombre'] : SIN_ESPECIFICAR; ?></p>
                                                                        <p class="recomendacion-responsable"><strong>Responsable:</strong> <?= !empty($r['empleados_nombre_titulado_siglas']) ? $r['empleados_nombre_titulado_siglas'] : SIN_ESPECIFICAR; ?></p>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <p class="observacion-bg-default-light text-xs-center">RESPONSABLES DE SOLVENTACIÓN DE LA AUDITORÍA</p>
                                                        </div>
                                                        <div class="firmas">
                                                            <div class="firmas_involucrados">
                                                                <?php array_push($involucrados, $auditoria['cc_empleados_id']); ?>
                                                                <?php if (count($involucrados) > 0): ?>
                                                                    <?php $involucrados = array_unique($involucrados); ?>
                                                                    <?php foreach ($involucrados as $i): ?>
                                                                        <?php if (!empty($i)): $e = $this->SAC_model->get_empleado($i); ?>
                                                                            <div class="firmas_empleado empleado_<?= $e['empleados_id']; ?>">
                                                                                <div class="firmas_empleado_nombre"><?= $e['empleados_nombre_titulado_siglas']; ?></div>
                                                                                <div class="firmas_empleado_cargo"><?= $e['empleados_cargo']; ?></div>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <p>No se han definido los responsables de las recomendaciones.</p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <p class="observacion-bg-default-light text-xs-center">RESPONSABLES DE LA EJECUCIÓN DE LA AUDITORÍA</p>
                                                        </div>
                                                        <div class="firmas">
                                                            <div class="firmas_involucrados">
                                                                <?php $auditor_lider = $this->SAC_model->get_empleado($auditoria['auditorias_auditor_lider']); ?>
                                                                <div class="firmas_empleado empleado_<?= $auditor_lider['empleados_id']; ?>">
                                                                    <div class="firmas_empleado_nombre"><?= $auditor_lider['empleados_nombre_titulado_siglas']; ?></div>
                                                                    <div class="firmas_empleado_cargo">Auditor Líder</div>
                                                                </div>
                                                                <?php $superiores = $this->SAC_model->get_jefes_de_empleado($auditor_lider['empleados_id'], $auditoria['auditorias_id'], $auditoria['auditorias_periodos_id']); ?>
                                                                <?php foreach ($superiores as $s): $e = $this->SAC_model->get_empleado($s); ?>
                                                                    <div class="firmas_empleado empleado_<?= $e['empleados_id']; ?>">
                                                                        <div class="firmas_empleado_nombre"><?= $e['empleados_nombre_titulado_siglas']; ?></div>
                                                                        <div class="firmas_empleado_cargo"><?= $e['empleados_cargo']; ?></div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <span class="version_documento"><?= $documento['documentos_versiones_prefijo_iso'] . $documento['documentos_versiones_codigo_iso'] . " " . $documento['documentos_versiones_numero_iso']; ?></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-xs-center">
                                                        <span class="texto-foja">ESTA FOJA FORMA PARTE INTEGRANTE DE LA OBSERVACIÓN No. <?= $o['observaciones_numero']; ?></span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group row hidden-print">
                    <div class="col-sm-12 text-xs-center">
                        <a href="<?= base_url() . $this->uri->segment(1); ?>" class="btn btn-default">Cancelar</a>
                        <?php if (!$documento_autorizado && $this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_MODIFICAR, APP_NAMESPACE, 'Documentos')): ?>
                            <button type="button" class="btn btn-primary m-l-2 boton_guardar"><?= $etiquetaBoton; ?></button>
                        <?php endif; ?>
                        <input type="hidden" name="<?= $this->module['id_field'] ?>" value="<?= $id; ?>">
                        <input type="hidden" name="documentos_id" id="documentos_id" value="<?= isset($documento['documentos_id']) && $accion === "modificar" ? $documento['documentos_id'] : 0; ?>">
                        <input type="hidden" name="accion" id="accion" value="<?= $accion; ?>">
                        <input type="hidden" name="documentos_tipos_id" id="documentos_tipos_id" value="<?= $documento['documentos_versiones_documentos_tipos_id']; ?>">
                        <input type="hidden" name="documentos_versiones_id" id="documentos_versiones_id" value="<?= $documento['documentos_versiones_id']; ?>">
                        <input type="hidden" id="auditorias_enlace_designado" value="<?= $auditoria['auditorias_enlace_designado']; ?>">
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<link href="<?= base_url(); ?>resources/styles/cedulas_observacion_impresion.css" rel="stylesheet" type="text/css"/>