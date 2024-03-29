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
 * @param string $title Cadena de texto a mostrar en el atributo TITLE de la etiqueta. Este atributo sirve para el tour de Ayuda.
 * @param string $description Descrpición del elemento
 * @param boolean $aceptar_enter TRUE para que en la etiqueta se acepte el ENTER dentro del Texto. FALSE para cualquier otro caso.
 * @param boolean $nl2br TRUE para indicar que se usará la función nl2br() para la constante. FALSE para indicar que NO se use.
 * @return string Código HTML de la etiqueta SPAN
 */
function span_editable($r, $constante, $default_value = NULL, $title = NULL, $descripcion = NULL, $aceptar_enter = FALSE, $nl2br = FALSE) {
    if (empty($default_value)) {
        $default_value = SIN_ESPECIFICAR;
    }
    $real_title = $real_descripcion = NULL;
    if (!empty($title) && is_array($title) && isset($title[$constante])) {
        $real_title = $title[$constante];
    } elseif (!empty($title) && is_string($title)) {
        $real_title = $title;
    }
    if (!empty($descripcion) && is_array($descripcion) && isset($descripcion[$constante])) {
        $real_descripcion = $descripcion[$constante];
    } elseif (!empty($descripcion) && is_string($descripcion)) {
        $real_descripcion = $descripcion;
    }
    $r[$constante] = trim($r[$constante]);
    $r[$constante] = trim($r[$constante],'<br>');
    $html = '<span '
            . 'id="' . $constante . '" '
            . 'contenteditable="true" '
//            . (!empty($title) ? 'title="' . $title . '"' : '') . ' '
            . 'class="editable" '
            . ($aceptar_enter ? 'aceptar-enter="1"' : '') . ' '
            . 'default-value="' . $default_value . '" '
            . (!empty($real_title) ? 'data-tour-title="' . htmlentities($real_title) . '" ' : '')
            . (!empty($real_descripcion) ? 'data-tour-description="' . htmlentities($real_descripcion) . '" ' : '')
            . '>'
            . (isset($r, $r[$constante]) ? ($nl2br ? nl2br($r[$constante]) : $r[$constante]) : '')
            . '</span>';
    return $html;
}

/**
 * Permite crear una etiqueta SPAN para seleccionar una fecha de calendario
 * @param array $r Arreglo con los valores del documento
 * @param integer $constante Constante del documento a utilizar
 * @return string Código HTML de la etiqueta SPAN
 */
function span_calendario($r, $constante) {
    $fecha = isset($r, $r[$constante]) ? $r[$constante] : date('Y-m-d');
    $html = '<a href="#" class="xeditable" id="' . $constante . '" data-type="date" data-placement="top" data-format="yyyy-mm-dd" data-viewformat="dd/mm/yyyy" data-pk="1" data-title="Seleccione fecha:" data-value="' . $fecha . '">' . mysqlDate2Date($fecha) . '</a>';
    return $html;
}

function span_show_hide($r, $constante, $texto_span = SIN_ESPECIFICAR, $etiqueta_boton = NULL) {
    $con_valor = (isset($r, $r[$constante]) && ($r[$constante] == 1 || !empty($r[$constante])));
    if (empty($etiqueta_boton)) {
        $etiqueta_boton = 'X';
    }
    $html = '<span class="show-hide">'
            . '<span id="span' . $constante . '" class="bg-white ' . ($con_valor ? '' : 'hidden-xs-up') . '">'
            . $texto_span
            . '</span>'
            . '<button type="button" onclick="ocultar_span(\'span' . $constante . '\', this);" class="btn btn-sm btn-danger btn-hide hidden-print ' . (!$con_valor ? 'hidden-xs-up' : '') . '"><i class="fa fa-close"></i></button>'
            . '<button type="button" onclick="mostrar_span(\'span' . $constante . '\', this);" class="btn btn-sm btn-success btn-show hidden-print ' . ($con_valor ? 'hidden-xs-up' : '') . '">' . $etiqueta_boton . '</button>'
            . "</span>";
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
    $con_valor = (isset($r, $r[$constante]) && ($r[$constante] == 1 || !empty($r[$constante])));
    $html = '<p class="text-justify ' . ($con_valor ? 'bg-punteado text-justify texto-sangria' : 'text-xs-center') . ' show-hide">'
            . '<span id="parrafo' . $constante . '" class="bg-white ' . ($con_valor ? '' : 'hidden-xs-up') . '">'
            . $texto_parrafo . '</span>'
            . '<button type="button" onclick="ocultar_parrafo(\'parrafo' . $constante . '\', this);" class="btn btn-sm btn-danger btn-hide hidden-print ' . (!$con_valor ? 'hidden-xs-up' : '') . '"><i class="fa fa-close"></i></button>'
            . '<button type="button" onclick="mostrar_parrafo(\'parrafo' . $constante . '\', this);" class="btn btn-sm btn-success btn-show hidden-print ' . ($con_valor ? 'hidden-xs-up' : '') . '">' . $etiqueta_boton . '</button>'
            . '<input type="hidden" name="constantes[' . $constante . ']" value="' . (isset($r, $r[$constante]) ? $r[$constante] : 0) . '">'
            . '</p>';
    return $html;
}

/**
 * Crea una etiqueta SPAN con la clase CSS "resaltar", la cual hace destacar el texto en modo pantalla
 * @param string $texto Cadena de texto a mostrar dentro de la etiqueta
 * @param string $title Cadena de texto a mostrar en el atributo TITLE de la etiqueta. Este atributo sirve para el tour de Ayuda.
 * @param string $descripcion Cadena de texto con la descripción del atributo
 * @param string $css_class Nombre de las clases adicionales que se incluirán dentro del atriburo CLASS de la etiqueta SPAN
 * @param string $tooltiptext Cadena de texto del título de la funcionalidad de Tour
 * @return string Código HTML de la etiqueta SPAN
 */
function span_resaltar($texto, $title = NULL, $descripcion = NULL, $css_class = NULL, $tooltiptext = NULL) {
    $real_title = $real_descripcion = NULL;
    if (!empty($title) && is_array($title) && isset($title[$constante])) {
        $real_title = $title[$constante];
    } elseif (!empty($title) && is_string($title)) {
        $real_title = $title;
    }
    if (!empty($descripcion) && is_array($descripcion) && isset($descripcion[$constante])) {
        $real_descripcion = $descripcion[$constante];
    } elseif (!empty($descripcion) && is_string($descripcion)) {
        $real_descripcion = $descripcion;
    }
    $html = '<span ' . (!empty($tooltiptext) ? 'title="' . $tooltiptext . '"' : '') . ' class="resaltar '
            . $css_class . '" '
            . (!empty($real_title) ? 'data-tour-title="' . htmlentities($real_title) . '" ' : '')
            . (!empty($real_descripcion) ? 'data-tour-description="' . htmlentities($real_descripcion) . '" ' : '')
            . '>'
            . $texto
            . '</span>';
    return $html;
}

function span_resaltar_constante($r, $constante, $title = NULL, $descripcion = NULL, $css_class = NULL) {
    $return = "";
    if (isset($r, $r[$constante])) {
        $texto = $r[$constante];
        $return = span_resaltar($texto, $title, $descripcion, $css_class);
    }
    return $return;
}

function span_resaltar_constante_fecha($r, $constante, $title = NULL, $descripcion = NULL, $css_class = NULL) {
    $return = "";
    if (isset($r, $r[$constante])) {
        $texto = mysqlDate2OnlyDate($r[$constante]);
        $return = span_resaltar($texto, $title, $descripcion, $css_class);
    }
    return $return;
}

/**
 *
 * @param array $r Arreglo con los valores del documento
 * @param integer $constante Constante del documento a utilizar
 * @param array $opciones Arerglo con las opciones a visualizar en la etiqueta
 * @param boolean $usar_index TRUE para indicar que se enviará el índice de arreglo como valor a enviar. FALSE para indiciar que se enviará el elemento del índice.
 * @return string
 */
function span_opciones($r, $constante, $opciones = array(), $usar_index = TRUE) {
    $label = $r[$constante];
    $default_value = $r[$constante];
    if ($usar_index) {
        $label = $opciones[$r[$constante]];
    }
    $html = '<span id="' . $constante . '" '
            . 'name="constantes[' . $constante . ']" '
            . 'class="resaltar opciones" '
            . 'default-value="' . $default_value . '" '
            . 'data-opciones="' . implode('|', $opciones) . '"'
            . '>' . $label . '</span>';
    return $html;
}

/**
 *
 * @param integer $tipo_asistencia
 * @param string $label_boton
 * @return string
 */
function genera_boton_autocomplete($tipo_asistencia, $label_boton = NULL) {
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
    $html = '<span id="autocomplete_' . $tipo[$tipo_asistencia] . '" class="input-group hidden-xs-up hidden-print">
        <input type="text" class="autocomplete form-control" placeholder="Empleado">
        <span class="input-group-btn">
            <button class="btn btn-sm btn-danger ocultar" type="button" style="padding-top:1px; padding-bottom:2px;"><i class="fa fa-close"></i></button>
        </span>,
    </span>
    </span>
    <a class="btn btn-sm btn-success hidden-print btn_agregar" href="#" data-tipo="' . $tipo[$tipo_asistencia] . '" data-asistencias-tipo="' . $tipo_asistencia . '">' . $label[$tipo_asistencia] . '</a>';
    return $html;
}

/**
 * Crea un etiqueta SPAN con las funcionalidades para asignar asistencias al documento
 * @param array $asistencias Arreglo con los empleados que asisten al documento
 * @param integer $tipo_asistencia Identificador de la asistencia
 * @param array $auditoria Arreglo que contiene las variables de la auditoría
 * @return string Código HTML de la etiqueta SPAN
 */
function span_agregar_asistencias($asistencias, $tipo_asistencia, $auditoria = NULL) {
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
        if (isset($d[$tipo_asistencia]) && is_array($d[$tipo_asistencia]) && !empty($d[$tipo_asistencia]) && $direcciones_id > 0) {
            $aux = $CI->SAC_model->get_direccion($direcciones_id);
            $html .= '<span class="resaltar" id="direcciones_' . $direcciones_id . '">';
            $aux['nombre_completo_direccion'];
            $asistentes = array();
            foreach ($d[$tipo_asistencia] as $e) {
                $total_asistentes++;
                $asistentes[] = '<span class="resaltar empleado_' . $e['empleados_id'] . '">'
                        . (intval($e['empleados_genero']) === GENERO_FEMENINO ? ' la ' : ' el ')
                        . $e['empleados_nombre_titulado'] . ", " . $e['empleados_cargo']
                        . (!empty($auditoria) && isset($auditoria['auditorias_enlace_designado']) && $e['empleados_id'] == $auditoria['auditorias_enlace_designado'] ? ', Enlace Designado' : '')
                        . '<input type="hidden" name="' . $tipo[$tipo_asistencia] . '[]" value="' . $e['empleados_id'] . '">'
                        . '<span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="' . $e['empleados_id'] . '">&times;</span>'
                        . '</span>'
                        . '</span>';
            }
            if (count($asistentes) > 1) {
                $ultimo = array_pop($asistentes);
                $html .= implode(", ", $asistentes) . '<plural class="conjuncion"> y </plural>' . $ultimo;
            } else {
                $html .= implode(", ", $asistentes);
            }
        }
    }
    $html .= genera_boton_autocomplete($tipo_asistencia);
    return $html;
}

/*
 * Devuelve la cadena con el método de identificación de un empleado
 * @param array $empleado Arreglo con la información del empleado
 * @return Cadena con el método de identificación
 */

function get_identificacion($empleado) {
    $return = SIN_ESPECIFICAR_BOLD;
    if (isset($empleado['empleados_licencia_manejo']) && !empty($empleado['empleados_licencia_manejo'])) {
        $return = "licencia de conducir con folio " . $empleado['empleados_licencia_manejo'];
    } elseif (isset($empleado["empleados_pasaporte"]) && !empty($empleado['empleados_pasaporte'])) {
        $return = "pasaporte con folio " . $empleado['empleados_pasaporte'];
    } elseif (isset($empleado["empleados_cedula_profesional"]) && !empty($empleado['empleados_cedula_profesional'])) {
        $return = "cédula profesional con folio " . $empleado["empleados_cedula_profesional"];
    } elseif (isset($empleado['empleados_credencial_elector_delante'])) {
        $return = 'credencial para votar con clave de elector '
                . (!empty($empleado['empleados_credencial_elector_delante']) ? $empleado['empleados_credencial_elector_delante'] : '<a href="#" class="sin-especificar" basedatos="' . APP_DATABASE_SAC . '" tabla="empleados" campo="empleados_credencial_elector_delante" where="empleados_credencial_elector_delante" where_valor="' . $empleado['empleados_credencial_elector_delante'] . '" title="Clic para cambiar el valor">' . SIN_ESPECIFICAR_BOLD . '</a>')
                . ' y número identificador '
                . (!empty($empleado['empleados_credencial_elector_detras']) ? $empleado['empleados_credencial_elector_detras'] : '<a href="#" class="sin-especificar" basedatos="' . APP_DATABASE_SAC . '" tabla="empleados" campo="empleados_credencial_elector_detras" where="empleados_credencial_elector_detras" where_valor="' . $empleado['empleados_credencial_elector_detras'] . '" title="Clic para cambiar el valor">' . SIN_ESPECIFICAR_BOLD . '</a>');
    }
    return $return;
}

/**
 * Genera el texto de asistencias
 * @param array $asistencias Arreglo con las asistencias al documento
 * @param boolean $distribuir TRUE indica que se ordenaran por UA, FALSE indica que es indistinto
 * @param integer $tipo_asistencia Identificador del tipo de asistencia que se considerará para generar el texto
 * @param boolean $solo_nombre TRUE para indicar que solo muestre el nombre, FALSE indicará nombre + cargo
 * @param boolean $incluir_domicilio TRUE para agregar el domicilio del empleado, FALSE para cualquier otro caso.
 * @param boolean $incluir_articulo TRUE indica que se agregarán artículos gramaticales. FALSE no los agrega.
 * @param boolean $enlade_designado Identificador del empleado que funge como enlace designado para añadirle el texto de "Enlace designado". Por default es NULL
 * @param string $post_texto_adicional Texto adicional que se incluirá al final de cada empleado.
 * @param string $separador Caracter por el cual se separan los empleados. Por default es el punto y coma.
 * @return string Texto completo
 */
function crear_texto_asistencias($asistencias = array(), $distribuir = TRUE, $tipo_asistencia = NULL, $solo_nombre = FALSE, $incluir_domicilio = FALSE, $incluir_articulos = FALSE, $enlace_designado = NULL, $post_texto_adicional = NULL, $separador = ";") {
    $return = "";
    if (is_array($asistencias) && !empty($asistencias)) {
        $a = array();
        $aux = "";
        foreach ($asistencias as $direcciones_id => $d) {
            if ($distribuir) {
                $aux .= '<span class="direcciones_' . $direcciones_id . '">';
            }
            if (isset($d[$tipo_asistencia])) {
                foreach ($d[$tipo_asistencia] as $index => $e) {
                    if (!empty($e)) {
                        $aux = "";
                        if ($incluir_articulos) {
                            if ($incluir_domicilio) {
                                $aux = " el ";
                            } else {
                                $aux .= (intval($e['empleados_genero']) === GENERO_FEMENINO ? ' la ' : ' el ');
                            }
                        }
                        if ($incluir_domicilio) {
                            $aux .= ' servidor público ';
                        }
                        $aux .= $e['empleados_nombre_titulado'] . ($solo_nombre ? NULL : ', ' . $e['empleados_cargo']) . (!empty($enlace_designado) && $enlace_designado == $e['empleados_id'] ? ', Enlace Designado' : NULL);
                        if ($incluir_domicilio) {
                            $aux .= ", quien manifiesta ser de nacionalidad mexicana y con domicilio particular en "
                                    . (!empty($e['empleados_domicilio']) ? $e['empleados_domicilio'] : '<a href="#" class="sin-especificar" basedatos="' . APP_DATABASE_SAC . '" tabla="empleados" campo="empleados_domicilio" where="empleados_domicilio" where_valor="' . $e['empleados_domicilio'] . '" title="Clic para cambiar el valor">' . SIN_ESPECIFICAR_BOLD . '</a>')
                                    . " de la localidad de "
                                    . (!empty($e['empleados_localidad']) ? Capitalizar($e['empleados_localidad']) : '<a href="#" class="sin-especificar" basedatos="' . APP_DATABASE_SAC . '" tabla="empleados" campo="empleados_localidad" where="empleados_id" where_valor="' . $e['empleados_id'] . '" title="Clic para cambiar el valor">' . SIN_ESPECIFICAR_BOLD . '</a>')
                                    . " se identifica con "
                                    . get_identificacion($e)
                                    . ', la cual contiene su nombre y fotografía que concuerda con sus rasgos fisonómicos y en la que se aprecia '
                                    . 'su firma, que reconoce como suya por ser la misma que utiliza para validar todos sus actos tanto públicos '
                                    . 'como privados'
                                    . '<input type="hidden" name="testigos[]" value="' . $e['empleados_id'] . '">'
                                    . '<span type="button" class="autocomplete_empleados_delete label label-danger" title="Eliminar" data-empleados-id="' . $e['empleados_id'] . '">&times;</span>'
                            ;
                        }
                        $css_class = "empleado_" . $e['empleados_id'];
                        $str = span_resaltar($aux, $css_class);
                        array_push($a, $str);
                    }
                }
            }
            if ($distribuir) {
                $aux .= '</span>';
            }
        }
        $cadena = "";
        if (count($a) > 0) {
            $ultimo = NULL;
            if (count($a) > 1) {
                $ultimo = '<plural class="conjuncion"> y </plural>' . array_pop($a);
            }
            $cadena = implode($separador . " ", $a) . $ultimo;
        }
        $return = $cadena;
    }
    return $return;
}

/**
 * Conviernte una cadena HTML a su representación en texto plano
 * @param string $html Cadena que contiene etiquetas HTML
 * @return string Cadena sin etiquetas HTML
 */
function my_strip_tags($html = NULL) {
    $txt = '';
    if (!empty($html)) {
        $txt = str_ireplace("\n", "", $html);
        $txt = str_ireplace(array('</p>', '<br>'), "\n", $txt);
        $txt = strip_tags($txt);
        $txt = html_entity_decode($txt);
    }
    return $txt;
}

/**
 * Regresa la cantidad de días entres dos fechas proporcionadas
 * @param string $fecha1 Cadena de fecha en formato YYYY-MM-DD
 * @param string $fecha2 Cadena de fecha en formato YYYY-MM-DD
 * @return integer Número de dias entre las fechas. Si el número es positivo significa que fecha1 es mayor a fecha. Si el número es negativo significa que fecha2 es mayor a fecha1.
 */
function diferencia_entre_fechas($fecha1, $fecha2) {
    if (empty($fecha1) || empty($fecha2)) {
        return NULL;
    }
    $date1 = strtotime($fecha1);
    $date2 = strtotime($fecha2);
    $subTime = $date2 - $date1;
    $y = ($subTime / (60 * 60 * 24 * 365));
    $d = ($subTime / (60 * 60 * 24)) % 365;
    $h = ($subTime / (60 * 60)) % 24;
    $m = ($subTime / 60) % 60;

//    echo "Difference between " . date('Y-m-d H:i:s', $date1) . " and " . date('Y-m-d H:i:s', $date2) . " is:\n";
//    echo $y . " years\n";
//    echo $d . " days\n";
//    echo $h . " hours\n";
//    echo $m . " minutes\n";

    $fecha1 = new DateTime($fecha1);
    $fecha2 = new DateTime($fecha2);
    $signo = "+";
    if ($fecha1 > $fecha2) {
        $signo = "-";
    }

    return intval($signo . abs($d));
}

/**
 * Regresa la cantidad de días hábiles entre dos fechas
 * @param string $fecha1 Cadena de fecha en formato YYYY-MM-DD
 * @param string $fecha2 Cadena de fecha en formato YYYY-MM-DD
 * @return integer Cantidad de días hábiles
 */
function get_dias_habiles_entre_fechas($fecha1, $fecha2) {
    $dias = 0;
    if (empty($fecha1) || empty($fecha2)) {
        return NULL;
    }
    $fecha1 = strtotime($fecha1);
    $fecha2 = strtotime($fecha2);

    $signo = "";
    if ($fecha1 > $fecha2) {
        $aux = $fecha1;
        $fecha1 = $fecha2;
        $fecha2 = $aux;
        $signo = "-";
    }

    for ($fecha1; $fecha1 < $fecha2; $fecha1 = strtotime('+1 day ' . date('Y-m-d', $fecha1))) {
        $dia = date("N", $fecha1);
        if ($dia < 6) {
            $dias++;
        }
    }

    return intval($signo . $dias);
}
