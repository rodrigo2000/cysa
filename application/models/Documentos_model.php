<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Documentos_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "d";
        $this->model_name = __CLASS__;
    }

    function getResultados($limit = NULL, $start = NULL) {
        $this->db->join("documentos_versiones dv", "dv.documentos_versiones_documentos_id = " . $this->table_prefix . "." . $this->id_field, "INNER");
        parent::getResultados($limit, $start);
    }

    /**
     * Función para crear un documento
     * @param integer $auditorias_id Identificador de la auditoría
     * @param integer $documentos_tipos_id Identificador del tipo de documento
     * @param integer $documentos_versiones_id Identificador de la versión que desea que tenga el documento
     * @return boolean|array Regresa información del documento generado. FALSE en caso de no poder generar el documento.
     */
    function crear($auditorias_id, $documentos_tipos_id, $documentos_versiones_id = NULL) {
        $return = array(
            'sucess' => 'warning',
            'message' => 'No se especificó la auditoría y el tipo de documento'
        );
        if (!empty($auditorias_id) && !empty($documentos_tipos_id)) {
            if (empty($documentos_versiones_id)) {
                $version_activa = $this->Documentos_versiones_model->get_version_vigente_del_tipo_de_documento($documentos_tipos_id);
            }
            $data = array(
                'documentos_documentos_tipos_id' => $documentos_tipos_id,
                'documentos_documentos_versiones_id' => $documentos_versiones_id,
                'documentos_auditorias_id' => $auditorias_id,
                'documentos_is_cancelado' => 0,
                'documentos_is_aprobado' => 0
            );
            $return = $this->insert($data);
        }
        return $return;
    }

    /**
     * Obtiene la información de un documento
     * @param integer $documentos_id Identificador del documento
     * @return boolean|array Regresa la información del documento. FALSE en caso de no encontrar el documento.
     */
    function get_documento($documentos_id) {
        $return = FALSE;
        if (!empty($documentos_id)) {
            $this->db
                    ->join("documentos_versiones dv", "dv.documentos_versiones_id = " . $this->table_prefix . ".documentos_documentos_versiones_id", "INNER")
                    ->join("logotipos l", "l.logotipos_id = " . $this->table_prefix . ".documentos_logotipos_id", "LEFT");
            $return = $this->get_uno($documentos_id);
            $valores = $this->Documentos_valores_model->get_valores_de_documento($documentos_id);
            $return['valores'] = $valores;
            $aux = $this->Documentos_constantes_model->get_constantes_de_documento($return['documentos_documentos_tipos_id']);
            $constantes = array_column($aux, 'documentos_constantes_id', 'documentos_constantes_nombre');
            $return['constantes'] = $constantes;
            foreach ($constantes as $c => $v) {
                if (!defined($c)) {
                    define($c, intval($v));
                }
            }
        }
        return $return;
    }

    /**
     * Devuelve un listado de los documentos asociados a la auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @param integer $documentos_tipos_id Identificador del tipo de documento.
     * @return array Listado de documentos
     */
    function get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id = NULL) {
        $return = array();
        if (!empty($auditorias_id)) {
            if (!empty($documentos_tipos_id) && is_numeric($documentos_tipos_id)) {
                $this->db->where("documentos_documentos_tipos_id", $documentos_tipos_id);
            }
            $result = $this->db
                    ->join("documentos_versiones dv", "dv.documentos_versiones_id = " . $this->table_prefix . ".documentos_documentos_versiones_id", "INNER")
                    ->join("logotipos l", "l.logotipos_id = " . $this->table_prefix . ".documentos_logotipos_id", "LEFT")
                    ->where("documentos_auditorias_id", $auditorias_id)
                    ->where($this->table_prefix . ".fecha_delete IS NULL")
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
                foreach ($return as $index => $d) {
                    $return[$index]['valores'] = $this->Documentos_valores_model->get_valores_de_documento($d['documentos_id']);
                    $aux = $this->Documentos_constantes_model->get_constantes_de_documento($documentos_tipos_id);
                    $constantes = array_column($aux, 'documentos_constantes_id', 'documentos_constantes_nombre');
                    $return[$index]['tooltiptext'] = array_column($aux, 'documentos_constantes_tooltiptext', 'documentos_constantes_id');
                    $return[$index]['descripciones'] = array_column($aux, 'documentos_constantes_descripcion', 'documentos_constantes_id');
                    foreach ($constantes as $c => $v) {
                        if (!defined($c)) {
                            define($c, intval($v));
                        }
                    }
                }
            } elseif (!empty($documentos_tipos_id)) {
                $return[0] = $this->get_template($documentos_tipos_id);
            }
        }
        return $return;
    }

    function get_template($documentos_tipos_id) {
        $return = array();
        if (!empty($documentos_tipos_id)) {
            $documentos_versiones = $this->Documentos_versiones_model->get_version_vigente_del_tipo_de_documento($documentos_tipos_id);
            $aux = $this->Documentos_constantes_model->get_constantes_de_documento($documentos_tipos_id);
            $constantes = array_column($aux, 'documentos_constantes_id', 'documentos_constantes_nombre');
            $return['tooltiptext'] = array_column($aux, 'documentos_constantes_tooltiptext', 'documentos_constantes_id');
            $return['descripciones'] = array_column($aux, 'documentos_constantes_descripcion', 'documentos_constantes_id');
            foreach ($constantes as $c => $v) {
                if (!defined($c)) {
                    define($c, intval($v));
                }
            }
            $return = array_merge($documentos_versiones, $return);
        }
        return $return;
    }

    /**
     * Devuelve información del tipo de documento de un documento
     * @param integer $documentos_id Identificador del documento
     * @return array Información del tipo de documento
     */
    function get_tipo_de_documento_de_documento($documentos_id = NULL) {
        $return = array();
        if (!empty($documentos_id)) {
            $result = $this->db->select("dt.*")
                    ->join("documentos_tipos dt", "dt.documentos_tipos_id = d.documentos_documentos_tipos_id", "INNER")
                    ->where($this->id_field, $documentos_id)
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result && $result->num_rows() == 1) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

}
