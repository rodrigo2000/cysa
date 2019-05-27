<?php

class Documentos extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de documentos";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "d";
        $this->_initialize();
    }

    /**
     * Guarda la información del documento. Si el documento no existe, entonces lo crea.
     */
    function guardar() {
        $constantes = $this->input->post('constantes');
        $documentos_id = intval($this->input->post('documentos_id'));
        $auditorias_id = intval($this->input->post('auditorias_id'));
        $documentos_tipos_id = intval($this->input->post('documentos_tipos_id'));
        $logotipos_id = intval($this->input->post('headers_id'));
        $accion = $this->input->post('accion');
        $documentos_versiones_id = intval($this->input->post('documentos_versiones_id'));
        $html = $this->input->post('html');
        $this->Documentos_model->get_template($documentos_tipos_id);
        // Involucrados
        $involucrados = $this->input->post('involucrados[]');
        if (is_array($involucrados)) {
            foreach ($involucrados as $empleados_id) {
                $this->Asistencias_model->insert_update($documentos_id, $empleados_id, TIPO_ASISTENCIA_INVOLUCRADO);
            }
        }
        // Involucrados de la Contraloria
        $involucrados_contraloria = $this->input->post('involucrados_contraloria[]');
        if (is_array($involucrados_contraloria)) {
            foreach ($involucrados_contraloria as $empleados_id) {
                $this->Asistencias_model->insert_update($documentos_id, $empleados_id, TIPO_ASISTENCIA_INVOLUCRADO_CONTRALORIA);
            }
        }
        // Testigos
        $testigos = $this->input->post("testigos[]");
        if (is_array($testigos)) {
            foreach ($testigos as $empleados_id) {
                $this->Asistencias_model->insert_update($documentos_id, $empleados_id, TIPO_ASISTENCIA_TESTIGO);
            }
        }
        switch ($documentos_tipos_id) {
            case TIPO_DOCUMENTO_ACTA_INICIO_AUDITORIA:
                if (!isset($constantes[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS])) { // ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS
                    $constantes[ACTA_INICIO_ASISTENCIA_DE_FUNCIONARIOS] = 0;
                }
                break;
            case TIPO_DOCUMENTO_ACTA_CIERRE_ENTREGA_INFORMACION:
                if (isset($constantes[ACEI_PARRAFO_U2])) {
                    $constantes[ACEI_PARRAFO_U2] = my_strip_tags($constantes[ACEI_PARRAFO_U2]);
                }
                break;
            case TIPO_DOCUMENTO_ACTA_RESULTADOS_AUDITORIA:
                $declaraciones = $this->input->post('declaraciones');
                if (!empty($declaraciones)) {
                    foreach ($declaraciones as $empleados_id => $declaracion) {
                        $this->Asistencias_declaraciones_model->insert_update($documentos_id, $empleados_id, $declaracion);
                    }
                }
                if (isset($constantes[ACTA_RESULTADOS_REDACCION])) {
                    $constantes[ACTA_RESULTADOS_REDACCION] = my_strip_tags($constantes[ACTA_RESULTADOS_REDACCION]);
                }
                break;
            case TIPO_DOCUMENTO_AUTORIZACION_AUDITORIA_NO_PROGRAMADA:
                if (isset($constantes[AANP_JUSTIFICACION])) {
                    $constantes[AANP_JUSTIFICACION] = my_strip_tags($constantes[AANP_JUSTIFICACION]);
                }
                break;
            case TIPO_DOCUMENTO_REPROGRAMACION:
            case TIPO_DOCUMENTO_AMPLIACION:
                if (isset($constantes[AMPLIA_REPROG_MOTIVO])) {
                    $constantes[AMPLIA_REPROG_MOTIVO] = my_strip_tags($constantes[AMPLIA_REPROG_MOTIVO]);
                }
                if (isset($constantes[AMPLIA_REPROG_OBSERVACIONES])) {
                    $constantes[AMPLIA_REPROG_OBSERVACIONES] = my_strip_tags($constantes[AMPLIA_REPROG_OBSERVACIONES]);
                }
                break;
            case TIPO_DOCUMENTO_RESOLUCION_PRORROGA:
                if (isset($constantes[RESOL_PRORROG_P_TIPO_MEDIO_SOL])) {
                    $opciones = array(NULL, 'oficio', 'correo electrónico');
                    $constantes[RESOL_PRORROG_P_TIPO_MEDIO_SOL] = array_search($constantes[RESOL_PRORROG_P_TIPO_MEDIO_SOL], $opciones);
                    if ($constantes[RESOL_PRORROG_P_TIPO_MEDIO_SOL] == 2) {
                        $constantes[RESOL_PRORROG_P_NUM_MEDIO_SOL] = NULL;
                    }
                }
                if (isset($constantes[RESOL_PRORROG_P_DIAS_HABILES_OTORG])) {
                    $constantes[RESOL_PRORROG_P_DIAS_HABILES_OTORG] = my_strip_tags($constantes[RESOL_PRORROG_P_DIAS_HABILES_OTORG]);
                }
                break;
            default :
                break;
        }
        if (empty($documentos_id)) {
            $documento = $this->Documentos_model->crear($auditorias_id, $documentos_tipos_id, $documentos_versiones_id);
            if ($documento['state'] === 'success') {
                $documentos_id = $documento['data']['insert_id'];
            }
        }
        $this->Documentos_model->update($documentos_id, array('documentos_logotipos_id' => $logotipos_id));
        $constantes = array_map('trim', $constantes);
        foreach ($constantes as $constantes_id => $valor) {
            $insert = array(
                'documentos_valores_documentos_constantes_id' => $constantes_id,
                'documentos_valores_documentos_id' => $documentos_id,
                'documentos_valores_valor' => $valor
            );
            $sql_insert = $this->db->set($insert)->get_compiled_insert("documentos_valores");

            $update = array(
                'documentos_valores_valor' => $valor
            );
            $sql_update = $this->db->set($update)->get_compiled_update("documentos_valores");
            $sql = $sql_insert . " ON DUPLICATE KEY UPDATE documentos_valores_valor = '" . $valor . "'";
            $this->db->query($sql);
            $json = array(
                'success' => TRUE,
                'documentos_id' => $documentos_id,
                'accion' => 'modificar',
            );
        }
        $html = utf8_decode($html);
        $this->Documentos_blob_model->insert_update($documentos_id, 'html', $html);
        echo json_encode($json);
    }

    function _post_delete($status, $id, $data = NULL, $dataDelete = NULL) {
        $this->module['controller'] = "Auditoria";
        return parent::_post_delete($status, $id, $data, $dataDelete);
    }

}
