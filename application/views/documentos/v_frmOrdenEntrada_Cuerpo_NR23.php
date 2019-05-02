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
                                    <?php $direccion = $this->SAC_model->get_direccion($d['valores'][ORD_ENT_ID_DIR_AUDIT]); ?>
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
                                        <?php $fechaDelOficio = isset($r) && isset($r[ORD_ENT_FECHA]) ? $r[ORD_ENT_FECHA] : $hoy; ?>
                                        Mérida, Yucatán, a <a href="#" class="xeditable" id="<?= ORD_ENT_FECHA; ?>" data-pk="<?= ORD_ENT_FECHA; ?>" data-type="date" data-placement="left" data-format="yyyy-mm-dd" data-title="Fecha del oficio" title="Fecha de emisión del oficio" data-value="<?= $fechaDelOficio; ?>"><?= mysqlDate2Date($fechaDelOficio); ?></a><br>
                                        Asunto: Orden de Auditoría <?= ($auditoria['auditorias_segundo_periodo'] == 1 ? '2' : '') . $auditoria['auditorias_areas_siglas']; ?>/<span contenteditable="true" id="<?= ORD_ENT_NUMERO_OFICIO; ?>" class="editable" title="El número consecutivo de Orden" default-value="XXX"><?= isset($r) ? $r[ORD_ENT_NUMERO_OFICIO] : ''; ?></span>/<?= date("Y"); ?><br>
                                        Clasificación: RS
                                    </p>
                                    <p class="text-left text-sm-left texto-resaltar">
                                        <?= $oficio_para['nombre']; ?><br>
                                        <?= $oficio_para['cargo']; ?><br>
                                        PRESENTE
                                        <input type="hidden" name="constantes[<?= ORD_ENT_ID_DIR_AUDIT; ?>]" value="<?= isset($r[ORD_ENT_ID_DIR_AUDIT]) && !empty($r[ORD_ENT_ID_DIR_AUDIT]) ? $r[ORD_ENT_ID_DIR_AUDIT] : $oficio_para['direcciones_id']; ?>">
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        Con fundamento en lo dispuesto en los artículos 210 y 211 fracción II de la Ley de Gobierno de los Municipios del
                                        Estado de Yucatán, 2 fracción II, XXI, 102, 110, 111 y 112 de la Ley de Responsabilidades Administrativas del Estado
                                        de Yucatán, y con la finalidad de evaluar, promover y fortalecer el cumplimiento de los principios rectores del
                                        servicio público en los programas sustantivos y normatividad aplicable,
                                        <?php if ($auditoria['auditorias_is_programada'] == 1): ?>
                                            <span class="resaltar"><?= "de acuerdo a nuestro Programa Anual de Auditoría" ?></span>,
                                        <?php endif; ?>
                                        se dará inicio a la auditoría <span class="resaltar" title="Número de la auditoría"><?= $auditoria['numero_auditoria']; ?></span>,
                                        que tiene por objetivo <span class="resaltar" title="Objetivo de la auditoría"><?= $auditoria['auditorias_objetivo']; ?></span>, en
                                        <?php
                                        $aux = "";
                                        if ($auditoria['cc_etiqueta_departamento'] != 1) {
                                            $aux .= " el Departamento de " . capitalizar($auditoria['departamentos_nombre']) . ", perteneciente a ";
                                        }
                                        if ($auditoria['cc_etiqueta_subdireccion'] != 1) {
                                            $aux .= " la Subdirección de " . capitalizar($auditoria['subdirecciones_nombre']) . " de ";
                                        }
                                        $aux .= " la Dirección a su cargo";
                                        ?>
                                        <span class="resaltar" title="Nombre del Departamento, Subdirección o Dirección a la cual se realizará la auditoría"><?= $aux; ?></span>, misma que se llevará a
                                        cabo en <span id="<?= ORD_ENT_DOMICILIO_UA; ?>" contenteditable="true" class="editable" title="Domicilio donde habrá de efectuarse la auditoría" default-value="ESPECIFICAR DOMICILIO"><?= !empty($auditoria['direcciones_ubicacion']) ? $auditoria['direcciones_ubicacion'] : ''; ?></span>.
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        Para la práctica de la presente auditoría, se ha comisionado a los siguientes servidores públicos:
                                    </p>
                                    <table id="equipo_auditoria" class="table-sm table-bordered m-b-1" align="center">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Cargo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $lider = $this->Auditorias_model->get_lider_auditoria(); ?>
                                            <tr>
                                                <td class="align-middle"><p><?= $lider['empleados_nombre_titulado_siglas']; ?></p></td>
                                                <td class="align-middle"><p><?= $lider['empleados_puestos_id'] == PUESTO_AUDITOR ? 'Auditor Líder' : capitalizar($lider['puestos_nombre']); ?></p></td>
                                            </tr>
                                            <?php $equipo = $this->Auditorias_model->get_equipo_auditoria($auditoria['auditorias_id']); ?>
                                            <?php foreach ($equipo as $e): ?>
                                                <tr>
                                                    <td class="align-middle"><p><?= $e['empleados_nombre_titulado_siglas']; ?></p></td>
                                                    <td class="align-middle"><p><?= capitalizar($e['puestos_nombre']); ?></p></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <p class="text-justify texto-sangria">
                                        <?php $fechaMaximaSolicitudInformacion = isset($r) && isset($r[ORD_ENT_FECHA_SI]) ? $r[ORD_ENT_FECHA_SI] : agregar_dias($fechaDelOficio, 3); // Aumentamos 3 días  ?>
                                        Para el desarrollo de la auditoría, solicito su colaboración para que a más tardar el día
                                        <a href="#" class="xeditable" id="<?= ORD_ENT_FECHA_SI; ?>" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="1" data-title="Seleccione fecha:" data-value="<?= $fechaMaximaSolicitudInformacion; ?>"><?= mysqlDate2Date($fechaMaximaSolicitudInformacion); ?></a>, proporcione al
                                        equipo de auditoría, quien podrá actuar en forma individual y/o conjunta durante el desarrollo de la misma, los
                                        registros, reportes, informes, correspondencia, acceso a los sistemas y demás documentación relativa a sus
                                        operaciones financieras, presupuestales y de consecución de metas, detallada en el documento anexo que
                                        forma parte integrante del presente oficio, así como asignar un espacio físico adecuado para los
                                        auditores e insumos necesarios en sus instalaciones para la correcta ejecución de la misma.
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        Asimismo, se requiere designe vía oficio en un término no mayor a dos días hábiles posteriores a la
                                        notificación del presente documento al servidor público adscrito a su
                                        <span class="resaltar"><?= $oficio_para['tratamiento'] ?></span>, facultado
                                        para ser el enlace en la atención de la presente auditoría, quien será responsable de solicitar a las
                                        unidades administrativas auditadas la información, documentación y aclaraciones que se requieran; así como
                                        la designación de dos testigos para la firma del Acta de Inicio de Auditoría, acompañando copia de sus
                                        identificaciones oficiales.
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        <?php $fechaVisitaAuditor = isset($r) ? $r[ORD_ENT_FECHA_VISITA] : agregar_dias($fechaDelOficio, 5); // +5 días, pero solo aumentamos dos porque ya habiamos aumentado 3 días  ?>
                                        De igual forma, me permito informarle que con fecha
                                        <a href="#" class="xeditable" id="<?= ORD_ENT_FECHA_VISITA; ?>" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="1" data-title="Seleccione fecha:" data-value="<?= $fechaVisitaAuditor; ?>"><?= mysqlDate2Date($fechaVisitaAuditor); ?></a>,
                                        el Auditor Líder a cargo de la auditoría se presentará en el domicilio donde habrá de efectuarse ésta,
                                        para formalizar el acto de inicio de los trabajos de auditoría, por lo que se solicita su presencia o la
                                        del enlace designado, así como de los testigos asignados, en el horario de
                                        <span id="<?= ORD_ENT_HORA_VISITA; ?>" contenteditable="true" class="editable" default-value="HH:MM"><?= isset($r) ? $r[ORD_ENT_HORA_VISITA] : ''; ?></span> horas. La omisión a lo anterior, podrá ser causal
                                        de responsabilidad prevista en el artículo 51 fracción VII inciso c) de la Ley de Responsabilidades
                                        Administrativas del Estado de Yucatán.
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        No omito comunicarle que cualquier queja, inconformidad o reporte del personal de esta Unidad de
                                        Contraloría podrá efectuarse a través de las líneas telefónicas
                                        <span id="<?= ORD_ENT_TELEFONO; ?>" contenteditable="true" class="editable" default-value="(999) 923 6872 y 923 6859"><?= isset($r[ORD_ENT_TELEFONO]) && !empty($r[ORD_ENT_TELEFONO]) ? $r[ORD_ENT_TELEFONO] : ''; ?></span>,
                                        extensión
                                        <span id="<?= ORD_ENT_EXTEN; ?>" contenteditable="true" class="editable" default-value="82255"><?= isset($r[ORD_ENT_EXTEN]) && !empty($r[ORD_ENT_EXTEN]) ? $r[ORD_ENT_EXTEN] : ''; ?></span>,
                                        así como al correo electrónico
                                        <span id="<?= ORD_ENT_CORREO; ?>" contenteditable="true" class="editable" default-value="contraloriateescucha@merida.gob.mx"><?= isset($r[ORD_ENT_CORREO]) && !empty($r[ORD_ENT_CORREO]) ? $r[ORD_ENT_CORREO] : ''; ?></span>
                                        para su atención correspondiente.
                                    </p>
                                    <p class="text-justify texto-sangria">
                                        Sin otro particular, hago propicia la ocasión para enviarle un cordial saludo.
                                    </p>
                                    <div class="salto-solo-si-es-necesario">
                                        <p class="texto-resaltar" style="margin-bottom: 2cm;">ATENTAMENTE</p>
                                        <div id="firma-titular-contraloria" class="texto-resaltar">
                                            <?= mb_strtoupper($oficio_de['nombre']); ?><br>
                                            <?= mb_strtoupper($oficio_de['cargo']); ?>
                                            <input type="hidden" name="constantes[<?= ORD_ENT_ID_DIR_CONTRA; ?>]" value="<?= isset($r[ORD_ENT_ID_DIR_CONTRA]) && !empty($r[ORD_ENT_ID_DIR_CONTRA]) ? $r[ORD_ENT_ID_DIR_CONTRA] : $oficio_de['empleados_id']; ?>">
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
                                                $ccp_texto_plantilla = "Nombre del Presidente Municipal/ Puesto.";
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
                                            <span id="<?= ORD_ENT_CCP; ?>" contenteditable="true" class="editable" default-value="<?= $ccp_texto_plantilla; ?>" aceptar-enter="1"><?= isset($r) ? nl2br($r[ORD_ENT_CCP]) : nl2br($ccp_texto_plantilla); ?></span><br>
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