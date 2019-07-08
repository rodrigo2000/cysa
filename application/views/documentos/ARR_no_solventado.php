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
                <div id="oficio-menu-opciones" class="text-xs-center m-b-1 hidden-print">
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
                                                <div>ACTA DE RESULTADOS DE AUDITORÍA</div>
                                            </div>
                                            <div class="col-xs-3"></div>
                                        </div>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="oficio-body">
                                <tr>
                                    <td>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                En la ciudad de Mérida, capital del Estado de Yucatán, Estados Unidos Mexicanos con fundamento en lo dispuesto
                                                en el tercer párrafo del artículo 113 de la Ley de Responsabilidades Administrativas del Estado de Yucatán, siendo las
                                                <?= span_editable($r, ACTA_RESULTADOS_HORA_INI, 'HH:MM', $tooltiptext, $descripciones); ?>
                                                horas del día
                                                <?= span_calendario($r, ACTA_RESULTADOS_FECHA) ?>,
                                                se reúnen en
                                                <?= span_editable($r, ACTA_RESULTADOS_UBICACION, 'Lugar donde se realiza la lectura', $tooltiptext, $descripciones) ?>,
                                                ubicada en
                                                <?= span_editable($r, ACTA_RESULTADOS_DIRUBICACION, 'Domicilio de la ubicación donde se realiza la lectura', $tooltiptext, $descripciones); ?>;
                                                <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO); ?>,
                                                por la <?= LABEL_CONTRALORIA; ?>
                                                <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA) ?>
                                                y en calidad de testigos
                                                <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_TESTIGO) ?>,
                                                a efecto de dar a conocer el resultado de la auditoría
                                                <?= span_resaltar($auditoria['numero_auditoria'], 'Número de la auditoría'); ?>
                                                que tiene por objetivo
                                                <?= span_resaltar($auditoria['auditorias_objetivo'], 'Objetivo de la auditoría'); ?>,
                                                <span class="bg-white">
                                                    iniciada el
                                                    <?= span_resaltar(mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_real']), 'Fecha de inicio de la auditoría') . ($auditoria['auditorias_tipo'] == 1 ? " de acuerdo al Programa Anual de Auditoría de la " . LABEL_CONTRALORIA : '') . ", "; ?>
                                                    obteniéndose lo siguiente:
                                                </span>
                                            </span>
                                        </p>
                                        <p class="text-xs-center bg-punteado">
                                            <span class="bg-white" style="padding-left:5px; padding-right: 5px; font-weight: bolder;">RESULTADO DE LA AUDITORÍA</span>
                                        </p>
                                        <table id="resultados_auditoria" class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th class="text-xs-center">Cédulas<br>número</th>
                                                    <th class="text-xs-center">Recomendación<br>número</th>
                                                    <th class="text-xs-center">Responsable</th>
                                                    <th class="text-xs-center">Clasificación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $servidores_publicos = array(); ?>
                                                <?php $contador_recomendaciones = 0; ?>
                                                <?php foreach ($auditoria['observaciones'] as $o): ?>
                                                    <?php $recomendaciones = $this->Auditorias_model->get_recomendaciones_de_observacion($o['observaciones_id']); ?>
                                                    <?php foreach ($recomendaciones as $recom): $contador_recomendaciones++; ?>
                                                        <?php $empleado = $this->SAC_model->get_empleado($recom['recomendaciones_empleados_id']); ?>
                                                        <?php $servidores_publicos[$empleado['empleados_id']] = $empleado; ?>
                                                        <tr>
                                                            <td class="text-xs-center"><?= $o['observaciones_numero']; ?></td>
                                                            <td class="text-xs-center"><?= $o['observaciones_numero'] . "." . $recom['recomendaciones_numero']; ?></td>
                                                            <td><?= $empleado['empleados_nombre_titulado_siglas'] . ", " . $empleado['empleados_cargo']; ?></td>
                                                            <td class="text-xs-center"><?= $recom['recomendaciones_clasificaciones_nombre']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <?php $plural = $contador_recomendaciones > 0 ? TRUE : FALSE; ?>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                Se anexa<?= $plural ? 'n' : ''; ?> copia<?= $plural ? 's' : ''; ?> de la<?= $plural ? 's' : ''; ?>
                                                cédula<?= $plural ? 's' : ''; ?> de observaci<?= $plural ? 'ones' : 'ón'; ?> en la<?= $plural ? 's' : ''; ?>
                                                cual<?= $plural ? 'es' : ''; ?> se detalla<?= $plural ? 'n' : ''; ?> entre otros,
                                                los hallazgos, recomendaci<?= $plural ? 'ones' : 'ón'; ?> correctiva<?= $plural ? 's' : ''; ?> y
                                                preventiva<?= $plural ? 's' : ''; ?>, misma<?= $plural ? 's' : ''; ?> que forman parte integrante de la
                                                presente.
                                            </span>
                                        </p>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                Se hace del conocimiento al Titular de la Unidad Administrativa auditada y de los servidores públicos
                                                responsables de solventar, el contenido de las cédulas de observaciones, según el artículo 114 de la Ley de
                                                Responsabilidades Administrativas del Estado de Yucatán.
                                            </span>
                                        </p>
                                        <p class="text-justify bg-punteado involucrados">
                                            <span class="bg-white">
                                                <singular>El</singular><plural>Los</plural> responsable de solventar la<plural>s</plural>
                                                observaci<singular>ón</singular><plural>ón</plural> planteada<plural>s</plural> tendrá un plazo de
                                                <?= span_editable($r, ACTA_RESULTADOS_DIAS_SOLVENTA, NULL, PLAZO_SOLV_AP); ?>
                                                días hábiles, contados a partir del día hábil siguiente a la notificación del Acta de Resultados de Auditoría
                                                para poner a disposición de la Unidad de Contraloría en el domicilio indicado al inicio de la presente,
                                                la documentación e información que solvente lo observado.
                                            </span>
                                        </p>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                Para efectos del proceso de solventación, el auditor citará a los responsables de solventar a partir del día
                                                <?= span_editable($r, ACTA_RES_DIAS_SOLVENTA_RESPON, NULL, PLAZO_SOLV_RES); ?>
                                                y hasta el día
                                                <?= span_editable($r, ACTA_RES_DIAS_SOLVENTA_RESPONSS, NULL, PLAZO_SOLV_RESPO); ?>
                                                posterior al día hábil siguiente a la notificación del Acta de Resultados de Auditoría, a fin que le
                                                proporcionen la documentación e información que acredite los avances de solventación.
                                            </span>
                                        </p>
                                        <?php foreach ($servidores_publicos as $sr): $declaracion = (isset($documento['documentos_id']) && !empty($documento['documentos_id']) ? $this->Asistencias_declaraciones_model->get_declaracion($documento['documentos_id'], $sr['empleados_id']) : NULL); ?>
                                            <p class="text-justify bg-punteado">
                                                <span class="bg-white">
                                                    El servidor público <?= span_resaltar($sr['empleados_nombre_titulado'] . ", " . $sr['empleados_cargo']); ?>
                                                    quien manifiesta ser de nacionalidad mexicana y con domicilio particular en
                                                    <?= span_resaltar($sr['empleados_domicilio']); ?>,
                                                    de la localidad de <?= span_resaltar($sr['empleados_localidad']); ?> se identifica con
                                                    <?= span_resaltar(get_identificacion($sr)); ?>,
                                                    la cual contiene su nombre y fotografía que concuerda con sus rasgos fisonómicos y en la que se aprecia su firma,
                                                    que reconoce como suya por ser la misma que utiliza para validar todos sus actos tanto públicos como privados,
                                                    con relación a los resultados de la auditoría que le fueron dados a conocer, declara:
                                                    <span id="<?= $sr['empleados_id']; ?>" name="declaraciones[]" contenteditable="true" class="editable" default-value="[TECLEAR LOS COMENTARIOS DEL FUNCIONARIO]"><?= !empty($declaracion) ? $declaracion : ''; ?></span>.
                                                </span>
                                            </p>
                                        <?php endforeach; ?>
                                        <?php $span = span_editable($r, ACTA_RESULTADOS_ACLARA_FUNC, '[ACLARACIONES]'); ?>
                                        <?= agregar_parrafo_show_hide($r, ACTA_RESULTADOS_ACLARA_FUNC, $span, 'Agregar párrafo de declaraciones'); ?>
                                        <p class="text-justify bg-punteado involucrados">
                                            <span class="bg-white">
                                                Habiendo escuchado <singular>al</singular><plural>a los</plural> responsable<plural>s</plural>
                                                de solventar la<plural>s</plural> observaci<singular>ón</singular><plural>s</plural>,
                                                se le<plural>s</plural> notifica, que deberá<plural>n</plural> entregar la documentación e
                                                información que solvente la<plural>s</plural> observaci<singular>ón</singular><plural>ones</plural>
                                                dentro del plazo establecido, advirtiéndole<plural>s</plural> que su incumplimiento podrá derivar en
                                                faltas administrativas e incluso hacerse acreedor<plural>es</plural> a las sanciones estipuladas en
                                                la Ley de Gobierno de los Municipios del Estado de Yucatán y la Ley de Responsabilidades Administrativas del
                                                Estado de Yucatán.
                                            </span>
                                        </p>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                Se le comunica al Titular de la Unidad Administrativa auditada que por razones debidamente justificadas,
                                                podrá solicitar ampliación al plazo originalmente otorgado por una sola ocasión, dentro de los primeros
                                                <?= span_editable($r, ACTA_REV_DIAS_PLAZO_OBS, 'Días de plazo para solventar la observación', '20'); ?>
                                                días hábiles del plazo otorgado para solventar la<?= $plural ? 's' : ''; ?> observaci<?= $plural ? 'ón' : 'ones'; ?>.
                                            </span>
                                        </p>
                                        <?php $txt_parrafo_omision = 'Se hace del conocimiento de los presentes que la omisión o negativa de firma no afecta la validez y efectos legales de la presente acta'; ?>
                                        <?= agregar_parrafo_show_hide($r, ACTA_RESULTADOS_OMI_FIR, $txt_parrafo_omision, 'Agregar párrafo de omisión de firmas'); ?>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                Leída la presente acta y no habiendo más hechos que plasmar se da por concluida la diligencia siendo las
                                                <?= span_editable($r, ACTA_RESULTADOS_HORA_FIN, 'HH:MM'); ?>
                                                horas de la misma fecha en que fue iniciada. Asimismo, previa lectura de lo asentado, los que en ella
                                                intervinieron la firman al margen y al calce de todas y cada una de las fojas, haciéndose constar que
                                                este documento fue elaborado en
                                                <?= span_editable($r, ACTA_RESULTADOS_NUMERO_EJEMPLARES, CONSTANTE_CANTIDAD_EJEMPLARES_ACTA); ?>
                                                ejemplares originales, de los cuales se hace entrega de uno al
                                                servidor público con quien se entendió la diligencia.
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
                <?php endif; ?>
            </form>
        <?php endif; ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        var mostrar = 1;
        plurales(mostrar, ".involucrados");
    });
</script>