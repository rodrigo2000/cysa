<?php

function CI() {
    $CI = & get_instance();
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
        $hh-=12;
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
        $output.=$sha1[$i] . $base64[$i];
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
            $base64.=$cadena[$i];
        }
    } else {
        for ($i = 1; $i <= strlen($sha1) * 2; $i = $i + 2) {
            $base64.=$cadena[$i];
        }
        $base64.=substr($cadena, strlen($sha1) * 2);
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
    return intval($signo.$diasHabiles);
}
