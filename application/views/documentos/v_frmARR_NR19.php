<?php

$ARR_solventado = "documentos/ARR_solventado";
$ARR_no_solventado = "documentos/ARR_no_solventado";
$ARR_parcialmente_solventado = "documentos/ARR_parcialmente_solventado";

$status_recomendaciones = array();

$numero_revision = $this->Auditoria_model->get_numero_revision($auditoria['auditorias_id']);
if ($numero_revision > 0 && $numero_revision % 2 === 0) {
    $numero_revision--;
}
$etapa_auditoria = $this->Auditoria_model->get_etapa();
$todo_solventado = TRUE;
foreach ($auditoria['observaciones'] as $o) {
    foreach ($o['recomendaciones'] as $r) {
        foreach ($r['avances'] as $a) {
            if ($a['recomendaciones_avances_numero_revision'] == $numero_revision) {
                if ($a['recomendaciones_avances_recomendaciones_status_id'] != OBSERVACIONES_STATUS_SOLVENTADA) {
                    $todo_solventado = FALSE;
                }
            }
        }
    }
}
$fecha = SIN_ESPECIFICAR;
if (isset($r[ACTA_REV_FECHA_SOLVENTA])) {
    $fecha = mysqlDate2OnlyDate($r[ACTA_REV_FECHA_SOLVENTA]);
    // Revisamos si tuvo prórroga
    $f = get_prorrogas($_SESSION['idAuditoria']);
    if (!empty($f) && isset($f[0]['prorroga_detalles']['32'])) {
        $meses = array(
            'enero' => 1,
            'febrero' => 2,
            'marzo' => 3,
            'abril' => 4,
            'mayo' => 5,
            'junio' => 6,
            'julio' => 7,
            'agosto' => 8,
            'septiembre' => 9,
            'octubre' => 10,
            'noviembre' => 11,
            'diciembre' => 12
        );
        $aux = explode(" de ", $fechaVencimientoParaSolventarObservaciones);
        $fecha = $aux[2] . "-" . $meses[$aux[1]] . "-" . $aux[0];
        $fecha = getTotalHabiles_v2($fecha, intval($f[0]['prorroga_detalles']['32']));
        $fechaVencimientoParaSolventarObservaciones = mysqlDate2Date($fecha);
    }
}
$jefes = $this->SAC_model->get_jefes_de_empleado($auditoria['auditorias_auditor_lider']);

$data = array(
    'todo_solventado' => $todo_solventado,
    'status_label' => array(
        OBSERVACIONES_STATUS_NO_SOLVENTADA => 'No solventada',
        OBSERVACIONES_STATUS_SOLVENTADA => 'Solventada',
        OBSERVACIONES_STATUS_NO_SE_ACEPTA => 'No se acepta',
        OBSERVACIONES_STATUS_ATENDIDA => 'Atendida',
        OBSERVACIONES_STATUS_NO_ATENDIDA => 'No atendida'
    ),
    'fecha_limite_para_solventar' => $fecha,
    'director' => $this->SAC_model->get_empleado($jefes[PUESTO_DIRECTOR]),
    'subdirector' => $this->SAC_model->get_empleado($jefes[PUESTO_SUBDIRECTOR]),
);

$this->load->view($ARR_solventado, $data);
