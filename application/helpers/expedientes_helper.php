<?php

/**
 * Devuelve el folio del expediente a partir de una dupla
 * @param array $data Arreglo que contiene las partes con la cual se forma el folio de un expediente
 * @return string Folio del expediente
 */
function get_folio($data) {
    $data['anio'] = intval($data['anio']);
    $data['expedientes_anio'] = intval($data['expedientes_anio']);
    $subseccionesNumero = isset($data['expedientes_subsecciones_numero']) ? $data['expedientes_subsecciones_numero'] : NULL;
    $seriesNumero = isset($data['expedientes_series_numero']) ? $data['expedientes_series_numero'] : NULL;
    $subseriesNumero = isset($data['expedientes_subseries_numero']) ? $data['expedientes_subseries_numero'] : NULL;
    $cc = $data['expedientes_clv_dir'] . $data['expedientes_clv_subdir'] . $data['expedientes_clv_depto']; // centro de costos
    if ((isset($data['anio']) && intval($data['anio']) == 2018) || ($data['expedientes_anio'] == 2018 && is_null($data['expedientes_idAuditoria']))) {
        if (!empty($data['label_clv_subdir'])) {
            $data['expedientes_clv_subdir'] = $data['label_clv_subdir'];
        }
        if (!empty($data['label_clv_depto'])) {
            $data['expedientes_clv_depto'] = $data['label_clv_depto'];
        }
        switch ($cc) {
            case "534":
                $data['expedientes_clv_subdir'] = 2;
                break;
            case "515":
                $data['expedientes_clv_depto'] = 4;
                break;
            case "514":
                $data['expedientes_clv_subdir'] = 2;
                $data['expedientes_clv_depto'] = 4;
                break;
            default:
                break;
        }
    } elseif (isset($data['anio']) && $data['anio'] < 2015) { // Para expedientes 2015 y anteriores
        switch ($cc) {
            case "534":
                $data['expedientes_clv_subdir'] = 1; // Se acordó con Sandra que debe mostrarse 521
                $data['expedientes_clv_depto'] = 2;
                break;
            default:
                break;
        }
    } else { // Para expedientes 2015 y 2017
        switch ($cc) {
            case "533": // ATI
                $data['expedientes_clv_subdir'] = 2; // Debe ser 523
                break;
            case "532": // AIN
                $data['expedientes_clv_subdir'] = 2; // Debe ser 522
                break;
            case "515":
                $data['expedientes_clv_depto'] = 4; // Debe ser 514
                break;
            case "514":
                $data['expedientes_clv_depto'] = 2; // Debe ser 512
                break;
            case "531":
                $data['expedientes_clv_subdir'] = 2; // Debse ser 521
                break;
            case "534":
                $data['expedientes_clv_subdir'] = 1; // Debe ser 512
                $data['expedientes_clv_depto'] = 2;
                break;
            default:
                break;
        }
    }
    if (!isset($data['secciones_clave'])) {
        $dbExpedientes = conectar_a_db_expedientes();
        $strSQL = "SELECT * FROM secciones WHERE secciones_id = " . $data['expedientes_secciones_id'] . " LIMIT 1";
        $result = $dbExpedientes->ejecutaQuery($strSQL);
        if (is_resource($result) && mysql_num_rows($result) == 1) {
            $row = mysql_fetch_assoc($result);
            $data['secciones_clave'] = $row['secciones_clave'];
        } else {
            $data['secciones_clave'] = NULL;
        }
    }
    $partes = array(
        substr("0" . $data['expedientes_clv_dir'], -2),
        substr("0" . $data['expedientes_clv_subdir'], -2),
        substr("0" . $data['expedientes_clv_depto'], -2),
        $data['secciones_clave'],
        ($subseccionesNumero != 0 ? substr("0" . $subseccionesNumero, -2) : 0),
        ($seriesNumero != 0 ? substr("0" . $seriesNumero, -2) : 0),
        ($subseriesNumero != 0 ? substr("0" . $subseriesNumero, -2) : 0),
        $data['expedientes_consecutivo'],
        $data['expedientes_anio']
    );
    return implode(".", $partes);
}

function parse_cc($anio, $clv_dir, $clv_subdir, $clv_depto) {
    $cc = $clv_dir . $clv_subdir . $clv_depto;
    $idDireccion = $clv_dir;
    $idSubdireccion = $clv_subdir;
    $idDepartamento = $clv_depto;
    if ($anio < 2015) { // para expedientes creados entre [2012 y 2014]
    } elseif ($anio < 2018) { // para expedientes creados entre 2015 y 2017
        switch ($cc) {
            case "533": // ATI
                $idSubdireccion = "02"; // Debe ser 523
                break;
            case "532": // AIN
                $idSubdireccion = "02"; // Debe ser 522
                break;
            case "515":
                $idDepartamento = "04"; // Debe ser 514
                break;
            case "514":
                $idDepartamento = "02"; // Debe ser 512
                break;
            case "531":
                $idSubdireccion = "02";
                break;
            default:
                break;
        }
    } else { // Para expedientes creados de 2018 a fecha actual
        switch ($cc) {
            case "533": // ATI
                $idSubdireccion = "02"; // Debe ser 523
                break;
            case "532": // AIN
                $idSubdireccion = "02"; // Debe ser 522
                break;
            case "534":
                $idSubdireccion = "02"; // Debe ser 524
                break;
            case "521":
                $idSubdireccion = "03"; // Debe ser 534
                $idDepartamento = "04"; // Debe ser 512
                break;
            case "531":
                $idSubdireccion = "02";
                break;
            case "514":
                $idSubdireccion = "02";
                break;
            default:
                // Jurídico queda igual
                break;
        }
    }
    $return = array(
        'label_clv_dir' => $idDireccion,
        'label_clv_subdir' => $idSubdireccion,
        'label_clv_depto' => $idDepartamento
    );
    return $return;
}
