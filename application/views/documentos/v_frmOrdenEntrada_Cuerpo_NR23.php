<!-- DDslick -->
<script src="<?= base_url(); ?>resources/plugins/ddslick/jquery.ddslick.min.js" type="text/javascript"></script>
<!-- moments.js -->
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/min/moment.min.js" type="text/javascript"></script>
<script src="<?= APP_SAC_URL; ?>resources/plugins/moment/locale/es.js" type="text/javascript"></script>
<!-- DateRangePicker -->
<link href="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
<script src="<?= APP_SAC_URL; ?>resources/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
<!-- xEditable -->
<link href="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" type="text/css"/>
<!--<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-editable.min.js" type="text/javascript"></script>-->
<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/x-editable-bs4.js" type="text/javascript"></script>
<script src="<?= base_url(); ?>resources/plugins/x-editable-develop/dist/bootstrap3-editable/js/bootstrap-datepicker.es.js" type="text/javascript"></script>
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
            <form id="frmOficios" name="frmOficios" class="<?= $documento_autorizado || $is_finalizada ? 'autorizado' : ''; ?><?= $accion === "descargar" ? ' impresion' : ''; ?>" method="post" action="<?= $urlAction; ?>">
                <div class="text-xs-center hidden-print oficio-menu-opciones">
                    <?php $this->load->view('documentos/menu_opciones'); ?>
                </div>
                <?php if ($is_finalizada || $documento_autorizado): ?>
                    <?php echo $this->Documentos_blob_model->get_html($auditoria['auditorias_id'], $documento['documentos_versiones_documentos_tipos_id']); ?>
                <?php else: ?>
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
                                                    <option value="<?= $l['logotipos_id']; ?>" data-imagesrc="<?= base_url() . "resources/imagen_institucional/" . $l['logotipos_header_archivo']; ?>" <?= (empty($documento) && $l['logotipos_is_activo'] == 1) || (isset($documento['documentos_logotipos_id']) && $l['logotipos_id'] == $documento['documentos_logotipos_id']) ? 'selected="selected"' : ''; ?>></option>
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
                                            <?php $fechaDelOficio = isset($r, $r[ORD_ENT_FECHA]) ? $r[ORD_ENT_FECHA] : $hoy; ?>
                                            Mérida, Yucatán, a <a href="#" class="xeditable" id="<?= ORD_ENT_FECHA; ?>" data-pk="<?= ORD_ENT_FECHA; ?>" data-type="date" data-placement="left" data-format="yyyy-mm-dd" data-title="Fecha del oficio" title="Fecha de emisión del oficio" data-value="<?= $fechaDelOficio; ?>"><?= mysqlDate2Date($fechaDelOficio); ?></a><br>
                                            Asunto: Orden de Auditoría <?= ($auditoria['auditorias_segundo_periodo'] == 1 ? '2' : '') . $auditoria['auditorias_areas_siglas']; ?>/<span contenteditable="true" id="<?= ORD_ENT_NUMERO_OFICIO; ?>" class="editable" title="El número consecutivo de Orden" default-value="XXX"><?= isset($r) ? $r[ORD_ENT_NUMERO_OFICIO] : ''; ?></span>/<?= $auditoria['auditorias_anio']; ?><br>
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
                                                <?= span_resaltar("de acuerdo a nuestro Programa Anual de Auditoría"); ?>,
                                            <?php endif; ?>
                                            se dará inicio a la auditoría
                                            <?= span_resaltar($auditoria['numero_auditoria'], 'Número de la auditoría'); ?>,
                                            que tiene por objetivo
                                            <?= span_resaltar($auditoria['auditorias_objetivo'], 'Objetivo de la auditoría'); ?>, en
                                            <?php
                                            $aux = "";
                                            if ($auditoria['cc_etiqueta_departamento'] != 1) {
                                                $aux .= " el Departamento de " . capitalizar($auditoria['departamentos_nombre']) . ", perteneciente a ";
                                            }
                                            if ($auditoria['cc_etiqueta_subdireccion'] != 1) {
                                                $aux .= " la Subdirección de " . capitalizar($auditoria['subdirecciones_nombre']) . " de ";
                                            }
                                            $aux .= " la Dirección a su cargo";
                                            echo span_resaltar($aux, "Nombre del Departamento, Subdirección o Dirección a la cual se realizará la auditoría");
                                            ?>, misma que se llevará a
                                            cabo en
                                            <?= span_editable($r, ORD_ENT_DOMICILIO_UA, 'ESPECIFICAR DOMICILIO', 'Domicilio donde habrá de efectuarse la auditoría'); ?>.
                                        </p>
                                        <p class="text-justify texto-sangria">
                                            Para la práctica de la presente auditoría, se ha comisionado a los siguientes servidores públicos:
                                        </p>
                                        <table id="equipo_auditoria" class="table-sm table-bordered m-b-1 mismo-tamano-fuente-p" align="center">
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
                                            <a href="#" class="xeditable" id="<?= ORD_ENT_FECHA_SI; ?>" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="<?= ORD_ENT_FECHA_SI; ?>" data-title="Seleccione fecha:" data-value="<?= $fechaMaximaSolicitudInformacion; ?>"><?= mysqlDate2Date($fechaMaximaSolicitudInformacion); ?></a>
                                            proporcione al equipo de auditoría, quien podrá actuar en forma individual y/o conjunta durante el desarrollo
                                            de la misma, los registros, reportes, informes, correspondencia, acceso a los sistemas y demás documentación
                                            relativa a sus operaciones financieras, presupuestales y de consecución de metas, detallada en el documento anexo que
                                            forma parte integrante del presente oficio, así como asignar un espacio físico adecuado para los
                                            auditores e insumos necesarios en sus instalaciones para la correcta ejecución de la misma.
                                        </p>
                                        <p class="text-justify texto-sangria">
                                            Asimismo, se requiere designe vía oficio en un término no mayor a dos días hábiles posteriores a la
                                            notificación del presente documento al servidor público adscrito a su
                                            <?= span_resaltar($oficio_para['tratamiento']); ?>,
                                            facultado para ser el enlace en la atención de la presente auditoría, quien será responsable de solicitar a las
                                            unidades administrativas auditadas la información, documentación y aclaraciones que se requieran; así como
                                            la designación de dos testigos para la firma del Acta de Inicio de Auditoría, acompañando copia de sus
                                            identificaciones oficiales.
                                        </p>
                                        <p class="text-justify texto-sangria">
                                            <?php $fechaVisitaAuditor = isset($r) ? $r[ORD_ENT_FECHA_VISITA] : agregar_dias($fechaDelOficio, 5); // +5 días, pero solo aumentamos dos porque ya habiamos aumentado 3 días  ?>
                                            De igual forma, me permito informarle que con fecha
                                            <a href="#" class="xeditable" id="<?= ORD_ENT_FECHA_VISITA; ?>" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="<?= ORD_ENT_FECHA_VISITA; ?>" data-title="Seleccione fecha:" data-value="<?= $fechaVisitaAuditor; ?>"><?= mysqlDate2Date($fechaVisitaAuditor); ?></a>,
                                            el Auditor Líder a cargo de la auditoría se presentará en el domicilio donde habrá de efectuarse ésta,
                                            para formalizar el acto de inicio de los trabajos de auditoría, por lo que se solicita su presencia o la
                                            del enlace designado, así como de los testigos asignados, en el horario de
                                            <span id="<?= ORD_ENT_HORA_VISITA; ?>" contenteditable="true" class="editable" default-value="HH:MM"><?= isset($r) ? $r[ORD_ENT_HORA_VISITA] : ''; ?></span> horas.
                                            La omisión a lo anterior, podrá ser causal de responsabilidad prevista en el artículo 51 fracción VII inciso c)
                                            de la Ley de Responsabilidades Administrativas del Estado de Yucatán.
                                        </p>
                                        <p class="text-justify texto-sangria">
                                            No omito comunicarle que cualquier queja, inconformidad o reporte del personal de esta Unidad de
                                            Contraloría podrá efectuarse a través de las líneas telefónicas
                                            <?= span_editable($r, ORD_ENT_TELEFONO, '(999) 923 6872 y 923 6859'); ?>,
                                            extensión
                                            <?= span_editable($r, ORD_ENT_EXTEN, '82255'); ?>,
                                            así como al correo electrónico
                                            <?= span_editable($r, ORD_ENT_CORREO, 'contraloriateescucha@merida.gob.mx'); ?>
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
                                            <?php if (CONTRALORIA_MOSTRAR_MISION): ?>
                                                <div id="mision" class="texto-mision">
                                                    <br><b>MISIÓN</b><br>
                                                    <?= $this->CYSA_model->get_mision(); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="texto-ccp">
                                                C.c.p. <?php $ccp_texto_plantilla = $this->CYSA_model->get_ccp_template(); ?>
                                                <?= span_editable($r, ORD_ENT_CCP, $ccp_texto_plantilla, NULL, NULL, TRUE); ?>
                                                <!-- Este espacio se queda porque funciona como un BR -->
                                                Minutario
                                                Expediente
                                                <?= $this->Auditorias_model->get_siglas_de_empleados_para_documento_de_auditoria($auditoria['auditorias_auditor_lider'], $auditoria['auditorias_id']); ?><br><br>
                                                <?= $documento['documentos_versiones_prefijo_iso'] . $documento['documentos_versiones_codigo_iso'] . " " . $documento['documentos_versiones_numero_iso']; ?>
                                            </div>
                                        </div>
                                        <div class="salto-de-pagina">
                                            <p class="text-xs-center bold">
                                                <?= mb_strtoupper(LABEL_CONTRALORIA); ?><br>
                                                SOLICITUD DE DOCUMENTACIÓN E INFORMACIÓN PRELIMINAR <?= $auditoria['numero_auditoria']; ?><br>
                                                ANEXO
                                            </p>
                                            <p class="bold">Disposiciones Generales:</p>
                                            <ol type="I" class="text-justify">
                                                <li>La documentación e información deberá entregarse a través de oficio firmado por el titular de la dependencia o entidad, indicando si es original, copia fotostática o si se pondrá a disposición. El contenido deberá identificarse por cada punto, con el propósito de dar respuesta a todos ellos. Asimismo, en caso de no contar con alguno de los documentos solicitados, deberá notificar dicha situación.</li>
                                                <li>La documentación e información proporcionada de forma impresa para la ejecución de auditoría, deberá estar validada por quien la elaboró y por el responsable del área.</li>
                                                <li>La documentación requerida es enunciativa, mas no limitativa, por tanto, se deberá presentar adicionalmente toda aquella documentación que se considere complemente el rubro, objetivo, proceso, sistema, etc., objeto de la auditoría.</li>
                                                <li>De requerirse durante la práctica de la auditoría documentación e información no contenida en el requerimiento preliminar, será solicitada por escrito de manera fundada y motivada, la cual deberá proporcionarse dentro de los cinco días hábiles contados a partir del día siguiente de notificada la solicitud.</li>
                                            </ol>
                                            <p class="bold">Requerimiento:</p>
                                            <p class="texto-sangria">
                                                Se solicita la siguiente información preliminar para efectos del desarrollo de la
                                                presente<?= span_editable($r, ORD_ENT_REQUERIMIENTOS_SI_FECHA_CORTE, ', con corte al DD de MMMMM de AAAA'); ?>
                                            </p>
                                            <?php
                                            $aux = "1. Organigrama vigente.<br>
  2. Descriptivas y/o manuales vigentes autorizados de los procedimientos aplicados para (objeto de la auditor&iacute;a)<br>
  3. Reglamento interior.<br>
  4. Programa Operativo Anual.<br>
  5. Copia de la Reglamentaci&oacute;n vigente aplicable.<br>
  6. Otros (informaci&oacute;n adicional requerida para la auditor&iacute;a).";
                                            ?>
                                            <div style="margin-left: 1cm;">
                                                <?= span_editable($r, ORD_ENT_REQUERIMIENTOS_SI, $aux, NULL, NULL, TRUE, TRUE); ?>
                                            </div>
                                            <p></p>
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
                                                    <option value="<?= $l['logotipos_id']; ?>" data-imagesrc="<?= base_url() . "resources/imagen_institucional/" . $l['logotipos_footer_archivo']; ?>" <?= (empty($documento) && $l['logotipos_is_activo'] == 1) || (isset($documento['documentos_logotipos_id']) && $l['logotipos_id'] == $documento['documentos_logotipos_id']) ? 'selected="selected"' : ''; ?>></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
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
                <div class="text-xs-center hidden-print oficio-menu-opciones">
                    <?php $this->load->view('documentos/menu_opciones'); ?>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>