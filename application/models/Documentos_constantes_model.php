<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Documentos_constantes_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "dc";
        $this->model_name = __CLASS__;
    }

    function getResultados($limit=NULL, $start=NULL) {
        $this->db->join('documentos_tipos dt', 'documentos_tipos_id = documentos_constantes_documentos_tipos_id', "INNER");
        return parent::getResultados($limit, $start);
    }

    function get_constantes_de_documento($documentos_tipos_id) {
        $return = array();
        if (!empty($documentos_tipos_id)) {
            $this->db->where('documentos_constantes_documentos_tipos_id', $documentos_tipos_id);
            $return = $this->get_todos();
        }
        return $return;
    }

}
