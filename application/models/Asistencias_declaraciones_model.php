<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Asistencias_declaraciones_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "ad";
        $this->model_name = __CLASS__;
    }

    function get_declaracion($documentos_id, $empleados_id) {
        $return = NULL;
        if (!empty($documentos_id) && !empty($empleados_id)) {
            $result = $this->db
                    ->where('asistencias_declaraciones_documentos_id', $documentos_id)
                    ->where('asistencias_declaraciones_empleados_id', $empleados_id)
                    ->limit(1)
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $return = $result->row()->asistencias_declaraciones_declaracion;
            }
        }
        return $return;
    }

    function insert_update($documentos_id, $empleados_id, $texto_declaracion = NULL) {
        $return = array('success' => FALSE, 'message' => 'ERROR en insert-update de declaraciones');
        $result = $this->db
                ->where('asistencias_declaraciones_documentos_id', $documentos_id)
                ->where('asistencias_declaraciones_empleados_id', $empleados_id)
                ->limit(1)
                ->get($this->table_name);
        if ($result && $result->num_rows() == 0) {
            $data = array(
                'asistencias_declaraciones_documentos_id' => $documentos_id,
                'asistencias_declaraciones_empleados_id' => $empleados_id,
                'asistencias_declaraciones_declaracion' => $texto_declaracion
            );
            $return = $this->insert($data);
        } else {
            $ahora = ahora();
            $this->db
                    ->set('asistencias_declaraciones_declaracion', $texto_declaracion)
                    ->set('fecha_update', $ahora)
                    ->where('asistencias_declaraciones_documentos_id', $documentos_id)
                    ->where('asistencias_declaraciones_empleados_id', $empleados_id)
                    ->update($this->table_name);
            $return = array('success' => TRUE, 'message' => 'La declaración ha sido actualizada');
        }
        return $return;
    }

}
