<?php

function CI() {
    $CI = &get_instance();
    return $CI;
}

/*
 * Función que regresa la fecha y hora del momento en el que fue llamada
 */

function ahora() {
    return date("Y-m-d H:i:s");
}

/**
 * Función que regresa el nombre de día de la semana
 * @param mixed $d Numero del día de la semana
 * @return string Nombre del día de la semana
 */
function getNombreDelDia($d) {
    $d = intval($d);
    if ($d > 6) {
        return "";
    }
    $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
    return $dias[$d];
}

/**
 * Función que regresa el nombre de mes del año
 * @param mixed $m Numero del mes del año
 * @return string Nombre del mes del año
 */
function getNombreDelMes($m) {
    $m = intval($m);
    if ($m > 12) {
        return "";
    }
    $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    return $meses[$m];
}

function p($string) {
    return htmlentities($string, ENT_NOQUOTES, "UTF-8");
}

function mysqlDate2Date($f, $addBR = TRUE) {  // yyyy-mm-dd H:m:ss    ==>     13 de Febrero de 2015 <br> 00:00pm
    if (trim($f) == "")
        return "";
    $pos = strpos($f, " ");
    $hora = $fecha = "";
    if ($pos !== false) {
        list($fecha, $hora) = explode(" ", $f);
    } else {
        $fecha = $f;
        $hora = "";
    }
    list($a, $m, $d) = preg_split("/[\/|-]/", $fecha);
    if ($hora != "") {
        list($hh, $mm, $ss) = explode(":", $hora);
    }
    $ampm = "am";
    if (isset($hh) && intval($hh) > 12) {
        $ampm = "pm";
        $hh -= 12;
    }
    $cadena = $d . ' de ' . getNombreDelMes($m) . ' de ' . $a . ($hora != "" ? ($addBR ? "<br>" : '&nbsp;') . substr("0" . $hh, -2) . ":" . $mm . $ampm : '');
    return $cadena;
}

function mysqlDate2OnlyDate($f, $addDayName = FALSE) { // yyyy-mm-dd H:m:ss    ==>     Lunes, 13 de Febrero de 2015
    $cadena = "";
    if (!empty($f)) {
        $pos = strpos($f, " ");
        $fecha = "";
        if ($pos !== false) {
            list($fecha, $hora) = explode(" ", $f);
        } else {
            $fecha = $f;
        }

        if ($fecha !== "") {
            list($a, $m, $d) = preg_split("/[\/|-]/", $fecha);
            $dateVal = new DateTime($a . "-" . $m . "-" . $d);
            $dia = $dateVal->format("w"); // w = 0 (para domingo) hasta 6 (para sábado)
            $cadena = ($addDayName ? getNombreDelDia($dia) . ', ' : '') . $d . ' de ' . getNombreDelMes($m) . ' de ' . $a;
        }
    }
    return $cadena;
}

if (!function_exists('hex2bin')) {

    function hex2bin($str) {
        $sbin = "";
        $len = strlen($str);
        for ($i = 0; $i < $len; $i += 2) {
            $sbin .= pack("H*", substr($str, $i, 2));
        }

        return $sbin;
    }

}

function randomPassword($len = 8) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789()-$";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $len; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function random_color_part() {
    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

function encriptar($cadena) {
    $key = "Maravilloso que todo sea tan facil";
    $sha1 = sha1($key);
    $base64 = base64_encode($cadena);
    $min = min(array(strlen($sha1), strlen($base64)));
    $output = "";
    for ($i = 0; $i < $min; $i++) {
        $output .= $sha1[$i] . $base64[$i];
    }
    if (strlen($sha1) < strlen($base64)) {
        $output .= substr($base64, $min);
    } else {
        $output .= substr($sha1, $min);
    }
    return bin2hex($output);
}

function desencriptar($cadena) {
    $key = "Maravilloso que todo sea tan facil";
    $sha1 = sha1($key);
    $cadena = hex2bin($cadena);
    $max = strlen($cadena);
    $min = $max;
    $base64 = "";
    $shaMasLargo = (strlen($cadena) < strlen($sha1) * 2 );
    if ($shaMasLargo) {
        for ($i = $max; $i > 0; $i--) {
            $buscar = substr($sha1, $i);
            if (strlen($buscar) > 2) {
                if (strpos($cadena, $buscar) === FALSE) {
                    $min = $i + 1;
                    break;
                }
            }
        }
        for ($i = 1; $i <= $min * 2; $i = $i + 2) {
            $base64 .= $cadena[$i];
        }
    } else {
        for ($i = 1; $i <= strlen($sha1) * 2; $i = $i + 2) {
            $base64 .= $cadena[$i];
        }
        $base64 .= substr($cadena, strlen($sha1) * 2);
    }
    $output = base64_decode($base64);
    return $output;
}

function getIndexValueFromArray($searchKey, $array, $replace = FALSE) {
    $temp = array();
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            if (strpos($key, $searchKey) !== FALSE) {
                $key = ($replace !== FALSE ? str_replace($searchKey, $replace, $key) : $key);
                $temp[$key] = $value;
            }
        }
    }
    return $temp;
}

function isArrayEmpty($array) {
    foreach ($array as $a) {
        if ($a != "")
            return FALSE;
    }
    return TRUE;
}

/* Esta función ordena un array segun un arreglo de ids */

function ordenarArray($array, $ordenamiento) {
    $orden = explode(",", $ordenamiento);
    $ids = array_column($array, 'cat_tipos_subcuentas_id');
    $return = array();
    foreach ($orden as $o) {
        $index = array_search($o, $ids);
        array_push($return, $array[$index]);
    }
    return $return;
}

function getStatusDeAuditoria($status = NULL) {
    $statusAuditoria = array(NULL, "Cancelada", "En proceso", "Finalizada", "Reprogramada");
    if (!empty($status))
        return $statusAuditoria[$status];
    return "";
}

/**
 * Función que convierte un valor entero a su representación en fecha/hora
 * @param int $integer Valor entero a convertir
 * @return date Regresa la representación en formato de fecha Y-m-d H:i:s de un entero
 */
function int2date($integer) {
    $date = $integer;
    if (!empty($integer) && strpos($integer, "-") === FALSE) {
        $format = 'Y-m-d H:i:s';
        $date = date($format, $integer);
    }
    return $date;
}

/*
 * DESCRIPCION: Calcula un desplazamiento de X dias tomando en cuenta la semana de lunes a viernes
 * *Bajado de :http://foros.cristalab.com/sumas-dias-a-una-fecha-excepto-sabados-y-domingos-t54136/
 * PARAMETROS DE ENTRADA: @fecha.-Fecha base a desplazar en formato date
 *                       @dias.- offset, numero de dias de incremento a @fecha
 * PARAMETROS DE SALIDA: N/A.
 * VALOR DE RETORNO: Regresa la nueva fecha.
 */

function getFechaOffset_v2($fecha, $dias) {
    //echo "fecha_ $fecha, dias_ $dias<br>";
    $datestart = strtotime($fecha);
    //$datesuma = 15 * 86400;
    $diasemana = date('N', $datestart); //domingo=7;
    //echo "6.- diasemana $diasemana<br>";
    $totaldias = $diasemana + $dias;
    //echo "7.- totaldias $totaldias<br>";
    if ($dias == '-0') {//caso especial. Cero dias pero contando negativamente.
        $datestart = strtotime($fecha);
        if ((date('N', $datestart)) == 7) {
            $datestart = strtotime(date('Y-m-d', $datestart) . ' -2 day');
        } elseif ((date('N', $datestart)) == 6) {
            $datestart = strtotime(date('Y-m-d', $datestart) . ' -1 day');
        }
        return $twstart = date('Y-m-d', $datestart);
    }
    if ((($dias <= ($diasemana * -1)) && $dias <= 0)) {//cuentas negativas.
        $datestart = strtotime($fecha . ' -' . $diasemana . ' days'); //$datestart ahora sera el domingo previo a la fecha $datestart original.
        // en este punto sabemos que contaremos como negativos empezando del dia domingo.
        $totalRestantes = $dias + $diasemana; //este es el numero de dias que queda por restar.
        for ($i = 0; $i <= abs($totalRestantes); $i++) {
            if ((date('N', $datestart)) == 7) {
                $datestart = strtotime(date('Y-m-d', $datestart) . ' -2 day');
            } elseif ((date('N', $datestart)) == 1) {
                $datestart = strtotime(date('Y-m-d', $datestart) . ' -3 day');
            } else {
                $datestart = strtotime(date('Y-m-d', $datestart) . ' -1 day');
            }
        }
        return $twstart = date('Y-m-d', $datestart);
    } else {
        $signo = 1;
        $findesemana = intval($totaldias / 5) * 2;
        //echo "8.- findesemana $findesemana<br>";
        $diasabado = $totaldias % 5;
        //echo "9.- diasabado $diasabado<br>";
        if ($diasabado == 6)
            $findesemana ++;
        elseif ($diasabado == 0)
            $findesemana = $findesemana - 2;
        //echo "10.- findesemana $findesemana<br>";
        //60seg*60min+24hrs = 86400
        $findesemana *= $signo;
        $diasAdd = ($dias + $findesemana);
        $total = strtotime($fecha . ' +' . $diasAdd . ' days');
        //$total = (($dias+$findesemana) * 86400)+$datestart ;Se cambio por que los dias de cambio de horario genera errores ya que no duran 86400 segundos
        //echo '$dias '.$dias.'<br>';}
        return $twstart = date('Y-m-d', $total);
    }//fin de else
}

/**
 * Esta función devuelve la cantidad de días hábiles (quita sábados y domingos) entre dos fechas. Las
 * fechas no tienen que estar en un orden específico.
 * @link http://mx1.php.net/manual/es/function.date.php
 * @param string $fecha1 Fecha en formato YYYY-MM-DD
 * @param string $fecha2 Fecha en formato YYYY-MM-DD
 * @return int Cantidad de días hábiles entre dos fechas
 */
function getDiasHabiles($fecha1, $fecha2) {
    $diasHabiles = 0;
    $signo = "+";
    if ($fecha1 > $fecha2) {
        $temp = $fecha1;
        $fecha1 = $fecha2;
        $fecha2 = $temp;
        $signo = "-";
    }
    $fecha1 = new DateTime($fecha1);
    $fecha2 = new DateTime($fecha2);
    $oneDay = new DateInterval("P1D");
    $diasNoHabiles = array(6, 7); // 1 (para lunes) hasta 7 (para domingo)

    for ($fecha1; $fecha1 < $fecha2; $fecha1->add($oneDay)) {
        if (!in_array($fecha1->format("N"), $diasNoHabiles)) {
            $diasHabiles++;
        }
    }
    return intval($signo . $diasHabiles);
}

function get_cargo_de_empleado(&$row) {
    $cargo = "";
    if (!empty($row) && isset($row['cc_departamentos_id'])) {
        $cargo = $row['puestos_nombre'] . " DE " . $row['departamentos_nombre'];
        if ($row['empleados_puestos_id'] == PUESTO_DIRECTOR && $row['cc_direcciones_id'] == APP_DIRECCION_CONTRALORIA) {
            $cargo = (!empty($row['empleados_nombramiento']) ? $row['empleados_nombramiento'] : $row['puestos_nombre']) . " DE LA " . strtoupper($row['direcciones_nombre']);
        } elseif ($row['empleados_puestos_id'] == PUESTO_DIRECTOR || $row['empleados_puestos_id'] == 145 /* 145 = Director General (Descentralizadas) */) {
            $cargo = $row['puestos_nombre'] . " DE " . $row['direcciones_nombre'];
        } elseif ($row['empleados_puestos_id'] == PUESTO_SUBDIRECTOR) {
            if (substr($row['departamentos_nombre'], -3) === "ICA") {
                $row['departamentos_nombre'] = preg_replace("/ica/i", "ico", $row['departamentos_nombre']);
            }
            $patrones = array('/direccion/i', '/dirección/i');
            $cargo = preg_replace($patrones, 'DIRECTOR', $row['departamentos_nombre']);
        } elseif ($row['empleados_puestos_id'] == PUESTO_JEFE_DEPARTAMENTO) {
            $cargo = $row['puestos_nombre'] . ($row['departamentos_nombre'] === "ADMINISTRATIVO" ? ' ' : ' DE ') . $row['departamentos_nombre'];
            $cargo = preg_replace("/departamento de departamento/i", "departamento", $cargo);
        } elseif ($row['empleados_puestos_id'] == PUESTO_AUDITOR) {
            switch ($row['empleados_numero_empleado']) {
                case 9790: // Janelle Polanco
                    $cargo = "COORDINADOR DE NORMATIVIDAD Y ATENCIÓN A QUEJAS";
                    break;
                case 11862: // Jose Carlos Zapata Rivero
                    $cargo = "AUDITOR INTERNO DE OBRA PÚBLICA";
                    break;
                case 16224: //Marcos Ek Gala
                case 15897: // Manuel Encalada
                case 8600: // Jose Luis Lugo Cab
                case 13700: // Azalia
                case 16731: //: Lupita
                case 7413: //Norma
                case 16283: // Miguel Lopez
                case 11657: // Juan Vazquez
                case 4124: // Lucy Manzanero
                    $cargo = "AUDITOR INTERNO";
                    break;
                case 12124: // Celia Sosa
                case 14415: // Karen Alonzo
                    $cargo = $row['puestos_nombre'];
                    break;
                default:
                    $patrones = array('/auditoria/i', '/auditoría/i');
                    $cargo = preg_replace($patrones, 'auditor', $row['departamentos_nombre']);
            }
        } elseif ($row['empleados_puestos_id'] == PUESTO_COORDINADOR_AUDITORIA) {
            $cargo = "COORDINADOR DE ";
            switch ($row['empleados_numero_empleado']) {
                case 9907: // Alex Mass
                    $cargo .= "AUDITORÍA DE OBRA PÚBLICA";
                    break;
                case 13875: // Miguel Dzib
                case 9754: // Leti
                case 178: // Moises Sanguino
                    $cargo .= "AUDITORÍA";
                    break;
                default:
                    $cargo .= $row['departamentos_nombre'];
                    break;
            }
        } elseif ($row['empleados_puestos_id'] == 297) { // 297 = Coordinador juridico
            $cargo = $row['puestos_nombre'];
        } elseif ($row['empleados_puestos_id'] == 143) { // 143 = AUXILIAR OPERATIVO INTERNO
            $cargo = $row['puestos_nombre'];
        }
    }
    $cargo = capitalizar($cargo);
    $row['empleados_cargo'] = $cargo;
    return TRUE;
}

/**
 * Genera valores al arreglo del empleado
 * @param array $empleado
 * @return boolean
 */
function get_nombre_titulado(&$empleado) {
    if (!empty($empleado)) {
        if ($empleado['empleados_genero'] === GENERO_FEMENINO && !empty($empleado['titulos_femenino_siglas'])) {
            $empleado['empleados_nombre_titulado_siglas'] = $empleado['titulos_femenino_siglas'] . " " . $empleado['nombre_completo'];
            $empleado['empleados_nombre_titulado'] = $empleado['titulos_femenino_nombre'] . " " . $empleado['nombre_completo'];
        } else {
            $empleado['empleados_nombre_titulado_siglas'] = $empleado['titulos_masculino_siglas'] . " " . $empleado['nombre_completo'];
            $empleado['empleados_nombre_titulado'] = $empleado['titulos_masculino_nombre'] . " " . $empleado['nombre_completo'];
        }
    }
    return TRUE;
}

/**
 * Agrega la cantidad de daas hábiles a una fecha especificada.
 * @param date $fechaInicio Fecha base en formato YYYY-MM-DD.
 * @param integer $dias Cantidad de días hábiles a agregar o quitar a la fecha
 * @param boolean $solo_habiles TRUE para indicar que agregue o quite dias hábiles. FALSE para agregar todos los días
 * @return string Devuelve la fecha con la cantidad de días agregados
 */
function agregar_dias($fechaInicio, $dias, $solo_habiles = TRUE) {
    $DT_FechaFin = new DateTime($fechaInicio);
    $fechaFin = $DT_FechaFin->format('Y-m-d');
    $dias = intval($dias);
    $unDia = ($dias > 0 ? '+' : '-') . '1 day';
    while ($dias !== 0) {
        $DT_FechaFin->modify($unDia);
        $fechaFin = $DT_FechaFin->format('Y-m-d');
        $isWeekend = is_fin_de_semana($fechaFin);
        while ($isWeekend) {
            $DT_FechaFin->modify($unDia);
            $fechaFin = $DT_FechaFin->format('Y-m-d');
            $isWeekend = is_fin_de_semana($fechaFin);
        }
        $dias--;
    }
    $dias_inhabiles = get_dias_inabiles_entre_fechas($fechaInicio, $fechaFin);
    foreach ($dias_inhabiles as $dh) {
        $DT_FechaFin->modify($unDia);
        $fechaFin = $DT_FechaFin->format('Y-m-d');
    }
    $return = $fechaFin;
    return $return;
}

/**
 * Devuelve el listado de días inhábiles entre dos fechas
 * @param string $fecha_inicio Fecha de inicio en formato YYYY-MM-DD
 * @param string $fecha_fin Fecha de fin en formato YYYY-MM-DD
 * @param boolean $incluir_fines_de_semana TRUE para indicar que tambien se incluyan los dias inhabiles que caigan en fin de semana. FALSE en cualquier otro caso
 * @return array Listado de fechas de días inhábiles
 */
function get_dias_inabiles_entre_fechas($fecha_inicio, $fecha_fin, $incluir_fines_de_semana = FALSE) {
    $return = array();
    $CI = CI();
    if (!empty($fecha_inicio) && !empty($fecha_fin)) {
        if (!$incluir_fines_de_semana) {
            $CI->db
                    ->where("WEEKDAY(dias_inhabiles_fecha) <>", 5)
                    ->where("WEEKDAY(dias_inhabiles_fecha) <>", 6);
        }
        $result = $CI->db
                ->where("dias_inhabiles_fecha BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_fin . "'")
                ->get("dias_inhabiles");
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
        }
    }
    return $return;
}

/**
 * Obtiene las siglas de un empleado
 * @param array $empleado Información del empleado
 * @return boolean Devuelve TRUE al finalizar la función
 */
function get_siglas_de_empleado(&$empleado) {
    $siglas = "";
    if (!empty($empleado)) {
        $siglas = $empleado['empleados_nombre'][0]
                . (!empty($empleado['empleados_apellido_paterno']) ? $empleado['empleados_apellido_paterno'][0] : '')
                . (!empty($empleado['empleados_apellido_materno']) ? $empleado['empleados_apellido_materno'][0] : '');
    }
    $empleado['empleado_siglas'] = $siglas;
    return TRUE;
}

function forma_nombre_completo_de_ua(&$empleado) {
    if (!empty($empleado) && is_array($empleado)) {
        $generos = array("la", "el");
        $g = $h = NULL;
        if (isset($empleado['tipos_ua_genero']) && !is_null($empleado['tipos_ua_genero']) && is_numeric($empleado['tipos_ua_genero'])) {
            $g = $generos[$empleado['tipos_ua_genero']];
        }
        if (isset($empleado['tipos_ua_nombre']) && !empty($empleado['tipos_ua_nombre'])) {
            $h = $empleado['tipos_ua_nombre'];
        }
        $data = array($g, $h, 'de', $empleado['direcciones_nombre']);
        $empleado['nombre_completo_direccion'] = implode(" ", $data);
    }
    return TRUE;
}

function is_vocal($letra) {
    $return = FALSE;
    $vocales = array(
        'a', 'e', 'i', 'o', 'u',
        'á', 'é', 'í', 'ó', 'u',
        'A', 'E', 'I', 'O', 'U',
        'Á', 'É', 'Í', 'Ó', 'Ú'
    );
    $return = in_array($letra, $vocales);
    return $return;
}
