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
                                            <span id="<?= HORA_INI; ?>" contenteditable="true" class="editable" default-value="HH:MM"><?= isset($r) ? $r[HORA_INI] : ''; ?></span>
                                            horas del día
                                            <a href="#" class="xeditable" id="<?= FECHA_INI; ?>" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="1" data-title="Seleccione fecha:" data-value="<?= $fecha_acto_inicio; ?>"><?= mysqlDate2Date($fecha_acto_inicio); ?></a>,
                                            reunidos en
                                            <span id="<?= UBICACION; ?>" contenteditable="true" class="editable" default-value="<?= implode(" ", array($generos[$ubicacion['tipos_ua_genero']], $ubicacion['tipos_ua_nombre'], "de", $ubicacion['direcciones_nombre'])); ?>"><?= isset($r) && isset($r[UBICACION]) ? $r[UBICACION] : ''; ?></span>,
                                            ubicada en
                                            <span id="<?= DIRUBICACION; ?>" contenteditable="true" class="editable" default-value="<?= !empty($ubicacion['direcciones_ubicacion']) ? $ubicacion['direcciones_ubicacion'] : 'calle __ número ___ por __ y ___'; ?>"><?= isset($r) && isset($r[DIRUBICACION]) ? $r[DIRUBICACION] : ''; ?></span>;
                                            por
                                            <span id="seccion_involucrados">
                                                <?php foreach ($documento['asistencias'] as $direcciones_id => $d): ?>
                                                    <?php if (isset($d[TIPO_ASISTENCIA_INVOLUCRADO]) && is_array($d[TIPO_ASISTENCIA_INVOLUCRADO]) && !empty($d[TIPO_ASISTENCIA_INVOLUCRADO])): ?>
                                                        <?php $aux = $this->SAC_model->get_direccion($direcciones_id); ?>
                                                        <span class="resaltar" id="direcciones_<?= $direcciones_id; ?>">
                                                            <?= $aux['nombre_completo_direccion']; ?>
                                                            <?php foreach ($d[TIPO_ASISTENCIA_INVOLUCRADO] as $e): ?>
                                                                <span class="resaltar empleado_<?= $e['empleados_id']; ?>">
                                                                    <?= $e['empleados_nombre_titulado'] . ", " . $e['empleados_cargo']; ?>;
                                                                    <input type="hidden" name="involucrados[]" value="<?= $e['empleados_id']; ?>">
                                                                    <span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="<?= $e['empleados_id']; ?>">&times;</span>
                                                                </span>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <span id="autocomplete_involucrados" class="input-group hidden-xs-up hidden-print">
                                                    <input type="text" class="autocomplete form-control" placeholder="Empleado">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-danger ocultar" type="button"><i class="fa fa-close"></i></button>
                                                    </span>,
                                                </span>
                                            </span>
                                            <a class="btn btn-sm btn-success hidden-print btn_agregar" href="#" data-tipo="involucrados" data-asistencias-tipo="<?= TIPO_ASISTENCIA_INVOLUCRADO; ?>">AGREGAR INVOLUCRADOS</a>
                                            y por la <?= LABEL_CONTRALORIA; ?>
                                            <span class="resaltar"><?= $auditoria['empleados_nombre_titulado'] . ", Auditor Líder"; ?></span>,
                                            así como
                                            <span id="seccion_testigos">
                                                <?php foreach ($documento['asistencias'] as $direcciones_id => $d): ?>
                                                    <?php if (isset($d[TIPO_ASISTENCIA_TESTIGO]) && is_array($d[TIPO_ASISTENCIA_TESTIGO])): ?>
                                                        <?php foreach ($d[TIPO_ASISTENCIA_TESTIGO] as $e): ?>
                                                            <span class="resaltar empleado_<?= $e['empleados_id']; ?>">
                                                                <?= ($e['empleados_genero'] == GENERO_MASCULINO ? ' el ' : ' la ') . $e['empleados_nombre_titulado'] . ", " . $e['empleados_cargo']; ?>
                                                                <input type="hidden" name="testigos[]" value="<?= $e['empleados_id']; ?>">
                                                                <span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="<?= $e['empleados_id']; ?>">&times;</span>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <span id="autocomplete_testigos" class="input-group hidden-xs-up hidden-print">
                                                    <input type="text" class="autocomplete form-control" placeholder="Empleado">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-danger ocultar" type="button"><i class="fa fa-close"></i></button>
                                                    </span>,
                                                </span>
                                            </span>
                                            <a class="btn btn-sm btn-success hidden-print btn_agregar" href="#" data-tipo="testigos" data-asistencias-tipo="<?= TIPO_ASISTENCIA_TESTIGO; ?>">AGREGAR TESTIGO</a>
                                            <span id="seccion_testigos_2">
                                                est<span class="singular">e</span><span class="plural">os</span> último<span class="plural">s</span> en calidad de testigo<span class="plural">s</span>
                                            </span>
                                            <span class="bg-white">
                                                a efecto de hacer constar el vencimiento de los plazos acordados entre el auditor líder y la unidad administrativa
                                                sujeta a revisión para la entrega de la documentación e información correspondiente a la auditoría
                                                <span class="resaltar"><?= $auditoria['numero_auditoria']; ?></span>
                                                que tiene por objetivo
                                                <span class="resaltar"><?= $auditoria['auditorias_objetivo']; ?></span>;
                                                y toda vez que
                                                <span id="<?= CUMPLE_ENTREGA; ?>" name="constantes[<?= CUMPLE_ENTREGA; ?>]" class="resaltar opciones" default-value="se dio total" data-opciones="se dio total|se dio parcial|no se dio"><?= isset($r) && isset($r[CUMPLE_ENTREGA]) && !empty($r[CUMPLE_ENTREGA]) ? $r[CUMPLE_ENTREGA] : ''; ?></span>
                                                cumplimiento a la solicitud de documentación e información preliminar anexo en el oficio No.
                                                <span class="resaltar"><?= $auditoria['numero_auditoria']; ?></span>
                                                emitida por la Unidad de Contraloría Municipal, con este documento se efectúa el cierre del plazo otorgado
                                                para la recepción de la información de la auditoría referida, dejándose constancia que con fecha
                                                <?php $fecha_cumplimiento = isset($r) && isset($r[FECHA_ENTREGAS]) ? $r[FECHA_ENTREGAS] : date('Y-m-d'); ?>
                                                <a href="#" class="xeditable" id="<?= FECHA_ENTREGAS; ?>" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="1" data-title="Seleccione fecha:" data-value="<?= $fecha_cumplimiento; ?>"><?= mysqlDate2Date($fecha_cumplimiento); ?></a>,
                                                <span id="<?= UBICACION; ?>" contenteditable="true" class="editable" default-value="<?= $auditoria['nombre_completo_direccion']; ?>"><?= isset($r) ? $r[UBICACION] : ''; ?></span>,
                                                <span class="si_no bg-white">no</span>
                                                entregó la siguiente documentación e información:
                                            </span><br>
                                            <div id="<?= ACEI_PARRAFO_U2; ?>" contenteditable="true" class="editable span" aceptar-enter="1" default-value="Documentación Recibida:
                                                 <br>1.	Organigrama vigente.
                                                 <br>2.	Descriptivas y/o manuales vigentes autorizados de los procedimientos aplicados para (objeto de la auditoría)
                                                 <br>3.	Reglamento interior.
                                                 <br>4.	Programa Operativo Anual.
                                                 <br>5.	Copia de la Reglamentación vigente aplicable.
                                                 <br>6.	Otros (información adicional requerida para la auditoría)."><p><?= isset($r) && isset($r[ACEI_PARRAFO_U2]) && !empty($r[ACEI_PARRAFO_U2]) ? nl2br($r[ACEI_PARRAFO_U2]) : ''; ?></p></div>
                                        </span>
                                    </p>
                                    <p class="text-justify <?= isset($r) && isset($r[OMITE_PARRAFO1]) && $r[OMITE_PARRAFO1] == 1 ? 'bg-punteado text-justify texto-sangria' : 'text-xs-center'; ?> show-hide">
                                        <span id="parrafo1" class="bg-white <?= isset($r) && isset($r[OMITE_PARRAFO1]) && $r[OMITE_PARRAFO1] == 1 ? '' : 'hidden-xs-up'; ?> se_dio_total se_dio_parcial">
                                            Por lo que la auditoría se delimitará únicamente a la documentación e información presentada oportunamente y descrita con anterioridad.
                                        </span>
                                        <button type="button" onclick="ocultar_parrafo('parrafo1', this);" class="btn btn-sm btn-danger btn-hide hidden-print <?= !isset($r) || !isset($r[OMITE_PARRAFO1]) || $r[OMITE_PARRAFO1] == 0 ? 'hidden-xs-up' : ''; ?>"><i class="fa fa-close"></i></button>
                                        <button type="button" onclick="mostrar_parrafo('parrafo1', this);" class="btn btn-sm btn-success btn-show hidden-print <?= isset($r) && isset($r[OMITE_PARRAFO1]) && $r[OMITE_PARRAFO1] == 1 ? 'hidden-xs-up' : ''; ?>">Agregar párrafo 1</button>
                                        <input type="hidden" name="constantes[<?= OMITE_PARRAFO1; ?>]" value="<?= isset($r) && isset($r[OMITE_PARRAFO1]) ? $r[OMITE_PARRAFO1] : 0; ?>">
                                    </p>
                                    <p class="text-justify <?= isset($r) && isset($r[OMITE_PARRAFO2]) && $r[OMITE_PARRAFO2] == 1 ? 'bg-punteado text-justify texto-sangria' : 'text-xs-center'; ?> show-hide se_dio_parcial no_se_dio">
                                        <span id="parrafo2" class="bg-white <?= isset($r) && isset($r[OMITE_PARRAFO2]) && $r[OMITE_PARRAFO2] == 1 ? '' : 'hidden-xs-up'; ?>">
                                            Se hace del conocimiento del/los involucrado(s) que la falta de respuesta oportuna al requerimiento de documentación hecha por la Unidad de Contraloría, podría derivar en alguna responsabilidad por incumplimiento de las obligaciones establecidas en los términos de la Ley de Gobierno de los Municipios del Estado de Yucatán y la Ley de Responsabilidades Administrativa del Estado de Yucatán.
                                        </span>
                                        <button type="button" onclick="ocultar_parrafo('parrafo2', this);" class="btn btn-sm btn-danger btn-hide hidden-print <?= !isset($r) || !isset($r[OMITE_PARRAFO2]) || $r[OMITE_PARRAFO2] == 0 ? 'hidden-xs-up' : ''; ?>"><i class="fa fa-close"></i></button>
                                        <button type="button" onclick="mostrar_parrafo('parrafo2', this);" class="btn btn-sm btn-success btn-show hidden-print <?= isset($r) && isset($r[OMITE_PARRAFO2]) && $r[OMITE_PARRAFO2] == 1 ? 'hidden-xs-up' : ''; ?>">Agregar párrafo 2</button>
                                        <input type="hidden" name="constantes[<?= OMITE_PARRAFO2; ?>]" value="<?= isset($r) && isset($r[OMITE_PARRAFO2]) ? $r[OMITE_PARRAFO2] : 0; ?>">
                                    </p>
                                    <p class="text-justify <?= isset($r) && isset($r[OMITE_FIRMA]) && $r[OMITE_FIRMA] == 1 ? 'bg-punteado text-justify texto-sangria' : 'text-xs-center'; ?> show-hide">
                                        <span id="parrafo3" class="bg-white <?= isset($r) && isset($r[OMITE_FIRMA]) && $r[OMITE_FIRMA] == 1 ? '' : 'hidden-xs-up'; ?>">
                                            Se apercibe a los servidores públicos que intervinieron en esta diligencia que la omisión o negativa de firma no afecta la validez y efectos legales de la presente acta.
                                        </span>
                                        <button type="button" onclick="ocultar_parrafo('parrafo3', this);" class="btn btn-sm btn-danger btn-hide hidden-print <?= !isset($r) || !isset($r[OMITE_FIRMA]) || $r[OMITE_FIRMA] == 0 ? 'hidden-xs-up' : ''; ?>"><i class="fa fa-close"></i></button>
                                        <button type="button" onclick="mostrar_parrafo('parrafo3', this);" class="btn btn-sm btn-success btn-show hidden-print <?= isset($r) && isset($r[OMITE_FIRMA]) && $r[OMITE_FIRMA] == 1 ? 'hidden-xs-up' : ''; ?>">Agregar párrafo de omisión de firmas</button>
                                        <input type="hidden" name="constantes[<?= OMITE_FIRMA; ?>]" value="<?= isset($r) && isset($r[OMITE_FIRMA]) ? $r[OMITE_FIRMA] : 0; ?>">
                                    </p>
                                    <p class="text-justify bg-punteado">
                                        <span class=" bg-white">
                                            Leída la presente en presencia de todos los que en ella intervinieron se da por terminada la diligencia siendo las
                                            <span id="<?= HORA_FIN; ?>" contenteditable="true" class="editable" default-value="HH:MM"><?= isset($r) ? $r[HORA_FIN] : ''; ?></span>
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
<script src="<?= APP_CYSA_URL; ?>resources/scripts/auditorias_documentos_acei.js" type="text/javascript"></script>