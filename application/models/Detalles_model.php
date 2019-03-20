<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Detalles_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_prefix = "det";
        $this->table_name = "detalles";
        $this->id_field = "detalles_id";
        $this->model_name = __CLASS__;
    }

    function getResultados($limit=NULL, $start=NULL) {
        $this->db->order_by("detalles_id", "ASC");
        return parent::getResultados($limit, $start);
    }

    function get_detalle($idDetalle) {
        $return = array();
        return $return;
    }

    function get_detalles_de_documento($idTipoDocumento) {
        $return = array();
        if (!empty($idTipoDocumento)) {
            $this->db->where("detalles_tipos_documentos_id", $idTipoDocumento);
            $return = $this->getResultados(NULL, NULL);
        }
        return $return;
    }

}
