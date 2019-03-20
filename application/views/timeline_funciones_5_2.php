<?php

@session_start();

if (!function_exists('array_column')) {
    /**
     * Devuelve los valores de una sola columna del array de entrada
     * @param array $input Un array multidimensional o un array de objetos desde el que extraer una columna de valores. Si se proporciona un array de objetos, entonces se podrá extraer directamente las propiedades públicas. Para poder extraer las proiedades protegidas o privadas, la clase debe implementar los métodos mágicos __get() y __isset().
     * @param mixed $column_key La columna de valores a devolver. Este valor podría ser una clave de tipo integer de la columna de la cual obtener los datos, o podría ser una clave de tipo string para un array asociativo o nombre de propiedad. También prodría ser NULL para devolver array completos u objetos (útil junto con index_key para reindexar el array).
     * @param mixed $index_key [optional] <br>La columna a usar como los índices/claves para el array devuelto. Este valor podría ser la clave de tipo integer de la columna, o podría ser el nombre de la clave de tipo string.
     * @return array Devuelve un array de valores que representa una sola columna del array de entrada.
     */
    function array_column($input, $column_key = null, $index_key = null) {
	$result = array();
	$i = 0;
	foreach ($input as $v) {
	    $k = $index_key === null || !isset($v[$index_key]) ? $i++ : $v[$index_key];
	    $result[$k] = $column_key === null ? $v : (isset($v[$column_key]) ? $v[$column_key] : null);
	}
	return $result;
    }

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

//echo get_dias_habiles_entre_fechas('2016-09-20', '2016-09-10');
