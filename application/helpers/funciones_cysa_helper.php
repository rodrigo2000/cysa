<?php

/*
 * Funciones genéricas dentro de CYSA
 */

function capitalizar($texto) {
    $return = "";
    $omitir = array("de", "la", "el", "del", "dif", "los", "y", "o", "en", "con", "a", "e", "o", "u", "su", "que", "se", "ha", "mas", "más", "él");
    if (!empty($texto)) {
        $texto = mb_strtolower($texto);
        $aux = explode(" ", $texto);
        $aux = array_map("trim", $aux);
        foreach ($aux as $index => $a) {
            if (!in_array($a, $omitir)) {
                $aux[$index] = ucfirst($a);
            }
        }
        // Sin importar cual sea la primera palabra, la convertirmos en mayúsculas
        $aux[0] = ucfirst($aux[0]);
        $return = implode(" ", $aux);
    }
    return $return;
}

/**
 * Verifica que la fecha proporcionada sea sábado o domingo
 * @param date $fecha Fecha en formato YYYY-MM-DD
 * @return boolean Devuelve TRUE cuando la fecha es sábado o domingo. FALSE en cualquier otro caso.
 */
function is_fin_de_semana($fecha) {
    $fecha = strtotime($fecha);
    $fecha = date("l", $fecha);
    $fecha = strtolower($fecha);
    $return = FALSE;
    if ($fecha == "saturday" || $fecha == "sunday") {
        $return = TRUE;
    }
    return $return;
}

function get_frase_de_ua($a) {
    $return = "";
    if (!empty($a)) {
        if ($a['direcciones_is_descentralizada'] != 1) {
            if ($a['cc_etiqueta_departamento'] != 1) {
                $return .= " al departamento de " . $a['departamentos_nombre'];
            }
            if (!empty($return)) {
                $return .= " de ";
            }
            $return .= " al organismo a su cargo";
        } else {
            if ($a['cc_etiqueta_departamento'] != 1) {
                $return = " al departamento de " . $a['departamentos_nombre'];
            }
            if ($a['cc_etiqueta_subdireccion'] != 1) {
                $return .= " de la Subdirección de " . $a['subdirecciones_nombre'];
            }
            if (empty($return)) {
                $return = "a";
            } else {
                $return .= " de ";
            }
            $return .= " la dirección a su cargo";
        }
    }
    return $return;
}

function span_editable($r, $constante, $default_value = SIN_ESPECIFICAR) {
    $html = '<span id="' . $constante . '" contenteditable="true" class="editable" default-value="' . $default_value . '">' . (isset($r) && isset($r[$constante]) ? $r[$constante] : '') . '</span>';
    return $html;
}

function span_calendario($r, $constante) {
    $fecha = isset($r) && isset($r[ACTA_RESULTADOS_FECHA]) ? $r[ACTA_RESULTADOS_FECHA] : date('Y-m-d');
    $html = '<a href="#" class="xeditable" id="' . $constante . '" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="1" data-title="Seleccione fecha:" data-value="' . $fecha . '">' . mysqlDate2Date($fecha) . '</a>';
    return $html;
}

function span_resaltar($texto) {
    $html = '<span class="resaltar">' . $texto . '</span>';
    return $html;
}

function span_agregar_asistencias($asistencias, $tipo_asistencia) {
    $label = array(
        TIPO_ASISTENCIA_RESPONSABLE => 'Agregar responsables',
        TIPO_ASISTENCIA_TESTIGO => 'Agregar testigos',
        TIPO_ASISTENCIA_INVOLUCRADO => 'Agregar involucrados',
        TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA => 'Agregar involucrados'
    );
    $tipo = array(
        TIPO_ASISTENCIA_RESPONSABLE => 'responsables',
        TIPO_ASISTENCIA_TESTIGO => 'testigos',
        TIPO_ASISTENCIA_INVOLUCRADO => 'involucrados',
        TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA => 'involucrados_contraloria'
    );
    $html = '<span id="seccion_' . $tipo[$tipo_asistencia] . '">';
    $CI = CI();
    $CI->load->model("SAC_model");
    $total_asistentes = 0;
    foreach ($asistencias as $direcciones_id => $d) {
        if (isset($d[$tipo_asistencia]) && is_array($d[$tipo_asistencia]) && !empty($d[$tipo_asistencia])) {
            $aux = $CI->SAC_model->get_direccion($direcciones_id);
            $html .= '<span class = "resaltar" id = "direcciones_' . $direcciones_id . '">';
            $aux['nombre_completo_direccion'];
            $asistentes = array();
            foreach ($d[$tipo_asistencia] as $e) {
                $total_asistentes++;
                $asistentes[] = '<span class="resaltar empleado_' . $e['empleados_id'] . '">'
                        . ($e['empleados_genero'] == GENERO_MASCULINO ? ' el ' : ' la ')
                        . $e['empleados_nombre_titulado'] . ", " . $e['empleados_cargo']
                        . '<input type="hidden" name="' . $tipo[$tipo_asistencia] . '[]" value="' . $e['empleados_id'] . '">'
                        . '<span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="' . $e['empleados_id'] . '">&times;</span>'
                        . '</span>'
                        . '</span>';
            }
            if (count($asistentes) > 1) {
                $ultimo = array_pop($asistentes);
                $html .= implode(", ", $asistentes) . '<span class="plural"> y </span>' . $ultimo;
            } else {
                $html .= implode(", ", $asistentes);
            }
        }
    }

    $html .= '<span id="autocomplete_' . $tipo[$tipo_asistencia] . '" class="input-group hidden-xs-up hidden-print">
        <input type="text" class="autocomplete form-control" placeholder="Empleado">
        <span class="input-group-btn">
            <button class="btn btn-danger ocultar" type="button"><i class="fa fa-close"></i></button>
        </span>,
    </span>
    </span>
    <a class="btn btn-sm btn-success hidden-print btn_agregar" href="#" data-tipo="' . $tipo[$tipo_asistencia] . '" data-asistencias-tipo="' . $tipo_asistencia . '">' . $label[$tipo_asistencia] . '</a>';
    return $html;
}

/*
 * Devuelve la cadena con el método de identificación de un empleado
 * @param array $empleado Arreglo con la información del empleado
 * @return Cadena con el método de identificación
 */

function get_identificacion($empleado) {
    $return = SIN_ESPECIFICAR;
    if (isset($empleado['empleados_licencia_manejo']) && !empty($empleado['empleados_licencia_manejo'])) {
        $return = "licencia de conducir con folio " . $empleado['empleados_licencia_manejo'];
    } elseif (isset($empleado['empleados_credencial_elector_delante'])) {
        $return = 'credencial para votar con clave de elector '
                . (!empty($empleado['empleados_credencial_elector_delante']) ? $empleado['empleados_credencial_elector_delante'] : SIN_ESPECIFICAR)
                . ' y número identificador '
                . (!empty($empleado['empleados_credencial_elector_detras']) ? $empleado['empleados_credencial_elector_detras'] : SIN_ESPECIFICAR);
    }
    return $return;
}
