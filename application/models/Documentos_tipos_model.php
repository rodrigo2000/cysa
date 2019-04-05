<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Documentos_tipos_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "dt";
        $this->model_name = __CLASS__;
    }

    /**
     * Devuelve el identificador del tipo de documento asociado a si siglas
     * @param string $siglas Cadena que corresponde a las siglas del tipo de documento
     * @return integer Identificador del tipo de documento
     */
    function parse_siglas($siglas) {
        $return = FALSE;
        if (!empty($siglas)) {
            $result = $this->db
                    ->select($this->id_field)
                    ->like('documentos_tipos_abreviacion', $siglas)
                    ->limit(1)
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $return = $result->row()->documentos_tipos_id;
            }
        }
        return $return;
    }

}
