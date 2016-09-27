<?php

class Timeline extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'timeline';
        $this->module['controller'] = 'Timeline';
        $this->module['title'] = 'Línea de tiempo';
        $this->module['title_list'] = "Línea de tiempo de";
        $this->module['title_new'] = "Nuevo hito";
        $this->module['title_edit'] = "Editar hito";
        $this->module['title_delete'] = "Eliminar hito";
        $this->module["id_field"] = "idAuditoria";
        $this->module['tabla'] = "cat_" . $this->module['name'];
        $this->module['prefix'] = "time";

        $this->rulesForm = array(
            array('field' => 'clientes_nombre', 'label' => 'nombre del cliente', 'rules' => 'required|trim',),
            array('field' => 'clientes_rfc', 'label' => 'RFC', 'rules' => 'required|trim|min_length[10]|max_length[14]|callback_validaRFC', 'errors' => array('is_unique' => 'Este %s ya ha sido capturado.')),
            array('field' => 'clientes_email', 'label' => 'Correo electrónico', 'rules' => 'required|trim|max_length[200]')
        );
        $this->_initialize();
    }

    function index($idAuditoria = NULL) {
        if (!is_null($idAuditoria)) {
            $this->session->set_userdata('auditoria', array("id_auditoria" => $idAuditoria));
        }
        parent::index();
    }

    function visualizar($view = NULL, $data = array()) {
        $idAuditoria = 3;
        $proceso = $this->{$this->module['controller'] . "_model"}->get_proceso($idAuditoria);
        $idAuditoria = isset($this->session->auditoria['id_auditoria']) ? $this->session->auditoria['id_auditoria'] : 694;
        $tipoAuditoria = $this->db->select("tipo")->where("idAuditoria", $idAuditoria)->get("cat_auditoria")->row()->tipo;
        switch ($tipoAuditoria) {
            case 'SA':
                $this->db
                        ->select('fechaIniRev1 fechaIniAudit')
                        ->select('fechaFinRev1 fechaFinAudit')
                        ->select('fechaIniRealRev1 fechaIniReal')
                        //->select('DATE(FROM_UNIXTIME(fechaFinReal)) fechaFinReal')
                        ->select("fechaAprovacionRev1 fechaFinReal")
                        ->select('DATEDIFF(fechaIniRealRev1, fechaIniRev1) reprogramacion_inicio_dias_naturales')
                        ->select('DATEDIFF(fechaFinRealRev1, fechaFinRev1) reprogramacion_fin_dias_naturales');
                break;
            default:
                $this->db
                        ->select('DATE(FROM_UNIXTIME(fechaIniAudit)) fechaIniAudit')
                        ->select('DATE(FROM_UNIXTIME(fechaFinAudit)) fechaFinAudit')
                        ->select('DATE(FROM_UNIXTIME(fechaIniReal)) fechaIniReal')
                        //->select('DATE(FROM_UNIXTIME(fechaFinReal)) fechaFinReal')
                        ->select("fechaAprovacion fechaFinReal")
                        ->select('DATEDIFF(DATE(FROM_UNIXTIME(fechaIniReal)), DATE(FROM_UNIXTIME(fechaIniAudit))) reprogramacion_inicio_dias_naturales')
                        ->select('DATEDIFF(DATE(FROM_UNIXTIME(fechaFinReal)), DATE(FROM_UNIXTIME(fechaFinAudit))) reprogramacion_fin_dias_naturales');
                break;
        }
        $auditoria = $this->db
                ->select('idAuditoria')
                ->select("CONCAT(area,'/',tipo,'/',numero,'/',anio) AS nombreAuditoria")
                ->select("tipo, idEmpleado")
                ->where('idAuditoria', $idAuditoria)
                ->get("cat_auditoria")
                ->row_array();
        $etapas = $this->{$this->module['controller'] . "_model"}->get_etapas($proceso['procesos_id']);
        $tareas = $this->{$this->module['controller'] . "_model"}->get_tareas($idAuditoria, array_column($etapas, 'etapas_id'), $auditoria);
        $entregables = $this->{$this->module['controller'] . "_model"}->get_entregables(array_column($tareas, 'tareas_id'));
        $auditoria['reprogramacion_inicio_dias_habiles'] = getDiasHabiles($auditoria['fechaIniAudit'], $auditoria['fechaIniReal']);
        $auditoria['reprogramacion_fin_dias_habiles'] = getDiasHabiles($auditoria['fechaFinAudit'], $auditoria['fechaFinReal']);
        $lider = $this->Empleados_model->get_empleado($auditoria['idEmpleado']);
        $auditoria['lider'] = $lider['nombre'] . " " . $lider['aPaterno'] . " " . $lider['aMaterno'];
        $equipoArray = $this->Auditorias_model->get_equipo_auditoria($idAuditoria);
        $equipo = array();
        foreach ($equipoArray as $e) {
            $nombre = $e['nombre'] . " " . $e['aPaterno'] . " " . $e['aMaterno'];
            array_push($equipo, $nombre);
        }
        if (count($equipo) > 0) {
            $auditoria['equipo'] = $equipo;
        }

        $data = array(
            'procesos_id' => $proceso['procesos_id'],
            'etapas' => $etapas,
            'tareas' => $tareas,
            'entregables' => $entregables,
            'auditoria' => $auditoria
        );
//        echo "<pre>";
//        print_r($tareas);
//        die();
        parent::visualizar($view, $data);
    }

}
