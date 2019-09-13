<?php
$direcciones = array();
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
                            En la ciudad de Mérida, capital del Estado de Yucatán, Estados Unidos Mexicanos, con fundamento en lo
                            dispuesto en el tercer párrafo del artículo 113 de la Ley de Responsabilidades Administrativas del Estado
                            de Yucatán, siendo las
                            <?= span_editable($r, ACTA_RESULTADOS_HORA_INI, 'HH:MM'); ?>
                            horas del día
                            <?= span_calendario($r, ACTA_RESULTADOS_FECHA) ?>,
                            se reúnen en la
                            <?= span_editable($r, ACTA_RESULTADOS_UBICACION, 'Lugar donde se realiza la lectura') ?>,
                            ubicada en
                            <?= span_editable($r, ACTA_RESULTADOS_DIRUBICACION, 'calle ___ número ___ por __ y __'); ?>;
                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO); ?>,
                            por la <?= LABEL_CONTRALORIA; ?>
                            y en calidad de testigos
                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_TESTIGO) ?>;
                            a efecto de dar a conocer el resultado de la auditoría
                            <?= span_resaltar($auditoria['numero_auditoria']); ?>
                            que tiene por objetivo
                            <?= span_resaltar($auditoria['auditorias_objetivo']); ?>,
                            <span class="bg-white">
                                iniciada el
                                <?= span_resaltar(mysqlDate2OnlyDate($auditoria['auditorias_fechas_inicio_real'])) . ($auditoria['auditorias_tipo'] == 1 ? " de acuerdo al Programa Anual de Auditoría de la " . LABEL_CONTRALORIA : '') . ", "; ?>,
                                obteniéndose lo siguiente:
                            </span>
                        </span>
                    </p>
                    <p class="text-xs-center bg-punteado">
                        <span class="bg-white" style="padding-left:5px; padding-right: 5px; font-weight: bolder;">RESULTADOS DE AUDITORÍA</span>
                    </p>
                    <?php $con_valor = (isset($r) && isset($r[ACTA_RESULTADOS_REDACCION]) && ($r[ACTA_RESULTADOS_REDACCION] == 1 || !empty($r[ACTA_RESULTADOS_REDACCION]))); ?>
                    <div class="show-hide">
                        <div id="parrafo<?= ACTA_RESULTADOS_REDACCION; ?>" contenteditable="true" class="editable span <?= $con_valor ? 'bg-punteado text-justify texto-sangria' : 'text-xs-center'; ?>" aceptar-enter="1" default-value="[Redacción de información de inexistencia de Observaciones]" style="border: 1px solid black;">
                            <?= $con_valor ? nl2br($r[ACTA_RESULTADOS_REDACCION]) : ''; ?>
                        </div>
                        <button type="button" onclick="ocultar_parrafo('parrafo<?= ACTA_RESULTADOS_REDACCION; ?>', this);" class="btn btn-sm btn-danger btn-hide hidden-print <?= !$con_valor ? 'hidden-xs-up' : ''; ?>"><i class="fa fa-close"></i></button>
                        <button type="button" onclick="mostrar_parrafo('parrafo<?= ACTA_RESULTADOS_REDACCION; ?>', this);" class="btn btn-sm btn-success btn-show hidden-print <?= $con_valor ? 'hidden-xs-up' : ''; ?>">Agregar redacción</button>
                    </div>
                    <p class="text-justify bg-punteado">
                        <span class=" bg-white">
                            <span><?= !isset($r[ACTA_RESULTADOS_REDACCION]) ? "E" : "Como resultado de lo anterior, e"; ?></span>n
                            función del objetivo de la auditoría y de la aplicación de pruebas basadas en un muestreo sobre los controles
                            operantes, la normatividad vigente y la documentación proporcionada por el área auditada, concluimos la inexistencia
                            de observaciones.
                        </span>
                    </p>
                    <p class="text-justify bg-punteado">
                        <span class=" bg-white">
                            Asimismo, es importante establecer que el adecuado ambiente de control es responsabilidad de la unidad
                            administrativa sujeta a revisión.
                        </span>
                    </p>
                    <p class="text-justify bg-punteado">
                        <span class=" bg-white">
                            Por lo anterior, con el presente acto, se hace constar formalmente la conclusión de la auditoría <?= $auditoria['numero_auditoria']; ?>.
                        </span>
                    </p>
                    <p class="text-justify bg-punteado">
                        <span class=" bg-white">
                            De igual forma, le exhortamos a que todo el personal de
                            <?= $texto_foja; ?>
                            continúe realizando sus funciones de acuerdo a la normatividad vigente, con el fin de promover la
                            optimización y transparencia en el manejo de los recursos para beneficio de la ciudadanía.
                        </span>
                    </p>
                    <?php $txt = 'Se hace del conocimiento de los presentes que la omisión o negativa de firma dará lugar al levantamiento del acta correspondiente y que dicha circunstancia no afecta la validez y efectos legales de la presente acta.'; ?>
                    <?= agregar_parrafo_show_hide($r, ACTA_RESULTADOS_OMI_FIR, $txt, 'Agregar en caso de omitirse alguna firma'); ?>
                    <p class="text-justify bg-punteado">
                        <span class=" bg-white">
                            Leída la presente acta y no habiendo más hechos que plasmar se da por concluida la diligencia siendo las
                            <?= span_editable($r, ACTA_RESULTADOS_HORA_FIN, 'HH:MM'); ?>
                            horas de la misma fecha en que fue iniciada. Asimismo, previa lectura de lo asentado, los que en ella
                            intervinieron la firman al margen y calce de todas y cada una de las fojas, haciéndose constar que este
                            documento fue elaborado en
                            <?= span_editable($r, ACTA_RESULTADOS_NUMERO_EJEMPLARES, CONSTANTE_CANTIDAD_EJEMPLARES_ACTA); ?>
                            ejemplares originales, de los cuales se hace entrega de uno al servidor público con quien se entendió
                            la diligencia.
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
                            <?php
                            if (!isset($documento['asistencias'][APP_DIRECCION_CONTRALORIA])) {
                                $documento['asistencias'][APP_DIRECCION_CONTRALORIA] = array(TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA => array());
                            }
                            ?>
                            <?php array_unshift($documento['asistencias'][APP_DIRECCION_CONTRALORIA][TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA], $director, $subdirector); // Agregamos al Director y Subdirector ?>
                            <?php foreach ($documento['asistencias'] as $direcciones_id => $d): $direccion = $this->SAC_model->get_direccion($direcciones_id); ?>
                                <?php if (isset($d[TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA])): ?>
                                    <div class="direccion_<?= $direcciones_id; ?>">
                                        <p class="firmas_ua_nombre"><?= $direccion['direcciones_nombre']; ?></p>
                                        <?php foreach ($d[TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA] as $e): ?>
                                            <div class="firmas_empleado empleado_<?= $e['empleados_id']; ?>">
                                                <div class="firmas_empleado_nombre"><?= $e['empleados_nombre_titulado_siglas']; ?></div>
                                                <div class="firmas_empleado_cargo"><?= $auditoria['auditorias_auditor_lider'] == $e['empleados_id'] ? 'Auditor Líder' : $e['empleados_cargo']; ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
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
<div class="form-group hidden-print">
    <div class="col-sm-12 text-xs-center">
        <input type="hidden" name="<?= $this->module['id_field'] ?>" value="<?= $id; ?>">
        <input type="hidden" name="documentos_id" id="documentos_id" value="<?= isset($documento['documentos_id']) && $accion === "modificar" ? $documento['documentos_id'] : 0; ?>">
        <input type="hidden" name="accion" id="accion" value="<?= $accion; ?>">
        <input type="hidden" name="documentos_tipos_id" id="documentos_tipos_id" value="<?= $documento['documentos_versiones_documentos_tipos_id']; ?>">
        <input type="hidden" name="documentos_versiones_id" id="documentos_versiones_id" value="<?= $documento['documentos_versiones_id']; ?>">
    </div>
</div>