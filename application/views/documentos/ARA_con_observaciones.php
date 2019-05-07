<?php
$sinEspecificar = '<b>[SIN ESPECIFICAR]</b>';
$fecha_notificacion_OE = NULL;
$fecha_cumplimiento = NULL;

$OA = $this->Documentos_model->get_documentos_de_auditoria($auditoria['auditorias_id'], TIPO_DOCUMENTO_ORDEN_AUDITORIA);
foreach ($OA as $oa) {
    if (isset($oa['documento_is_aprobado']) && $oa['documentos_is_aprobado'] == 1 && isset($oa['valores'])) {
        $fecha_notificacion_OE = $oa['valores'][ORD_ENT_FECHA_VISITA];
        $fecha_cumplimiento = $oa['valores'][ORD_ENT_FECHA_SI];
    }
}
$RAP = $this->Documentos_model->get_documentos_de_auditoria($auditoria['auditorias_id'], TIPO_DOCUMENTO_RESOLUCION_AMPLIACION_PLAZO);
foreach ($RAP as $rap) {
    if (isset($rap['documento_is_aprobado']) && $rap['documento_is_aprobado'] == 1 && isset($rap['valores'])) {
        $fecha_cumplimiento = $rap['valores'][RESOL_AMPLI_FECHA_CUMPLIMIENTO];
    }
}
$texto_foja = "";
$direcciones = array();
$asistencias = $documentos[$index]['asistencias'];
foreach ($asistencias as $direcciones_id => $d) {
    if (isset($d[TIPO_ASISTENCIA_INVOLUCRADO])) {
        $aux = $this->SAC_model->get_direccion($direcciones_id);
        array_push($direcciones, $aux['nombre_completo_direccion']);
    }
}
if (count($direcciones) > 1) {
    $ultimo = array_pop($direcciones);
    $texto_foja = implode(", ", $direcciones) . " y " . $ultimo;
} else {
    $texto_foja = implode(", ", $direcciones);
}
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
                                        <span class=" bg-white">
                                            En la ciudad de Mérida, capital del Estado de Yucatán, Estados Unidos Mexicanos con fundamento en lo dispuesto
                                            en el tercer párrafo del artículo 113 de la Ley de Responsabilidades Administrativas del Estado de Yucatán, siendo las
                                            <?= span_editable($r, ACTA_RESULTADOS_HORA_INI, 'HH:MM'); ?>
                                            horas del día
                                            <?= span_calendario($r, ACTA_RESULTADOS_FECHA) ?>,
                                            se reúnen en
                                            <?= span_editable($r, ACTA_RESULTADOS_UBICACION, 'Lugar donde se realiza la lectura') ?>,
                                            ubicada en
                                            <?= span_editable($r, ACTA_RESULTADOS_DIRUBICACION, 'calle ___ número ___ por __ y __'); ?>;
                                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO); ?>,
                                            por la <?= LABEL_CONTRALORIA; ?>
                                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA) ?>
                                            y en calidad de testigos 
                                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_TESTIGO) ?>, a efecto de dar a conocer el resultado de la auditoría
                                            <?= span_resaltar($auditoria['numero_auditoria']); ?>
                                            que tiene por objetivo
                                            <?= span_resaltar($auditoria['auditorias_objetivo']); ?>,
                                            iniciada el
                                            <?= span_resaltar(mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_real'])) . ($auditoria['auditorias_tipo'] == 1 ? " de acuerdo al Programa Anual de Auditoría de la " . LABEL_CONTRALORIA : '') . ", "; ?>
                                            obteniéndose lo siguiente:
                                        </span>
                                    </p>
                                    <p class="text-xs-center bg-punteado">
                                        <span class="bg-white" style="padding-left:5px; padding-right: 5px; font-weight: bolder;">RESULTADOS DE AUDITORÍA</span>
                                    </p>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th class="text-xs-center">Cédulas número</th>
                                            <th class="text-xs-center">Recomendación número</th>
                                            <th class="text-xs-center">Responsable</th>
                                            <th class="text-xs-center">Clasificación</th>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </table>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            Se anexa(n) copia(s) de la(s) cédula(s) de observación(es) en la(s) cual(es) se detalla(n) entre otros, los hallazgos, recomendación(es) correctiva(s) y preventiva(s), misma(s) que forman parte integrante de la presente.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            Se hace del conocimiento al Titular de la Unidad Administrativa auditada y de los servidores públicos responsables de solventar, el contenido de las cédulas de observaciones, según el artículo 114 de la Ley de Responsabilidades Administrativas del Estado de Yucatán.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            El (los) responsable de solventar la(s) observación(es) planteada(s) tendrá un plazo de (12) días hábiles, contados a partir del día hábil siguiente a la notificación del Acta de Resultados de Auditoría para poner a disposición de la Unidad de Contraloría en el domicilio indicado al inicio de la presente, la documentación e información que solvente lo observado.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            Para efectos del proceso de solventación, el auditor citará a los responsables de solventar a partir del día (13)[treinta y seis] y hasta el día (13)[treinta y nueve] posterior al día hábil siguiente a la notificación del Acta de Resultados de Auditoría, a fin que le proporcionen la documentación e información que acredite los avances de solventación.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            (14)[El servidor público (11) quien manifiesta ser de nacionalidad mexicana y con domicilio particular en (16), se identifica con (15), (el/la) cual contiene su nombre y fotografía que concuerda con sus rasgos fisonómicos y en (el/la) que se aprecia su firma, que reconoce como suya por ser  la misma que utiliza para validar todos sus actos tanto públicos como privados, con relación a los resultados de la auditoría que le fueron dados a conocer, declara:(18).]
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            (19)[ACLARACIONES]
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            Habiendo escuchado al (a los) responsable(s) de solventar la(s) observación(es), se le(s) notifica, que deberá(n) entregar la documentación e información que solvente la(s) observación(es) dentro del plazo establecido, advirtiéndole(s) que su incumplimiento podrá derivar en faltas administrativas e incluso hacerse acreedor(es) a las sanciones estipuladas en la Ley de Gobierno de los Municipios del Estado de Yucatán y la Ley de Responsabilidades Administrativas del Estado de Yucatán.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            Se le comunica al Titular de la Unidad Administrativa auditada que por razones debidamente justificadas, podrá solicitar ampliación al plazo originalmente otorgado por una sola ocasión, dentro de los primeros (20) días hábiles del plazo otorgado para solventar la(s) observación(es).
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            (21)[Se hace del conocimiento de los presentes que la omisión o negativa de firma no afecta la validez y efectos legales de la presente acta].
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            Leída la presente acta y no habiendo más hechos que plasmar se da por concluida la diligencia siendo las
                                            <?= span_editable($r, ACTA_RESULTADOS_HORA_FIN, 'HH:MM'); ?>
                                            horas de la misma fecha en que fue iniciada. Asimismo, previa lectura de lo asentado, los que en ella intervinieron la firman al margen y al calce de todas y cada una de las fojas, haciéndose constar que este documento fue elaborado en (23) ejemplares originales, de los cuales se hace entrega de uno al servidor público con quien se entendió la diligencia.
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
            </form>
        <?php endif; ?>
    </div>
</div>
<script src="<?= APP_CYSA_URL; ?>resources/scripts/auditorias_documentos_ara.js" type="text/javascript"></script>