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

/**
 * Crea una etiqueta SPAN para editar texto
 * @param array $r Arreglo con los valores del documento
 * @param integer $constante Constante del documento a utilizar
 * @param string $default_value Cadena de texto que tendrá el SPAN de forma predeterminada
 * @return string Código HTML de la etiqueta SPAN
 */
function span_editable($r, $constante, $default_value = SIN_ESPECIFICAR) {
    $html = '<span id="' . $constante . '" contenteditable="true" class="editable" default-value="' . $default_value . '">' . (isset($r) && isset($r[$constante]) ? $r[$constante] : '') . '</span>';
    return $html;
}

/**
 * Permite crear una etiqueta SPAN para seleccionar una fecha de calendario
 * @param array $r Arreglo con los valores del documento
 * @param integer $constante Constante del documento a utilizar
 * @return string Código HTML de la etiqueta SPAN
 */
function span_calendario($r, $constante) {
    $fecha = isset($r) && isset($r[$constante]) ? $r[$constante] : date('Y-m-d');
    $html = '<a href="#" class="xeditable" id="' . $constante . '" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="1" data-title="Seleccione fecha:" data-value="' . $fecha . '">' . mysqlDate2Date($fecha) . '</a>';
    return $html;
}

/**
 * Función para generar un párrafo que se muestra y oculta
 * @param array $r Arreglo con los valores del documento
 * @param integer $constante Constante del documento a utilizar
 * @param string $texto_parrafo Cadena de texto que se mostrará en el párrafo
 * @param string $etiqueta_boton Cadena de texto que tendrá el botón
 * @return string Código HTML del párrafo
 */
function agregar_parrafo_show_hide($r, $constante, $texto_parrafo = SIN_ESPECIFICAR, $etiqueta_boton = 'Agregar párrafo') {
    $html = '<p class="text-justify ' . (isset($r) && isset($r[$constante]) && $r[$constante] == 1 ? 'bg-punteado text-justify texto-sangria' : 'text-xs-center') . ' show-hide">'
            . '<span id="parrafo'.$constante.'" class="bg-white ' . (isset($r) && isset($r[$constante]) && $r[$constante] == 1 ? '' : 'hidden-xs-up') . '">'
            . $texto_parrafo . '</span>'
            . '<button type="button" onclick="ocultar_parrafo(\'parrafo' . $constante . '\', this);" class="btn btn-sm btn-danger btn-hide hidden-print ' . (!isset($r) || !isset($r[$constante]) || empty($r[$constante]) || $r[$constante] == 0 ? 'hidden-xs-up' : '') . '"><i class="fa fa-close"></i></button>'
            . '<button type="button" onclick="mostrar_parrafo(\'parrafo' . $constante . '\', this);" class="btn btn-sm btn-success btn-show hidden-print ' . (isset($r) && isset($r[$constante]) && ($r[$constante] == 1 || !empty($r[$constante])) ? 'hidden-xs-up' : '') . '">' . $etiqueta_boton . '</button>'
            . '<input type="hidden" name="constantes[' . $constante . ']" value="' . (isset($r) && isset($r[$constante]) ? $r[$constante] : 0) . '">'
            . '</p>';
    return $html;
}

/**
 * Crea una etiqueta SPAN con la clase CSS "resaltar", la cual hace destacar el texto en modo pantalla
 * @param string $texto Cadena de texto a mostrar dentro de la etiqueta
 * @return string Código HTML de la etiqueta SPAN
 */
function span_resaltar($texto) {
    $html = '<span class="resaltar">' . $texto . '</span>';
    return $html;
}

/**
 * Crea un etiqueta SPAN con las funcionalidades para asignar asistencias al documento
 * @param array $asistencias Arreglo con los empleados que asisten al documento
 * @param integer $tipo_asistencia Identificador de la asistencia
 * @return string Código HTML de la etiqueta SPAN
 */
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
