<?php

$ARA_sin_observaciones = "documentos/ARA_sin_observaciones";
$ARA_con_observaciones = "documentos/ARA_con_observaciones";

$jefes = $this->SAC_model->get_jefes_de_empleado($auditoria['auditorias_auditor_lider']);

$data = array(
    'director' => $this->SAC_model->get_empleado($jefes[PUESTO_DIRECTOR]),
    'subdirector' => $this->SAC_model->get_empleado($jefes[PUESTO_SUBDIRECTOR]),
);

if ($auditoria['auditorias_is_sin_observaciones'] == 1 || count($auditoria['observaciones']) == 0) {
    $this->load->view($ARA_sin_observaciones, $data);
} else {
    $this->load->view($ARA_con_observaciones, $data);
}