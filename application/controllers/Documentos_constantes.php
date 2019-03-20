<?php

class Documentos_constantes extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Variables de documentos";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nueva constante";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "dc";
        $this->_initialize();

        $this->rulesForm = array(
            array('field' => 'documentos_constantes_nombre', 'label' => 'Nombre', 'rules' => 'required|trim'),
            array('field' => 'documentos_constantes_descripcion', 'label' => 'Descripción', 'rules' => 'required|trim'),
            array('field' => 'documentos_constantes_documentos_tipos_id', 'label' => 'Tipo de documento', 'rules' => 'required|trim|is_natural_no_zero', 'errors' => array('is_natural_no_zero' => 'Debe especificar el %s')),
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

    public function nuevo($data = array()) {
        $data = array(
            'documentos_tipos' => $this->Documentos_tipos_model->get_todos()
        );
        parent::nuevo($data);
    }

    public function modificar($id = null, $data = array()) {
        $data = array(
            'documentos_tipos' => $this->Documentos_tipos_model->get_todos()
        );
        parent::modificar($id, $data);
    }

}
