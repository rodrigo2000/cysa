<?php
$aux = $this->Auditorias_tipos_model->get_uno($auditoria['auditorias_tipo']);
$tipo_auditoria = $aux['auditorias_tipos_nombre'] . " (" . $auditoria['auditorias_tipos_siglas'] . ")";
$aux = $this->Auditorias_areas_model->get_uno($auditoria['auditorias_area']);
$area_responsable = $aux['auditorias_areas_nombre'];
$aux = array(
    $auditoria['direcciones_nombre'],
    $auditoria['subdirecciones_nombre'],
    $auditoria['departamentos_nombre']
);
$unidad_administrativa = implode(" / ", $aux);
?>
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
            <form id="frmOficios" name="frmOficios" class="acta <?= $documento_autorizado || $is_finalizada ? 'autorizado' : ''; ?><?= $accion === "descargar" ? ' impresion' : ''; ?>" method="post" action="<?= $urlAction; ?>">
                <div class="text-xs-center hidden-print oficio-menu-opciones">
                    <?php $this->load->view('documentos/menu_opciones'); ?>
                </div>
                <?php if ($is_finalizada || $documento_autorizado): ?>
                    <?php echo $this->Documentos_blob_model->get_html($auditoria['auditorias_id'], $documento['documentos_versiones_documentos_tipos_id']); ?>
                <?php else: ?>
                <div class="form-group row hidden-print">
                    <label for="auditorias_tipos_siglas" class="col-xs-12 col-sm-3 col-md-2 control-label">Sustituye a</label>
                    <div class="col-sm-6 col-md-4">
                        <select name="constantes[<?= AANP_ID_AUDITORIA_SUSTITUTA; ?>]" class="form-control">
                            <option value="0">NINGUNA</option>
                            <?php foreach ($auditoroas_por_sustituir as $s): ?>
                                <option value="<?= $s['auditorias_id'] ?>"><?= !empty($s['auditorias_rubro']) ? $s['auditorias_rubro'] : 'SIN RUBRO'; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('auditorias_tipos_siglas'); ?>
                    </div>
                </div>
                <div id="oficio-hoja" class="acta <?= $documento_autorizado ? 'autorizado' : ''; ?>">
                    <?php $r = isset($documento['valores']) && !empty($documento['valores']) && $accion !== "nuevo" ? $documento['valores'] : NULL; ?>
                    <div class="watermark">PARA REVISIÓN</div>
                    <table width="100%">
                        <thead>
                            <tr>
                                <td>
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <img src="<?= APP_SAC_URL; ?>resources/images/logo-icon.png" alt=""/>
                                        </div>
                                        <div class="col-xs-6 text-xs-center">
                                            <div style="font-size: 15pt; font-weight: bold;">AYUNTAMIENTO DE MÉRIDA</div>
                                            <div><?= mb_strtoupper(LABEL_CONTRALORIA); ?></div>
                                            <div>AUTORIZACIÓN DE AUDITORÍAS NO PROGRAMADAS</div>
                                        </div>
                                        <div class="col-xs-3"></div>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody id="oficio-body">
                            <tr>
                                <td>
                                    <?php $sinEspecificar = '<b>SIN ESPECIFICAR</b>'; ?>
                                    <?php $fecha_solicitud = isset($r) && isset($r[AANP_FSOLICITUD]) ? $r[AANP_FSOLICITUD] : ahora(); ?>
                                    <p class="text-xs-right m-t-3 m-b-1"><b>Fecha de solicitud:</b> <?= mysqlDate2OnlyDate($fecha_solicitud); ?></p>
                                    <table class="table mismo-tamano-fuente-p" style="border:1px solid black;">
                                        <tbody>
                                            <tr>
                                                <td width="200">Unidad Administrativa:</td>
                                                <td>
                                                    <?php $aux = isset($r) && isset($r[AANP_UNIDAD]) ? $r[AANP_UNIDAD] : $unidad_administrativa; ?>
                                                    <?= $aux; ?>
                                                    <input type="hidden" name="constantes[<?= AANP_UNIDAD; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tipo de Auditoría:</td>
                                                <td>
                                                    <?php $aux = isset($r) && isset($r[AANP_TIPO]) ? $r[AANP_TIPO] : $tipo_auditoria; ?>
                                                    <?= $aux; ?>
                                                    <input type="hidden" name="constantes[<?= AANP_TIPO; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Objetivo a revisar:</td>
                                                <td>
                                                    <?php $aux = isset($r) && isset($r[AANP_OBJETIVO]) ? $r[AANP_OBJETIVO] : ucfirst($auditoria['auditorias_objetivo']); ?>
                                                    <?= $aux; ?>
                                                    <input type="hidden" name="constantes[<?= AANP_OBJETIVO; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Área responsable:</td>
                                                <td>
                                                    <?php $aux = isset($r) && isset($r[AANP_ARESPONSABLE]) ? $r[AANP_ARESPONSABLE] : $area_responsable; ?>
                                                    <?= $aux; ?>
                                                    <input type="hidden" name="constantes[<?= AANP_ARESPONSABLE; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Auditor Líder:</td>
                                                <td>
                                                    <?php $aux = isset($r) && isset($r[AANP_AURESPONSABLE]) ? $r[AANP_AURESPONSABLE] : $auditoria['auditor_lider_nombre_completo']; ?>
                                                    <?= $aux; ?>
                                                    <input type="hidden" name="constantes[<?= AANP_AURESPONSABLE; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fecha de inicio:</td>
                                                <td>
                                                    <?php $aux = isset($r) && isset($r[AANP_FINICIO]) ? $r[AANP_FINICIO] : mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_programado']); ?>
                                                    <?= $aux; ?>
                                                    <input type="hidden" name="constantes[<?= AANP_FINICIO; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Justificación o motivo:</td>
                                                <td>
                                                    <?= span_editable($r, AANP_JUSTIFICACION); ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="firmas row">
                                        <div class="col-xs-4 col-print-4">
                                            <?php $e = $this->Empleados_model->get_jefe($auditoria['auditorias_periodos_id'], $auditoria['auditorias_auditor_lider']); ?>
                                            <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : $sinEspecificar; ?>">
                                                <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : $sinEspecificar; ?></div>
                                                <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : $sinEspecificar; ?></div>
                                                <div class="firmas_empleado_cargo">Solicitó</div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-print-4">
                                            <?php $e = $this->CYSA_model->get_subdirector_de_contraloria($auditoria['auditorias_periodos_id']); ?>
                                            <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : $sinEspecificar; ?>">
                                                <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : $sinEspecificar; ?></div>
                                                <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : $sinEspecificar; ?></div>
                                                <div class="firmas_empleado_cargo">Vo.Bo.</div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-print-4-">
                                            <?php $e = $this->CYSA_model->get_director_de_contraloria($auditoria['auditorias_periodos_id']); ?>
                                            <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : $sinEspecificar; ?>">
                                                <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : $sinEspecificar; ?></div>
                                                <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : $sinEspecificar; ?></div>
                                                <div class="firmas_empleado_cargo">Autorizó</div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-xs-right">
                                    <?= $documento['documentos_versiones_prefijo_iso'] . $documento['documentos_versiones_codigo_iso'] . " " . $documento['documentos_versiones_numero_iso']; ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="form-group hidden-print">
                    <div class="col-sm-12 text-xs-center">
                        <input type="hidden" name="<?= $this->module['id_field'] ?>" value="<?= $id; ?>">
                        <input type="hidden" name="documentos_id" id="documentos_id" value="<?= isset($documento['documentos_id']) && $accion === "modificar" ? $documento['documentos_id'] : 0; ?>">
                        <input type="hidden" name="accion" id="accion" value="<?= $accion; ?>">
                        <input type="hidden" name="documentos_tipos_id" id="documentos_tipos_id" value="<?= $documento['documentos_versiones_documentos_tipos_id']; ?>">
                        <input type="hidden" name="documentos_versiones_id" id="documentos_versiones_id" value="<?= $documento['documentos_versiones_id']; ?>">
                    </div>
                </div>
                <?php endif; ?>
                <div class="text-xs-center hidden-print oficio-menu-opciones">
                    <?php $this->load->view('documentos/menu_opciones'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<script src="<?= APP_CYSA_URL; ?>resources/scripts/auditorias_documentos_acei.js" type="text/javascript"></script>
<link href="<?= APP_CYSA_URL; ?>resources/styles/auditorias_documentos_acei.css" rel="stylesheet" type="text/css"/>