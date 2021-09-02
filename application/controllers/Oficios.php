<?php

class Oficios extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de Oficios";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "d";
        $this->_initialize();
    }

    function _initialize() {
        return TRUE;
    }

    /**
     * Esta función NO ACTUALIZA NINGÚN VALOR EN LA BASE DE DATOS, solo sirve para devolverle al componente
     * los valores que debe mostrar
     */
    function actualizar_campo_de_oficio() {
        $return = array('state' => 'success');
        $documentos_id = $this->input->post('pk');
        $constante = $this->input->post("name");
        $valor = $this->input->post("value");

        $return['nuevo_valor_por_mostrar'] = mysqlDate2OnlyDate($valor);
        $return['nuevo_valor_guardado'] = $valor;
        $return['nombre_campo'] = $constante;

        header("Content-type: application/json");
        echo json_encode($return);
    }

    /**
     * Esta función permite asignar un valor directo a la base de datos
     */
    function asignar_valor() {
        sleep(1);
        $return = array(
            'success' => FALSE,
            'error' => NULL,
            'valor' => NULL
        );
        $basedatos = $this->input->post('basedatos');
        $tabla = $this->input->post('tabla');
        $campo = $this->input->post('campo');
        $where = $this->input->post('where');
        $where_valor = $this->input->post('where_valor');
        $valor = $this->input->post('valor');

        $db = $this->getDatabase($basedatos);
        $db->where($where, $where_valor)
                ->set($campo, $valor)
                ->from($tabla);
        //echo $db->get_compiled_update();
        //$result = $db->update();
        if (TRUE or $db->affected_rows() > 0) {
            // Se actualizó el registro
            $return['success'] = TRUE;
            $return['valor'] = strval($valor);
        } else {
            // no se actualizó ningún registro
            $return['error'] = $db->error();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($return, JSON_PRETTY_PRINT);
    }

}
