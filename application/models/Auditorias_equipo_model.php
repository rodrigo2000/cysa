<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Auditorias_equipo_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_auditorias_id";
        $this->table_prefix = "eq";
        $this->model_name = __CLASS__;
    }

    function eliminar_equipo_de_auditoria($auditorias_id) {
        $return = FALSE;
        if (!empty($auditorias_id)) {
            $this->db
                    ->where($this->id_field, $auditorias_id)
                    ->where('auditorias_equipo_tipo', TIPO_PERMISO_EQUIPO_TRABAJO)
                    ->delete($this->table_name);
            $return = TRUE;
        }
        return $return;
    }

    function eliminar_equipo_adicional_auditoria($auditorias_id) {
        $return = FALSE;
        if (!empty($auditorias_id)) {
            $this->db
                    ->where($this->id_field, $auditorias_id)
                    ->where('auditorias_equipo_tipo', TIPO_PERMISO_ADICIONAL)
                    ->delete($this->table_name);
            $return = TRUE;
        }
        return $return;
    }

    function insert($data) {
        if ($this->{$this->module['controller'] . "_model"}->puedo_insertar()) {
            if ($this->db->insert($this->table_name, $data)) {
                $id = $this->db->insert_id();
                $return = array(
                    'state' => 'success',
                    'message' => 'Se ha agregado el registro.',
                    'data' => array(
                        'insert_id' => isset($data[$this->id_field]) ? $data[$this->id_field] : $id
                    )
                );
            } else {
                $this->inserted_id = false;
                $error = $this->db->error();
                $return = array(
                    'state' => 'warning',
                    'message' => 'No fue posible agregar el registro. Código ' . $error['code'] . ": " . $error['message'],
                    'query' => $this->db->last_query()
                );
            }
//        $this->Bitacora_model->insert(array(
//            'tabla' => $this->table_name,
//            'modulo' => $this->model_name,
//            'accion' => "INSERT",
//            'data' => json_encode($data),
//            'result' => json_encode($return),
//            'mensaje' => sprintf("El usuario %d agregó el registro %d", $this->session->id_usuario, intval($id))
//        ));
        } else {
            $return = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para insertar información'
            );
        }
        return $return;
    }

}
