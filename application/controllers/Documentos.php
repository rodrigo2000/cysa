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
        echo json_encode($json);
    }

    function _post_delete($status, $id, $data = NULL, $dataDelete = NULL) {
        $this->module['controller'] = "Auditoria";
        return parent::_post_delete($status, $id, $data, $dataDelete);
    }

}
