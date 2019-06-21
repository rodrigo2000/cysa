<?php

class Recomendaciones_avances extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de avances de recomendaciones";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "rec_ava";
        $this->_initialize();
    }

    function guardar() {
        $return = array(
            'success' => FALSE,
            'message' => "Error desconocido"
        );
        $post = $this->input->post();
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa['auditorias_id'];
        foreach ($post['recomendaciones_id'] as $index => $p) {
            $data = array(
                'recomendaciones_avances_numero_revision' => $post['recomendaciones_avances_numero_revision'][$index],
                'recomendaciones_avances_recomendaciones_id' => $post['recomendaciones_id'][$index],
                'recomendaciones_avances_recomendaciones_clasificaciones_id' => $post['recomendaciones_clasificaciones_id'][$index],
                'recomendaciones_avances_recomendaciones_status_id' => $post['recomendaciones_status_id'][$index],
                'recomendaciones_avances_empleados_id' => $post['recomendaciones_empleados_id'][$index],
                    //'recomendaciones_avances_descripcion' => $post['recomendaciones_avaces_descripcion'][$index],
            );
            if (empty($post['recomendaciones_id'][$index])) {
//                $return = $this->Recomendaciones_model->insert($data);
//                $return['data']['accion'] = "nuevo";
//                $return['message'] = "Se ha agregado la recomendación.";
            } else {
                $this->db
                        ->where('recomendaciones_avances_numero_revision', $post['recomendaciones_avances_numero_revision'][$index])
                        ->where('recomendaciones_avances_recomendaciones_id', $post['recomendaciones_id'][$index]);
                $return['success'] = $this->db->update($this->module['tabla'], $data);
                $return['data']['accion'] = "actualizar";
                $return['message'] = "Se ha actualizado el avance de la recomendación.";
                unset($data['recomendaciones_avances_descripcion']);
                $return['data'] = array_merge($return['data'], $data);
            }
            if ($return['success']) {
                $return['state'] = 'success';
            }
        }
        if ($return['state'] === 'success') {
            $return['success'] = TRUE;
        }
        echo json_encode($return);
    }

}
