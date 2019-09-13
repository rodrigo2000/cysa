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
                        <?php $fechaDelOficio = isset($r) && isset($r[RESOL_PRORROG_P_FECHA_OFICIO]) ? $r[RESOL_PRORROG_P_FECHA_OFICIO] : $hoy; ?>
                        Mérida, Yucatán, a <a href="#" class="xeditable" id="<?= RESOL_PRORROG_P_FECHA_OFICIO; ?>" data-pk="<?= RESOL_PRORROG_P_FECHA_OFICIO; ?>" data-type="date" data-placement="left" data-format="yyyy-mm-dd" data-title="Fecha del oficio" title="Fecha de emisión del oficio" data-value="<?= $fechaDelOficio; ?>"><?= mysqlDate2Date($fechaDelOficio); ?></a><br>
                        OFICIO: No. <?= $auditoria['auditorias_areas_siglas']; ?>/<span contenteditable="true" id="<?= RESOL_PRORROG_P_NUM_OFICIO_FOLIO; ?>" class="editable" title="El número consecutivo de Orden" default-value="XXX"><?= isset($r) ? $r[RESOL_PRORROG_P_NUM_OFICIO_FOLIO] : ''; ?></span>/<?= $auditoria['auditorias_anio']; ?><br>
                        ASUNTO: RESOLUCIÓN DE PRÓRROGA<br>
                        CLASIFICACIÓN: RS
                    </p>
                    <p class="text-left text-sm-left texto-resaltar">
                        <?= $oficio_para['nombre']; ?><br>
                        <?= $oficio_para['cargo']; ?><br>
                        PRESENTE
                        <input type="hidden" name="constantes[<?= RESOL_PRORROG_P_ID_DIR_AUDIT; ?>]" value="<?= isset($r[RESOL_PRORROG_P_ID_DIR_AUDIT]) && !empty($r[RESOL_PRORROG_P_ID_DIR_AUDIT]) ? $r[RESOL_PRORROG_P_ID_DIR_AUDIT] : $oficio_para['direcciones_id']; ?>">
                    </p>
                    <p class="text-justify texto-sangria">
                        En virtud del
                        <?= span_opciones($r, RESOL_PRORROG_P_TIPO_MEDIO_SOL, array(NULL, 'oficio', 'correo electrónico')); ?>
                        de solicitud de prórroga
                        <span class="si_no <?= isset($r) && $r[RESOL_PRORROG_P_TIPO_MEDIO_SOL] == 2 ? 'hidden-xs-up' : ''; ?>">No. <?= span_editable($r, RESOL_PRORROG_P_NUM_MEDIO_SOL); ?></span>
                        recibido el
                        <?= span_calendario($r, RESOL_PRORROG_P_FECHA_RECIBIDO_MEDIO); ?>
                        y de conformidad con lo establecido en las Disposiciones en Materia de Auditoría del Municipio de Mérida, esta
                        <?= span_resaltar(LABEL_CONTRALORIA); ?>
                        determina que es procedente otorgarle un plazo de
                        <?= span_editable($r, RESOL_PRORROG_P_DIAS_HABILES_OTORG); ?>
                        días hábiles para la solventación o atención de la<plural>s</plural> observaci<singular>ón</singular><plural>ones</plural>
        <?= span_editable($r, RESOL_PRORROG_P_OBSERV_INVOLUCRADAS); ?>,
        correspondiente<plural>s</plural> a la auditoría
        <?= span_resaltar($auditoria['numero_auditoria']); ?>,
        siendo la fecha máxima de cumplimiento
        <!-- Administrativas -->
        <?php $dias = isset($r) && isset($r[RESOL_PRORROG_P_DIAS_HABILES_OTORG]) ? $r[RESOL_PRORROG_P_DIAS_HABILES_OTORG] : PLAZO_SOLV_AP; ?>
        <?php $fecha = agregar_dias($auditoria['auditorias_fechas_inicio_programado'], $dias, TRUE); ?>
        <?php $aux = ' el día ' . span_resaltar(mysqlDate2OnlyDate($fecha)) . ' para la<plural>s</plural> Recomendaci<singular>ón</singular><plural>ones</plural> correctiva<plural>s</plural>'; ?>
        <?= span_show_hide($r, RESOL_PRORROG_P_RECO_INVOLUCRADAS_ADM, $aux, 'Agregar observaciones administrativa'); ?>
        <span class="conjuncion"> y </span>
        <!-- Operativas -->
        <?php $dias = isset($r) && isset($r[RESOL_PRORROG_P_DIAS_HABILES_OTORG_OPE]) ? $r[RESOL_PRORROG_P_DIAS_HABILES_OTORG_OPE] : PLAZO_SOLV_AP_OP; ?>
        <?php $fecha = agregar_dias($auditoria['auditorias_fechas_inicio_programado'], $dias, TRUE); ?>
        <?php $aux = ' el día ' . span_resaltar(mysqlDate2OnlyDate($fecha)) . ' para las Recomendaci<singular>ón</singular><plural>ones</plural> Preventiva<plural>s</plural>'; ?>
        <?= span_show_hide($r, RESOL_PRORROG_P_RECO_INVOLUCRADAS_OPE, $aux, 'Agregar observaciones operativas'); ?>
        </p>
        <p class="text-justify texto-sangria">
            En virtud del oficio de solicitud de prórroga No.
            <span class="si_no <?= isset($r) && $r[RESOL_PRORROG_P_TIPO_MEDIO_SOL] == 2 ? 'hidden-xs-up' : ''; ?>">No. <?= span_editable($r, RESOL_PRORROG_P_NUM_MEDIO_SOL); ?></span>
            recibido el
            <?= span_calendario($r, RESOL_PRORROG_P_FECHA_RECIBIDO_MEDIO); ?>
            correspondiente a la Auditoría
            <?= span_resaltar($auditoria['numero_auditoria']); ?>
            esta
            <?= span_resaltar(LABEL_CONTRALORIA); ?>
            determina que no es procedente en virtud de:
            <?= span_editable($r, RESOL_PRORROG_P_RAZON_NO_PROCEDE, NULL, NULL, NULL, TRUE); ?>
        </p>
        <p class="text-justify texto-sangria">
            Sin otro particular, hago propicia la ocasión para enviarle un cordial saludo.
        </p>
        <div class="salto-solo-si-es-necesario">
            <p class="texto-resaltar" style="margin-bottom: 2cm;">ATENTAMENTE</p>
            <div id="firma-titular-contraloria" class="texto-resaltar">
                <?= mb_strtoupper($oficio_de['nombre']); ?><br>
                <?= mb_strtoupper($oficio_de['cargo']); ?>
                <input type="hidden" name="constantes[<?= ROP_TITULAR_CONTRALORIA_EMPLEADOS_ID; ?>]" value="<?= isset($r[ROP_TITULAR_CONTRALORIA_EMPLEADOS_ID]) && !empty($r[ROP_TITULAR_CONTRALORIA_EMPLEADOS_ID]) ? $r[ROP_TITULAR_CONTRALORIA_EMPLEADOS_ID] : $oficio_de['empleados_id']; ?>">
            </div>
            <?php if (CONTRALORIA_MOSTRAR_MISION): ?>
                <div id="mision" class="texto-mision">
                    <br><b>MISIÓN</b><br>
                    <?= $this->CYSA_model->get_mision(); ?>
                </div>
            <?php endif; ?>
            <div class="texto-ccp">
                C.c.p. <?php $ccp_texto_plantilla = $this->CYSA_model->get_ccp_template(); ?>
                <?= span_editable($r, RESOL_PRORROG_P_CCP_OFICIO, $ccp_texto_plantilla, NULL, NULL, TRUE); ?><br>
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
<div class="form-group hidden-print">
    <div class="col-sm-12 text-xs-center">
        <input type="hidden" name="<?= $this->module['id_field'] ?>" value="<?= $id; ?>">
        <input type="hidden" name="documentos_id" id="documentos_id" value="<?= isset($documento['documentos_id']) && $accion === "modificar" ? $documento['documentos_id'] : 0; ?>">
        <input type="hidden" name="accion" id="accion" value="<?= $accion; ?>">
        <input type="hidden" name="documentos_tipos_id" id="documentos_tipos_id" value="<?= $documento['documentos_versiones_documentos_tipos_id']; ?>">
        <input type="hidden" name="documentos_versiones_id" id="documentos_versiones_id" value="<?= $documento['documentos_versiones_id']; ?>">
    </div>
</div>
<script src="<?= base_url(); ?>resources/scripts/auditorias_documento_orp.js" type="text/javascript"></script>