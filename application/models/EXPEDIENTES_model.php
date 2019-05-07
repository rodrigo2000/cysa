<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class EXPEDIENTES_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = "expedientes";
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "exp";
        $this->model_name = __CLASS__;

        $this->dbExpedientes = $this->getDatabase(APP_NAMESPACE_EXPEDIENTES);
    }

    function get_expediente_de_auditoria($auditorias_id = NULL) {
        $return = array();
        if (!empty($auditorias_id)) {
            $result = $this->dbExpedientes
                    ->where("expedientes_idAuditoria", $auditorias_id)
                    ->limit(1)
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function get_expediente($expedientes_id = NULL) {

    }

    function update_expedientes_por_auditoria($id, $data) {
        $return = array(
            'state' => 'error',
            'message' => 'Ocurrió un error al editar el registro.',
        );
        if (count($data) > 0) {
            $this->dbExpedientes
                    ->where('expedientes_idAuditoria', $id);
            $result = $this->dbExpedientes->update($this->table_name, $data);
        } else {
            $result = true;
        }
        if ($result) {
            $return = array(
                'state' => 'success',
                'message' => 'Se ha editado el registro.',
                'data' => array(
                    'affected_rows' => $result === true ? 0 : $this->dbExpedientes->affected_rows(),
                    'query' => $result === true ? '' : $this->dbExpedientes->last_query()
                )
            );
        } else {
            $error = $this->dbExpedientes->error();
            $return = array(
                'state' => 'warning',
                'message' => 'No fue posible actualizar el registro. Código ' . $error['code'] . ": " . $error['message'],
                'query' => $this->dbExpedientes->last_query()
            );
        }
        return $return;
    }

    function update($id, $data) {
        if ($this->{$this->module['controller'] . "_model"}->puedo_modificar()) {
            $return = array(
                'state' => 'error',
                'message' => 'Ocurrió un error al editar el registro.',
            );
            if (count($data) > 0) {
                $this->dbExpedientes
                        ->where($this->id_field, $id);
                $result = $this->dbExpedientes->update($this->table_name, $data);
            } else {
                $result = true;
            }
            if ($result) {
                $return = array(
                    'state' => 'success',
                    'message' => 'Se ha editado el registro.',
                    'data' => array(
                        'affected_rows' => $result === true ? 0 : $this->dbExpedientes->affected_rows(),
                        'query' => $result === true ? '' : $this->dbExpedientes->last_query()
                    )
                );
            } else {
                $error = $this->dbExpedientes->error();
                $return = array(
                    'state' => 'warning',
                    'message' => 'No fue posible actualizar el registro. Código ' . $error['code'] . ": " . $error['message'],
                    'query' => $this->dbExpedientes->last_query()
                );
            }
        } else {
            $return = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para modificar información'
            );
        }
//        $this->Bitacora_model->insert(array(
//            'tabla' => $this->table_name,
//            'modulo' => $this->model_name,
//            'accion' => "UPDATE",
//            'data' => json_encode($data),
//            'result' => json_encode($return),
//            'mensaje' => sprintf("El usuario %d actualizó la información del registro %d", $this->session->id_usuario, $id)
//        ));
        return $return;
    }

    function get_proto_expedientes() {
        $config['hostname'] = APP_DATABASE_HOSTNAME;
        $config['username'] = APP_DATABASE_USERNAME;
        $config['password'] = APP_DATABASE_PASSWORD;
        $config['database'] = 'proto_' . APP_DATABASE_EXPEDIENTES;
        $config['dbdriver'] = 'mysqli';
        $config['dbprefix'] = '';
        $config['pconnect'] = FALSE;
        $config['db_debug'] = TRUE;
        $config['cache_on'] = FALSE;
        $config['cachedir'] = '';
        $config['char_set'] = 'utf8';
        $config['dbcollat'] = 'utf8_general_ci';

        return $this->load->database($config, TRUE);
    }

}
