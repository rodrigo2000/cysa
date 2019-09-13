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
                            <?= span_editable($r, RESOL_AMPLI_CCP, $ccp_texto_plantilla, NULL, NULL, TRUE); ?>

                            Minutario
                            Expediente
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
<div class="form-group hidden-print">
    <div class="col-sm-12 text-xs-center">
        <input type="hidden" name="<?= $this->module['id_field'] ?>" value="<?= $id; ?>">
        <input type="hidden" name="documentos_id" id="documentos_id" value="<?= isset($documento['documentos_id']) && $accion === "modificar" ? $documento['documentos_id'] : 0; ?>">
        <input type="hidden" name="accion" id="accion" value="<?= $accion; ?>">
        <input type="hidden" name="documentos_tipos_id" id="documentos_tipos_id" value="<?= $documento['documentos_versiones_documentos_tipos_id']; ?>">
        <input type="hidden" name="documentos_versiones_id" id="documentos_versiones_id" value="<?= $documento['documentos_versiones_id']; ?>">
    </div>
</div>