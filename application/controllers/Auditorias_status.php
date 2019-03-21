<?php

class Auditorias_status extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de status de auditorías";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "as";
        $this->_initialize();

        $this->rulesForm = array(
            array('field' => 'auditorias_status_nombre', 'label' => 'Nombre', 'rules' => 'required|trim'),
        );
        $accion = $this->input->post('accion');
        if ($accion === "modificar") {
            $t = array(
                array('field' => $this->module['id_field'], 'label' => 'ID', 'rules' => 'required|trim|is_natural_no_zero')
            );
            foreach ($t as $tt) {
                array_push($this->rulesForm, $tt);
            }
        }
    }

}
