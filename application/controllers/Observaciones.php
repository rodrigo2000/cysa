<?php

class Observaciones extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de observaciones";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "o";
        $this->_initialize();
    }

    function guardar($observaciones_id = NULL) {
        $return = array(
            'success' => FALSE,
            'message' => "Erroe desconocido"
        );
        $post = $this->input->post();
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa['auditorias_id'];
        foreach ($post['observaciones_titulo'] as $index => $p) {
            $data = array(
                'observaciones_auditorias_id' => $auditorias_id,
                'observaciones_numero' => $post['observaciones_numero'][$index],
                'observaciones_titulo' => $post['observaciones_titulo'][$index],
                'observaciones_descripcion' => $post['observaciones_descripcion'][$index],
                'observaciones_has_anexos' => 0,
                'observaciones_is_eliminada' => 0
            );
            if (empty($post['observaciones_id'][$index])) {
                $return = $this->Observaciones_model->insert($data);
                $return['data']['accion'] = "nuevo";
                $return['data']['message'] = "Se ha agregado la observación.";
                $return['data']['observaciones_numero'] = $this->Observaciones_model->get_siguiente_numero_de_observacion($auditorias_id);
            } else {
                $return = $this->Observaciones_model->update($post['observaciones_id'][$index], $data);
                $return['data']['accion'] = "actualizar";
                $return['data']['message'] = "Se ha actualizaco la observación.";
                $return['data']['observaciones_id'] = intval($post['observaciones_id'][$index]);
                unset($data['observaciones_descripcion']);
                $return['data'] = array_merge($return['data'], $data);
            }
            $return['data']['observaciones_numero'] = $post['observaciones_numero'][$index];
        }
        if ($return['state'] === 'success') {
            $return['success'] = TRUE;
        }
        echo json_encode($return);
    }

}
