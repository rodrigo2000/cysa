<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Observaciones_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_prefix = "o";
        $this->table_name = "observaciones";
        $this->id_field = "observaciones_id";
        $this->model_name = __CLASS__;
    }

    function delete($id) {
        $this->db->set('observaciones_is_eliminada', 1);
        return parent::delete($id);
    }

    function getResultados($limit = NULL, $start = NULL) {
        $this->db->order_by("observaciones_numero", "ASC");
        return parent::getResultados($limit, $start);
    }

    function get_observacion($observaciones_id = NULL) {
        $return = array();
        if (!empty($observaciones_id)) {
            $return = $this->get_uno($observaciones_id);
            $return['recomendaciones'] = $this->get_recomendaciones_de_observacion($observaciones_id);
        }
        return $return;
    }

    function get_observaciones($auditorias_id = NULL, $incluir_eliminadas = FALSE) {
        $return = array();
        if (!empty($auditorias_id)) {
            $real_auditoria_origen_id = $this->Auditorias_model->get_real_auditoria_origen($auditorias_id);
            if (!$incluir_eliminadas) {
                $this->db
                        ->group_start()
                        ->where("observaciones_is_eliminada !=", 1)
                        ->or_where("fecha_delete", NULL)
                        ->group_end();
            }
            $this->db->where("observaciones_auditorias_id", $real_auditoria_origen_id);
            $return = $this->getResultados();
        }
        return $return;
    }

    function get_recomendaciones_de_observacion($observaciones_id) {
        $return = array();
        if (!empty($observaciones_id)) {
            $return = $this->Recomendaciones_model->get_recomendaciones($observaciones_id);
        }
        return $return;
    }

    function is_solventada($observaciones_id) {
        $return = FALSE;
        if (!empty($observaciones_id)) {
            $status = $this->get_status($observaciones_id);
            if (isset($status[1]) && count($status) == 1) {
                $return = TRUE;
            }
        }
        return $return;
    }

    function get_status($observaciones_id) {
        $return = array();
        if (!empty($observaciones_id)) {
            $recomendaciones = $this->get_recomendaciones_de_observacion($observaciones_id);
            $status = array();
            foreach ($recomendaciones as $r) {
                $status_id = intval($r['recomendaciones_status_id']);
                if (!isset($status[$status_id])) {
                    $status[$status_id] = array();
                }
                array_push($status[$status_id], $r['recomendaciones_id']);
            }
            $return = $status;
        }
        return $return;
    }

    function get_siguiente_numero_de_observacion($auditorias_id) {
        $return = 0;
        if (!empty($auditorias_id)) {
            $result = $this->db->select_max('observaciones_numero')
                    ->where("observaciones_auditorias_id", $auditorias_id)
                    ->where("observaciones_is_eliminada", 0)
                    ->where("fecha_delete", NULL)
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $return = intval($result->row()->observaciones_numero) + 1;
            }
        }
        return $return;
    }

    /**
     * Esta función re-enumera las observaciones de un auditoría. Se debe usar después de eliminar o destruir una observación
     * @param integer $auditorias_id Identificador de la auditoría
     * @return boolen Devuelve un arreglo de identificadores ordenados ascendentemente reflando el orden actual de las observaciones. Arreglo vacío si no hubo cambios.
     */
    function reenumerar_observaciones_de_auditoria($auditorias_id) {
        $return = array();
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->where("fecha_delete", NULL)
                    ->where("observaciones_auditorias_id", $auditorias_id)
                    ->order_by('observaciones_numero', 'ASC')
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $observaciones = $result->result_array();
                foreach ($observaciones as $index => $o) {
                    $this->db
                            ->set("observaciones_numero", $index + 1)
                            ->where($this->id_field, $o[$this->id_field])
                            ->update($this->table_name);
                }
                if ($this->db->affected_rows() > 0) {
                    $this->db
                            ->where("observaciones_auditorias_id", $auditorias_id)
                            ->order_by('observaciones_numero', 'ASC');
                    $aux = $this->get_todos();
                    $return = array_column($aux, 'observaciones_id', 'observaciones_numero');
                }
            }
        }
        return $return;
    }

}
