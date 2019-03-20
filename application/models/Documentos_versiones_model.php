<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Documentos_versiones_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "dv";
        $this->model_name = __CLASS__;
    }

    function getResultados($limit = NULL, $start = NULL) {
        $this->db->join("documentos_tipos dt", "documentos_tipos_id = documentos_versiones_documentos_tipos_id", "INNER");
        return parent::getResultados($limit, $start);
    }

    function get_version($idVersion) {
        $return = array();
        return $return;
    }

    /**
     * Obtiene la versión vigente de un tipo de documento
     * @param integer $documentos_tipos_id Identificador del tipo de documento
     * @return array Arreglo que contiene la información de la versión del documento
     */
    function get_version_vigente_del_tipo_de_documento($documentos_tipos_id) {
        $return = array();
        if (!empty($documentos_tipos_id)) {
            $result = $this->db
                    ->where("documentos_versiones_documentos_tipos_id", $documentos_tipos_id)
                    ->where("documentos_versiones_is_vigente", 1)
                    ->limit(1)
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

}
