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
