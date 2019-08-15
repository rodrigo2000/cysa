<?php
$txt_documentacion_recibida = "Documentación Recibida:
<br>1. Organigrama vigente.
<br>2. Descriptivas y/o manuales vigentes autorizados de los procedimientos aplicados para (objeto de la auditoría)
<br>3. Reglamento interior.
<br>4. Programa Operativo Anual.
<br>5. Copia de la Reglamentación vigente aplicable.
<br>6. Otros (información adicional requerida para la auditoría).";
$direcciones = array();
$direcciones_id = $auditoria['auditorias_direcciones_id'];
$ubicacion = $this->SAC_model->get_direccion($direcciones_id);
$generos = array('la', 'el');
if (empty($asistencias) || empty($asistencias[$direcciones_id]) || empty($asistencias[$direcciones_id][TIPO_ASISTENCIA_INVOLUCRADO])) {
    $direcciones_id = $auditoria['auditorias_direcciones_id'];
    $e = $this->SAC_model->get_director_de_ua($direcciones_id, $auditoria['cc_periodos_id']);
    $asistencias[$direcciones_id] = array(TIPO_ASISTENCIA_INVOLUCRADO => array($e));
    $documentos[$index]['asistencias'] = $asistencias;
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
                    <div id="oficio-hoja" class="acta <?= $documento_autorizado ? 'autorizado' : ''; ?>">
                        <?php $r = isset($documento['valores']) && !empty($documento['valores']) && $accion !== "nuevo" ? $documento['valores'] : NULL; ?>
                        <div class="watermark">PARA REVISIÓN</div>
                        <table>
                            <thead>
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <img src="<?= APP_SAC_URL; ?>resources/images/logo-icon.png" alt=""/>
                                            </div>
                                            <div class="col-xs-6 text-xs-center">
                                                <div style="font-size: 15pt; font-weight: bold;">AYUNTAMIENTO DE MÉRIDA</div>
                                                <div><?= LABEL_CONTRALORIA; ?></div>
                                                <br>
                                                <div>ACTA DE CIERRE DE ENTREGA DE INFORMACIÓN</div>
                                            </div>
                                            <div class="col-xs-3"></div>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="oficio-body">
                                <tr>
                                    <td>
                                        <?php $fecha_acto_inicio = isset($r) && isset($r[FECHA_INI]) ? $r[FECHA_INI] : date('Y-m-d'); ?>
                                        <p class="text-justify bg-punteado">
                                            <span class=" bg-white">
                                                En la ciudad de Mérida, capital del Estado de Yucatán, Estados Unidos Mexicanos, siendo las
                                                <?= span_editable($r, HORA_INI, 'HH:MM') ?>
                                                horas del día
                                                <?= span_calendario($r, FECHA_INI); ?>,
                                                reunidos en
                                                <?= span_editable($r, UBICACION, implode(" ", array($generos[intval($ubicacion['tipos_ua_genero'])], $ubicacion['tipos_ua_nombre'], "de", $ubicacion['direcciones_nombre']))); ?>,
                                                ubicada en
                                                <?= span_editable($r, DIRUBICACION, 'calle __ número ___ por __ y ___'); ?>;
                                                por
                                                <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO); ?>
                                                y por la <?= LABEL_CONTRALORIA; ?>
                                                <span class="resaltar"><?= $auditoria['empleados_nombre_titulado'] . ", Auditor Líder"; ?></span>,
                                                así como
                                                <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_TESTIGO); ?>
                                                <span id="seccion_testigos_2">
                                                    est<singular>e</singular><plural>os</plural> último<plural>s</plural> en calidad de testigo<plural>s</plural>
                                                </span>
                                                <span class="bg-white">
                                                    a efecto de hacer constar el vencimiento de los plazos acordados entre el auditor líder y la unidad administrativa
                                                    sujeta a revisión para la entrega de la documentación e información correspondiente a la auditoría
                                                    <?= span_resaltar($auditoria['numero_auditoria']); ?>
                                                    que tiene por objetivo
                                                    <?= span_resaltar($auditoria['auditorias_objetivo']); ?>;
                                                    y toda vez que
                                                    <span id="<?= CUMPLE_ENTREGA; ?>" name="constantes[<?= CUMPLE_ENTREGA; ?>]" class="resaltar opciones" default-value="se dio total" data-opciones="se dio total|se dio parcial|no se dio"><?= isset($r) && isset($r[CUMPLE_ENTREGA]) && !empty($r[CUMPLE_ENTREGA]) ? $r[CUMPLE_ENTREGA] : ''; ?></span>
                                                    cumplimiento a la solicitud de documentación e información preliminar anexo en el oficio No.
                                                    <?= span_resaltar($auditoria['numero_auditoria']); ?>
                                                    emitida por la Unidad de Contraloría Municipal, con este documento se efectúa el cierre del plazo otorgado
                                                    para la recepción de la información de la auditoría referida, dejándose constancia que con fecha
                                                    <?php $fecha_cumplimiento = isset($r) && isset($r[FECHA_ENTREGAS]) ? $r[FECHA_ENTREGAS] : date('Y-m-d'); ?>
                                                    <?= span_calendario($r, FECHA_ENTREGAS); ?>,
                                                    <?= span_editable($r, UBICACION, $auditoria['nombre_completo_direccion']); ?>,
                                                    <span class="si_no <?= isset($r) && isset($r[CUMPLE_ENTREGA]) && $r[CUMPLE_ENTREGA] !== "no se dio" ? 'hidden-xs-up' : ''; ?>">no</span>
                                                    entregó la siguiente documentación e información:
                                                </span>
                                            </span>
                                        </p>
                                        <div id="<?= ACEI_PARRAFO_U2; ?>" contenteditable="true" class="editable span" aceptar-enter="1" default-value="<?= $txt_documentacion_recibida; ?>">
                                            <?= isset($r) && isset($r[ACEI_PARRAFO_U2]) && !empty($r[ACEI_PARRAFO_U2]) ? nl2br($r[ACEI_PARRAFO_U2]) : ''; ?>
                                        </div>
                                        <?php $parrafo1 = 'Por lo que la auditoría se delimitará únicamente a la documentación e información presentada oportunamente y descrita con anterioridad.'; ?>
                                        <?= agregar_parrafo_show_hide($r, OMITE_PARRAFO1, $parrafo1, 'Agregar párrafo 1'); ?>
                                        <?php $parrafo2 = 'Se hace del conocimiento del/los involucrado(s) que la falta de respuesta oportuna al requerimiento de documentación hecha por la Unidad de Contraloría, podría derivar en alguna responsabilidad por incumplimiento de las obligaciones establecidas en los términos de la Ley de Gobierno de los Municipios del Estado de Yucatán y la Ley de Responsabilidades Administrativa del Estado de Yucatán.'; ?>
                                        <?= agregar_parrafo_show_hide($r, OMITE_PARRAFO2, $parrafo2, 'Agregar párrafo 2'); ?>
                                        <?php $parrafo3 = 'Se apercibe a los servidores públicos que intervinieron en esta diligencia que la omisión o negativa de firma no afecta la validez y efectos legales de la presente acta.'; ?>
                                        <p class="text-justify bg-punteado">
                                            <span class=" bg-white">
                                                Leída la presente en presencia de todos los que en ella intervinieron se da por terminada la diligencia siendo las
                                                <?= span_editable($r, HORA_FIN, 'HH:MM'); ?>
                                                horas del mismo día de su inicio, firmándose de conformidad como debida aceptación y constancia.
                                            </span>
                                        </p>
                                        <p class="text-xs-center firmas_firmas">FIRMAS</p>
                                        <div class="firmas">
                                            <div class="firmas_involucrados">
                                                <?php foreach ($documento['asistencias'] as $direcciones_id => $d): $direccion = $this->SAC_model->get_direccion($direcciones_id); ?>
                                                    <?php if (isset($d[TIPO_ASISTENCIA_INVOLUCRADO])): ?>
                                                        <div class="direccion_<?= $direcciones_id; ?>">
                                                            <p class="firmas_ua_nombre"><?= $direccion['direcciones_nombre']; ?></p>
                                                            <?php foreach ($d[TIPO_ASISTENCIA_INVOLUCRADO] as $e): ?>
                                                                <div class="firmas_empleado empleado_<?= $e['empleados_id']; ?>">
                                                                    <div class="firmas_empleado_nombre"><?= $e['empleados_nombre_titulado_siglas']; ?></div>
                                                                    <div class="firmas_empleado_cargo"><?= $e['empleados_cargo']; ?></div>
                                                                    <div class="firmas_empleado_enlace">ENLACE DESIGNADO</div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <?php $auditor_lider = $this->SAC_model->get_empleado($auditoria['auditorias_auditor_lider']); ?>
                                                <p class="firmas_ua_nombre"><?= $auditor_lider['direcciones_nombre']; ?></p>
                                                <div class="firmas_empleado">
                                                    <div class="firmas_empleado_nombre"><?= $auditor_lider['empleados_nombre_titulado_siglas']; ?></div>
                                                    <div class="firmas_empleado_cargo">Auditor Líder</div>
                                                </div>
                                            </div>
                                            <div class="firmas_testigos">
                                                <p class="firmas_ua_nombre">TESTIGOS</p>
                                                <?php foreach ($documento['asistencias'] as $direcciones_id => $d): ?>
                                                    <?php if (isset($d[TIPO_ASISTENCIA_TESTIGO])): ?>
                                                        <?php foreach ($d[TIPO_ASISTENCIA_TESTIGO] as $e): ?>
                                                            <div class="firmas_empleado empleado_<?= $e['empleados_id']; ?>">
                                                                <div class="firmas_empleado_nombre"><?= $e['empleados_nombre_titulado_siglas']; ?></div>
                                                                <div class="firmas_empleado_cargo"><?= $e['empleados_cargo']; ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <div>ESTA FOJA FORMA PARTE INTEGRANTE DEL ACTA INICIO DE AUDITOR&Iacute;A DE <?= $texto_foja; ?></div>
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
            </form>
        <?php endif; ?>
        <div class="text-xs-center hidden-print oficio-menu-opciones">
            <?php $this->load->view('documentos/menu_opciones'); ?>
        </div>
    </div>
</div>
<script src="<?= APP_CYSA_URL; ?>resources/scripts/auditorias_documentos_acei.js" type="text/javascript"></script>
