<?php

function get_presidente_municipal() {
    $CI = CI();
    $return = array();
    $SAC = $CI->load->database('sac', TRUE);
    $result = $SAC->where("empleados_puestos_id", PUESTO_PRESIDENTE_MUNICIPAL)
            ->where("empleados_fecha_baja IS NULL")
            ->get("empleados");
    $aux = $result->result_array();
    if ($aux && count($aux) > 0) {
        $return = $aux[0];
    } elseif ($aux && is_array($aux)) {
        $return = $aux;
    }
    return $return;
}

function get_sindico(){
    $CI = CI();
    $return = array();
    $SAC = $CI->load->database('sac', TRUE);
    $result = $SAC->where("empleados_puestos_id", PUESTO_SINDICO)
            ->where("empleados_fecha_baja IS NULL")
            ->get("empleados");
    $aux = $result->result_array();
    if ($aux && count($aux) > 0) {
        $return = $aux[0];
    } elseif ($aux && is_array($aux)) {
        $return = $aux;
    }
    return $return;
}
