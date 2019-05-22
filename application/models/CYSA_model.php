<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class CYSA_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = NULL;
        $this->id_field = NULL;
        $this->table_prefix = NULL;
        $this->model_name = __CLASS__;
    }

    function get_director_de_contraloria($periodos_id = NULL) {
        $return = array();
        if (empty($periodos_id)) {
            $p = $this->SAC_model->get_ultimo_periodo();
            $periodos_id = intval($p['periodos_id']);
        }
        $this->dbSAC = $this->getDatabase(APP_NAMESPACE_SAC);
        $result = $this->dbSAC
                ->where("cc_periodos_id", $periodos_id)
                ->where("cc_direcciones_id", APP_DIRECCION_CONTRALORIA)
                ->where("cc_etiqueta_subdireccion", 1)
                ->where("cc_etiqueta_departamento", 1)
                ->where("cc.fecha_delete IS NULL")
                ->order_by("cc_periodos_id", "DESC")
                ->limit(1)
                ->get("centros_costos cc");
        if ($result && $result->num_rows() > 0) {
            $cc = $result->row_array();
            $return = $this->SAC_model->get_empleado($cc['cc_empleados_id']);
        }
        return $return;
    }

    function get_subdirector_de_contraloria($periodos_id = NULL) {
        $return = array();
        if (empty($periodos_id)) {
            $p = $this->SAC_model->get_ultimo_periodo();
            $periodos_id = intval($p['periodos_id']);
        }
        $this->dbSAC = $this->getDatabase(APP_NAMESPACE_SAC);
        $result = $this->dbSAC
                ->where("cc_periodos_id", $periodos_id)
                ->where("cc_direcciones_id", APP_DIRECCION_CONTRALORIA)
                ->where("cc_etiqueta_subdireccion", 2)
                ->where("cc_etiqueta_departamento", 1)
                ->where("cc.fecha_delete IS NULL")
                ->order_by("cc_periodos_id", "DESC")
                ->limit(1)
                ->get("centros_costos cc");
        if ($result && $result->num_rows() > 0) {
            $cc = $result->row_array();
            $return = $this->SAC_model->get_empleado($cc['cc_empleados_id']);
        }
        return $return;
    }

}
