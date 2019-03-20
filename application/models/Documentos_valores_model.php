<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Documentos_valores_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_documentos_id";
        $this->table_prefix = "dv";
        $this->model_name = __CLASS__;
    }

    function get_valores_de_documento($documentos_id) {
        $return = array();
        if (!empty($documentos_id)) {
            $this->db->where($this->id_field, $documentos_id);
            $aux = $this->getResultados(NULL, NULL);
            foreach ($aux as $a) {
                $return[$a['documentos_valores_documentos_constantes_id']] = $a['documentos_valores_valor'];
            }
        }
        return $return;
    }

}
