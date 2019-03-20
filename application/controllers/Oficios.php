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

    function actualizar_campo_de_oficio() {
        $return = array('state' => 'success');
        $id = $this->input->post('pk');
        $nombre_campo = $this->input->post("name");
        $valor = $this->input->post("value");

        $return['nuevo_valor_por_mostrar'] = mysqlDate2OnlyDate($valor);
        $return['nuevo_valor_guardado'] = $valor;
        $return['nombre_campo'] = $nombre_campo;

        header("Content-type: application/json");
        echo json_encode($return);
    }

}
