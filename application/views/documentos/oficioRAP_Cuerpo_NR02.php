<!-- DDslick -->
<script src="<?= base_url(); ?>resources/plugins/ddslick/jquery.ddslick.min.js" type="text/javascript"></script>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
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
<div class="card">
    <div class="card-header no-bg1 b-a-0 hidden-print">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (!empty($this->session->userdata(APP_NAMESPACE))) : ?>
            <?php echo validation_errors(); ?>
            <form id="frmOficios" name="frmOficios" class="<?= $documento_autorizado ? 'autorizado' : ''; ?><?= $accion === "descargar" ? ' impresion' : ''; ?>" method="post" action="<?= $urlAction; ?>">
                <div class="text-xs-center m-b-1 hidden-print">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Nuevo</button>
                        <div class="dropdown-menu" style="height: 300px; overflow: auto;">
                            <?php foreach ($direcciones as $d): ?>
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
                                    <?php $direccion = $this->SAC_model->get_direccion($d['valores'][RESOL_AMPLI_ID_UA]); ?>
                                    <a style="margin-right:20px;" class="dropdown-item" href="<?= base_url() . $this->module['controller'] . "/documento/" . $this->uri->segment(3) . "/" . $d['documentos_id']; ?>"><?= ($documento['documentos_id'] == $d['documentos_id'] ? '<i class="fa fa-check"></i> ' : '<i style="padding-left:16px;"></i> ') . $direccion['direcciones_nombre']; ?> <span class="badge badge-primary badge-pill bg-red pull-right"><?= $d['documentos_id']; ?></span></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <a id="btn-regresar" class="btn btn-default m-l-2" href="<?= base_url() . $this->uri->segment(1) . "/" . $auditoria['auditorias_id']; ?>#documentos">Regresar</a>
                    <a id="btn-eliminar" class="btn btn-danger m-l-2 actualizar_id <?= $hidden; ?>" href="<?= base_url() . "Documentos/eliminar" . (isset($documento['documentos_id']) ? '/' . $documento['documentos_id'] : ''); ?>">Eliminar</a>
                </div>
                <div id="oficio-hoja" class="<?= $documento_autorizado ? 'autorizado' : ''; ?>">
                    <?php $r = isset($documento['valores']) && !empty($documento['valores']) && $accion !== "nuevo" ? $documento['valores'] : NULL; ?>
                    <div class="watermark">PARA REVISIÓN</div>
                    <table>
                        <thead>
                            <tr>
                                <td>
                                    <?php if ($documento_autorizado || $accion === "descargar"): ?>
                                        <div class="text-xs-center m-b-1">
                                            <img src="<?= base_url() . "resources/imagen_institucional/" . $documento['logotipos_header_archivo']; ?>">
                                        </div>
                                    <?php else: ?>
                                        <select name="headers_id" id="headers_id" class="ddslick">
                                            <?php foreach ($logotipos as $l): ?>
                                                <option value="<?= $l['logotipos_id']; ?>" data-imagesrc="<?= base_url() . "resources/imagen_institucional/" . $l['logotipos_header_archivo']; ?>" <?= (!empty($documento) && $l['logotipos_is_activo'] == 1) || (isset($documento['documentos_logotipos_id']) && $l['logotipos_id'] == $documento['documentos_logotipos_id']) ? 'selected="selected"' : ''; ?>></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </thead>
                        <tbody id="oficio-body">
                            <tr>
                                <td>
                                    <p class="text-right text-sm-right">
                                        <?php $hoy = date('Y-m-d'); ?>
                                        <?php $fechaDelOficio = isset($r) && isset($r[RESOL_AMPLI_FECHA]) ? $r[RESOL_AMPLI_FECHA] : $hoy; ?>
                                        Mérida, Yucatán, a <a href="#" class="xeditable" id="<?= RESOL_AMPLI_FECHA; ?>" data-pk="<?= RESOL_AMPLI_FECHA; ?>" data-type="date" data-placement="left" data-format="yyyy-mm-dd" data-title="Fecha del oficio" title="Fecha de emisión del oficio" data-value="<?= $fechaDelOficio; ?>"><?= mysqlDate2Date($fechaDelOficio); ?></a><br>
                                        OFICIO: No. <?= $auditoria['auditorias_areas_siglas']; ?>/<?= $auditoria['auditorias_segundo_periodo'] == 0 ? '2-' : ''; ?><span contenteditable="true" id="<?= RESOL_AMPLI_NUMERO; ?>" class="editable" title="El número consecutivo de Orden" default-value="XXX"><?= isset($r) ? $r[RESOL_AMPLI_NUMERO] : ''; ?></span>/<?= $auditoria['auditorias_anio']; ?><br>
                                        ASUNTO: RESOLUCIÓN DE AMPLIACIÓN DE PLAZO<br>
                                        CLASIFICACIÓN: RS
                                    </p>
                                    <p class="text-left text-sm-left texto-resaltar">
                                        <?= $oficio_para['nombre']; ?><br>
                                        <?= $oficio_para['cargo']; ?><br>
                                        PRESENTE
                                        <input type="hidden" name="constantes[<?= RESOL_AMPLI_ID_UA; ?>]" value="<?= isset($r[RESOL_AMPLI_ID_UA]) && !empty($r[RESOL_AMPLI_ID_UA]) ? $r[RESOL_AMPLI_ID_UA] : $oficio_para['direcciones_id']; ?>">
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        En virtud del oficio No.
                                        <?= span_editable($r, RESOL_AMPLI_NUMERO_OFICIO_RECIBIDO); ?>
                                        de fecha
                                        <?= span_calendario($r, RESOL_AMPLI_FECHA_OFICIO_RECIBIDO); ?>,
                                        recibido
                                        <?= span_editable($r, RESOL_AMPLI_LUGAR_FECHA_OFICIO_RECIBIDO); ?>,
                                        mediante el cual solicita
                                        <?= span_editable($r, RESOL_AMPLI_DIAS_SOLICITADOS); ?>
                                        días de ampliación de tiempo para poner a disposición la información preliminar para efectos del desarrollo de
                                        la auditoría, requerida en el Anexo de la Orden de Auditoría
                                        <?= span_resaltar($auditoria['numero_auditoria']); ?>,
                                        al respecto me permito informarle lo siguiente:
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        De conformidad con lo establecido en el penúltimo párrafo del artículo 112 de la Ley de Responsabilidades
                                        Administrativas del Estado de Yucatán, esta Unidad de Contraloría determina que
                                        <?= span_editable($r, RESOL_AMPLI_ES_PROCEDENTE, 'sí/no'); ?>
                                        es procedente otorgarle un plazo de
                                        <?= span_editable($r, RESOL_AMPLI_DIAS_OTORGADOS, $r[RESOL_AMPLI_DIAS_SOLICITADOS]); ?>
                                        días hábiles para poner a disposición la documentación e información preliminar, siendo la fecha máxima
                                        de cumplimiento el día
                                        <?= span_editable($r, RESOL_AMPLI_FECHA_CUMPLIMIENTO); ?>.
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        Sin otro particular, hago propicia la ocasión para enviarle un cordial saludo.
                                    </p>
                                    <div class="salto-solo-si-es-necesario">
                                        <p class="texto-resaltar" style="margin-bottom: 2cm;">ATENTAMENTE</p>
                                        <div id="firma-titular-contraloria" class="texto-resaltar">
                                            <?= mb_strtoupper($oficio_de['nombre']); ?><br>
                                            <?= mb_strtoupper($oficio_de['cargo']); ?>
                                            <input type="hidden" name="constantes[<?= RAP_TITULAR_CONTRALORIA_ID_EMPLEADO; ?>]" value="<?= isset($r[RAP_TITULAR_CONTRALORIA_ID_EMPLEADO]) && !empty($r[RAP_TITULAR_CONTRALORIA_ID_EMPLEADO]) ? $r[RAP_TITULAR_CONTRALORIA_ID_EMPLEADO] : $oficio_de['empleados_id']; ?>">
                                        </div>
                                        <?php if (CONTRALORIA_MOSTRAR_MISION): ?>
                                            <div id="mision" class="texto-mision">
                                                <br><b>MISIÓN</b><br>
                                                <?= $this->CYSA_model->get_mision(); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="texto-ccp">
                                            C.c.p. <?php $ccp_texto_plantilla = $this->CYSA_model->get_ccp_template(); ?>
                                            <?= span_editable($r, RESOL_AMPLI_CCP, 'con copia para...', $ccp_texto_plantilla, NULL, NULL, TRUE); ?><br>
                                            Minutario<br>
                                            Expediente<br><br>
                                            <?= $this->Auditorias_model->get_siglas_de_empleados_para_documento_de_auditoria($auditoria['auditorias_auditor_lider'], $auditoria['auditorias_id']); ?><br><br>
                                            <?= $documento['documentos_versiones_prefijo_iso'] . $documento['documentos_versiones_codigo_iso'] . " " . $documento['documentos_versiones_numero_iso']; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <?php if ($documento_autorizado || $accion === "descargar"): ?>
                                        <div class="text-xs-center m-t-1">
                                            <img src="<?= base_url() . "resources/imagen_institucional/" . $documento['logotipos_footer_archivo']; ?>">
                                        </div>
                                    <?php else: ?>
                                        <select name="footers_id" id="footers_id" class="ddslick">
                                            <?php foreach ($logotipos as $l): ?>
                                                <option value="<?= $l['logotipos_id']; ?>" data-imagesrc="<?= base_url() . "resources/imagen_institucional/" . $l['logotipos_footer_archivo']; ?>" <?= (!empty($documento) && $l['logotipos_is_activo'] == 1) || (isset($documento['documentos_logotipos_id']) && $l['logotipos_id'] == $documento['documentos_logotipos_id']) ? 'selected="selected"' : ''; ?>></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
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