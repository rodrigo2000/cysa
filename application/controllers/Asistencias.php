<?php

class Asistencias extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de asistencias";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "asis";
        $this->_initialize();
    }

    function _initialize() {
        return TRUE;
    }

    function agregar_asistencia() {
        $return = array(
            'success' => FALSE,
            'message' => "Solo se permite método POST"
        );
        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $documentos_id = $this->input->post("documentos_id");
            $empleados_id = $this->input->post("empleados_id");
            $asistencias_tipo = $this->input->post("asistencias_tipo");
            if (empty($documentos_id)) {
                $return['message'] = "No se ha especificado el identificador del documento.";
            } elseif (empty($empleados_id)) {
                $return['message'] = "No se ha especificado el identificador del empleado. Guarde primero el documento.";
            } else {
                $r = $this->Asistencias_model->insert_update($documentos_id, $empleados_id, $asistencias_tipo);
                if ($r === TRUE) {
                    $return['success'] = TRUE;
                    $return['message'] = "OK";
                } elseif (is_array($r)) {
                    $return = $r;
                }
            }
        }
        echo json_encode($return);
    }

    function eliminar_asistencia() {
        $return = array(
            'success' => FALSE,
            'message' => "Solo se permite método POST"
        );
        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $documentos_id = $this->input->post("documentos_id");
            $empleados_id = $this->input->post("empleados_id");
            if (empty($documentos_id)) {
                $message = "No se ha especificado el identificador del documento.";
            } elseif (empty($empleados_id)) {
                $message = "No se ha especificado el identificador del empleado.";
            } else {
                $r = $this->Asistencias_model->insert_update($documentos_id, $empleados_id, TIPO_ASISTENCIA_ELIMINADO);
                if ($r !== FALSE) {
                    $return['success'] = TRUE;
                    $return['message'] = "OK";
                }
            }
        }
        echo json_encode($return);
    }

}
