<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class MY_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->table_prefix = "";
        $this->table_name = "";
        $this->id_field = "";
        $this->model_name = "";
        $this->valida_login = TRUE;
        $this->valida_acceso_al_modulo = TRUE;
        $this->valida_puedo_insertar = TRUE;
        $this->valida_puedo_modificar = TRUE;
        $this->valida_puedo_eliminar = TRUE;
        $this->valida_puedo_destruir = TRUE;

        $this->siempre_insertar = FALSE;
        $this->siempre_modificar = FALSE;
        $this->siempre_eliminar = FALSE;
        $this->siempre_destruir = FALSE;

        date_default_timezone_set('America/Merida');
    }

    function actualizar_session($variable, $valor, $namespace = APP_NAMESPACE) {
        $aux = $this->session->userdata($namespace);
        $aux[$variable] = $valor;
        $this->session->set_userdata($namespace, $aux);
    }

    function isAdmin() {
        return ($this->session->userdata('perfiles_id') == 1);
    }

    function tengo_acceso_al_modulo($nombre_controlador = NULL) {
        $return = FALSE;
        if (!empty($nombre_controlador)) {
            $permisos = $this->session->userdata('permisos');
            if (!empty($permisos) && isset($permisos[APP_NAMESPACE]) && isset($permisos[APP_NAMESPACE][$nombre_controlador]) && $permisos[APP_NAMESPACE][$nombre_controlador] == TRUE) {
                $return = TRUE;
            } elseif ($this->isAdmin()) {
                $return = TRUE;
            }
        }
        return $return;
    }

    function tengo_acceso_aqui() {
        $controller = &get_instance();
        return $this->tengo_acceso_al_modulo($controller->module['controller']);
    }

    function puedo_visualizar() {
        return $this->tengo_permiso(PERMISOS_VISUALIZAR);
    }

    function puedo_insertar() {
        $return = $this->siempre_insertar;
        if ($this->valida_puedo_insertar) {
            $return = $this->tengo_permiso(PERMISOS_NUEVO);
        }
        return $return;
    }

    function puedo_modificar() {
        $return = $this->siempre_modificar;
        if ($this->valida_puedo_modificar) {
            $return = $this->tengo_permiso(PERMISOS_MODIFICAR);
        }
        return $return;
    }

    function puedo_eliminar() {
        $return = $this->siempre_eliminar;
        if ($this->valida_puedo_eliminar) {
            $this->tengo_permiso(PERMISOS_ELIMINAR);
        }
        return $return;
    }

    function puedo_destruir() {
        $return = $this->siempre_destruir;
        if ($this->valida_puedo_destruir) {
            $this->tengo_permiso(PERMISOS_DESTRUIR);
        }
        return $return;
    }

    /**
     * Indica si se tiene el permiso para relizar cierta acción
     * @param integer $permisos_id Permiso al que se desea acceder
     * @param string $app Nombre de la aplicación. De forma predeterminada es el sistema que invoca la acción
     * @param string $controlador Nombre del controlador al que se desea realizar una acción. De forma predeterminada es el controlador que invoca la acción
     * @return boolean Devuelve TRUE cuando el usuario logueado tiene permiso para realizar la acción en el controlador
     */
    function tengo_permiso($permisos_id, $app = NULL, $controlador = NULL) {
        $return = FALSE;
        $permisos = $this->session->userdata('permisos');
        if (empty($app)) {
            $app = APP_NAMESPACE;
        }
        if (empty($controlador)) {
            $controlador = &get_instance()->module['controller'];
        }
        if (!empty($permisos) && isset($permisos[$app]) && isset($permisos[$app][$controlador]) && isset($permisos[$app][$controlador][$permisos_id]) && $permisos[$app][$controlador][$permisos_id] === TRUE) {
            $return = TRUE;
        } elseif ($this->isAdmin()) {
            $return = TRUE;
        }
        return $return;
    }

    public function record_count() {
        return $this->db->count_all_results($this->table_name . " " . $this->table_prefix);
    }

    public function getResultados($limit = NULL, $start = NULL) {
        $return = array();
        if (!empty($limit) && !empty($start)) {
            $this->db->limit($limit, $start);
        }
        $query = $this->db->get($this->table_name . " " . $this->table_prefix);
        if (!$query) {
            $db_error = $this->db->error();
            echo "<p><b>Error " . $db_error['code'] . ": " . $db_error['message'] . "</b><br>\nQuery: " . $this->db->last_query() . "</p>";
        } elseif ($query->num_rows() > 0) {
            $return = $query->result_array();
        }
        return $return;
    }

    /**
     * Devuelve el registro correspondiente al identificador proporcionado
     * @param integer $id Identificador numérico del registro
     * @return array Devuelve un arreglo con la informaición del registro
     */
    public function get_uno($id) {
        $return = array();
        if (!empty($id)) {
            $query = $this->db->where($this->id_field, $id)
                    ->limit(1)
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($query && $query->num_rows() == 1) {
                $return = $query->row_array();
            }
        }
        return $return;
    }

    /**
     * Función genérica para obtener todos los elementos de una tabla. De forma predeterminada solo regresa elementos que no esten eliminados.
     * @param number $limit El número máximo de filas devueltas por la consulta SELECT
     * @param number $start Número del primer registro por el que comenzará el resultado devuelto.
     * @param boolean TRUE para que incluir elementos que han sido eliminados. FALSE en caso contrario.
     * @return array Devuelve todos las filas de una tabla
     */
    function get_todos($limit = NULL, $start = NULL, $incluirEliminados = FALSE) {
        if (!$incluirEliminados) {
            $this->db->where($this->table_prefix . ".fecha_delete IS NULL");
        }
        return $this->getResultados($limit, $start);
    }

    /**
     * Conviernte una tabla en una arreglo bidimensional
     * @param string $index Nombre del campo que contiene el valor del índice del arreglo
     * @param string $valor Nombre del campo que contiene el valor del elemento del arreglo
     * @return array Tabla convertida en arreglo
     */
    function getResultadosAsArray($index, $valor) {
        $return = array();
        $data = $this->getResultados(NULL, NULL);
        foreach ($data as $d) {
            $return[$d[$index]] = $d[$valor];
        }
        return $return;
    }

    function initSessionDeUsuario($username, $password) {
        $return = array(
            'success' => FALSE,
            'config' => NULL,
            'message' => NULL,
        );
        if (empty($username)) {
            $return['message'] = "Olvidaste el nombre de usuario y/o contraseña";
            return $return;
        }
        $result = $this->db->select("u.*, e.*, p.puestos_nombre, cc.*")
                ->where('usuarios_username', $username)
                ->join("empleados e", "empleados_id = usuarios_empleados_id", "LEFT")
                ->join("puestos p", "puestos_id = e.empleados_puestos_id", "LEFT")
                ->join("centros_costos cc", "cc_id = empleados_cc_id", "LEFT")
                ->get('usuarios u');
        if ($result->num_rows() == 1) {
            $usuario = $result->row_array();
            if ($usuario['usuarios_is_activo'] == 1) {
                $password_encriptado = hash('sha256', $password);
                if ($usuario['usuarios_contrasena'] === $password_encriptado) {
                    $data = array(
                        'logueado' => TRUE,
                        'empleados_id' => intval($usuario['usuarios_empleados_id']),
                        'usuarios_id' => intval($usuario['usuarios_id']),
                        'numero_empleado' => intval($usuario['empleados_numero_empleado']),
                        'username' => $username,
                        'nombre_completo' => $usuario['empleados_nombre'] . " " . $usuario['empleados_apellido_paterno'] . " " . $usuario['empleados_apellido_materno'],
                        'puesto' => $usuario['puestos_nombre'],
                        'puestos_id' => $usuario['empleados_puestos_id'],
                        'cc_id' => $usuario['empleados_cc_id'],
                        'cc_label' => $usuario['cc_etiqueta_direccion'] . "." . $usuario['cc_etiqueta_subdireccion'] . "." . $usuario['cc_etiqueta_departamento'],
                        'direcciones_id' => $usuario['cc_direcciones_id'],
                        'subdirecciones_id' => $usuario['cc_subdirecciones_id'],
                        'departamentos_id' => $usuario['cc_departamentos_id'],
                        'activo' => $usuario['usuarios_is_activo'],
                        'correo' => $usuario['empleados_correo_electronico'],
                        'perfiles_id' => $usuario['usuarios_perfiles_id'],
                        'avatar' => $usuario['usuarios_avatar'],
                        'permisos' => $this->Permisos_usuario_model->get_jerarquia_de_permisos($usuario['usuarios_id'], APP_PERMISOS_TIPO_JERARQUIA_VARIABLES)
                    );
                    $return = array(
                        'success' => TRUE,
                        'config' => $data
                    );
                } elseif (empty($usuario['usuarios_contrasena'])) {
                    redirect(base_url() . "Login/primera_vez/" . $usuario['usuarios_id']);
                } else {
                    $return['message'] = "Su contraseña es incorrecta.";
                }
            } elseif ($result['intentos'] < 5) {
                $return['message'] = "Su <strong>usuario</strong> esta desactivado. Por favor comuníquese a SOPORTE para brindarle información de este inconveniente.";
            }
        } else {
            $return['message'] = 'Nombre de usuario incorrecto.';
        }
        return $return;
    }

    function delete($id) {
        if ($this->valida_puedo_eliminar || $this->{$this->module['controller'] . "_model"}->puedo_eliminar()) {
            $fechaDelete = date("Y-m-d H:i:s");
            $this->db->set('fecha_delete', $fechaDelete)
                    ->where($this->id_field, $id);
            $r = $this->db->update($this->table_name);
            if ($r || $this->db->affected_rows() > 0) {
                $return = array(
                    'state' => 'success',
                    'message' => 'Se ha marcado de eliminado el registro.'
                );
            } else {
                $return = array(
                    'state' => 'error',
                    'message' => 'No se pudo marcar de eliminado el registro.'
                );
            }
        } else {
            $return = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para eliminar información'
            );
        }
//        $this->Bitacora_model->insert(array(
//            'tabla' => $this->table_name,
//            'modulo' => $this->model_name,
//            'accion' => "DELETE",
//            'data' => json_encode(array($this->id_field => $id)),
//            'result' => json_encode($return),
//            'mensaje' => sprintf("El usuario %d eliminó el registro %d", $this->session->id_usuario, $id)
//        ));
        return $return;
    }

    function destroy($id) {
        if ($this->{$this->module['controller'] . "_model"}->puedo_destruir()) {
            $this->db->where($this->id_field, $id);
            $r = $this->db->delete($this->table_name);
            if ($r || $this->db->affected_rows() > 0) {
                $return = array(
                    'state' => 'success',
                    'message' => 'Se ha destruído el registro.'
                );
            } else {
                $return = array(
                    'state' => 'error',
                    'message' => 'No se pudo destruído el registro.'
                );
            }
//        $this->Bitacora_model->insert(array(
//            'tabla' => $this->table_name,
//            'modulo' => $this->model_name,
//            'accion' => "DELETE",
//            'data' => json_encode(array($this->id_field => $id)),
//            'result' => json_encode($return),
//            'mensaje' => sprintf("El usuario %d eliminó el registro %d", $this->session->id_usuario, $id)
//        ));
        } else {
            $return = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para destruir información'
            );
        }
        return $return;
    }

    function deleteBatch($id) {
        $this->db->where_in($this->id_field, $id);
        $r = $this->db->delete($this->table_name);
        if ($r || $this->db->affected_rows() > 0) {
            $return = array(
                'state' => 'success',
                'message' => count($id) > 1 ? 'Se han eliminado ' . count($id) . ' registros.' : 'Se ha eliminado el registro.'
            );
        } else {
            $return = array(
                'state' => 'error',
                'message' => 'No se pudo eliminar el registro.'
            );
        }
//        $this->Bitacora_model->insert(array(
//            'tabla' => $this->table_name,
//            'modulo' => $this->model_name,
//            'accion' => "DELETE",
//            'data' => json_encode(array($this->id_field => $id)),
//            'result' => json_encode($return),
//            'mensaje' => sprintf("El usuario %d eliminó los registros %s", $this->session->id_usuario, implode(",", $id))
//        ));
        return $return;
    }

    function update($id, $data) {
        if ($this->{$this->module['controller'] . "_model"}->puedo_modificar()) {
            $return = array(
                'state' => 'error',
                'message' => 'Ocurrió un error al editar el registro.',
            );
            if (count($data) > 0) {
                $this->db
                        ->set("fecha_update", date("Y-m-d H:i:s"))
                        ->where($this->id_field, $id);
                $result = $this->db->update($this->table_name, $data);
            } else {
                $result = true;
            }
            if ($result) {
                $return = array(
                    'state' => 'success',
                    'message' => 'Se ha editado el registro.',
                    'data' => array(
                        'affected_rows' => $result === TRUE ? 0 : $this->db->affected_rows(),
                        'query' => $result === TRUE ? '' : $this->db->last_query()
                    )
                );
            } else {
                $error = $this->db->error();
                $return = array(
                    'state' => 'warning',
                    'message' => 'No fue posible actualizar el registro. Código ' . $error['code'] . ": " . $error['message'],
                    'query' => $this->db->last_query()
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

    function insert($data) {
        if ($this->{$this->module['controller'] . "_model"}->puedo_insertar()) {
            if (!isset($data['fecha_insert']) || empty($data['fecha_insert'])) {
                $data['fecha_insert'] = date("Y-m-d H:i:s");
            }
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

    function insert_batch($data) {
        if ($this->db->insert_batch($this->table_name, $data)) {
            $return = array(
                'state' => 'success',
                'message' => 'Se ha' . (count($data) > 1 ? 'n' : '') . ' agregado ' . count($data) . ' el registro' . (count($data) > 1 ? 's' : '') . '.',
                'data' => ''
            );
        } else {
            $return = array(
                'state' => 'warning',
                'message' => 'No fue posible agregar los registros. ' . $this->db->_error_message()
            );
        }
//        $this->Bitacora_model->insert(array(
//            'tabla' => $this->table_name,
//            'modulo' => $this->model_name,
//            'accion' => "INSERT_BATCHA",
//            'data' => json_encode($data),
//            'result' => json_encode($return),
//            'mensaje' => sprintf("El usuario %d agregó múltiples registros", $this->session->id_usuario)
//        ));
        return $return;
    }

    /**
     * Función que permite conectarse a una base de datos previamente configurada.
     * @param string $dbName Nombre de configuración a usar para conectarse a la base de datos
     * @return resource Devuelve la instancia con la conexión a la base de datos, FALSE en caso de error
     */
    function getDatabase($dbName) {
        $CI = &get_instance();
        return $CI->load->database($dbName, TRUE);
    }

    function get_database($nombre_basedatos) {
        $return = FALSE;
        if (!empty($nombre_basedatos)) {
            $nombre_basedatos = constant("APP_DATABASE_" . APP_NAMESPACE);
        }
        $CI = &get_instance();
        $return = $CI->load->database($nombre_basedatos, TRUE);
        return $return;
    }

    function isTipoUsuario($tipo) {
        return $this->session->id_tipousuario == $tipo;
    }

    /**
     * Función que indica si el perfil del usuario proporcionado corresponde al perfil
     * del usuario de la sesión del usuario
     * @param type $idPerfil Identificador del perfil del usuario a verificar
     * @return bool Devuelve TRUE cuando el perfil del usuario proporcionado es igual al perfil del usuario de la sesión del usuario. Devuelve FALSE cuando son diferentes.
     */
    function isPerfil($idPerfil) {
        return $this->session->id_perfil == $idPerfil;
    }

}
