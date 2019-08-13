<?php

class Importar extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Listado de importaciones";
        $this->module['title_list'] = $this->module['title'];
        $this->_initialize();
    }

    function index() {
        $this->visualizar("importar_view");
    }

    function iniciar_importacion() {
        $r = NULL;
        ini_set('max_execution_time', 320); //600 seconds = 10 minutes
        if (ob_get_level() == 0) {
            ob_start();
        }
        $catalogo = $this->input->post('catalogo');
        $id = $message = "";
        foreach ($catalogo as $c) {
            switch ($c) {
                case 'documentos_tipos':
                    $message = $this->Importar_model->importar_documentos_tipos();
                    $id = $c;
                    break;
                case 'documentos_constantes':
                    $message = $this->Importar_model->importar_documentos_constantes();
                    $id = $c;
                    break;
                case 'versiones':
                    $message = $this->Importar_model->importar_versiones();
                    $id = $c;
                    break;
                case 'auditorias':
                    $message = $this->Importar_model->importar_auditorias();
                    $id = $c;
                    break;
                case 'observaciones':
                    $message = $this->Importar_model->importar_observaciones();
                    $id = $c;
                    break;
                case 'recomendaciones':
                    $message = $this->Importar_model->importar_recomendaciones();
                    $id = $c;
                    break;
                case 'avances':
                    $message = $this->Importar_model->importar_avances_recomendaciones();
                    $id = $c;
                    break;
                case 'equipos_trabajo':
                    $message = $this->Importar_model->importar_equipos_trabajo();
                    $id = $c;
                    break;
                case 'involucrados':
                    $message = $this->Importar_model->importar_involucrados();
                    $id = $c;
                    break;
                case 'asistencias':
                    $message = $this->Importar_model->importar_asistencias();
                    $id = $c;
                    break;
                case 'documentos':
                    $message = $this->Importar_model->importar_documentos();
                    $id = $c;
                    break;
            }
        }
        $json = array(
            'success' => TRUE,
            'id' => $id,
            'message' => $message
        );
        echo json_encode($json);
//        $this->Importar_model->importar_documentos_tipos();
//        $this->Importar_model->importar_documentos_constantes();
//        $this->Importar_model->importar_versiones();
//        $r = $this->Importar_model->importar_auditorias();
//        $this->Importar_model->importar_equipos_trabajo();
        ob_end_flush();
    }

}
