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
            <?php $documento = $documentos[$index]; ?>
            <?php $hidden = !isset($documento['documentos_id']) || empty($documento['documentos_id']) ? 'hidden-xs-up' : ''; ?>
            <?php $documento_autorizado = isset($documento['documentos_is_aprobado']) && $documento['documentos_is_aprobado'] == 1 ? TRUE : FALSE; ?>
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
                                    <?php $direccion = $this->SAC_model->get_direccion($d['valores'][CITATORIO_ID_UA]); ?>
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
                                        <?php $fechaDelOficio = isset($r) && isset($r[FECHA_LECTURA_CITATORIO]) ? $r[FECHA_LECTURA_CITATORIO] : $hoy; ?>
                                        Mérida, Yucatán, a <a href="#" class="xeditable" id="<?= FECHA_LECTURA_CITATORIO; ?>" data-pk="<?= FECHA_LECTURA_CITATORIO; ?>" data-type="date" data-placement="left" data-format="yyyy-mm-dd" data-title="Fecha del oficio" title="Fecha de emisión del oficio" data-value="<?= $fechaDelOficio; ?>"><?= mysqlDate2Date($fechaDelOficio); ?></a><br>
                                        Oficio NO. <?= ($auditoria['auditorias_segundo_periodo'] == 1 ? '2' : '') . $auditoria['auditorias_areas_siglas']; ?>/<span contenteditable="true" id="<?= CITATORIO_OFICIO; ?>" class="editable" title="El número consecutivo de Orden" default-value="XXX"><?= isset($r) ? $r[CITATORIO_OFICIO] : ''; ?></span>/<?= date("Y"); ?><br>
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
                                        <span class="resaltar"><?= $auditoria['numero_auditoria']; ?></span>,
                                        que tiene por objetivo
                                        <span class="resaltar"><?= $auditoria['auditorias_objetivo']; ?></span>,
                                        realizada
                                        <span class="resaltar">
                                            <?php echo get_frase_de_ua($auditoria); ?>
                                        </span>,
                                        mismo que se llevará a cabo el
                                        <span class="resaltar"><?= isset($r[FECHA_LECTURA_CITATORIO]) ? mysqlDate2OnlyDate($r[FECHA_LECTURA_CITATORIO]) : 'POR AGENDAR/CAPTURAR EN LÍNEA DE TIEMPO'; ?></span>
                                        a las
                                        <span id="<?= H_LECTURA_CITATORIO; ?>" contenteditable="true" class="editable" default-value="HH:MM"><?= isset($r[H_LECTURA_CITATORIO]) ? $r[H_LECTURA_CITATORIO] : ''; ?></span> horas,
                                        en
                                        <span id="<?= CITATORIO_UBICACION_UA; ?>" contenteditable="true" class="editable" default-value="la Dirección de [Unidad Administrativa]"><?= isset($r[CITATORIO_UBICACION_UA]) ? $r[CITATORIO_UBICACION_UA] : ''; ?></span>,
                                        ubicada en
                                        <span id="<?= CITATORIO_UBICACION; ?>" contenteditable="true" class="editable" default-value="la calle ___ número ___ por ___ y ___ (de la/del) __________ de esta ciudad de Mérida, Yucatán."><?= isset($r[CITATORIO_UBICACION]) ? $r[CITATORIO_UBICACION] : ''; ?></span>
                                    </p>
                                    <?php if (!empty($auditoria['auditorias_enlace_designado'])): ?>
                                        <p class="text-justify texto-sangria">
                                            Asimismo, se le informa que esta diligencia podrá ser atendida por el enlace designado, el cual fue nombrado al
                                            inicio de la auditoría en el Oficio número
                                            <span class="resaltar"><?= !empty($auditoria['auditorias_folio_oficio_representante_designado']) ? $auditoria['auditorias_folio_oficio_representante_designado'] : $sinEspecificar; ?></span>,
                                            recibido el
                                            <span class="resaltar"><?= !empty($auditoria['auditorias_fechas_sello_oficio_representante_designado']) ? mysqlDate2OnlyDate($auditoria['auditorias_fechas_sello_oficio_representante_designado']) : $sinEspecificar; ?></span>.
                                        </p>
                                    <?php endif; ?>
                                    <p class="<?= $r[ASISTENCIA_PUBLICA] == 1 ? 'text-justify texto-sangria' : 'text-xs-center'; ?> show-hide">
                                        <span id="parrafo1" class="<?= $r[ASISTENCIA_PUBLICA] == 1 ? '' : 'hidden-xs-up'; ?>">
                                            Mucho agradeceré se haga acompañar de los servidores públicos
                                            <span id="seccion_involucrados">
                                                <?php
                                                $involucrados = array();
                                                $strInvolucrados = "";
                                                foreach ($documento['asistencias'] as $direcciones_id => $d) {
                                                    if (isset($d[TIPO_ASISTENCIA_INVOLUCRADO]) && is_array($d[TIPO_ASISTENCIA_INVOLUCRADO])) {
                                                        foreach ($d[TIPO_ASISTENCIA_INVOLUCRADO] as $e) {
                                                            $html = '<span class="resaltar empleado_' . $e['empleados_id'] . '">'
                                                                    . $e['empleados_nombre_titulado'] . ", " . trim($e['empleados_cargo'])
                                                                    . '<input type="hidden" name="involucrados[]" value="' . $e['empleados_id'] . '">'
                                                                    . '<span type="button" class="autocomplete_empleados_delete label label-danger hidden-print" title="Eliminar" data-empleados-id="' . $e['empleados_id'] . '">&times;</span>'
                                                                    . '</span>';
                                                            array_push($involucrados, $html);
                                                        }
                                                    }
                                                }
                                                if (count($involucrados) > 1) {
                                                    $ultimo = array_pop($involucrados);
                                                    $strInvolucrados = implode(", ", $involucrados) . ' <span class="plural"> y ' . ($e['empleados_genero'] == GENERO_MASCULINO ? 'del' : 'de la') . ' </span>' . $ultimo;
                                                } else {
                                                    $strInvolucrados = implode(", ", $involucrados);
                                                }
                                                echo $strInvolucrados;
                                                ?>
                                                <span id="autocomplete_involucrados" class="input-group hidden-xs-up hidden-print">
                                                    <input type="text" class="autocomplete form-control" placeholder="Empleado">
                                                    <span class="input-group-btn">
                                                        <button class="btn btn-danger ocultar" type="button"><i class="fa fa-close"></i></button>
                                                    </span>
                                                </span>
                                            </span>
                                            <a class="btn btn-sm btn-success hidden-print btn_agregar" href="#" data-tipo="involucrados" data-asistencias-tipo="<?= TIPO_ASISTENCIA_INVOLUCRADO; ?>">AGREGAR INVOLUCRADOS</a>,
                                            responsable<span class="plural">s</span> de la solventación de la<span class="plural">s</span> observaci<span class="singular">ón</span><span class="plural">ones</span>.
                                        </span>
                                        <button type="button" onclick="ocultar_parrafo('parrafo1', this);" class="btn btn-sm btn-danger btn-hide hidden-print <?= $r[ASISTENCIA_PUBLICA] == 0 ? 'hidden-xs-up' : ''; ?>"><i class="fa fa-close"></i></button>
                                        <button type="button" onclick="mostrar_parrafo('parrafo1', this);" class="btn btn-sm btn-success btn-show hidden-print <?= $r[ASISTENCIA_PUBLICA] == 1 ? 'hidden-xs-up' : ''; ?>">Agregar párrafo</button>
                                        <input type="hidden" name="constantes[<?= ASISTENCIA_PUBLICA; ?>]" value="<?= $r[ASISTENCIA_PUBLICA]; ?>">
                                    </p>
                                    <p class="<?= $r[CITATORIO_MOSTRAR_PARRAFO_4] == 1 ? 'text-justify texto-sangria' : 'text-xs-center'; ?> show-hide">
                                        <span id="parrafo2" class="<?= $r[CITATORIO_MOSTRAR_PARRAFO_4] == 1 ? '' : 'hidden-xs-up'; ?>">
                                            Lo que le tengo a bien comunicar en vía de notificación para los efectos correspondientes.
                                        </span>
                                        <button type="button" onclick="ocultar_parrafo('parrafo2', this);" class="btn btn-sm btn-danger btn-hide hidden-print <?= $r[CITATORIO_MOSTRAR_PARRAFO_4] == 0 ? 'hidden-xs-up' : ''; ?>"><i class="fa fa-close"></i></button>
                                        <button type="button" onclick="mostrar_parrafo('parrafo2', this);" class="btn btn-sm btn-success btn-show hidden-print <?= $r[CITATORIO_MOSTRAR_PARRAFO_4] == 1 ? 'hidden-xs-up' : ''; ?>">Agregar párrafo</button>
                                        <input type="hidden" name="constantes[<?= CITATORIO_MOSTRAR_PARRAFO_4; ?>]" value="1">
                                    </p>
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
                                        <div id="mision" class="texto-mision">
                                            <br><b>MISIÓN</b><br>
                                            Gobernar el Municipio de Mérida, con un enfoque de vanguardia que procure el desarrollo humano sustentable, con
                                            servicios públicos de calidad, una infraestructura funcional y una administración austera y eficiente, que
                                            promueva la participación ciudadana y consolide un crecimiento sustentable de su territorio para mejorar la
                                            calidad de vida y el acceso en igualdad de oportunidades a todos sus habitantes.
                                        </div>
                                        <div class="texto-ccp">
                                            C.c.p.
                                            <?php
                                            $ccp_texto_plantilla = "";
                                            $alcalde = get_presidente_municipal();
                                            if (!empty($alcalde)) {
                                                $alcalde = $this->SAC_model->get_empleado($alcalde['empleados_id']);
                                                $ccp_texto_plantilla = capitalizar($alcalde['nombre_completo']) . " / " . capitalizar($alcalde['puestos_nombre']) . ".";
                                            } else {
                                                $ccp_texto_plantilla = "Nombre del Presidente Municipal / Puesto.";
                                            }
                                            $sindico = get_sindico();
                                            if (!empty($sindico)) {
                                                $sindico = $this->SAC_model->get_empleado($sindico['empleados_id']);
                                                $ccp_texto_plantilla .= PHP_EOL . capitalizar($sindico['nombre_completo']) . " / " . capitalizar($sindico['puestos_nombre']) . ".";
                                            } else {
                                                $ccp_texto_plantilla .= PHP_EOL . "Nombre del Síndico / Puesto.";
                                            }
                                            $ccp_texto_plantilla .= PHP_EOL . "Nombre(s) del Titular(es) involucrado(s) en la auditoría/ Puesto.";
                                            ?>
                                            <span id="<?= CITATORIO_CCP; ?>" contenteditable="true" class="editable" default-value="<?= $ccp_texto_plantilla; ?>" aceptar-enter="1"><?= isset($r) ? nl2br($r[CITATORIO_CCP]) : nl2br($ccp_texto_plantilla); ?></span><br>
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