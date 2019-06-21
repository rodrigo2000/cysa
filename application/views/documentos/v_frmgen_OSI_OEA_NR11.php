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
    .btn, span[type=button].label {
        display: initial; /* Corrigen un problema de espaciado en los botones */
    }
</style>
<div class="card">
    <div class="card-header no-bg1 b-a-0 hidden-print">
        <?php $this->load->view('auditoria/header_view'); ?>
    </div>
    <div class="card-block">
        <?php if (!empty($this->session->userdata(APP_NAMESPACE))) : ?>
            <?php echo validation_errors(); ?>
            <form id="frmOficios" name="frmOficios" class="<?= $documento_autorizado ? 'autorizado' : ''; ?><?= $accion === "descargar" ? ' impresion' : ''; ?>" method="post" action="<?= $urlAction; ?>">
                <div id="oficio-menu-opciones" class="text-xs-center m-b-1 hidden-print">
                    <?php $this->load->view('documentos/menu_opciones'); ?>
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
                                        <?php $fechaDelOficio = isset($r) && isset($r[FECHA_LECTURA_CITATORIO]) ? $r[FECHA_LECTURA_CITATORIO] : $hoy; ?>
                                        Mérida, Yucatán, a <a href="#" class="xeditable" id="<?= FECHA_LECTURA_CITATORIO; ?>" data-pk="<?= FECHA_LECTURA_CITATORIO; ?>" data-type="date" data-placement="left" data-format="yyyy-mm-dd" data-title="Fecha del oficio" title="Fecha de emisión del oficio" data-value="<?= $fechaDelOficio; ?>"><?= mysqlDate2Date($fechaDelOficio); ?></a><br>
                                        Oficio NO. <?= ($auditoria['auditorias_segundo_periodo'] == 1 ? '2' : '') . $auditoria['auditorias_areas_siglas']; ?>/<span contenteditable="true" id="<?= CITATORIO_OFICIO; ?>" class="editable" title="El número consecutivo de Orden" default-value="XXX"><?= isset($r) ? $r[CITATORIO_OFICIO] : ''; ?></span>/<?= $auditoria['auditorias_anio']; ?><br>
                                        Asunto: Citatorio<br>
                                        Clasificación: RS
                                    </p>
                                    <p class="text-left text-sm-left texto-resaltar">
                                        <?= $oficio_para['nombre']; ?><br>
                                        <?= $oficio_para['cargo']; ?><br>
                                        PRESENTE
                                        <input type="hidden" name="constantes[<?= CITATORIO_ID_UA; ?>]" value="<?= isset($r[CITATORIO_ID_UA]) && !empty($r[CITATORIO_ID_UA]) ? $r[CITATORIO_ID_UA] : $oficio_para['direcciones_id']; ?>">
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        Por este medio y con fundamento en el artículo 114 de la Ley de Responsabilidades Administrativas del Estado de Yucatán,
                                        me permito solicitar su asistencia para llevar a cabo el acto de notificación de resultados, firma y entrega del
                                        <span id="<?= DOCTO_LECTURA; ?>" contenteditable="true" class="editable" default-value="Acta de Resultados de Auditoría, Cédula de Observación (es), Acta de Resultados de Revisión"><?= isset($r[DOCTO_LECTURA]) ? $r[DOCTO_LECTURA] : ''; ?></span>,
                                        referente a la auditoría número
                                        <?= span_resaltar($auditoria['numero_auditoria']); ?>
                                        que tiene por objetivo
                                        <?= span_resaltar($auditoria['auditorias_objetivo']); ?>,
                                        realizada
                                        <?= span_resaltar(get_frase_de_ua($auditoria)); ?>,
                                        mismo que se llevará a cabo el
                                        <?php $aux = isset($r[FECHA_LECTURA_CITATORIO]) ? mysqlDate2OnlyDate($r[FECHA_LECTURA_CITATORIO]) : 'POR AGENDAR/CAPTURAR EN LÍNEA DE TIEMPO'; ?>
                                        <?= span_resaltar($aux); ?>
                                        a las
                                        <?= span_editable($r, H_LECTURA_CITATORIO, 'HH:MM') ?>
                                        horas, en
                                        <?= span_editable($r, CITATORIO_UBICACION_UA, "la Dirección de [Unidad Administrativa]"); ?>,
                                        ubicada en
                                        <?= span_editable($r, CITATORIO_UBICACION, 'la calle ___ número ___ por ___ y ___ (de la/del) __________ de esta ciudad de Mérida, Yucatán.'); ?>
                                    </p>
                                    <?php if (!empty($auditoria['auditorias_enlace_designado'])): ?>
                                        <p class="text-justify texto-sangria">
                                            Asimismo, se le informa que esta diligencia podrá ser atendida por el enlace designado, el cual fue nombrado al
                                            inicio de la auditoría en el Oficio número
                                            <?php $aux = !empty($auditoria['auditorias_folio_oficio_representante_designado']) ? $auditoria['auditorias_folio_oficio_representante_designado'] : SIN_ESPECIFICAR; ?>
                                            ,<?= span_resaltar($aux); ?>
                                            recibido el
                                            <?php $aux = !empty($auditoria['auditorias_fechas_sello_oficio_representante_designado']) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_sello_oficio_representante_designado']) : SIN_ESPECIFICAR; ?>
                                            <?= span_resaltar($aux); ?>.
                                        </p>
                                    <?php endif; ?>
                                    <?php
                                    $aux = "Mucho agradeceré se haga acompañar de los servidores públicos "
                                            . span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO, $auditoria)
                                            . ' responsable<plural>s</plural> de la solventación de la<plural>s</plural> observaci<singular>ón</singular><plural>ones</plural>.';
                                    ?>
                                    <?= agregar_parrafo_show_hide($r, ASISTENCIA_PUBLICA, $aux, 'Mostrar párrafos de involucrados'); ?>
                                    <?= agregar_parrafo_show_hide($r, CITATORIO_MOSTRAR_PARRAFO_4, 'Lo que le tengo a bien comunicar en vía de notificación para los efectos correspondientes.', 'Agregar párrafo'); ?>
                                    <p class="text-justify texto-sangria">
                                        Sin otro particular, hago propicia la ocasión para enviarle un cordial saludo.
                                    </p>
                                    <div class="salto-solo-si-es-necesario">
                                        <p class="texto-resaltar" style="margin-bottom: 2cm;">ATENTAMENTE</p>
                                        <div id="firma-titular-contraloria" class="texto-resaltar">
                                            <?= mb_strtoupper($oficio_de['nombre']); ?><br>
                                            <?= mb_strtoupper($oficio_de['cargo']); ?>
                                            <input type="hidden" name="constantes[<?= CITATORIO_ID_DIR_CONTRA; ?>]" value="<?= isset($r[CITATORIO_ID_DIR_CONTRA]) && !empty($r[CITATORIO_ID_DIR_CONTRA]) ? $r[CITATORIO_ID_DIR_CONTRA] : $oficio_de['empleados_id']; ?>">
                                        </div>
                                        <?php if (CONTRALORIA_MOSTRAR_MISION): ?>
                                            <div id="mision" class="texto-mision">
                                                <br><b>MISIÓN</b><br>
                                                <?= $this->CYSA_model->get_mision(); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="texto-ccp">
                                            C.c.p. <?php $ccp_texto_plantilla = $this->CYSA_model->get_ccp_template(); ?>
                                            <?= span_editable($r, CITATORIO_CCP, $ccp_texto_plantilla, NULL, NULL, TRUE); ?><br>
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