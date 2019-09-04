<?php

class MY_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->template = "";
        $this->module['name'] = '';
        $this->module['folder'] = '';
        $this->module['controller'] = '';
        $this->module['function'] = '';
        $this->module['title'] = '';
        $this->module['title_list'] = "";
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Modificar";
        $this->module['title_delete'] = "Eliminar";
        $this->module['title_destroy'] = "Destruir";
        $this->module['id_field'] = "";
        $this->module['tabla'] = "";
        $this->module['prefix'] = "";
        $this->Breadcrumbs = array();
        date_default_timezone_set('America/Merida');
    }

    /**
     * Función que indica si el tipo de cuenta del usuario corresponde al tipo de cuenta proporcionado
     * @param int $idTipoCuenta Identificador del tipo de cuenta
     * @return bool Devuelve TRUE cuando el tipo de cuenta proporcionado es igual al tipo de cuenta de la sesión del usuario. Devuelve FALSE cuando son diferentes.
     */
    function isTipoCuenta($idTipoCuenta) {
        return $this->session->id_tipocuenta == $idTipoCuenta;
    }

    /**
     * Función que indica si el tipo de usuario proporcionado corresponde al tipo de usuario
     * de la sesión del usuario
     * @param type $idTipoUsuario Identificador del tipo de usuario a verificar
     * @return bool Devuelve TRUE cuando el tipo de usuario proporcionado es igual al tipo de usuario de la sesión del usuario. Devuelve FALSE cuando son diferentes.
     */
    function isTipoUsuario($idTipoUsuario) {
        return $this->session->id_tipousuario == $idTipoUsuario;
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

    /**
     * Función que inicializa variables de acceso restringido y
     * luego re-direcciona a la página correspondiente
     */
    function sin_permisos() {
        $permisos = $this->session->userdata('permisos');
        if (!empty($permisos)) {
            $informacion = array('state' => 'warning', 'message' => 'No tiene permisos suficientes');
            $this->session->set_flashdata("informacion", $informacion);
            $this->ajax_redirect();
        } else {
            $this->visualizar('sin_permisos_view');
        }
    }

    /**
     * Función que realiza un re-direccionamiento mediante encabezados HTTP
     * @param string $url Dirección URL a la cual se desea direccionar
     * @param bool $replace El parámetro opcional replace indica cuando el encabezado debe reemplazar un encabezado previo similar o añadir un segundo encabezado del mismo tipo. Por defecto lo reemplazará, pero si se pasa FALSE como segundo argumento se puede forzar múltiples encabezados del mismo tipo.
     * @param int $statusCode Fuerza el código de respuesta HTTP a un valor específico. Observe que este parámetro solamente tiene efecto si string no está vacío.
     */
    function ajax_redirect($url = NULL, $permanent = TRUE, $statusCode = 303) {
        if (empty($url)) {
            $url = base_url() . "Dashboard";
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === "XMLHttpRequest") {
            echo json_encode(array("success" => false, "redirect" => $url));
        } else {
            if (!headers_sent()) {
                header('location: ' . $url, $permanent, $statusCode);
            } else {
                echo '<script>location.href="' . $url . '"</script>';
            }
        }
        exit(0);
    }

    /**
     * Esta función verifica si el usuario ha iniciado sesión, y en caso de no haberlo hecho,
     * se direcciona a la página correspondiente para iniciar su sesión
     */
    function isLoggin() {
        if (!$this->session->logueado) {
            redirect(APP_SAC_URL . "Login");
        }
    }

    /**
     * Esta función inicializa variables del controlador que sirven para
     * generar las URL del controlador
     */
    function _initialize() {
        $this->isLoggin();
        if (!$this->{$this->module['controller'] . "_model"}->tengo_acceso_aqui()) {
            $this->sin_permisos();
        }
        $this->module['url'] = base_url() . $this->module['controller'];
        $this->module['listado_url'] = $this->module['url'] . '/';
        $this->module['edit_url'] = $this->module['url'] . '/modificar';
        $this->module['delete_url'] = $this->module['url'] . '/eliminar';
        $this->module['new_url'] = $this->module['url'] . '/nuevo';
        $this->module['read_url'] = $this->module['url'] . '/leer';
        $this->module['destroy_url'] = $this->module['url'] . '/destruir';
        $this->module['cancel_url'] = $this->module['url'];
        $this->module['catalogos_url'] = base_url() . "Catalogos";

        // Verificamos si tiene acceso al sistema actual, de lo contrario, lo redireccionamos
        // al primer sistema y controlador de su listado de permisos
//        $permisos = $this->session->userdata('permisos');
//        if (!empty($permisos)) {
//            $keys = array_keys($permisos);
//            $app = $keys[0];
//            $keys = array_keys($permisos[$app]);
//            $controller = $keys[0];
//            if ($app !== APP_NAMESPACE) {
//                redirect(APP_HOSTNAME . $app . "/" . $controller);
//            }
//        }

//        $this->Breadcrumbs = array(
//            APP_ABREVIACION => base_url(),
//            $this->module['controller'] => $this->module['url']
//        );
//        if (!empty($this->Breadcrumbs)) {
//            $this->module['breadcrumb'] = '<ol class="breadcrumb">';
//            $aux = array();
//            foreach ($this->Breadcrumbs as $key => $url) {
//                $this->module['breadcrumb'] .= '<li class="breadcrumb-item"><a href="' . $url . '">' . $key . '</a></li>';
//                array_push($aux, '<a href="' . $url . '">' . $key . '</a>');
//            }
////            $this->module['title'] = implode(" / ", $aux);
//        }
    }

    function index() {
        $this->{$this->module['controller'] . "_model"}->tengo_acceso_aqui();
        if ($this->{$this->module['controller'] . "_model"}->puedo_visualizar()) {
            $this->listado();
        } else {
            $this->sin_permisos();
        }
    }

    /**
     * Esta función permite cargar la vista indicada con la información proporcionada.
     * En caso de que la vista NO EXISTA esta función mostrará la vista asociada al error 404
     * @param string $view Nombre de la vista que se le presentará al usuario
     * @param array $data Una matriz asociativa de variables
     */
    function visualizar($view = NULL, $data = array()) {
        if ($view == NULL) {
            $view = $this->module['name'] . "_view";
        }
        /*
         * Verificamos si existe el módulo en la carpeta de usuarios, en caso de que no EXISTA, verificamos
         * si existe en la seccion de administrador, de lo contrario, marcamos error.
         */
        $rutaDefault = implode(DIRECTORY_SEPARATOR, array($view . ".php"));
        $ruta = implode(DIRECTORY_SEPARATOR, array(realpath("."), "application", "views", ""));
        if (file_exists($ruta . $rutaDefault)) {
            $this->template = $this->parser->parse($view, $data, TRUE);
        } else {
            $data = array(
                'heading' => "Error 404",
                'message' => 'Ooops!!!<br>No se pudo encontrar la página solicitada.<br><br>Posibles causas:<br><ul><li>Es posible que la URL solicitada NO exista</li><li>Su perfil de usuario no posee los permisos necesarios.</li></ul>',
                'ruta' => $ruta . $rutaDefault,
                'session' => $this->session->userdata()
            );
            $this->template = $this->parser->parse("../../../SAC/application/views/errors/html/error_404", $data, TRUE);
        }
        $this->load->view("../../../SAC/application/views/template_view");
    }

    /**
     * Esta función inicializa la configuración para generar la paginación de una matriz de datos
     * @param type $data Matriz asociativa de variables
     */
    function listado($data = array()) {
        if (!$this->{$this->module['controller'] . "_model"}->puedo_visualizar()) {
            $s['informacion'] = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para visualizar esta sección'
            );
            $this->session->set_flashdata($s);
            redirect($route['default_controller']);
        }
        $config = array();
        $config["base_url"] = $this->module['url'] . "/index/";
        $config["total_rows"] = $this->{$this->module['controller'] . "_model"}->record_count();
        $config["per_page"] = ($config['total_rows'] == 0 ? 1 : $config['total_rows']);
        $config["uri_segment"] = 3;
        $choice = $config["total_rows"] / $config["per_page"];
        $config["num_links"] = floor($choice);

        //config for bootstrap pagination class integration
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        $page = ($this->uri->segment($config['uri_segment'], 0));
        $data['page'] = $page;
        $data['per_page'] = $config['per_page'];
        if (!isset($data['registros'])) {
            $data["registros"] = $this->{$this->module['controller'] . "_model"}->get_todos($config["per_page"], $page);
        }
        $data["pagination"] = $this->pagination->create_links();
        $this->visualizar(NULL, $data);
    }

    function validaForm() {
        $this->form_validation->set_rules($this->rulesForm);
        foreach ($this->rulesForm as $rule) {
            $r[$rule['field']] = $this->input->post($rule['field']);
        }

        if ($this->input->server('REQUEST_METHOD') === "POST") {
            $result = array('success' => false);
            if ($this->form_validation->run() === FALSE) {
                foreach ($this->rulesForm as $rule) {
                    $result['errores'][$rule['field']] = form_error($rule['field']);
                }
            } else {
                $s = $this->_insert($r);
                if ($s['state'] === "success") {
                    $result['success'] = TRUE;
                    $result['data'] = $r;
                    $result['data'][$this->module['id_field']] = $s['data']['insert_id'];
                }
            }
        }
        echo json_encode($result);
    }

    /**
     * Función que carga la vista correspondiente a generar un nuevo elemento.
     * Cuando se accede a esta función mediante POST valida la información
     * proporcionada segun las reglas establecidas y en caso cumplir la validación
     * se insertan los datos en la base de datos
     * @param type $data Matriz de variables a validar para su posterior inserción
     */
    function nuevo($data = array()) {
        if (!$this->{$this->module['controller'] . "_model"}->puedo_insertar()) {
            $s['informacion'] = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para insertar información'
            );
            $this->session->set_flashdata($s);
            redirect($this->module['controller']);
        }
        $data['id'] = 0;
        if ($this->input->server('REQUEST_METHOD') === "POST") {
            $this->form_validation->set_rules($this->rulesForm);
            foreach ($this->rulesForm as $rule) {
                $r[$rule['field']] = $this->input->post($rule['field']);
            }
            if ($this->form_validation->run() === FALSE) {
                if (empty($data['r'])) {
                    $data['r'] = array();
                }
                $data['r'] = array_merge($data['r'], $r);
            } else {
                $s = $this->_insert($r);
                $ss = $this->_post_insert($s, $r);
                if ($s['state'] === 'success' && $ss === TRUE) {
                    $this->session->set_flashdata("informacion", $s);
                    redirect(base_url() . $this->module['name'] . "/");
                } else {
                    $data['r'] = $r;
                    $s['state'] = $s['state'] === "duplicate" ? "danger" : $s['state'];
                    $this->session->set_flashdata("informacion", $s);
                }
            }
        }
        if (!isset($data['tituloModulo']) || empty($data['tituloModulo'])) {
            $data['tituloModulo'] = $this->module['title_new'];
        }
        if (!isset($data['etiquetaBoton']) || empty($data['etiquetaBoton'])) {
            $data['etiquetaBoton'] = "Agregar";
        }
        if (!isset($data['urlAction']) || empty($data['urlAction'])) {
            $data['urlAction'] = $this->module['new_url'];
        }
        $data['accion'] = "nuevo";
        $data['token'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->visualizar($this->module['name'] . "_nuevo_view", $data);
    }

    /**
     * Función que permite realizar acciones después de haber intentado realizar
     * un INSERT en la base de datos
     * @param array $data Matriz de variables a insertar en la base de datos
     * @return array Matriz con valores sobre el resultado de la inserción
     */
    function _insert($data) {
        return $this->{$this->module['controller'] . '_model'}->insert($data);
    }

    function _post_insert(&$status, $data) {
        return TRUE;
    }

    function modificar($id = null, $data = array()) {
        if (!$this->{$this->module['controller'] . "_model"}->puedo_modificar()) {
            $s['informacion'] = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para modificar información'
            );
            $this->session->set_flashdata($s);
            redirect($this->module['controller']);
        }
        if ($this->input->server('REQUEST_METHOD') === "POST") {
            $id = $this->input->post($this->module['id_field']);
            $this->form_validation->set_rules($this->rulesForm);
            $id = $this->input->post($this->module['id_field']);
            foreach ($this->rulesForm as $rule) {
                $r[$rule['field']] = $this->input->post($rule['field']);
            }
            if ($this->form_validation->run() === FALSE) {
                $data['r'] = $r;
            } else {
                $s = $this->_update(intval($id), $r);
                $ss = $this->_post_update($s, $id, $r);
                $this->session->set_flashdata("informacion", $s);
                if ($s['state'] === 'success' && $ss === TRUE) {
                    redirect(base_url() . $this->module['name'] . "/");
                } else {
                    $data['r'] = $r;
                    $s['state'] = $s['state'] === "duplicate" ? "danger" : $s['state'];
                    $this->session->set_flashdata("informacion", $s);
                }
            }
        }

        if (!isset($data['r'])) {
            $res = $this->db->select($this->module['prefix'] . ".*")->where($this->module['prefix'] . "." . $this->module['id_field'], $id)->get($this->module['tabla'] . " " . $this->module['prefix']);
            if ($res->num_rows() == 1) {
                $data['r'] = $res->row_array();
            } else {
                $this->session->set_flashdata("informacion", array('state' => 'warning', 'message' => 'El elemento que intentó modificar no existe'));
                redirect(base_url() . $this->module['controller']);
            }
        }
        if (!isset($data['tituloModulo']) || empty($data['tituloModulo'])) {
            $data['tituloModulo'] = $this->module['title_edit'];
        }
        if (!isset($data['etiquetaBoton']) || empty($data['etiquetaBoton'])) {
            $data['etiquetaBoton'] = "Actualizar";
        }
        if (!isset($data['urlAction']) || empty($data['urlAction'])) {
            $data['urlAction'] = $this->module['edit_url'] . "/" . $id;
        }
        $data['id'] = $id;
        $data['accion'] = "modificar";
        $data['token'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->visualizar($this->module['name'] . "_nuevo_view", $data);
    }

    function _update($id, $data) {
        return $this->{$this->module['controller'] . '_model'}->update($id, $data);
    }

    function _post_update(&$status, $id, $r) {
        return TRUE;
    }

    function eliminar($id = NULL, $data = NULL) {
        if (!$this->{$this->module['controller'] . "_model"}->puedo_eliminar()) {
            $s['informacion'] = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para eliminar información'
            );
            $this->session->set_flashdata($s);
            redirect($this->module['controller']);
        }
        if ($this->input->server('REQUEST_METHOD') === "POST") {
            $id = $this->input->post("id");
            $dataDelete = $this->_pre_delete($id);
            $s = $this->_delete($id);
            $ss = $this->_post_delete($s, $id, $data, $dataDelete);
            $this->session->set_flashdata("informacion", $s);
            if ($s['state'] === 'success') {
                redirect(base_url() . $this->module['controller'] . "/");
            }
        }
        $res = $this->db->where($this->module['prefix'] . "." . $this->module['id_field'], $id)->get($this->module['tabla'] . " " . $this->module['prefix']);
        if ($res->num_rows() == 1) {
            $data['r'] = $res->row_array();
        } else {
            $this->session->set_flashdata("informacion", array('state' => 'warning', 'message' => 'El elemento que intentó eliminar no existe'));
            redirect(base_url() . $this->module['controller']);
        }
        if (!isset($data['etiqueta']) || empty($data['etiqueta'])) {
            $data['etiqueta'] = '¿Esta seguro que desea eliminar este elemento?';
        }
        if (!isset($data['urlActionDelete']) || empty($data['urlActionDelete'])) {
            $data['urlActionDelete'] = $this->module['delete_url'];
        }
        if (!isset($data['urlActionCancel']) || empty($data['urlActionCancel'])) {
            $data['urlActionCancel'] = $this->module['listado_url'];
        }
        $data['id'] = $id;
        $data['token'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->visualizar("eliminar_view", $data);
    }

    function eliminarVarios() {
        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $ids = explode(",", $this->input->post("ids"));
            $s = $this->{$this->module['controller'] . "_model"}->deleteBatch($ids);
            $this->session->set_flashdata("informacion", $s);
            redirect(base_url() . $this->module['name'] . "/");
        }
    }

    function _delete($id) {
        return $this->{$this->module['controller'] . '_model'}->delete($id);
    }

    function _post_delete($status, $id, $data = NULL, $dataDelete = NULL) {
        return TRUE;
    }

    /**
     * Función que permite conectarse a una base de datos previamente configurada.
     * @param string $dbName Nombre de configuración a usar para conectarse a la base de datos
     * @return resource Devuelve el objeto con la conexión a la base de datos, FALSE en caso de error
     */
    function getDatabase($dbName = "") {
        if (!empty($dbName)) {
            return $this->{$this->module['controller'] . "_model"}->getDatabase($dbName);
        }
    }

    function get_database($nombre_basedatos) {
        $return = FALSE;
        if (!empty($nombre_basedatos)) {
            $return = $this->{$this->module['controller'] . "_model"}->get_database($nombre_basedatos);
        }
        return $return;
    }

    function _pre_delete($id) {
        return array();
    }

    function destruir($id = NULL, $data = NULL) {
        if (!$this->{$this->module['controller'] . "_model"}->puedo_destruir()) {
            $s['informacion'] = array(
                'state' => 'danger',
                'message' => 'No tiene permisos para destruir información'
            );
            $this->session->set_flashdata($s);
            redirect($this->module['controller']);
        }
        if ($this->input->server('REQUEST_METHOD') === "POST") {
            $id = $this->input->post("id");
            $dataDelete = $this->_pre_destroy($id);
            $s = $this->_destroy($id);
            $ss = $this->_post_destroy($s, $id, $data, $dataDelete);
            $this->session->set_flashdata("informacion", $s);
            if ($s['state'] === 'success') {
                redirect(base_url() . $this->module['controller'] . "/");
            }
        }
        $res = $this->db->where($this->module['prefix'] . "." . $this->module['id_field'], $id)->get($this->module['tabla'] . " " . $this->module['prefix']);
        if ($res->num_rows() == 1) {
            $data['r'] = $res->row_array();
        } else {
            $this->session->set_flashdata("informacion", array('state' => 'warning', 'message' => 'El elemento que intentó eliminar no existe'));
            redirect(base_url() . $this->module['controller']);
        }
        if (!isset($data['etiqueta']) || empty($data['etiqueta'])) {
            $data['etiqueta'] = '¿Esta seguro que desea destruir este elemento?';
        }
        if (!isset($data['urlActionDelete']) || empty($data['urlActionDelete'])) {
            $data['urlActionDelete'] = $this->module['destroy_url'];
        }
        if (!isset($data['urlActionCancel']) || empty($data['urlActionCancel'])) {
            $data['urlActionCancel'] = $this->module['listado_url'];
        }
        $data['id'] = $id;
        $data['token'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->visualizar("eliminar_view", $data);
    }

    function _destroy($id) {
        return $this->{$this->module['controller'] . '_model'}->destroy($id);
    }

    function _pre_destroy($id) {
        return array();
    }

    function _post_destroy(&$status, $id, $data = NULL, $dataDelete = NULL) {
        return TRUE;
    }

}
