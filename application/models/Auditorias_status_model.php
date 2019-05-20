<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Auditorias_status_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "as";
        $this->model_name = __CLASS__;
    }

    function get_status_auditoria($data) {
        $valor = 0;
        $status_id = intval($data['auditorias_status_id']);
        switch ($status_id) {
            case 0:
                $valor = 1;
                if (empty($data['auditorias_fechas_inicio_programado']) || empty($data['auditorias_fechas_sello_orden_entrada'])) {
                    $valor = 5;
                }
                break;
            case 1:
                $valor = 2;
                if ($data['auditorias_fechas_inicio_programado'] != $data['auditorias_fechas_inicio_real']) {
                    $valor = 4;
                }
                break;
            case 2:
            case 3:
            case 4:
                $valor = 3;
                break;
            case 5:
                $valor = 5;
                break;
        }
        return $valor;
    }

}
