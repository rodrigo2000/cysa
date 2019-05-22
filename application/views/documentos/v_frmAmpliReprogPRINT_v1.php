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
$reprogramacion = array(
    'etapa' => AUDITORIA_ETAPA_AP
);
/*
 *
 * Falta verificar cómo obtener en qué etapa se encuentra la auditoría
 *
 */
$etapas = array('Resultados de Auditoría', 'Solventación de Observaciones', 'Segunda Solventación de Observaciones', 'Auditoría Finalizada');
$is_reprogramacion = FALSE;
if (isset($r) && !empty($r) && is_array($r)) {
    foreach ($r as $constante => $c) {
        switch ($constante) {
//            case SOLICITUD_AMPLIACION_P_FOLIO:
            case REPROGRAMACION_FOLIO:
                $reprogramacion['folio'] = $c;
                break;
//            case SOLICITUD_REPROGRAMACION_P_MOTIVO:
            case REPROGRAMACION_MOTIVO:
                $reprogramacion['motivo'] = $c;
                break;
//            case SOLICITUD_AMPLIACION_P_OBSERVACIONES:
            case REPROGRAMACION_OBSERVACIONES:
                $reprogramacion['observaciones'] = $c;
                break;
//            case SOLICITUD_AMPLIACION_P_FECHA_SOLICITUD:
            case REPROGRAMACION_FECHA_SOLICITUD:
                $reprogramacion['fecha_solicitud'] = $c;
                break;
            case REPROGRAMACION_DIAS_IMPACTO:
                $reprogramacion['dias_impacto'] = $c;
                break;
            case REPROGRAMACION_FECHA_INICIAL:
                $reprogramacion['fecha_inicio_programada'] = $c;
                if (!is_int($reprogramacion['fecha_inicio_programada'])) {
                    $reprogramacion['fecha_inicio_programada'] = strtotime($reprogramacion['fecha_inicio_programada']);
                }
                $is_reprogramacion = TRUE;
                break;
            case REPROGRAMACION_FECHA_FINAL:
                $reprogramacion['fecha_fin_programada'] = $c;
                break;
            default :
                break;
        }
    }
}
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
<link href="<?= APP_SIMA_URL; ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css"/>
<!--<script src="<?= APP_SIMA_URL; ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-editable.min.js" type="text/javascript"></script>-->
<script src="<?= APP_SIMA_URL; ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/x-editable-bs4.js" type="text/javascript"></script>
<script src="<?= APP_SIMA_URL; ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-datepicker.es.js" type="text/javascript"></script>
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
<style>
    /*    .table td, .table th, .table tr:last-child td {
            border:none;
        }
        .table td:first-child {
            font-weight: bold;
        }
        .table td:last-child {
            border-bottom: 1px solid black;
        }*/
</style>
<div class="card">
    <div class="card-header no-bg1 b-a-0 hidden-print">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (!empty($this->session->userdata(APP_NAMESPACE))) : ?>
            <?php $documento = $documentos[$index]; ?>
            <?php $hidden = !isset($documento['documentos_id']) || empty($documento['documentos_id']) ? 'hidden-xs-up' : ''; ?>
            <?php $documento_autorizado = isset($documento['documentos_is_aprobado']) && $documento['documentos_is_aprobado'] == 1 ? TRUE : FALSE; ?>
            <?php echo validation_errors(); ?>
            <form id="frmOficios" name="frmOficios" class="acta <?= $documento_autorizado ? 'autorizado' : ''; ?><?= $accion === "descargar" ? ' impresion' : ''; ?>" method="post" action="<?= $urlAction; ?>">
                <div class="text-xs-center m-b-1 hidden-print">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Nuevo</button>
                        <div class="dropdown-menu" style="height: 300px; overflow: auto;">
                            <?php foreach ($direcciones_select as $d): ?>
                                <a class="dropdown-item" href="<?= base_url() . $this->module['controller'] . "/documento/" . $this->uri->segment(3) . "/nuevo/" . $d['direcciones_id']; ?>"><?= $d['direcciones_nombre_cc']; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (!$documento_autorizado): ?>
                        <button type="button" class="btn btn-primary boton_guardar m-l-2"><?= $etiquetaBoton; ?></button>
                        <?php if ($this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_AUTORIZAR_DOCUMENTO)): ?>
                            <a id="btn-autorizar" href="<?= $this->module['autorizar_url'] . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" class="actualizar_id btn btn-default btn-warning m-l-2 <?= $hidden; ?>">Autorizar</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($this->{$this->module['controller'] . "_model"}->tengo_permiso(PERMISOS_DESAUTORIZAR_DOCUMENTO)): ?>
                            <a id="btn-autorizar" href="<?= $this->module['desautorizar_url'] . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" class="actualizar_id btn btn-default btn-danger m-l-2">Desautorizar</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a id="btn-vista-impresion" href="<?= base_url() . $this->module['controller'] . "/descargar" . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>" class="actualizar_id btn btn-info m-l-2 <?= $hidden; ?>" target="_blank">Imprimir</a>
                    <?php if (count($documentos) > 1): ?>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-default dropdown-toggle m-l-2" data-toggle="dropdown">Oficios</button>
                            <div class="dropdown-menu">
                                <?php foreach ($documentos as $d): ?>
                                    <?php $direccion = $this->SAC_model->get_direccion($d['valores'][ORD_ENT_ID_DIR_AUDIT]); ?>
                                    <a style="margin-right:20px;" class="dropdown-item" href="<?= base_url() . $this->module['controller'] . "/documento/" . $this->uri->segment(3) . "/" . $d['documentos_id']; ?>"><?= ($documento['documentos_id'] == $d['documentos_id'] ? '<i class="fa fa-check"></i> ' : '<i style="padding-left:16px;"></i> ') . $direccion['direcciones_nombre']; ?> <span class="badge badge-primary badge-pill bg-red pull-right"><?= $d['documentos_id']; ?></span></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <a id="btn-regresar" class="btn btn-default m-l-2" href="<?= base_url() . $this->uri->segment(1) . "/" . $auditoria['auditorias_id']; ?>#documentos">Regresar</a>
                    <a id="btn-eliminar" class="btn btn-danger m-l-2 actualizar_id <?= $hidden; ?>" href="<?= base_url() . "Documentos/eliminar" . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>">Eliminar</a>
                </div>
                <div id="oficio-hoja" class="acta <?= $documento_autorizado ? 'autorizado' : ''; ?>">
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
                                            <div>SOLICITUD DE REPROGRAMACIÓN</div>
                                        </div>
                                        <div class="col-xs-3"></div>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody id="oficio-body">
                            <tr>
                                <td>
                                    <table class="table mismo-tamano-fuente-p" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <th width="300">Folio:</th>
                                                <td>
                                                    <?php $aux = isset($reprogramacion['folio']) ? $reprogramacion['folio'] : 1; ?>
                                                    <?= str_pad($aux, 3, "0", STR_PAD_LEFT); ?>
                                                    <input type="hidden" name="constantes[<?= REPROGRAMACION_FOLIO; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Fecha de solicitud:</th>
                                                <td>
                                                    <?php $aux = isset($reprogramacion['fecha_solicitud']) ? $reprogramacion['fecha_solicitud'] : ahora(); ?>
                                                    <?= mysqlDate2OnlyDate($aux); ?>
                                                    <input type="hidden" name="constantes[<?= REPROGRAMACION_FECHA_SOLICITUD; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Número de auditoría:</th>
                                                <td>
                                                    <?php $aux = empty($auditoria['auditorias_numero']) ? $auditoria['numero_auditoria'] : 'Sin asignar'; ?>
                                                    <?= $aux; ?>
                                                    <input type="hidden" name="constantes[<?= REPROGRAMACION_NUMERO_AUDITORIA; ?>]" value="<?= $aux; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Etapa de auditoría:</th>
                                                <td>
                                                    <?= $etapas[$reprogramacion['etapa']]; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Unidad Administrativa:</th>
                                                <td>
                                                    <?= $auditoria['direcciones_nombre']; ?>
                                                    <input type="hidden" name="constantes[<?= REPROGRAMACION_UA_AUDITADA; ?>]" value="<?= $auditoria['direcciones_nombre']; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Subdirección:</th>
                                                <td>
                                                    <?= $auditoria['subdirecciones_nombre']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Área auditada:</th>
                                                <td>
                                                    <?= $auditoria['departamentos_nombre']; ?>
                                                    <input type="hidden" name="constantes[<?= REPROGRAMACION_AREA_AUDITADA; ?>]" value="<?= $auditoria['departamentos_nombre']; ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Área Responsable de Auditoría:</th>
                                                <td>
                                                    <?= $auditoria['auditorias_areas_nombre']; ?>
                                                    <input type="hidden" name="constantes[<?= REPROGRAMACION_AREA_RESPONSABLE; ?>]" value="<?= $auditoria['departamentos_nombre']; ?>">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="table table-bordered mismo-tamano-fuente-p">
                                        <tbody>
                                            <tr><th colspan="5">Actividad o motivo</th></tr>
                                            <tr><td colspan="5"><?= span_editable($r, REPROGRAMACION_MOTIVO, NULL, TRUE) ?></td></tr>
                                            <tr>
                                                <th colspan="2" class="text-xs-center">Fecha Inicio</th>
                                                <th colspan="2" class="text-xs-center">Fecha Final</th>
                                                <th class="text-xs-center">Días de impacto</th>
                                            </tr>
                                            <tr>
                                                <th class="text-xs-center">Programada</th>
                                                <th class="text-xs-center">Real</th>
                                                <th class="text-xs-center">Programada</th>
                                                <th class="text-xs-center">Real</th>
                                                <td class="text-xs-center v-align-middle" rowspan="2">0</td>
                                            </tr>
                                            <tr>
                                                <td class="text-xs-center v-align-middle">
                                                    <?php $aux = $auditoria['auditorias_fechas_inicio_programado']; ?>
                                                    <?= mysqlDate2OnlyDate($aux); ?>
                                                    <input type="hidden" name="constantes[<?= REPROGRAMACION_FECHA_INICIAL; ?>]" value="<?= $aux; ?>">
                                                </td>
                                                <td class="text-xs-center v-align-middle">
                                                    <button class="btn btn-secondary btn-block component-daterangepicker" id="input_fecha_programada_terminacion" type="button" datepicker="fecha_programada_terminacion"><?= isset($r) && !empty($r['fecha_programada_terminacion']) && $r['fecha_programada_terminacion'] !== '0000-00-00' ? mysqlDate2OnlyDate($r['fecha_programada_terminacion']) : '<i class="fa fa-calendar"></i>'; ?></button>
                                                    <input type="hidden" id="fecha_programada_terminacion" name="fecha_programada_terminacion" value="<?= isset($r) && isset($r[REPROGRAMACION_FECHA_PROGRAMADA_TERMINACION]) && $r[REPROGRAMACION_FECHA_PROGRAMADA_TERMINACION] !== '0000-00-00' ? $r[REPROGRAMACION_FECHA_PROGRAMADA_TERMINACION] : ''; ?>">
                                                </td>
                                                <td class="text-xs-center v-align-middle">N/A</td>
                                                <td class="text-xs-center v-align-middle" class="col-xs-12" style="border-right:1px solid rgba(0,0,0,.1);">N/A</td>
                                            </tr>
                                            <tr><th colspan="5">Observaciones</td></th>
                                            <tr><td colspan="5"><?= span_editable($r, REPROGRAMACION_OBSERVACIONES, NULL, TRUE); ?></td></tr>
                                        </tbody>
                                    </table>
                                    <div class="firmas row">
                                        <div class="col-xs-4 col-print-4">
                                            <?php $e = $this->Empleados_model->get_jefe($auditoria['auditorias_periodos_id'], $auditoria['auditorias_auditor_lider']); ?>
                                            <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : SIN_ESPECIFICAR; ?>">
                                                <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : SIN_ESPECIFICAR; ?></div>
                                                <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : SIN_ESPECIFICAR; ?></div>
                                                <div class="firmas_empleado_cargo">Solicitó</div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-print-4">
                                            <?php $e = $this->CYSA_model->get_subdirector_de_contraloria($auditoria['auditorias_periodos_id']); ?>
                                            <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : SIN_ESPECIFICAR; ?>">
                                                <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : SIN_ESPECIFICAR; ?></div>
                                                <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : SIN_ESPECIFICAR; ?></div>
                                                <div class="firmas_empleado_cargo">Vo.Bo.</div>
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-print-4-">
                                            <?php $e = $this->CYSA_model->get_director_de_contraloria($auditoria['auditorias_periodos_id']); ?>
                                            <div class="firmas_empleado empleado_<?= !empty($e) && isset($e['empleados_id']) ? $e['empleados_id'] : SIN_ESPECIFICAR; ?>">
                                                <div class="firmas_empleado_nombre"><?= !empty($e) && isset($e['empleados_nombre_titulado_siglas']) ? $e['empleados_nombre_titulado_siglas'] : SIN_ESPECIFICAR; ?></div>
                                                <div class="firmas_empleado_cargo"><?= !empty($e) && isset($e['empleados_cargo']) ? $e['empleados_cargo'] : SIN_ESPECIFICAR; ?></div>
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
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker_2.js" type="text/javascript"></script>
<!-- Personalizados -->
