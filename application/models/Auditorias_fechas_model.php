<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Auditorias_fechas_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_auditorias_id";
        $this->table_prefix = "af";
        $this->model_name = __CLASS__;
    }

    function get_ultima_etapa($auditorias_id) {
        $return = array();
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->where("auditorias_fechas_auditorias_id", $auditorias_id)
                    ->limit(1)
                    ->order_by("auditorias_fechas_etapa", "DESC")
                    ->get("auditorias_fechas");
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function get_primera_etapa($auditorias_id) {
        $return = array();
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->where("auditorias_fechas_auditorias_id", $auditorias_id)
                    ->limit(1)
                    ->order_by("auditorias_fechas_etapa", "ASC")
                    ->get("auditorias_fechas");
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function get_fechas_de_auditoria($auditorias_id = NULL) {
        $return = array();
        if (empty($auditorias_id)) {
            $session = $this->session->userdata(APP_NAMESPACE);
            $auditorias_id = $session['auditorias_id'];
        }
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->where("auditorias_fechas_auditorias_id", $auditorias_id)
                    ->order_by("auditorias_fechas_etapa", "ASC")
                    ->get("auditorias_fechas");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function insert($data) {
        foreach ($data as $index => $d) {
            if (empty($d) || $d === '0000-00-00') {
                $data[$index] = NULL;
            }
        }
        parent::insert($data);
    }

    function update($id, $data) {
        foreach ($data as $index => $d) {
            if (empty($d) || $d === '0000-00-00') {
                $data[$index] = NULL;
            }
        }
        return parent::update($id, $data);
    }

}
