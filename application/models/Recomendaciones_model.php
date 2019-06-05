<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Recomendaciones_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_prefix = "rec";
        $this->table_name = "recomendaciones";
        $this->id_field = "recomendaciones_id";
        $this->model_name = __CLASS__;
    }

    function get_todos($limit = NULL, $start = NULL, $incluirEliminados = FALSE) {
        $this->db
                ->join("recomendaciones_clasificaciones rc", "rc.recomendaciones_clasificaciones_id = " . $this->table_prefix . ".recomendaciones_clasificaciones_id", "LEFT")->select("rc.recomendaciones_clasificaciones_nombre")
                ->join("recomendaciones_status rs", "rs.recomendaciones_status_id = " . $this->table_prefix . ".recomendaciones_status_id", "LEFT")->select("rs.recomendaciones_status_nombre")
                // BASE DE DATOS DE SAC
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = " . $this->table_prefix . ".recomendaciones_empleados_id", "LEFT")->select("e.*")
                ->select($this->table_prefix . ".*, CONCAT(e.empleados_nombre,' ',e.empleados_apellido_paterno, ' ',e.empleados_apellido_materno) AS 'nombre_completo'")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".titulos t", "t.titulos_id = e.empleados_titulos_id", "LEFT")->select("t.*")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".titulos subt", "subt.titulos_id = e.empleados_titulos_id", "LEFT")->select("subt.*")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".centros_costos cc", "cc.cc_id = e.empleados_cc_id", "LEFT")->select("cc.*")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".puestos p", "p.puestos_id = e.empleados_puestos_id", "LEFT")->select("p.puestos_nombre")
//                ->join("empleados_cc_historico ecch", "ecch.historico_" . $this->id_field . " = e." . $this->id_field, "LEFT")->select("ecch.historico_fecha_baja")->where("ecch.historico_fecha_baja IS NULL")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "LEFT")->select("direcciones_nombre, direcciones_nombre_generico, direcciones_ubicacion, direcciones_is_descentralizada, direcciones_tipos_ua_id")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".tipos_ua tua", "tua.tipos_ua_id = d.direcciones_tipos_ua_id", "LEFT")->select("tua.tipos_ua_nombre, tua.tipos_ua_genero")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "LEFT")->select("s.subdirecciones_nombre")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "LEFT")->select("dd.departamentos_nombre");
        $return = parent::get_todos($limit, $start, $incluirEliminados);
        foreach ($return as $index => $row) {
            forma_nombre_completo_de_ua($return[$index]);
            get_nombre_titulado($return[$index]);
            get_cargo_de_empleado($return[$index]);
            get_siglas_de_empleado($return[$index]);
        }
        return $return;
    }

    function get_recomendacion($recomendaciones_id) {
        $return = array();
        if (!empty($recomendaciones_id)) {
            $return = $this->get_uno($recomendaciones_id);
        }
        return $return;
    }

    /**
     *
     * @param type $observaciones_id
     * @param type $ordenados_por_numero
     * @return type
     */
    function get_recomendaciones($observaciones_id, $ordenados_por_numero = FALSE) {
        $return = array();
        if ($ordenados_por_numero) {
            $this->db->order_by("recomendaciones_numero", "ASC");
        }
        if (!empty($observaciones_id)) {
            $this->db->where("recomendaciones_observaciones_id", $observaciones_id);
            $return = $this->get_todos();
        }
        return $return;
    }

    function get_siguiente_numero_de_recomendacion($observaciones_id) {
        $return = 0;
        if (!empty($observaciones_id)) {
            $result = $this->db->select_max('recomendaciones_numero')
                    ->where("recomendaciones_observaciones_id", $observaciones_id)
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $return = $result->row()->recomendaciones_numero + 1;
            }
        }
        return $return;
    }

}
