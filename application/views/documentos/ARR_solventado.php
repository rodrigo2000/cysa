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
                            <div>ACTA DE RESULTADOS DE REVISIÓN</div>
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
                        <span class="bg-white">
                            En la ciudad de Mérida, capital del Estado de Yucatán, Estados Unidos Mexicanos, con fundamento en los
                            artículos 97 y 114 último párrafo de la Ley de Responsabilidades Administrativas del Estado de Yucatán, siendo las
                            <?= span_editable($r, ACTA_REV_HORA_INI, 'HH:MM', 'Horario', 'Hora de inicio de la lectura al acta de Resultados de Auditoría, en sistema horario de 24 horas. Ejemplo: 08:00'); ?>
                            horas del día
                            <?= span_editable($r, ACTA_REV_FECHA, NULL, 'Fecha del Acta', 'Fecha del Acta de Resultados de Auditoría, en formato clásico (día, mes, año). Ejemplo: 10 de diciembre de 2018'); ?>;
                            se reúnen en la
                            <?= span_editable($r, ACTA_REV_UBICACION, NULL, 'Unidad Administrativa', 'Nombre de la Unidad Administrativa donde se lleva a cabo la notificación del resultado de revisión de la auditoría') ?>,
                            que se ubica en
                            <?= span_editable($r, ACTA_REV_DIRUBICACION, NULL, 'Domicilio de la UA', 'Domicilio de la Unidad Administrativa en la cual se realizará el acto de notificación de los resultados de revisión de la auditoría. Ejemplo "la calle 50 número 471 por 51 y 53 del centro de esta ciudad".') ?>;
                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO); ?>,
                            y por la <?= LABEL_CONTRALORIA; ?>
                            <?= span_resaltar((intval($director['empleados_genero']) === GENERO_FEMENINO ? ' la ' : ' el ') . $director['empleados_nombre_titulado'] . ", " . $director['empleados_cargo']); ?>,
                            <?= span_resaltar((intval($subdirector['empleados_genero']) === GENERO_FEMENINO ? ' la ' : ' el ') . $subdirector['empleados_nombre_titulado'] . ", " . $subdirector['empleados_cargo']); ?>,
                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA) ?>
                            <?= span_agregar_asistencias($documento['asistencias'], TIPO_ASISTENCIA_TESTIGO) ?>,
                            <span class="testigos">
                                <singular>este</singular><plural>estos</plural> último<plural>s</plural> en calidad de testigo<plural>s</plural>
                            </span>; a efecto de dar a conocer el resultado de la revisión de la auditoría
                            <?= span_resaltar($auditoria['numero_auditoria']); ?>,
                            que tiene por objetivo
                            <?= span_resaltar($auditoria['auditorias_objetivo']); ?>.
                        </span>
                    </p>
                    <p class="text-justify bg-punteado">
                        <span class="bg-white">
                            Con fecha
                            <?= span_resaltar($fecha_limite_para_solventar); ?>
                            <?php $observaciones_plural = TRUE; ?>
                            venció el plazo para solventar la<?= $observaciones_plural ? 's' : ''; ?> observaci<?= $observaciones_plural ? 'ones' : 'ón'; ?>,
                            por lo que se procedió a la revisión y análisis de la documentación e información proporcionada por
                            el (los) responsable(s)
                            obteniéndose lo siguiente:
                        </span>
                    </p>
                    <p class="text-xs-center bg-punteado">
                        <span class="bg-white" style="padding-left:5px; padding-right: 5px; font-weight: bolder;">RESULTADOS DE REVISIÓN DE LA AUDITORÍA</span>
                    </p>
                    <table id="tabla_observaciones" border="1" style="padding: .3em .6em;">
                        <?php $observaciones_solventadas = $observaciones_no_solventadas = array(); ?>
                        <?php foreach ($auditoria['observaciones'] as $o): ?>
                            <?php foreach ($o['recomendaciones'] as $r): ?>
                                <?php foreach ($r['avances'] as $a): ?>
                                    <?php if ($a['recomendaciones_avances_recomendaciones_status_id'] == OBSERVACIONES_STATUS_SOLVENTADA): ?>
                                        <?php array_push($observaciones_solventadas, $o['observaciones_numero']); ?>
                                    <?php else: ?>
                                        <?php array_push($observaciones_no_solventadas, $o['observaciones_numero']); ?>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="2" class="text-xs-center">Cédula número <?= $o['observaciones_numero']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-xs-center">Recomendación <?= $o['observaciones_numero'] . "." . $r['recomendaciones_numero']; ?></td>
                                        <td class="text-xs-center"><?= $a['empleado']['empleados_nombre_titulado_siglas']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <?= $a['recomendaciones_avances_descripcion']; ?>
                                            <div style="margin-top:1em;"><strong>Estado:</strong> <?= $status_label[$a['recomendaciones_avances_recomendaciones_status_id']]; ?>.</div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </table>
                    <?php $observaciones_solventadas = array_unique($observaciones_solventadas); ?>
                    <?php $observaciones_no_solventadas = array_unique($observaciones_no_solventadas); ?>
                    <p class="text-justify bg-punteado">
                        <span class="bg-white">
                            Como resultado de la revisión realizada, se hace del conocimiento al Titular de la Unidad Administrativa auditada,
                            que la<?= count($observaciones_solventadas) > 1 ? 's' : ''; ?> observaci<?= count($observaciones_solventadas) > 1 ? 'ones' : 'ón'; ?>
                            <?= span_resaltar(implode(", ", $observaciones_solventadas)); ?>
                            <?php if (!empty($observaciones_solventadas)): ?>
                                fue<?= count($observaciones_solventadas) > 1 ? 'ron' : ''; ?> solventada<?= count($observaciones_solventadas) > 1 ? 's' : ''; ?> satisfactoriamente,
                                <?php if (empty($observaciones_no_solventadas)): ?>con lo anterior se desvirtúa la existencia de alguna probable falta administrativa, o en su caso, del daño o perjuicio determinado en las cédulas de observaciones, por lo que, con este documento concluye la auditoría <?= span_resaltar($auditoria['numero_auditoria']); ?><?php endif; ?>.
                            <?php endif; ?>
                            <?php if (!empty($observaciones_no_solventadas)): ?>
                                Asimismo, las observaciones <?= implode(", ", $observaciones_no_solventadas); ?> no fue(ron) solventadas,
                                ya que no se cumpli<?= count($observaciones_no_solventadas) > 1 ? 'eron' : 'ó'; ?> con <?= count($observaciones_no_solventadas) > 1 ? 'los' : 'el'; ?> acuerdo<?= count($observaciones_no_solventadas) > 1 ? 's' : ''; ?> firmados<?= count($observaciones_no_solventadas) > 1 ? 's' : ''; ?> en la Cédula de Observaciones,
                                por lo que se determina la existencia de alguna probable falta administrativa, daño o perjuicio establecidos
                                en las Cédulas de Observaciones.
                            <?php endif; ?>
                        </span>
                    </p>
                    <?php if (empty($observaciones_no_solventadas)): ?>
                        <p class="text-justify bg-punteado">
                            <span class="bg-white">
                                De igual forma, le exhortamos a que todo el personal de
                                <?= span_resaltar($texto_foja, 'UA Involucradas', 'Nombre de la(s) unidad(es) administrativa(s) involucradas en los resultados de la revisión de la auditoría'); ?>
                                continúe realizando sus funciones de acuerdo a la normatividad vigente, con el fin de promover la optimización
                                y transparencia en el manejo de los recursos para beneficio de la ciudadanía.
                            </span>
                        </p>
                        <p class="text-justify bg-punteado">
                            <span class="bg-white">
                                Asimismo, es importante establecer que el adecuado ambiente de control es responsabilidad de la unidad administrativa sujeta a revisión.
                            </span>
                        </p>
                    <?php else: ?>
                        <?php foreach ($auditoria['observaciones'] as $o): ?>
                            <?php foreach ($o['recomendaciones'] as $r): ?>
                                <?php foreach ($r['avances'] as $a): ?>
                                    <?php if ($a['recomendaciones_avances_recomendaciones_status_id'] != OBSERVACIONES_STATUS_SOLVENTADA): $sr = $a['empleado']; ?>
                                        <?php $declaracion = (isset($documento['documentos_id']) && !empty($documento['documentos_id']) ? $this->Asistencias_declaraciones_model->get_declaracion($documento['documentos_id'], $sr['empleados_id']) : NULL); ?>
                                        <p class="text-justify bg-punteado">
                                            <span class="bg-white">
                                                El servidor público <?= span_resaltar($sr['empleados_nombre_titulado'] . ", " . $sr['empleados_cargo']); ?>;
                                                quien manifiesta ser de nacionalidad mexicana y con domicilio particular en
                                                <?= span_resaltar($sr['empleados_domicilio']); ?>,
                                                de la localidad de <?= span_resaltar($sr['empleados_localidad']); ?> se identifica con
                                                <?= span_resaltar(get_identificacion($sr)); ?>,
                                                la cual contiene su nombre y fotografía que concuerda con sus rasgos fisonómicos y en la que se aprecia su firma,
                                                que reconoce como suya por ser la misma que utiliza para validar todos sus actos públicos como privados;
                                                con relación a los resultados de la revisión de la(s) observación(es) que le fueron dados a conocer, declara:
                                                <span id="<?= $sr['empleados_id']; ?>" name="declaraciones[]" contenteditable="true" class="editable" default-value="[TECLEAR LOS COMENTARIOS DEL FUNCIONARIO]"><?= !empty($declaracion) ? $declaracion : ''; ?></span>
                                            </span>
                                        </p>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <?php $span = span_editable($r, ACTA_REV_DECLARACIONES, '[ACLARACIONES]'); ?>
                        <?= agregar_parrafo_show_hide($r, ACTA_REV_DECLARACIONES, $span, 'Agregar párrafo de declaraciones'); ?>
                        <p class="text-justify bg-punteado">
                            <span class="bg-white involucrados">
                                Habiendo escuchado <singular>al</singular><plural>a los</plural> responsable<plural>s</plural> de solventar la<plural>s</plural>
                                observaci<singular>ón</singular><plural>ones</plural>, se le<plural>s</plural> notifica que, debido a que
                                el plazo otorgado para solventar la<plural>s</plural> obsevaci<singular>ón</singular><plural>ones</plural> venció el
                                <?= span_resaltar($fecha_limite_para_solventar); ?>
                                y a la presente fecha no ha<plural>n</plural> sido solventada<plural>s</plural>, se turnará el presente expediente al
                                área competente para que realice la investigación para determinar la probable falta administrativa por parte de
                                los Servidores Públicos Municipales.
                            </span>
                        </p>
                        <?php $txt_parrafo_omision = 'Se hace del conocimiento de los presentes que la omisión o negativa de firma no afecta la validez y efectos legales de la presente acta'; ?>
                        <?= agregar_parrafo_show_hide($r, ACTA_REV_ADD_PARRAFO_OMISION_FIRMAS, $txt_parrafo_omision, 'Agregar párrafo de omisión de firmas'); ?>
                    <?php endif; ?>
                    <p class="text-justify bg-punteado">
                        <span class="bg-white">
                            Leída la presente acta y no habiendo más hechos que plasmar se da por concluida la diligencia siendo las
                            <?= span_editable($r, ACTA_REV_HORA_FIN, 'HH:MM'); ?>
                            horas de la misma fecha en que fue iniciada. Asimismo, previa lectura de lo asentado, los que en ella
                            intervinieron la firman al margen y al calce de todas y cada una de las fojas, haciéndose constar que
                            este documento fue elaborado en
                            <?= span_editable($r, ACTA_REV_NUMERO_EJEMPLARES, '2'); ?>
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
<script>
    $(document).ready(function () {
        var mostrar = $("div.firmas_empleado", ".firmas_testigos").length;
        plurales(mostrar, ".testigos");
        var mostrar = $("div.firmas_empleado", ".firmas_involucrados").length;
        plurales(mostrar, ".involucrados");
    });
</script>