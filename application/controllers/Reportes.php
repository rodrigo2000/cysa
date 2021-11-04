<?php

class Reportes extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'reportes';
        $this->module['controller'] = 'Reportes';
        $this->module['title'] = 'Reportes';
        $this->module['title_list'] = "Reportes";
        $this->is_catalogo = FALSE;
        $this->_initialize();
    }

    function index() {
        $data = array(
            'anios' => $this->Auditorias_model->get_catalogo_anios_de_auditorias()
        );
        $this->visualizar($this->module['name'] . "_view", $data);
    }

    function reporte() {
        $reportes = $this->input->post('reporte');
        $anio = $this->input->post('anio');
        $status = $this->input->post('status');
        $json = array(
            'success' => TRUE,
            'archivo' => NULL
        );
        $campos_calculados = array(
            'observaciones_total',
            'observaciones_solventadas',
            'observaciones_no_solventadas',
            'observaciones_titulo',
            'recomendaciones_por_observacion'
        );
        if (!empty($reportes)) {
            switch ($reportes) {
                case 'control-auditorias':
                    $data = array();
                    $auditorias = $this->Reportes_model->generar_reporte_auditorias_en_proceso();
                    $campos = array(
                        'numero_auditoria' => array('label' => 'Número de la auditoría'),
                        'direcciones_nombre' => 2,
                        'auditorias_rubro' => 3,
                        'auditor_lider_nombre_completo',
                        'auditorias_fechas_sello_orden_entrada',
                        'auditorias_fechas_vobo_jefe',
                        'auditorias_fechas_vobo_subdirector',
                        'auditorias_fechas_vobo_director',
                        'auditorias_fechas_lectura'
                    );
                    $json = $this->Reportes_model->generar_reporte_xls($auditorias, 'Reporte de Control de Auditorias.xlsx', $campos);
                    break;
                case 'fechas-autorizacion':
                    $data = array();
                    $auditorias_status_id = NULL;
                    switch ($status) {
                        case 1: // Auditorias abiertas
                            $auditorias_status_id = AUDITORIAS_STATUS_EN_PROCESO;
                            break;
                        case 2: // Auditorias cerradas
                            $auditorias_status_id = array(
                                AUDITORIAS_STATUS_FINALIZADA,
                                AUDITORIAS_STATUS_FINALIZADA_RESERVADA,
                                AUDITORIAS_STATUS_FINALIZADA_MANUAL,
                                AUDITORIAS_STATUS_FINALIZADA_REPROGRAMADA,
                                AUDITORIAS_STATUS_FINALIZADA_SUSTITUIDA
                            );
                        default: // 0=Todas las auditorias
                            $auditorias_status_id = NULL;
                            break;
                    }
                    $auditorias = $this->Reportes_model->generar_reporte_fechas_autorizacion($anio, $auditorias_status_id);
                    $campos = array(
                        'numero_auditoria' => array('label' => 'Número de la auditoría'),
                        'direcciones_nombre' => 2,
                        'auditorias_rubro' => 3,
                        'auditor_lider_nombre_completo',
                        'auditorias_fechas_sello_orden_entrada',
                        'auditorias_fechas_vobo_jefe',
                        'auditorias_fechas_vobo_subdirector',
                        'auditorias_fechas_vobo_director',
                        'auditorias_fechas_lectura',
                        'auditorias_fechas_recibir_informacion_etapa_1',
                        'auditorias_fechas_vobo_jefe_etapa_1',
                        'auditorias_fechas_vobo_subdirector_etapa_1',
                        'auditorias_fechas_vobo_director_etapa_1',
                        'auditorias_fechas_lectura_etapa_1'
                    );
                    $json = $this->Reportes_model->generar_reporte_xls($auditorias, 'Reporte de fechas de autorizacion.xlsx', $campos);
                    break;
                case 'paa':
                    $this->db->order_by("auditorias_numero", "ASC");
                    $auditorias = $this->Reportes_model->generar_reporte_paa($anio);
                    $campos = array(
                        'numero_auditoria' => array('label' => 'Número de la auditoría'),
                        'direcciones_nombre' => 2,
                        'auditorias_rubro' => 3,
                        'auditor_lider_nombre_completo',
                        'auditorias_fechas_sello_orden_entrada',
                        'auditorias_status_nombre'
                    );
                    $json = $this->Reportes_model->generar_reporte_xls($auditorias, 'Reporte de PAA ' . $anio . '.xlsx', $campos);
                    break;
                case 'custom':
                    $solo_con_numero = FALSE;
                    $mas_datos = FALSE;
                    $campos = $this->input->post('campos');
                    if (count(array_intersect($campos, $campos_calculados)) > 0) {
                        $mas_datos = TRUE;
                    }
                    $this->db->order_by("auditorias_numero", "ASC");
                    $auditorias = $this->Reportes_model->generar_reporte_personalizado($anio, $status, $anio, $solo_con_numero, $mas_datos);
                    $json = $this->Reportes_model->generar_reporte_xls($auditorias, 'Reporte de personalizado ' . $anio . '.xlsx', $campos);
                    break;
                default:
                    $json['error'] = "No se seleccionó una opción válida.";
                    break;
            }
        }
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
    }

}
