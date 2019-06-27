<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Documentos_blob_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_documentos_id";
        $this->table_prefix = "blo";
        $this->model_name = __CLASS__;
    }

    function insert_update($documentos_id = NULL, $tipo = NULL, $blob = NULL) {
        $return = FALSE;
        if (!empty($documentos_id) && !empty($tipo)) {
            $result = $this->db
                    ->where($this->id_field, $documentos_id)
                    ->where('documentos_blob_tipo', $tipo)
                    ->get($this->table_name);
            if ($result->num_rows() == 0) {
                $data = array(
                    $this->id_field => $documentos_id,
                    'documentos_blob_contenido' => $blob,
                    'documentos_blob_tipo' => $tipo
                );
                $return = $this->insert($data);
            } else {
                $data = array('documentos_blob_contenido' => $blob);
                $this->db->where('documentos_blob_tipo', $tipo);
                $return = $this->update($documentos_id, $data);
            }
        }
        return $return;
    }

    function get_html($auditorias_id, $documentos_tipos_id) {
        $return = "";
        $documentos = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
        if (count($documentos) == 1) {
            $documento = $documentos[0];
        }
        if (!empty($documento) && isset($documento['documentos_blob_contenido']) && !empty($documento['documentos_blob_contenido'])) {
            $return = utf8_encode($documento['documentos_blob_contenido']);
        } else {
            $aux = $this->Importar_model->get_documento_de_auditoria($auditorias_id, $documentos_tipos_id);
            if (!empty($aux) && isset($aux['contenido'])) {
                $return = '<p class="text-xs-center"><a href="' . base_url() . 'Documento/antiguo_cysa/' . $aux['idDocto'] . '" class="btn btn-default" target="_blank">Visualizar documento</a></p>';
            } else {
                $return = '<p class="lead text-xs-center">'
                        . 'No se encontró el documento autorizado.'
                        . '<br>'
                        . 'Es probable que no se haya hecho este documento para esta auditoría.'
                        . '</p>';
            }
        }
        return $return;
    }

}
