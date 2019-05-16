<?php

$cysa = $this->session->userdata(APP_NAMESPACE);
$auditorias_id = $cysa['auditorias_id'];

$ARA_sin_observaciones = "documentos/ARA_sin_observaciones";
$ARA_con_observaciones = "documentos/ARA_con_observaciones";

$ARR_sin_observaciones = "";
$ARR_con_obserbaciones = "";

$template = "";
if ($auditoria['auditorias_anio'] < 2018) {
    // Para estas fechas aún existían SEGUIMIENTOS de auditoría
    if (empty($auditoria['auditorias_origen_id'])) {
        // NO TIENE seguimiento, por lo tanto es una AP
    } else {
        // SI TIENE seguimiento, por lo tanto es una SA
    }
} else { // Entonces es auditoría 2018 en adelante
    if ($auditoria['auditorias_is_sin_observaciones'] == 1 || count($auditoria['observaciones']) == 0) {
        $this->load->view($ARA_sin_observaciones);
    } else {
        $this->load->view($ARA_con_observaciones);
    }
}
