<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = strtolower(__CLASS__);
        $this->module['controller'] = __CLASS__;
        $this->module['title'] = "Dashboard";
        $this->is_catalogo = FALSE;
        $this->_initialize();
    }

    function index() {
        //var_dump($this->session->userdata());
        $data = array(
//            'auditores' => $this->Catalogos_model->get_auditores_lider(),
//            'auditorias' => $this->Catalogos_model->get_auditorias_de_empleado($this->session->userdata('empleados_id'))
        );
        $this->visualizar('dashboard_view', $data);
    }

    function error404() {
        $data = array(
            'heading' => "Error 404",
            'message' => 'Ooops!!!<br>No se pudo encontrar la página solicitada.<br><br>Posibles causas:<br><ul><li>Es posible que la URL solicitada NO exista</li><li>Su perfil de usuario no posee los permisos necesarios.</li></ul>',
            'session' => $this->session->userdata()
        );
        $this->load->view("errors/html/error_404.php", $data);
    }

    function politicas_privacidad() {
        $this->module['title'] = "Políticas de privacidad";
        $this->visualizar("politicas_privacidad_view");
    }

    function terminos_y_condiciones() {
        $this->load->view("terminos_y_condiciones_view.php");
    }

    function my_feed() {
        $start = $this->input->post("start");
        $end = $this->input->post("end");

        $id_event = $this->input->post("id");
        $event = $this->input->post("evento");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $event_type = intval($this->input->post("event_type"));
        $idAuditoria = intval($this->input->post("idAuditoria"));
        $emple = $this->input->post("emple");

        $idEmpleado = 5555; // $this->session->id_empleado;

        $res = $this->db
                ->select("DISTINCT(A.event_id), event_type, start_date, end_date, evento, A.idAuditoria")
                ->join("events_detalle B", "B.event_id = A.event_id", "LEFT")
                ->join("cat_auditoria_equipo C", "C.idAuditoria = A.idAuditoria", "LEFT")
                ->where("A.lider", $idEmpleado)
                ->or_where("B.idEmpleado", $idEmpleado)
                ->or_where("C.idEmpleado", $idEmpleado)
                ->order_by("end_date", "DESC")
                ->get("events_section A");
        //echo $this->db->last_query();
        $return = array();
        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $r) {
                $data = array(
                    'id' => $r['event_id'],
                    'title' => $r['evento'],
//                    'allDay' => FALSE,
                    'start' => date("Y-m-d"), //$r['start_date'],
//                    'end' => $r['end_date'],
//                    'url'=>NULL,
//                    'className'=>NULL,
//                    'editable'=>FALSE,
//                    'startEditable'=>FALSE,
//                    'durationEditable',
//                    'rendering',
//                    'overlap',
//                    'contraint',
//                    'source',
//                    'color',
//                    'backgroundColor',
//                    'borderColor',
//                    'textColor'
                );
                array_push($return, $data);
            }
        }
        echo json_encode($return);
    }

}
