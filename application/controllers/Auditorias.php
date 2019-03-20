<?php

class Auditorias extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->module['name'] = 'auditorias';
        $this->module['controller'] = 'Auditorias';
        $this->module['title'] = 'Auditorías';
        $this->module['title_list'] = "Mis auditorias";
        $this->module['title_new'] = "Nueva auditoría";
        $this->module['title_edit'] = "Editar auditoría";
        $this->module['title_delete'] = "Eliminar auditoría";
        $this->module["id_field"] = strtolower(__CLASS__) . "_id";
        $this->module['tabla'] = strtolower(__CLASS__);
        $this->module['prefix'] = "a";
        $this->_initialize();

        $this->rulesForm = array(
            array('field' => 'auditorias_area', 'label' => 'Área', 'rules' => 'required|trim|is_natural_no_zero', 'errors' => array('is_natural_no_zero' => 'Debe seleccionar un %s')),
            array('field' => 'auditorias_tipo', 'label' => 'Tipo', 'rules' => 'required|trim|is_natural_no_zero', 'errors' => array('is_natural_no_zero' => 'Debe seleccionar un %s')),
            array('field' => 'auditorias_numero', 'label' => 'Número', 'rules' => 'required|trim|numeric|is_natural_no_zero'),
            array('field' => 'auditorias_anio', 'label' => 'Año', 'rules' => 'required|trim'),
            array('field' => 'auditorias_segundo_periodo', 'label' => 'Tipo', 'rules' => 'required|trim'),
            array('field' => 'auditorias_is_programada', 'label' => 'Tipo', 'rules' => 'required|trim'),
            array('field' => 'auditorias_fechas_inicio_programado', 'label' => 'Fecha de inicio programado', 'rules' => 'required|trim'),
            array('field' => 'auditorias_fechas_inicio_real', 'label' => 'Fecha de inicio real', 'rules' => 'required|trim'),
            array('field' => 'auditorias_fechas_fin_programado', 'label' => 'Fecha de fin programado', 'rules' => 'required|trim'),
            array('field' => 'auditorias_fechas_fin_real', 'label' => 'Fecha de fin real', 'rules' => 'required|trim'),
            array('field' => 'auditorias_rubro', 'label' => 'Rubro', 'rules' => 'required|trim'),
            array('field' => 'auditorias_objetivo', 'label' => 'Objetivo', 'rules' => 'required|trim'),
            array('field' => 'auditorias_auditor_lider', 'label' => 'Auditor_lider', 'rules' => 'required|trim|is_natural_no_zero', 'errors' => array('is_natural_no_zero' => 'Debe seleccionar una %s')),
            array('field' => 'auditorias_direcciones_id', 'label' => 'Dirección', 'rules' => 'required|trim|is_natural_no_zero', 'errors' => array('is_natural_no_zero' => 'Debe seleccionar una %s')),
            array('field' => 'auditorias_subdirecciones_id', 'label' => 'Subdirección', 'rules' => 'required|trim|is_natural_no_zero', 'errors' => array('is_natural_no_zero' => 'Debe seleccionar una %s')),
            array('field' => 'auditorias_departamentos_id', 'label' => 'Departamento', 'rules' => 'required|trim|is_natural_no_zero', 'errors' => array('is_natural_no_zero' => 'Debe seleccionar un %s')),
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

    function index() {
        $data = array(
            'areas' => $this->Auditorias_areas_model->get_todos(NULL, NULL, TRUE),
            'tipos' => $this->Auditorias_tipos_model->get_todos(NULL, NULL, TRUE),
            'anios' => range(date("Y"), 2007, -1),
            'status' => $this->Auditorias_status_model->get_todos(NULL, NULL, TRUE),
            'direcciones' => $this->SAC_model->get_direcciones(),
        );
        $this->listado($data);
    }

    function nuevo($data = array(), $modal = FALSE) {
        $this->module['function'] = ucfirst(__FUNCTION__);
        $data = array(
            'areas' => $this->Auditorias_areas_model->get_todos(),
            'tipos' => $this->Auditorias_tipos_model->get_todos(),
            'anios' => array(date("Y") - 1, date("Y"), date("Y") + 1),
            'direcciones' => $this->SAC_model->get_direcciones(),
            'subdirecciones' => $this->SAC_model->get_subdirecciones(),
            'departamentos' => $this->SAC_model->get_departamentos(),
            'auditores' => $this->SAC_model->get_auditores_agrupados_por_cc()
        );
        parent::nuevo($data);
    }

    function modificar($id = NULL, $data = array()) {
        $data = array(
            'areas' => $this->Auditorias_areas_model->get_todos(),
            'tipos' => $this->Auditorias_tipos_model->get_todos(),
            'anios' => array(date("Y") - 1, date("Y"), date("Y") + 1),
            'direcciones' => $this->SAC_model->get_direcciones(),
            'subdirecciones' => $this->SAC_model->get_subdirecciones(),
            'departamentos' => $this->SAC_model->get_departamentos(),
            'auditores' => $this->SAC_model->get_auditores(),
        );
        parent::modificar($id, $data);
    }

    function eliminar($id = NULL, $data = array()) {
        $data = array(
            "etiqueta" => "¿Esta seguro que desea eliminar este cliente?",
            "urlActionDelete" => $this->module['delete_url'],
            "urlActionCancel" => $this->module['listado_url'],
            "id" => $id
        );
        parent::eliminar($id, $data);
    }

    function reporte_incidentes() {
        $data = array(
            "tituloModulo" => "Reporte de Incidentes",
            "etiquetaBoton" => "Agregar",
            "urlAction" => $this->module['new_url']
        );
        $this->visualizar($this->module['controller'] . "_reportes_view", $data);
    }

    function ajax_get_subdirecciones() {
        $return = array('success' => FALSE);
        $idDireccion = $this->input->post("idDireccion");
        $subdirecciones = $this->SAC_model->getSubdirecciones($idDireccion);
        if (count($subdirecciones) > 0) {
            $return['success'] = TRUE;
            $return['data'] = $subdirecciones;
        } else {
            $return['message'] = "No se encontraron subdirecciones para esta dirección";
        }
        header("Content-type: application/json");
        echo json_encode($return);
    }

    function ajax_get_departamentos() {
        $return = array('success' => FALSE);
        $idDireccion = $this->input->post("idDireccion");
        $idSubdireccion = $this->input->post("idSubdireccion");
        $departamentos = $this->SAC_model->getDepartamentos($idDireccion, $idSubdireccion);
        if (count($departamentos) > 0) {
            $return['success'] = TRUE;
            $return['data'] = $departamentos;
        } else {
            $return['message'] = "No se encontraron departamentos para esta subdirección";
        }
        echo json_encode($return);
    }

    function get_proximo_numero_auditoria($is_segundo_periodo = FALSE, $anio = NULL) {
        $consecutivo = $this->Auditorias_model->get_proximo_numero_auditoria($is_segundo_periodo);
        $json = array('consecutivo' => $consecutivo);
        echo json_encode($json);
    }

    function get_auditorias_ajax() {
        $draw = $this->input->post('draw');
        $start = $this->input->post("start");
        $length = $this->input->post("length");
        $search = $this->input->post('search');
        $columns = $this->input->post('columns');
        $order = $this->input->post('order');
        $filtro = $this->input->post('filtro');
        $recordsTotal = 0;
        $recordFiltered = 0;
        $data = array();
        $hoy = date("Y-m-d");

        $anio = $this->input->post("auditorias_anio");
        $tipo = $this->input->post("auditorias_tipo");
        $area = $this->input->post("auditorias_area");
        $status = $this->input->post("auditorias_status_id");
        $direcciones_id = $this->input->post("direcciones_id");
        if (empty($anio) || $anio == 0) {
            $anio = date("Y");
        }
        $this->db->where($this->module['prefix'] . ".auditorias_anio", intval($anio));

        if (!empty($tipo) && $tipo != "0") {
            $this->db->where($this->module['prefix'] . ".auditorias_tipo", $tipo);
        }

        if (!empty($area) && $area != "0") {
            $this->db->where($this->module['prefix'] . ".auditorias_area", $area);
        }

        if (!empty($status)) {
            switch (intval($status)) {
                case 1: // En proceso
                    $this->db->where("auditorias_status_id", 0);
                    break;
                case 2: // En proceso
                    $this->db->where("auditorias_status_id", 1)
                            ->where("auditorias_fechas_sello_orden_entrada IS NOT NULL");
                    break;
                case 3: // Finalizada
                    $this->db->where_not_in("auditorias_status_id", array(0, 1));
                    break;
                case 4: // Reprogramada
                    $this->db->where("auditorias_status_id", 1)
                            ->where("auditorias_fechas_inicio_programado !=", "auditorias_fechas_inicio_real", FALSE)
                            ->where("auditorias_fechas_sello_orden_entrada IS NULL");
                    break;
                case 5: // Sin iniciar
                    $this->db->where("auditorias_status_id", 1);
                    $this->db->group_start()
                            ->where("auditorias_fechas_inicio_programado >", "NOW()", FALSE)
                            ->or_where("auditorias_fechas_sello_orden_entrada IS NULL")
                            ->group_end();
                    break;
                case 6: // Suistutida
                    $this->db->where("auditorias_status_id <", 0);
                    break;
                default:
                    break;
            }
        }

        if (!empty($direcciones_id) && $direcciones_id != 0) {
            $cc_id = 0;
            $ccs = $this->SAC_model->get_cc_asociados_a_direccion($direcciones_id);
            if (!empty($ccs)) {
                $cc_id = array_column($ccs, 'cc_id');
            }
            $this->db->where_in('cc_id', $cc_id);
        }
        $result = $this->Auditorias_model->get_auditorias_ajax($search, $columns, $order);
        if (is_array($result['result'])) {
            $data = $result['result'];
            $data = array_slice($data, $start, $length);
            $recordsTotal = count($data);
        } else {
            echo "Error en cosulta:\n\n" . $result['sql'];
            echo "\n\n";
            var_dump($result['error']);
            die();
        }
        $datos = array();
        $status = array(NULL, "Cancelada", "En proceso", "Finalizada", "Reprogramada", "Sin iniciar", "Sustituída");
        $className = array(NULL, 'text-danger', 'text-info', 'text-success', 'text-warning', 'text-purple', 'text-purple');
        foreach ($data as $index => $r) {
            //$datos[$index] = $r;
            $datos[$index]['numero'] = '<span class="text-danger">SIN ASIGNAR</span>';
            if (!empty($r['auditorias_numero'])) {
                $aux = array(
                    ($r['auditorias_segundo_periodo'] == 1 ? '2' : '') . $r['auditorias_areas_siglas'],
                    $r['auditorias_tipos_nombre'],
                    sprintf('%1$03d', $r['auditorias_numero']),
                    $r['auditorias_anio'],
                );
                $datos[$index]['numero'] = implode('/', $aux);
            }
            if (!empty($r['auditorias_fechas_inicio_real'])) {
                $datos[$index]['numero'] .= '<br><small>' . mysqlDate2Date($r['auditorias_fechas_inicio_real']) . "</small>";
            }
            $datos[$index]['direccion'] = $r['direcciones_nombre'] . "<br>" . $r['subdirecciones_nombre'];
            $datos[$index]['fecha_inicio_programado'] = mysqlDate2Date($r['auditorias_fechas_inicio_programado']);
            $datos[$index]['fecha_inicio_real'] = mysqlDate2Date($r['auditorias_fechas_inicio_real']);
            $datos[$index]['aprobacion'] = mysqlDate2Date($r['auditorias_fechas_vobo_director']);

            $status_id = $this->Auditorias_status_model->get_status_auditoria($r);

            $datos[$index]['status'] = '<div class="' . $className[$status_id] . '">' . $status[$status_id] . '</div>';
            $acciones = array();
            if ($this->{$this->module['controller'] . "_model"}->puedo_modificar()) {
                $acciones[] = '<a href="' . $this->module['edit_url'] . '/' . $r[$this->module['id_field']] . '" class="btn btn-xs btn-info" title="' . $this->module['title_edit'] . '"><i class="fa fa-pencil"></i></a>';
            }
            if ($this->{$this->module['controller'] . "_model"}->puedo_eliminar()) {
                $acciones[] = '<a href="' . $this->module['delete_url'] . '/' . $r[$this->module['id_field']] . '" class="btn btn-xs btn-danger" title="' . $this->module['title_delete'] . '"><i class="fa fa-trash"></i></a>';
            }
            if ($this->{$this->module['controller'] . "_model"}->puedo_destruir()) {
                $acciones[] = '<a href="' . $this->module['destroy_url'] . "/" . $r[$this->module['id_field']] . '" class="btn btn-xs btn-danger" title="' . $this->module['title_destroy'] . '"><i class="fa fa-remove"></i></a>';
            }
            $datos[$index]['acciones'] = implode(" ", $acciones);
        }

        $json = array(
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => count($result['result']),
            "data" => $datos,
            "sql" => $result['sql'],
            "errores" => $result['error']
        );

        echo json_encode($json);
    }

}
