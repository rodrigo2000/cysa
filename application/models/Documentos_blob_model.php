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

}
