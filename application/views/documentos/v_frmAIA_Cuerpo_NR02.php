<?php
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
if (empty($asistencias)) {
    $asistencias[$auditoria['auditorias_direcciones_id']] = array(
        TIPO_ASISTENCIA_INVOLUCRADO => 0
    );
}
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
                                            <div>ACTA DE INICIO DE AUDITORÍA</div>
                                            <br>
                                            <div><?= $auditoria['numero_auditoria'] ?></div>
                                        </div>
                                        <div class="col-xs-3"></div>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody id="oficio-body">
                            <tr>
                                <td>
                                    <?php $enlace_designado = $auditoria['enlace_designado']; ?>
                                    <?php $fecha_acto_inicio = isset($r) && isset($r[ACTA_INICIO_FECHA_ACTO_INICIO]) ? $r[ACTA_INICIO_FECHA_ACTO_INICIO] : date('Y-m-d'); ?>
                                    <p class="text-justify bg-punteado">
                                        <span class="bg-white">En la ciudad de Mérida, capital del Estado de Yucatán, Estados Unidos Mexicanos,
                                            siendo las
                                            <?= span_editable($r, ACTA_INICIO_HORA_INI, 'HH:MM'); ?>
                                            horas del día
                                            <?= span_calendario($r, ACTA_INICIO_FECHA_ACTO_INICIO); ?>,
                                            con fundamento en lo dispuesto en los artículos 210 y 211 fracción II de la Ley de Gobierno de los Municipios del
                                            Estado de Yucatán, artículos 2 fracción XXI, 110, 111, 112 y 113 de la Ley de Responsabilidades Administrativas del
                                            Estado de Yucatán; se reúnen en la oficina que ocupa
                                            <?= span_editable($r, ACTA_INICIO_REUNIDOS_EN, 'ESPECIFICAR OFICINA'); ?>,
                                            ubicada en
                                            <?= span_editable($r, ACTA_INICIO_UBICACION, 'ESPECIFICAR UBICACIÓN'); ?>,
                                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO, $auditoria); ?>
                                            y por la <?= LABEL_CONTRALORIA; ?>
                                            <?= span_resaltar($auditoria['empleados_nombre_titulado']); ?>,
                                            Auditor Líder a efecto de formalizar el inicio de la auditoría número
                                            <?= span_resaltar($auditoria['numero_auditoria']); ?>
                                            que tiene por objetivo
                                            <?= span_resaltar($auditoria['auditorias_objetivo']); ?>,
                                            y cuyo inicio  fue notificado en fecha
                                            <?php $aux = !empty($auditoria['auditorias_fechas_inicio_real']) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_real']) : '<b>[CAPTURAR FECHA ORDEN AUDITORIA]</b>'; ?>
                                            <?= span_resaltar($aux); ?>,
                                            por Orden de Auditoría
                                            <?= span_resaltar($auditoria['numero_auditoria']); ?>,
                                            dirigido
                                            <?php
                                            $articulo = " al ";
                                            $director = $this->SAC_model->get_director_de_ua($auditoria['auditorias_direcciones_id']);
                                            if (!in_array($director['empleados_nombre_titulado'][0], array('A', 'E', 'I', 'O', 'U')) || $director['empleados_genero'] === GENERO_FEMENINO) {
                                                $articulo = " a la ";
                                            }
                                            ?>
                                            <?= span_resaltar($articulo . $director['empleados_nombre_titulado'] . ", " . $director['empleados_cargo']); ?>.
                                        </span>
                                    </p>
                                    <p class="text-xs-center bg-punteado"><span class="bg-white">HECHOS</span></p>
                                    <!-- Opción cuando no acudan a la diligencia los servidores públicos citados -->
                                    <div id="checkbox_asistencia" class="text-xs-center alert alert-info hidden-print">
                                        <input type="checkbox" id="chkAsistencia" name="constantes[<?= ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS ?>]" value="1" <?= isset($r[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS]) && $r[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS] == 1 ? 'checked="checked"' : ""; ?>>
                                        <label for="chkAsistencia"> Los funcionarios públicos asistieron a la lectura del acta</label>
                                    </div>
                                    <div id="noAsistencia" style="<?= isset($r[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS]) && $r[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS] == 1 ? 'display:none;' : ''; ?>">
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                Estando presente el
                                                <?= span_resaltar($auditoria['empleados_nombre_titulado']); ?>, Auditor Líder,
                                                hace constar que,
                                                <?php $aux = ($director['empleados_genero'] == GENERO_FEMENINO ? ' la ' : ' el ') . $director['empleados_nombre_titulado'] . ", " . $director['empleados_cargo']; ?>
                                                <?= span_resaltar($aux); ?>
                                                Titular de la Unidad Administrativa sujeta a auditaría,
                                                <?php $aux = !empty($auditoria['auditorias_enlace_designado']) ? $enlace_designado['empleados_nombre_titulado'] . ', Enlace Designado, ' : ''; ?>
                                                <?= span_resaltar($aux); ?>
                                                y los testigos
                                                <?= span_editable($r, ACTA_INICIO_TESTIGOS_NO_ASISTIERON, '[Título, nombre y cargo de los servidores públicos nombrados para intervenir como testigo en el acta de inicio de la auditoría, los cuales no asistieron al acto de inicio de auditoría]'); ?>,
                                                no se presentaron en ésta diligencia de inicio de auditoría, no obstante de haber hecho de conocimiento previamente
                                                mediante la Orden de Auditoría
                                                <?= span_resaltar($auditoria['numero_auditoria']); ?>
                                                recepcionado el
                                                <?php $aux = !empty($fecha_notificacion_OE) ? mysqlDate2OnlyDate($fecha_notificacion_OE) : SIN_ESPECIFICAR; ?>
                                                <?= span_resaltar($aux); ?>.
                                                Sirva el presente para manifestar que la omisión anterior podrá ser causal de responsabilidad prevista en el
                                                Ley de Responsabilidades Administrativas del Estado de Yucatán.
                                            </span>
                                        </p>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                Derivado de lo anterior, el suscrito
                                                <?= span_resaltar($auditoria['empleados_nombre_titulado']); ?>, Auditor Líder,
                                                nombra a los servidores públicos
                                                <?= crear_texto_asistencias($documento['asistencias'], FALSE, TIPO_ASISTENCIA_TESTIGO, TRUE); ?>,
                                                en calidad de testigos a fin de dejar constancia de la presente diligencia, quienes se comprometen a entregar
                                                un ejemplar del Acta de Inicio de Auditoría al titular de la unidad administrativa sujeta a auditaría.
                                            </span>
                                        </p>
                                    </div>
                                    <div id="siAsistencia" style="<?= isset($r[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS]) && $r[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS] == 1 ? '' : 'display:none;'; ?>">
                                        <?php if (empty($auditoria['auditorias_enlace_designado'])): ?>
                                            <!-- Opción 1 -->
                                            <p class="text-justify bg-punteado">
                                                <span class="bg-white">
                                                    El servidor público <?= Capitalizar($datosDirector['descTitFunc'] . " " . $datosDirector['nomFunc'] . ", " . $datosDirector['puestoFunc']); ?>, quien manifiesta ser de nacionalidad mexicana y
                                                    con domicilio particular en <?= $datosDirector['domAct']; ?>,  se identifica con <?= $datosDirector['docId']; ?> con clave de elector
                                                    <?= $datosDirector['credEl']; ?> y número de identificador <?= isset($datosDirector['credIden']) ? $datosDirector['credIden'] : SIN_ESPECIFICAR; ?>, la cual contiene su nombre y fotografía que
                                                    concuerda con sus rasgos fisonómicos y en la que se aprecia su firma, que reconoce como suya por ser la misma
                                                    que utiliza para validar todos sus actos tanto públicos como privados.
                                                </span>
                                            </p>
                                        <?php else: ?>
                                            <!-- Opcion 2 -->
                                            <p class="text-justify bg-punteado">
                                                <span class="bg-white">
                                                    El servidor público
                                                    <?= span_resaltar($enlace_designado['empleados_nombre_titulado']); ?>,
                                                    manifiesta que ha sido designado por el titular
                                                    de la unidad administrativa auditada para atender la presente auditoría mediante oficio número
                                                    <?php $aux = !empty($auditoria['auditorias_folio_oficio_representante_designado']) ? $auditoria['auditorias_folio_oficio_representante_designado'] : SIN_ESPECIFICAR; ?>
                                                    <?= span_resaltar($aux); ?>,
                                                    notificada a la <?= LABEL_NOMBRE_CONTRALORIA; ?> el
                                                    <?php $aux = !empty($auditoria['auditorias_fechas_sello_oficio_representante_designado']) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_sello_oficio_representante_designado']) : SIN_ESPECIFICAR; ?>
                                                    <?= span_resaltar($aux); ?>,
                                                    ser de nacionalidad mexicana y con domicilio particular en
                                                    <?= $enlace_designado['empleados_domicilio'] . " de la localidad de " . (!empty($enlace_designado['empleados_localidad']) ? Capitalizar($enlace_designado['empleados_localidad']) : SIN_ESPECIFICAR); ?>,
                                                    se identifica con
                                                    <?php
                                                    $identificacion = SIN_ESPECIFICAR;
                                                    if (!empty($enlace_designado['empleados_credencial_elector_delante'])) {
                                                        $identificacion = ' credencial para votar con clave de elector ' . $enlace_designado['empleados_credencial_elector_delante'] . ' y número identificador ' . (!empty($enlace_designado['empleados_credencial_elector_detras']) ? $enlace_designado['empleados_credencial_elector_detras'] : SIN_ESPECIFICAR);
                                                    } elseif (!empty($enlace_designado['empleados_licencia_manejo'])) {
                                                        $identificacion = " licencia de conducir con folio " . $enlace_designado['empleados_licencia_manejo'];
                                                    }
                                                    ?>
                                                    <?= span_resaltar($identificacion); ?>,
                                                    la cual contiene su nombre y fotografía que concuerda con sus rasgos fisonómicos y en la que se aprecia su firma,
                                                    que reconoce como suya por ser la misma que utiliza para validar todos sus actos tanto públicos como privados.
                                                </span>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-justify bg-punteado">
                                        <span class="bg-white">
                                            En el presente acto los testigos de asistencia
                                            <span id="seccion_testigos">
                                                <?php
                                                echo crear_texto_asistencias(
                                                        $documento['asistencias'], // Asistencias
                                                        FALSE, // distribuido
                                                        TIPO_ASISTENCIA_TESTIGO, // Tipo de asistencia
                                                        FALSE, // solo nombre
                                                        TRUE, // incluir domicilio
                                                        TRUE, // incluir articulo
                                                        $enlace_designado['empleados_id'], // Enlace designado
                                                        NULL // Post Texto
                                                );
                                                echo genera_boton_autocomplete(TIPO_ASISTENCIA_TESTIGO);
                                                ?>
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class="bg-white">
                                            De igual forma,
                                            <?php $aux = ($auditoria['empleados_genero'] === GENERO_FEMENINO ? ' la ' : ' el ') . $auditoria['empleados_nombre_titulado']; ?>
                                            <?= span_resaltar($aux); ?>,
                                            se identifica con credencial vigente que lo acredita como servidor público del Municipio de Mérida, expedida
                                            por la Dirección de Administración y en la que se exhiben sus datos personales, la cual se pone a la vista
                                            de las personas que intervienen en el presente acto.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class="bg-white">
                                            Acto seguido, se declara que a partir de la fecha en que se notificó el Oficio de Orden de Auditoría dieron
                                            inicio los trabajos de la auditoría número
                                            <?= span_resaltar($auditoria['numero_auditoria']); ?>. En dicho documento se establece el objetivo y se notifica el
                                            equipo de auditoría que participará en la auditoría. Adicionalmente, con relación al requerimiento de
                                            documentación e información preliminar formulada en el Anexo del Oficio de Orden de Auditoría, se establece
                                            que ésta deberá entregarse el
                                            <?php $aux = !empty($fecha_cumplimiento) ? mysqlDate2OnlyDate($fecha_cumplimiento) : SIN_ESPECIFICAR; ?>
                                            <?= span_resaltar($aux); ?>
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class="bg-white">
                                            Por otro lado, de requerirse durante la práctica de la auditoría documentación e información no contenida en
                                            el requerimiento preliminar, será solicitada por escrito de manera fundada y motivada, la cual deberá
                                            proporcionarse dentro de los cinco días hábiles contados a partir del día siguiente de notificada la solicitud.
                                        </span>
                                    </p>
                                    <p id="seccion_testigos_2" class="text-justify bg-punteado">
                                        <span class="bg-white">
                                            <!-- Opcional -->
                                            Por último, <span class="singular">el</span><span class="plural">los</span> servidor<span class="plural">es</span> público<span class="plural">s</span>
                                            <?=
                                            crear_texto_asistencias(
                                                    $documento['asistencias'], // Asistencias
                                                    FALSE, // distribuido
                                                    TIPO_ASISTENCIA_TESTIGO, // Tipo de asistencia
                                                    FALSE, // solo nombre
                                                    FALSE, // incluir domicilio
                                                    FALSE, // incluir articulo
                                                    NULL, // Enlace designado
                                                    NULL, // Post Texto
                                                    "," // Separador
                                            );
                                            ?>
                                            manifiesta<span class="plural">n</span> que le fueron explicados los trabajos de la auditoría.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class="bg-white">
                                            Leída la presente acta y no habiendo más hechos que plasmar se da por concluida la diligencia siendo las
                                            <?= span_editable($r, ACTA_INICIO_HORA_FIN, 'HH:MM'); ?> horas
                                            de la misma fecha en que fue iniciada. Asimismo, previa lectura de lo asentado, los que en ella intervinieron
                                            la firman al margen y al calce de todas y cada una de las fojas, haciéndose constar que este documento fue
                                            elaborado en <?= span_editable($r, ACTA_INICIO_NUMERO_EJEMPLARES, CONSTANTE_CANTIDAD_EJEMPLARES_ACTA); ?>
                                            ejemplares originales, de los cuales se hace entrega de uno al servidor público con quien
                                            se entendió la diligencia.
                                        </span>
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class="bg-white">
                                            Se hace del conocimiento de los presentes que la omisión o negativa de firma no afecta la validez y efectos
                                            legales de la presente acta.
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
                        <input type="hidden" id="auditorias_enlace_designado" value="<?= $auditoria['auditorias_enlace_designado'];?>">
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
<script src="<?= base_url(); ?>resources/scripts/auditorias_documentos_aia.js" type="text/javascript"></script>