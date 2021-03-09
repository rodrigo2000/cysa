<?php
$cysa = $this->session->userdata(APP_NAMESPACE);
$auditorias_id = $cysa['auditorias_id'];

$AP_ARA_sin_observaciones = "";
$AP_ARA_con_observaciones = "";

$SA_ARR_sin_observaciones = "";
$SA_ARR_obs_solv_y_pendientes = "";
$SA_ARR_obs_solventadas = "";
$SA_ARR_obs_pendientes = "";
$SA_ARR_obs_solve_y_atedidas = "";

$is_ARA = $is_ARR = FALSE;

$template = "";
if ($auditoria['auditorias_anio'] < 2018) {
    $status = $this->Auditorias_model->get_status_de_recomendaciones($auditoria['auditorias_id']);
    if (empty($auditoria['auditorias_origen_id'])) {
        $is_ARA = TRUE;
    } else {
        $is_ARR = TRUE;
    }
} else { // Entonces es auditoría 2018 en adelante
    echo "<h1>Ya no se usan Envío de Documentos para auditorías posteriores a 2017.</h1>";
}
?>
<div id="oficio-hoja" class="<?= $documento_autorizado ? 'autorizado' : ''; ?>">
    <?php $r = isset($documento['valores']) && !empty($documento['valores']) && $accion !== "nuevo" ? $documento['valores'] : NULL; ?>
    <div class="watermark">PARA REVISIÓN</div>
    <table>
        <thead>
            <tr>
                <td>
                    <?php if ($documento_autorizado || $accion === "descargar1"): ?>
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
                        <?php $fechaDelOficio = isset($r) && isset($r[FECHA_DOCTO_ENVIOS]) ? $r[FECHA_DOCTO_ENVIOS] : $hoy; ?>
                        Mérida, Yucatán, a <a href="#" class="xeditable" id="<?= FECHA_DOCTO_ENVIOS; ?>" data-pk="<?= FECHA_DOCTO_ENVIOS; ?>" data-type="date" data-placement="left" data-format="yyyy-mm-dd" data-title="Fecha del oficio" title="Fecha de emisión del oficio" data-value="<?= $fechaDelOficio; ?>"><?= mysqlDate2Date($fechaDelOficio); ?></a><br>
                        Oficio: <?= $auditoria['auditorias_areas_siglas']; ?>/<?= ($auditoria['auditorias_segundo_periodo'] == 1 ? '2-' : ''); ?><span contenteditable="true" id="<?= ED_OFICIO; ?>" class="editable" title="El número consecutivo de Orden" default-value="XXX"><?= isset($r) ? $r[ED_OFICIO] : ''; ?></span>/<?= $auditoria['auditorias_anio']; ?><br>
                        Asunto: Envío de Documentos <br>
                        Clasificación: RS
                    </p>
                    <p class="text-left text-sm-left texto-resaltar">
                        <?= $oficio_para['nombre']; ?><br>
                        <?= $oficio_para['cargo']; ?><br>
                        PRESENTE
                        <input type="hidden" name="constantes[<?= ED_ID_UA; ?>]" value="<?= isset($r[ED_ID_UA]) && !empty($r[ED_ID_UA]) ? $r[ED_ID_UA] : $oficio_para['direcciones_id']; ?>">
                    </p>
                    <p class="text-justify texto-sangria">
                        Por este medio le envío
                        <?= span_editable($r, DOC_ENVIADOS, 'el Acta de Resultados de Auditoría y Cédulas de Observación'); ?>,
                        correspondiente<plural>s</plural> a la auditoría
        <?= span_resaltar($auditoria['numero_auditoria']); ?>
        que tiene por objetivo
        <?= span_resaltar($auditoria['auditorias_objetivo']); ?>
        a
        <?= span_resaltar($auditoria['nombre_completo_direccion']); ?>
        a su cargo.
        </p>
        <?php if ($is_ARA && $auditoria['auditorias_is_sin_observaciones'] == 1): ?>
            <p class="text-justify texto-sangria">
                No omito hacer de su conocimiento, que no fueron encontradas observaciones que involucren a
                <?= span_resaltar($auditoria['nombre_completo_direccion']); ?>
                a su cargo, por lo que con este documento se da por terminada la presente.
            </p>
        <?php elseif (count($status[OBSERVACIONES_STATUS_ATENDIDA]) === 0): ?>
            <p class="text-justify texto-sangria plurales">
                No omito hacer de su conocimiento, que la<plural>s</plural> recomendaci<singular>ón</singular><plural>ones</plural>
            fue<plural>ron</plural> solventada<plural>s</plural> satisfactoriamente, por lo que con este documento se da
            por terminada la presente.
            </p>
        <?php else: ?>
            <p class="text-justify texto-sangria plurales">
                No omito hacer de su conocimiento, que la<plural>s</plural> recomendaci<singular>ón</singular><plural>ones</plural>
            (11) [(8) fue (ron) solventadas satisfactoriamente, la(s) recomendación(es) (7) no se encuentran solventadas y la(s) recomendación(es) (10) están atendidas].
            </p>
        <?php endif; ?>
        <p class="text-justify texto-sangria">
            Asimismo, le exhortamos, a que todo el personal
            de<?= (is_vocal($auditoria['nombre_completo_direccion'][0])) ? 'l' : ''; ?>
            <?= span_resaltar($auditoria['nombre_completo_direccion']); ?>
            continúe realizando las acciones implementadas
            <?= ($auditoria['auditorias_is_sin_observaciones'] == 0) ? 'como resultado de esta auditoría,' : ', '; ?>
            con el fin de promover la optimización y transparencia en el manejo de los recursos para beneficio de la ciudadanía.
        </p>
        <p class="text-justify texto-sangria">
            Sin otro particular, hago propicia la ocasión para enviarle un cordial saludo.
        </p>
        <div class="salto-solo-si-es-necesario">
            <p class="texto-resaltar" style="margin-bottom: 2cm;">ATENTAMENTE</p>
            <div id="firma-titular-contraloria" class="texto-resaltar">
                <?= mb_strtoupper($oficio_de['nombre']); ?><br>
                <?= mb_strtoupper($oficio_de['cargo']); ?>
                <input type="hidden" name="constantes[<?= OED_TITULAR_EMPLEADOS_ID; ?>]" value="<?= isset($r[OED_TITULAR_EMPLEADOS_ID]) && !empty($r[OED_TITULAR_EMPLEADOS_ID]) ? $r[OED_TITULAR_EMPLEADOS_ID] : $oficio_de['empleados_id']; ?>">
            </div>
            <?php if (CONTRALORIA_MOSTRAR_MISION): ?>
                <div id="mision" class="texto-mision">
                    <br><b>MISIÓN</b><br>
                    <?= $this->CYSA_model->get_mision(); ?>
                </div>
            <?php endif; ?>
            <div class="texto-ccp">
                C.c.p. <?php $ccp_texto_plantilla = $this->CYSA_model->get_ccp_template(); ?>
                <?php $aux = isset($r) && isset($r[ED_CCP]) ? nl2br($r[ED_CCP]) : nl2br($ccp_texto_plantilla); ?>
                <?= span_editable($r, ED_CCP, $aux, NULL, NULL, 1); ?><br>
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
<script>
    $(document).ready(function () {
        var mostrar = true;
        plurales(mostrar, ".plurales");
    });
</script>