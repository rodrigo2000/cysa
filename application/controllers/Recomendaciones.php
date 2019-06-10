<?php

class Recomendaciones extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de recomendaciones";
        $this->module['title_list'] = $this->module['title'];
        $this->module['title_new'] = "Nuevo";
        $this->module['title_edit'] = "Editar";
        $this->module['title_delete'] = "Eliminar";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "rec";
        $this->_initialize();
    }

    function guardar() {
        $return = array(
            'success' => FALSE,
            'message' => "Erroe desconocido"
        );
        $post = $this->input->post();
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa['auditorias_id'];
        foreach ($post['recomendaciones_id'] as $index => $p) {
            $data = array(
                'recomendaciones_observaciones_id' => $post['recomendaciones_observaciones_id'][$index],
                'recomendaciones_clasificaciones_id' => $post['recomendaciones_clasificaciones_id'][$index],
                'recomendaciones_status_id' => $post['recomendaciones_status_id'][$index],
                'recomendaciones_empleados_id' => $post['recomendaciones_empleados_id'][$index],
                'recomendaciones_descripcion' => $post['recomendaciones_descripcion'][$index],
            );
            if (empty($post['recomendaciones_id'][$index])) {
                $numero = $this->Recomendaciones_model->get_siguiente_numero_de_recomendacion($post['recomendaciones_observaciones_id'][$index]);
                $data['recomendaciones_numero'] = $numero;
                $return = $this->Recomendaciones_model->insert($data);
                $return['data']['accion'] = "nuevo";
                $return['data']['recomendaciones_numero'] = $numero;
                $return['message'] = "Se ha agregado la recomendación.";
            } else {
                $return = $this->Recomendaciones_model->update($post['recomendaciones_id'][$index], $data);
                $return['data']['accion'] = "actualizar";
                $return['message'] = "Se ha actualizado la recomendación.";
                unset($data['observaciones_descripcion']);
                $return['data'] = array_merge($return['data'], $data);
            }
        }
        if ($return['state'] === 'success') {
            $return['success'] = TRUE;
        }
        echo json_encode($return);
    }

    function eliminar_recomendacion() {
        $return = array(
            'state' => 'error',
            'success' => FALSE,
            'message' => 'Error desconocido'
        );
        $recomendaciones_id = $this->input->post('recomendaciones_id');
        if (!empty($recomendaciones_id)) {
            if (is_numeric($recomendaciones_id)) {
                $return = $this->Recomendaciones_model->delete($recomendaciones_id);
                if ($return['state'] === "success") {
                    $return['success'] = TRUE;
                    $return['message'] = "Se ha eliminado la recomendación.";
                    $return['data'] = array(
                        'recomendaciones_id' => $recomendaciones_id,
                        'selector' => 'recomedanciones_id' . $recomendaciones_id
                    );
                }
            } else {
                // Es una observación nueva, por lo tanto es solo eliminar la pestaña de la observación
                $return['success'] = TRUE;
                $return['data'] = array(
                    'recomendaciones_id' => $recomendaciones_id,
                    'selector' => 'recomedanciones_id' . $recomendaciones_id
                );
            }
        } else {
            $return['message'] = "Faltó especificar el identificador de la recomendación.";
        }
        echo json_encode($return);
        return $return;
    }

}
