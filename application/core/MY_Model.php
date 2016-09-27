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
        date_default_timezone_set('America/Merida');
    }

    public function record_count() {
        return $this->db->count_all_results($this->table_name . " " . $this->table_prefix);
    }

    public function getResultados($limit, $start) {
        $query = $this->db
                ->limit($limit, $start)
                ->get($this->table_name . " " . $this->table_prefix);
        //echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return array();
    }

    function initSessionDeUsuario($username, $password) {
        $return = array(
            'success' => FALSE,
            'config' => NULL,
            'message' => NULL,
        );
        if (empty($username) || empty($password)) {
            $return['message'] = "Olvidaste el nombre de usuario y/o contraseña";
            return $return;
        }
        $dbControl = $this->getDatabase('sac');
        $query = $dbControl->select("u.privilegio, u.idEmpleado, u.correo, u.bActivo, u.intentos, e.idPuesto, e.clv_dir, e.clv_subdir, e.clv_depto")
                ->where('u.usuario', $username)
                ->where('u.contrasenia', $password)
                ->join("dcont_empleado e", "e.idEmpleado = u.idEmpleado", "INNER")->select("e.nombre, e.aPaterno, e.aMaterno")
                ->join('ayunta_puesto p', 'p.idPuesto = e.idPuesto', "INNER")->select("denPuesto")
                ->limit(1)
                ->get("dcont_usuario u");
        if ($query->num_rows() == 1) {
            $result = $query->row_array();
            $this->db->set("intentos", "intentos+1", FALSE)->where("idEmpleado", $result['idEmpleado'])->update("dcont_usuario");
            if ($result['intentos'] < 5) {
                if ($result['bActivo'] == TRUE) {
                    $data = array(
                        'logueado' => TRUE,
                        'privilegios' => $result['privilegio'],
                        'id_empleado' => intval($result['idEmpleado']),
                        'usuario' => $username,
                        'nombre_usuario' => $result['nombre'] . " " . $result['aPaterno'] . " " . $result['aMaterno'],
                        'puesto' => $result['denPuesto'],
                        'id_puesto' => $result['idPuesto'],
                        'id_direccion' => $result['clv_dir'],
                        'id_subdireccion' => $result['clv_subdir'],
                        'id_departamento' => $result['clv_depto'],
                        'activo' => $result['bActivo'],
                        'correo' => $result['correo']
                    );
                    $return = array(
                        'success' => TRUE,
                        'config' => $data
                    );
                    $this->db->set("intentos", 0)->where("idEmpleado", $result['idEmpleado'])->update("dcont_usuario");
                    return $return;
                } else {
                    $return['message'] = "Su <strong>usuario</strong> esta desactivado. Por favor comuníquese a SOPORTE para brindarle información de este inconveniente.";
                    return $return;
                }
            } else {
                $return['message'] = "Su cuenta esta desactivada debido a que excedió la cantidad de intentos permitidos. Por favor comuníquese a SOPORTE para brindarle información de este inconveniente.";
                return $return;
            }
        } else {
            $return['message'] = 'Nombre de usuario o contraseña incorrectos.';
            return $return;
        }
    }

    function delete($id) {
        $this->db->where($this->id_field, $id);
        $r = $this->db->delete($this->table_name);
        if ($r || $this->db->affected_rows() > 0) {
            $return = array(
                'state' => 'success',
                'message' => 'Se ha eliminado el registro.'
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
//            'mensaje' => sprintf("El usuario %d eliminó el registro %d", $this->session->id_usuario, $id)
//        ));
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
        if (count($data) > 0) {
            $this->db->where($this->id_field, $id);
            $result = $this->db->update($this->table_name, $data);
        } else {
            $result = true;
        }
        if ($result) {
            $return = array(
                'state' => 'success',
                'message' => 'Se ha editado el registro.',
                'data' => array(
                    'affected_rows' => $result === true ? 0 : $this->db->affected_rows(),
                    'query' => $result === true ? '' : $this->db->last_query()
                )
            );
        } else {
            $return = array(
                'state' => 'error',
                'message' => 'Ocurrió un error al editar el registro.',
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
            $return = array(
                'state' => 'warning',
                'message' => 'No fue posible agregar el registro. ' . $this->db->_error_message()
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

    /**
     * Calcula la fecha maxima de plazo, en base a N dias de aplazamiento, incluye el
     * calculo de los dias inhabiles registrados en el catalogo de dias inhabiles.
     * @param fBase Fecha base al que se le calcula el offset en formato date.
     * @param maxDias Numero de dias de desplazamiento de la fecha base.
     * @return string Regresa la fecha limite de 'solventacion' en formato YYYY-MM-DD.
     */
    function getTotalHabiles_v2($fBase, $maxDias) {
        global $conex;
        $signo = 0;
        if ($maxDias > 0) {
            $signo = 1;
        };
        //echo $maxDias.'<br>';
        //Regresa la fecha (pero sin tomar en cuenta los dias inhabiles)
        $fVisibleRevision = getFechaOffset_v2($fBase, $maxDias);
        //echo "fechaVisible $fVisibleRevision<br>";

        if ($signo) {
            $fIni = $fBase;
            $fFin = $fVisibleRevision;
        }//si es negativo se invierte.
        else {
            $fIni = $fVisibleRevision;
            $fFin = $fBase;
        }

        //Contamos los dias inhabiles entre las dos fechas
//        $queryInhablies = 'SELECT COUNT(*) AS inhabiles FROM ' . BD_CYSA . '.' . TB_DIAS_INHABILES .
//                ' WHERE (fechaInhabil BETWEEN \'' . $fIni . '\' and \'' . $fFin .
//                '\') AND (WEEKDAY(fechaInhabil)<>5 and WEEKDAY(fechaInhabil)<>6)';
        $dbCYSA = $this->getDatabase("cysa");
        $rowInhabiles = $dbCYSA->select("COUNT(*) inhabiles")
                ->where("fechaInhabil BETWEEN '" . $fIni . "' AND '" . $fFin . "'")
                ->where('WEEKDAY(fechaInhabil) !=', 5)
                ->where('WEEKDAY(fechaInhabil) !=', 6)
                ->get('calendario_dias_inhabiles')
                ->row_array();

//        $resultInhabiles = $conex->ejecutaQuery($queryInhablies);
//        $rowInhabiles = mysql_fetch_assoc($resultInhabiles);
        //echo $rowInhabiles['inhabiles'].'--';
        if ($rowInhabiles['inhabiles'] > 0) {
            if (!$signo) {
                $agregaSigno = '-';
            } else {
                $agregaSigno = '+';
            }
            //echo  $fVisibleRevision.' '.$agregaSigno.'1 days';
            $datestart = strtotime($fVisibleRevision . ' ' . $agregaSigno . '1 days');
            $fVisibleRevision = date('Y-m-d', $datestart);
            //return $fVisibleRevision;
            $rowInhabiles['inhabiles'] --;

            if ($rowInhabiles['inhabiles'] > 0 && date('N', $datestart) == 7 && $agregaSigno == '-') {//Correcci�n para los negativos debido a que mandaba a domingo en vez de viernes y la funciongetTotalHabiles lo consideraba positivo.
                $datestart = strtotime($fVisibleRevision . ' ' . $agregaSigno . '2 days');
                $fVisibleRevision = date('Y-m-d', $datestart);
            }

            return $this->getTotalHabiles_v2($fVisibleRevision, $agregaSigno . $rowInhabiles['inhabiles']);
        } else {
            return $fVisibleRevision;
        }
    }

    /**
     * Función que indica si el tipo de usuario proporcionado corresponde al tipo de usuario 
     * de la sesión del usuario
     * @param type $idTipoUsuario Identificador del tipo de usuario a verificar
     * @return bool Devuelve TRUE cuando el tipo de usuario proporcionado es igual al tipo de usuario de la sesión del usuario. Devuelve FALSE cuando son diferentes.
     */
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
