<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Auditorias_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = strtolower(str_replace("_model", "", __CLASS__));
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "a";
        $this->model_name = __CLASS__;
    }

    function get_uno($id) {
        $return = array();
        if (!empty($id)) {
            $this->db
                    ->select($this->table_prefix . ".*")
                    ->select("CONCAT(IF(" . $this->table_prefix . ".auditorias_segundo_periodo=1,'2',''), aa.auditorias_areas_siglas, '/', at.auditorias_tipos_siglas, '/', LPAD(a.auditorias_numero,3,'0'), '/', " . $this->table_prefix . ".auditorias_anio) AS 'numero_auditoria'")
                    ->join("auditorias_areas aa", "aa.auditorias_areas_id = a.auditorias_area", "INNER")->select("aa.auditorias_areas_siglas, aa.auditorias_areas_nombre")
                    ->join("auditorias_tipos at", "at.auditorias_tipos_id = a.auditorias_tipo", "INNER")->select("at.auditorias_tipos_nombre, at.auditorias_tipos_siglas")
                    ->join("auditorias_fechas af", "af.auditorias_fechas_auditorias_id = " . $this->table_prefix . ".auditorias_id", "LEFT")->select("af.*");
            $return = parent::get_uno($id);
        }
        return $return;
    }

    function insert($data) {
        $data2 = $fechas = array();
        foreach ($data as $index => $d) {
            if (strstr($index, 'auditorias_fechas_') === FALSE) {
                $data2[$index] = $d;
            } else {
                $fechas[$index] = $d;
            }
        }
        if (empty($data2['auditorias_numero'])) {
            $data2['auditorias_numero'] = NULL;
        }
        $data2['auditorias_is_programada'] = 0;
        if (isset($data['auditorias_is_programada'])) {
            $data2['auditorias_is_programada'] = intval($data['auditorias_is_programada']);
        }
        $data2['auditorias_segundo_periodo'] = 0;
        if (isset($data['auditorias_segundo_periodo'])) {
            $data2['auditorias_segundo_periodo'] = intval($data['auditorias_segundo_periodo']);
        }
        if (!empty($data2['auditorias_fechas_inicio_real'])) {
            $fechas['auditorias_fechas_sello_orden_entrada'] = $data2['auditorias_fechas_inicio_real'];
            if (!empty($data['auditorias_status_id']) && $data['auditorias_status_id'] == AUDITORIAS_STATUS_SIN_INICIAR) {
                $data2['auditorias_status_id'] = AUDITORIAS_STATUS_EN_PROCESO;
            }
        }
        $periodos_id = $data['auditorias_periodos_id'];
        $direcciones_id = $data['auditorias_direcciones_id'];
        $subdirecciones_id = $data['auditorias_subdirecciones_id'];
        $departamentos_id = $data['auditorias_departamentos_id'];
        if (!empty($periodos_id) && !empty($direcciones_id) && !empty($subdirecciones_id) && !empty($departamentos_id)) {
            $cc = $this->SAC_model->get_cc_por_datos($periodos_id, $direcciones_id, $subdirecciones_id, $departamentos_id);
            $data2['auditorias_cc_id'] = $cc['cc_id'];
            $data2['clv_dir'] = $cc['clv_dir'];
            $data2['clv_subdir'] = $cc['clv_subdir'];
            $data2['clv_depto'] = $cc['clv_depto'];
        }
        $data2['auditorias_status_id'] = AUDITORIAS_STATUS_SIN_INICIAR; // Sin iniciar
        $return = parent::insert($data2);
        if ($return['state'] === 'success') {
            $fechas['auditorias_fechas_auditorias_id'] = $return['data']['insert_id'];
            $this->Auditorias_fechas_model->insert($fechas);
        }
        return $return;
    }

    function update($id, $data) {
        $data2 = $fechas = array();
        foreach ($data as $index => $d) {
            if (strstr($index, 'auditorias_fechas_') === FALSE) {
                $data2[$index] = $d;
            } else {
                $fechas[$index] = $d;
            }
        }
        $data2['auditorias_is_programada'] = 0;
        if (isset($data['auditorias_is_programada'])) {
            $data2['auditorias_is_programada'] = intval($data['auditorias_is_programada']);
        }
        $data2['auditorias_segundo_periodo'] = 0;
        if (isset($data['auditorias_segundo_periodo'])) {
            $data2['auditorias_segundo_periodo'] = intval($data['auditorias_segundo_periodo']);
        }
        if (!empty($data['auditorias_fechas_inicio_real'])) {
            $fechas['auditorias_fechas_sello_orden_entrada'] = $data['auditorias_fechas_inicio_real'];
            if (!empty($data['auditorias_status_id']) && $data['auditorias_status_id'] == AUDITORIAS_STATUS_SIN_INICIAR) {
                $data2['auditorias_status_id'] = AUDITORIAS_STATUS_EN_PROCESO;
            }
        }
        $data2['auditorias_cc_id'] = NULL;
        $periodos_id = $data['auditorias_periodos_id'];
        $direcciones_id = $data['auditorias_direcciones_id'];
        $subdirecciones_id = $data['auditorias_subdirecciones_id'];
        $departamentos_id = $data['auditorias_departamentos_id'];
        if (!empty($periodos_id) && !empty($direcciones_id) && !empty($subdirecciones_id) && !empty($departamentos_id)) {
            $cc = $this->SAC_model->get_cc_por_datos($periodos_id, $direcciones_id, $subdirecciones_id, $departamentos_id);
            if (!empty($cc)) {
                $data2['auditorias_cc_id'] = intval($cc['cc_id']);
            }
        }
        $return = parent::update($id, $data2);
        if ($return['state'] === 'success') {
            $this->Auditorias_fechas_model->update($id, $fechas);
        }
        return $return;
    }

    function delete($id) {
        $return = parent::delete($id);
        if ($return['state'] === 'success') {
            $this->Auditorias_fechas_model->delete($id);
        }
        return $return;
    }

    function get_auditorias_ajax($search = NULL, $columns = NULL, $order = NULL, $incluirEliminados = FALSE) {
        $where = array();
        $return = FALSE;
        if (!empty($search['value'])) {
            $this->db->group_start()
                    ->or_like("direcciones_nombre", $search['value'])
                    ->or_like("subdirecciones_nombre", $search['value'])
                    ->or_like("departamentos_nombre", $search['value'])
                    ->or_like("CONCAT(IF(" . $this->table_prefix . ".auditorias_segundo_periodo=1,'2',''), aa.auditorias_areas_siglas, '/', at.auditorias_tipos_siglas, '/', LPAD(a.auditorias_numero,3,'0'), '/', " . $this->table_prefix . ".auditorias_anio)", $search['value'])
                    ->or_like("empleados_nombre", $search['value'])
                    ->or_like("empleados_apellido_paterno", $search['value'])
                    ->or_like("empleados_apellido_materno", $search['value'])
                    ->or_where("MATCH (empleados_nombre, empleados_apellido_paterno, empleados_apellido_materno) AGAINST ('" . $search['value'] . "' IN NATURAL LANGUAGE MODE) > 0")
                    ->or_like("auditorias_rubro", $search['value'])
                    ->group_end();
//                    ->or_like("e.empleados_numero_empleado", $search['value'])
//                    ->where("e.fecha_delete IS NULL");
        }
        if (!$incluirEliminados) {
            $this->db->where($this->table_prefix . ".fecha_delete IS NULL");
        }
        $this->db
                ->select($this->table_prefix . ".*")
                ->select("CONCAT(IF(" . $this->table_prefix . ".auditorias_segundo_periodo=1,'2',''), aa.auditorias_areas_siglas, '/', at.auditorias_tipos_siglas, '/', LPAD(a.auditorias_numero,3,'0'), '/', " . $this->table_prefix . ".auditorias_anio) AS 'numero_auditoria'")
                ->join("auditorias_areas aa", "aa.auditorias_areas_id = a.auditorias_area", "INNER")->select("aa.auditorias_areas_siglas")
                ->join("auditorias_tipos at", "at.auditorias_tipos_id = a.auditorias_tipo", "INNER")->select("at.auditorias_tipos_nombre, at.auditorias_tipos_siglas")
                ->join("auditorias_fechas af", "af.auditorias_fechas_auditorias_id = " . $this->table_prefix . ".auditorias_id", "LEFT")->select("af.*")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = " . $this->table_prefix . ".auditorias_auditor_lider", "LEFT")->select("e.*,CONCAT(e.empleados_nombre,' ',e.empleados_apellido_paterno, ' ',e.empleados_apellido_materno) AS 'nombre_completo'")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".centros_costos cc", "cc.cc_id = " . $this->table_prefix . ".auditorias_cc_id ", "LEFT")->select("cc_id, cc_periodos_id, cc_direcciones_id, cc_subdirecciones_id, cc_departamentos_id, cc_etiqueta_direccion, cc_etiqueta_subdireccion, cc_etiqueta_departamento")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "LEFT")->select("direcciones_nombre, direcciones_is_descentralizada, direcciones_ubicacion")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "LEFT")->select("subdirecciones_nombre")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "LEFT")->select("departamentos_nombre");
        if (!empty($order)) {
            foreach ($order as $o) {
                $field = $columns[$o['column']]['name'];
                $this->db->order_by($field, $o['dir']);
            }
        } else {
            $this->db->order_by('relevancia', 'DESC');
        }
        $result = $this->db->get($this->table_name . " " . $this->table_prefix);
        $strSQL = $this->db->last_query();
        $error = $this->db->error();
        $return = array(
            'result' => $error['code'] == 0 ? $result->result_array() : array(),
            'sql' => $strSQL,
            'error' => $error
        );
        return $return;
    }

    /**
     * Regresa la información del lider de una auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @return array Información del auditor líder de la auditoría
     */
    function get_lider_auditoria($auditorias_id = NULL) {
        $return = array();
        if (empty($auditorias_id)) {
            $session = $this->session->userdata(APP_NAMESPACE);
            $auditorias_id = $session['auditorias_id'];
        }
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->select("CONCAT(e.empleados_nombre, ' ',e.empleados_apellido_paterno,' ',e.empleados_apellido_materno) AS 'nombre_completo'")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = a.auditorias_auditor_lider", "INNER")->select("e.*")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".puestos p", "p.puestos_id = e.empleados_puestos_id", "INNER")->select("p.puestos_nombre")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".titulos t", "t.titulos_id = e.empleados_titulos_id", "LEFT")->select("t.*")
                    ->where('auditorias_id', $auditorias_id)
                    ->limit(1)
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result->num_rows() > 0) {
                $return = $result->row_array();
                get_nombre_titulado($return);
                get_cargo_de_empleado($return);
                get_siglas_de_empleado($return);
            }
        }
        return $return;
    }

    /**
     * Regresa el equipo de trabajo asociado a una auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @param integer $tipo_permiso 1=Equipo de trabajo, 2=Acceso adicional, cualquier otro valor devuelve a todos
     * @return array Información de los auditores que apoyarán en la auditoría
     */
    function get_equipo_auditoria($auditorias_id, $tipo_permiso = NULL) {
        $return = array();
        if (!empty($auditorias_id)) {
            if (!empty($tipo_permiso)) {
                $this->db->where('auditorias_equipo_tipo', $tipo_permiso);
            }
            $result = $this->db
                    ->select("CASE
                    WHEN e.empleados_puestos_id IN (155) THEN 0
                    WHEN e.empleados_puestos_id IN (45, 290, 145, 294, 293) THEN 1
                    WHEN e.empleados_puestos_id IN (106, 157) THEN 2
                    WHEN e.empleados_puestos_id IN (59, 296, 60, 272) THEN 3
                    WHEN e.empleados_puestos_id IN (40, 269) THEN 4
                    ELSE 5 END AS 'orden'")
                    ->select("CONCAT(e.empleados_nombre, ' ',e.empleados_apellido_paterno,' ',e.empleados_apellido_materno) AS 'nombre_completo'")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = ae.auditorias_equipo_empleados_id", "INNER")->select("e.*")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".puestos p", "p.puestos_id = e.empleados_puestos_id", "INNER")->select("p.puestos_nombre")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".titulos t", "t.titulos_id = e.empleados_titulos_id", "LEFT")->select("t.*")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".centros_costos cc", "cc.cc_id = " . "e.empleados_cc_id", "LEFT")->select("cc.*")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "LEFT")->select("direcciones_nombre, direcciones_nombre_generico, direcciones_ubicacion, direcciones_is_descentralizada")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "LEFT")->select("s.subdirecciones_nombre")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "LEFT")->select("dd.departamentos_nombre")
                    ->where('auditorias_equipo_auditorias_id', $auditorias_id)
                    ->order_by("orden", "DESC")
                    ->get('auditorias_equipo ae');
            if ($result->num_rows() > 0) {
                $return = $result->result_array();
                foreach ($return as $index => $row) {
                    get_nombre_titulado($return[$index]);
                    get_cargo_de_empleado($return[$index]);
                    get_siglas_de_empleado($return[$index]);
                }
            }
        }
        return $return;
    }

    /**
     * Regresa información de una auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @return array Información de la auditoría
     */
    function get_auditoria($auditorias_id) {
        $return = array();
        if (!empty($auditorias_id)) {
            $result = $this->db
                    //->select("MATCH (empleados_nombre, empleados_apellido_paterno, empleados_apellido_materno) AGAINST ('" . $search['value'] . "' IN NATURAL LANGUAGE MODE) AS 'relevancia'")
                    ->select($this->table_prefix . ".*")
                    ->select("CONCAT(IF(" . $this->table_prefix . ".auditorias_segundo_periodo=1,'2',''), aa.auditorias_areas_siglas, '/', at.auditorias_tipos_siglas, '/', LPAD(a.auditorias_numero,3,'0'), '/', " . $this->table_prefix . ".auditorias_anio) AS 'numero_auditoria'")
                    ->join("auditorias_areas aa", "aa.auditorias_areas_id = a.auditorias_area", "INNER")->select("aa.auditorias_areas_siglas, aa.auditorias_areas_nombre")
                    ->join("auditorias_tipos at", "at.auditorias_tipos_id = a.auditorias_tipo", "INNER")->select("at.auditorias_tipos_nombre, at.auditorias_tipos_siglas")
                    ->join("auditorias_fechas af", "af.auditorias_fechas_auditorias_id = " . $this->table_prefix . ".auditorias_id", "LEFT")->select("af.*")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = " . $this->table_prefix . ".auditorias_auditor_lider", "LEFT")->select("e.*, CONCAT(e.empleados_nombre, ' ',e.empleados_apellido_paterno, ' ', e.empleados_apellido_materno) AS 'auditor_lider_nombre_completo'")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".puestos p", "p.puestos_id = empleados_puestos_id", "LEFT")->select("puestos_nombre")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".titulos t", "t.titulos_id = empleados_titulos_id", "LEFT")->select("t.titulos_masculino_siglas, t.titulos_masculino_nombre, t.titulos_femenino_siglas, t.titulos_femenino_nombre")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".centros_costos cc", "cc.cc_id = " . $this->table_prefix . ".auditorias_cc_id ", "LEFT")->select("cc.*")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "LEFT")->select("direcciones_nombre, direcciones_is_descentralizada, direcciones_ubicacion, direcciones_tipos_ua_id")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".tipos_ua tua", "tua.tipos_ua_id = d.direcciones_tipos_ua_id", "LEFT")->select("tua.tipos_ua_nombre, tua.tipos_ua_genero")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "LEFT")->select("subdirecciones_nombre")
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "LEFT")->select("departamentos_nombre")
                    ->where($this->id_field, $auditorias_id)
                    ->limit(1)
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($result && $result->num_rows() == 1) {
                $return = $result->row_array();
                $return['nombre_completo'] = $return['auditor_lider_nombre_completo'];
                forma_nombre_completo_de_ua($return);
                get_nombre_titulado($return);
                $return['auditoria_equipo'] = $this->get_equipo_auditoria($auditorias_id, TIPO_PERMISO_EQUIPO_TRABAJO);
                $return['auditoria_permisos_adicionales'] = $this->get_equipo_auditoria($auditorias_id, TIPO_PERMISO_ADICIONAL);
                $return['enlace_designado'] = $this->SAC_model->get_empleado($return['auditorias_enlace_designado']);
                $return['empleados_involucrados'] = $this->Auditorias_involucrados_model->get_empleados_involucrados_en_auditoria($auditorias_id);
                $return['observaciones'] = $this->Observaciones_model->get_observaciones($auditorias_id);
            }
        }
        return $return;
    }

    function get_real_auditoria_origen($auditorias_id = NULL) {
        $return = $auditorias_id;
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->select("auditorias_origen_id")
                    ->where("auditorias_id", $auditorias_id)
                    ->where("auditorias_status_id >=", AUDITORIAS_STATUS_FINALIZADA)
                    ->limit(1)
                    ->get($this->table_name);
            if ($result && $result->num_rows() > 0) {
                $a = $result->row();
                if (!empty($a->auditorias_origen_id)) {
                    $return = $this->get_real_auditoria_origen($a->auditorias_origen_id);
                }
            }
        }
        return intval($return);
    }

    /**
     * Identifica la etapa de la auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @param datetime $fecha Fecha en la que se desea saber la etapa de la auditoría. De forma predeterminada es la fecha en la que se hace la solicitud.
     * @param array $datos_auditoria Arreglo que contiene información de la auditoría
     * @return integer Entero que identifica la etapa de la auditoría
     */
    function get_etapa_de_auditoria($auditorias_id, $fecha = NULL, $datos_auditoria = NULL) {
        $return = FALSE;
        if (!empty($auditorias_id)) {
            if (empty($fecha)) {
                $fecha = ahora();
            }
            if (empty($datos_auditoria)) {
                $datos_auditoria = $this->get_auditoria($auditorias_id);
            }
            if ($datos_auditoria['auditorias_anio'] >= 2018) {
                $return = $this->get_etapa_de_auditoria_2018($auditorias_id, $fecha, $datos_auditoria);
            } else {
                $return = $this->get_etapa_procedimiento_1($auditorias_id, $fecha, $datos_auditoria);
            }
        }
        return $return;
    }

    function get_etapa_procedimiento_1($auditorias_id, $fecha = NULL, $datos_auditoria = NULL) {
        $return = AUDITORIA_ETAPA_AP;
        if (!empty($auditorias_id)) {
            if (empty($fecha)) {
                $fecha = ahora();
            }
            if (empty($datos_auditoria)) {
                $datos_auditoria = $this->get_auditoria($auditorias_id);
            }
            $auditorias_status_id = array(AUDITORIAS_STATUS_FINALIZADA, AUDITORIAS_STATUS_FINALIZADA_RESERVADA, AUDITORIAS_STATUS_FINALIZADA_MANUAL);
            if (in_array($datos_auditoria['auditorias_status_id'], $auditorias_status_id)) {
                $return = AUDITORIA_ETAPA_FIN;
            } elseif (empty($datos_auditoria['auditorias_fechas_lectura']) && intval($datos_auditoria['auditorias_is_sin_observaciones']) === 1) {
                $return = AUDITORIA_ETAPA_AP;
            } elseif (!empty($datos_auditoria['auditorias_fechas_lectura']) && intval($datos_auditoria['auditorias_is_sin_observaciones']) === 0) {
                // Como ya se ha leído el ARA, entonces se espera un $plazo=1 días para bloquear el acta
                $plazo = 1;
                $fecha_limite_para_editar = agregar_dias($datos_auditoria['auditorias_fechas_lectura'], $plazo, TRUE);
                $DT_fecha_limite_para_editar = new DateTime($fecha_limite_para_editar);
                if (empty($fecha)) {
                    $fecha = ahora();
                }
                $hoy = new DateTime($fecha);
                if ($hoy > $DT_fecha_limite_para_editar) {
                    $return = AUDITORIA_ETAPA_REV1;
                }
            } elseif (empty($datos_auditoria['auditorias_fechas_lectura'])) {
                $return = AUDITORIA_ETAPA_REV1;
            } else {
                $numero_revision = empty($datos_auditoria['auditorias_origen_id']) ? 3 : 5;
                $observaciones = $this->get_status_de_observaciones($auditorias_id, $numero_revision);
                if (intval($datos_auditoria['auditorias_is_sin_observaciones']) === 0 && !empty($observaciones['pendientes'])) {
                    $return = AUDITORIA_ETAPA_REV1;
                }
            }
        }
        return $return;
    }

    function get_etapa_de_auditoria_2018($auditorias_id, $fecha = NULL, $datos_auditoria = NULL) {
        $return = AUDITORIA_ETAPA_AP;
        if (!empty($auditorias_id)) {
            if (empty($datos_auditoria)) {
                $datos_auditoria = $this->get_auditoria($auditorias_id);
            }
            $auditorias_status_id = array(AUDITORIAS_STATUS_FINALIZADA, AUDITORIAS_STATUS_FINALIZADA_RESERVADA);
            if (in_array($datos_auditoria['auditorias_status_id'], $auditorias_status_id)) {
                $return = AUDITORIA_ETAPA_FIN;
            } elseif (empty($datos_auditoria['auditorias_fechas_lectura']) || intval($datos_auditoria['auditorias_is_sin_observaciones']) === 1) {
                $return = AUDITORIA_ETAPA_AP;
            } elseif (!empty($datos_auditoria['auditorias_fechas_lectura']) && intval($datos_auditoria['auditorias_is_sin_observaciones']) === 0) {
                // Como ya se ha leído el ARA, entonces se espera un $plazo=1 días para bloquear el acta
                $plazo = 1;
                $fecha_limite_para_editar = agregar_dias($datos_auditoria['auditorias_fechas_lectura'], $plazo, TRUE);
                $DT_fecha_limite_para_editar = new DateTime($fecha_limite_para_editar);
                if (empty($fecha)) {
                    $fecha = ahora();
                }
                $hoy = new DateTime($fecha);
                if ($hoy > $DT_fecha_limite_para_editar) {
                    $return = AUDITORIA_ETAPA_REV1;
                }
            } else {
                $numero_revision = empty($datos_auditoria['auditorias_origen_id']) ? 3 : 5;
                $observaciones = $this->get_status_de_observaciones($auditorias_id, $numero_revision);
                if (intval($datos_auditoria['auditorias_is_sin_observaciones']) === 0 && !empty($observaciones['pendientes'])) {
                    $return = AUDITORIA_ETAPA_REV1;
                }
            }
        }
        return $return;
    }

    /**
     * Devuelve la información de las personas que deben firmar un documento
     * @param ibteger $auditorias_id Identificador de la auditoría
     * @param integer $tipo Identificador del tipo de firma (Involucrados, Testigos, Contraloria, Responsables)
     * @return array Arreglo que contiene la información de los funcionarios públicos que deben firmar un documento
     */
    function get_firmas_de_auditoria($auditorias_id, $tipo = NULL) {
        $return = array();
        return $return;
    }

    /**
     * Regresa el conjunto de observaciones de un auditoría
     * @param type $auditorias_id Identificador de la auditoría
     * @return array Arreglo que contiene las observaciones asociadas a una auditoría
     */
    function get_observaciones_de_auditoria($auditorias_id) {
        $return = array();
        if (!empty($auditorias_id)) {
            $return = $this->Observaciones_model->get_observaciones($auditorias_id);
        }
        return $return;
    }

    /**
     * Permite obtener el listado de documentos generados de una auditoría
     * @param integer $auditorias_id Identificador de la auditoría
     * @param integer $idTipoDocumento optional Esta variable sirve por si se desea el listado de un tipo específico de documentos
     * @return array Arreglo con los identificadores de los documentos generados
     */
    function get_documentos_de_auditoria($auditorias_id, $idTipoDocumento = NULL) {
        $return = array();
        return $return;
    }

    function get_productos_no_conformes_de_auditoria($auditorias_id) {
        $return = array();
        return $return;
    }

    function get_notas_de_auditoria($auditorias_id) {
        
    }

    function get_reprogramaciones_de_auditoria($auditorias_id) {
        
    }

    function get_ampliaciones_de_auditoria($auditorias_id) {
        
    }

    function get_auditorias_de_empleado($idEmpleado) {
        $return = array();
        if (!empty($idEmpleado)) {
            $res = $this->db
                    ->select("a.*, a.tipo AS tipo, a.numero as valnum")
                    ->select("CONCAT(IF(segundoPeriodo=1,'2',''), a.auditorias_area, '/', a.auditorias_tipo, '/', a.auditorias_numero, '/', a.auditorias_anio) AS auditoria_nombre")
                    ->group_by("a.numero, id, rubro, num")
                    ->order_by("a.numero", "ASC")
                    ->order_by("tipo", "ASC")
                    ->order_by("anio", "ASC")
                    ->order_by("num", "ASC")
                    ->get($this->table_name . " " . $this->table_prefix);
            if ($res->num_rows() > 0) {
                $return = $res->result_array();
            }
        }
        return $return;
    }

    function get_proximo_numero_auditoria($segundo_periodo = FALSE, $anio = NULL) {
        $return = 1;
        if (empty($anio)) {
            $anio = intval(date("Y"));
        }
        if (!empty($anio)) {
            $result = $this->db
                    ->select("MAX(auditorias_numero) AS 'consecutivo'")
                    ->where("auditorias_segundo_periodo", intval($segundo_periodo))
                    ->where("auditorias_anio", $anio)
                    ->limit(1)
                    ->get("auditorias");
            if ($result && $result->num_rows() == 1) {
                $aux = $result->row_array();
                $return = intval($aux['consecutivo']) + 1;
            }
        }
        return $return;
    }

    function get_siglas_de_empleados_para_documento_de_auditoria($empleados_id, $auditorias_id) {
        $return = "";
        $empleado = $this->SAC_model->get_empleado($empleados_id);
        $mi_puesto = intval($empleado['empleados_puestos_id']);
        $siglas = array();
        if (!empty($empleados_id)) {
            array_push($siglas, $empleado['empleado_siglas']);
        }
        switch ($mi_puesto) {
            case PUESTO_AUDITOR:
            case PUESTO_COORDINADOR_AUDITORIA:
            case PUESTO_COORDINADOR:
                $coordinador = $this->SAC_model->get_coordinador_de_empleado($empleados_id, $auditorias_id);
                if (!empty($coordinador)) {
                    array_push($siglas, $coordinador['empleado_siglas']);
                }
            case PUESTO_JEFE_DEPARTAMENTO:
                $jefe = $this->SAC_model->get_jefe_de_empleado($empleados_id);
                if (!empty($jefe)) {
                    array_push($siglas, $jefe['empleado_siglas']);
                }
            case PUESTO_SUBDIRECTOR:
                $subdirector = $this->SAC_model->get_subdirector_de_empleado($empleados_id);
                if (!empty($subdirector)) {
                    array_push($siglas, $subdirector['empleado_siglas']);
                }
            case PUESTO_DIRECTOR:
                $titular = $this->SAC_model->get_director_de_ua(APP_DIRECCION_CONTRALORIA);
                if (!empty($titular)) {
                    array_push($siglas, $titular['empleado_siglas']);
                }
                break;
            default:
                break;
        }
        // Ponemos en minusculas al auditor lider
        $siglas[0] = strtolower($siglas[0]);
        // Invertimos el arreglo
        $siglas = array_reverse($siglas);
        // Unimos el arreglo
        $return = implode("/", $siglas) . "*";
        return $return;
    }

    function asignar_enlace_designado($auditorias_id = NULL, $empleados_id = NULL) {
        $return = FALSE;
        if (empty($empleados_id)) {
            $empleados_id = NULL;
        }
        if (!empty($auditorias_id)) {
            $result = $this->db
                    ->set("auditorias_enlace_designado", $empleados_id)
                    ->where("auditorias_id", $auditorias_id)
                    ->update($this->table_name);
            if ($result && $this->db->affected_rows() > 0) {
                $return = TRUE;
            }
        }
        return $return;
    }

    function get_involucrados($auditorias_id, $etapa = NULL, $tipo = NULL) {
        $return = array();
        if (!empty($auditorias_id)) {
            $return = $this->Auditorias_involucrados_model->get_empleados_involucrados_en_auditoria($auditorias_id, $etapa, $tipo);
        }
        return $return;
    }

    /**
     * Obtiene información de las auditoróas origen de una auditoría
     * @param integer $auditorias_id
     */
    function get_auditorias_origen($auditorias_id) {
//        $return = array();
//        if (!empty($auditorias_id)) {
//            $result = $this->db
//                    ->where("auditorias_id", $auditorias_id)
//                    ->where("auditorias_origen_id IS NOT NULL")
//                    ->where("auditorias_status_id >", 0)
//                    ->get($this->table_name);
//            if ($result && $result->num_rows() > 0) {
//                $return = $result->result_array();
//                foreach ($return as $r) {
//                    $r = $this->get_auditorias_origen($r['idAuditoriaOrigen']);
//                    if (is_array($r) && !empty($r)) {
//                        $return = array_merge($return, $r);
//                    }
//                }
//            }
//        }
        $return = $this->get_auditorias_origen_antiguo_cysa($auditorias_id);
        return $return;
    }

    function get_auditorias_origen_antiguo_cysa($auditorias_id) {
        $return = array();
        if (!empty($auditorias_id)) {
            $config['hostname'] = APP_DATABASE_HOSTNAME;
            $config['username'] = APP_DATABASE_USERNAME;
            $config['password'] = APP_DATABASE_PASSWORD;
            $config['database'] = 'proto_' . APP_DATABASE_CYSA;
            $config['dbdriver'] = 'mysqli';
            $config['dbprefix'] = '';
            $config['pconnect'] = FALSE;
            $config['db_debug'] = TRUE;
            $config['cache_on'] = FALSE;
            $config['cachedir'] = '';
            $config['char_set'] = 'utf8';
            $config['dbcollat'] = 'utf8_general_ci';

            $dbCYSA = $this->load->database($config, TRUE);
            $result = $dbCYSA
                    ->where("idAuditoria", $auditorias_id)
                    ->where("idAuditoriaOrigen IS NOT NULL")
                    ->where("statusAudit >", 0)
                    ->get("cat_auditoria");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
                foreach ($return as $r) {
                    $r = $this->get_auditorias_origen_antiguo_cysa($r['idAuditoriaOrigen']);
                    if (is_array($r) && !empty($r)) {
                        $return = array_merge($return, $r);
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Obtiene las recomendaciones de una observación
     * @param integer $observaciones_id Identificador de la observación
     * @param boolean $incluir_eliminados TRUE para incluiir las recomendaciones eliminadas, FALSE en caso contrario.
     * @return array Arreglo con las recomendaciones de la observación
     */
    function get_recomendaciones_de_observacion($observaciones_id, $incluir_eliminados = FALSE) {
        $return = array();
        if (!$incluir_eliminados) {
            $this->db->where("r.fecha_delete IS NULL");
        }
        if (!empty($observaciones_id)) {
            $result = $this->db->select("r.*")
                    ->join("recomendaciones_status rs", "rs.recomendaciones_status_id = r.recomendaciones_status_id", "INNER")->select("recomendaciones_status_nombre")
                    ->join("recomendaciones_clasificaciones rc", "rc.recomendaciones_clasificaciones_id = r.recomendaciones_clasificaciones_id", "INNER")->select("recomendaciones_clasificaciones_nombre")
                    ->where("recomendaciones_observaciones_id", $observaciones_id)
                    ->get("recomendaciones r");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    /**
     * Obtiene la información de la recomendación
     * @param integer $recomendaciones_id Identificador de la recomendacion
     * @return array Arreglo con los datos de la recomendación
     */
    function get_recomendacion($recomendaciones_id = NULL) {
        $return = array();
        if (!empty($recomendaciones_id)) {
            $result = $this->db->select("r.*")
                    ->join("recomendaciones_status rs", "rs.recomendaciones_status_id = r.recomendaciones_status_id", "INNER")->select("recomendaciones_status_nombre")
                    ->join("recomendaciones_clasificaciones rc", "rc.recomendaciones_clasificaciones_id = r.recomendaciones_clasificaciones_id", "INNER")->select("recomendaciones_clasificaciones_nombre")
                    ->where("recomendaciones_id", $recomendaciones_id)
                    ->limit(1)
                    ->get("recomendaciones r");
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    /**
     * Devuelve las auditorias sin número o que también están pendientes por iniciar
     * @param boolean $incluir_canceladas TRUE para indiciar que también devuelva las auditorias canceladas. FALSE para devolver solo las pendientes por iniciar
     * @param boolean $incluir_eliminadas TRUE para indicar que se incluyan las eliminadas. FALSE para cualquier otro caso.
     * @return array Listado de auditorias
     */
    function get_auditorias_sin_numero($incluir_canceladas = FALSE, $incluir_eliminadas = FALSE) {
        $return = array();
        $auditorias_status_id = array(AUDITORIAS_STATUS_EN_PROCESO);
        if ($incluir_canceladas) {
            array_push($auditorias_status_id, AUDITORIAS_STATUS_CANCELADA);
        }
        if (!$incluir_eliminadas) {
            $this->db->where("fecha_delete IS NULL");
        }
        $result = $this->db
                ->where_in("auditorias_status_id", $auditorias_status_id)
                ->where("auditorias_numero IS NULL")
                ->get($this->table_name);
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
        }
        return $return;
    }

    function get_status_de_observaciones($auditorias_id) {
        $return = array(
            'solventadas' => array(),
            'pendientes' => array()
        );
        if (!empty($auditorias_id)) {
            $observaciones = $this->Observaciones_model->get_observaciones($auditorias_id);
            if (is_array($observaciones) && !empty($observaciones)) {
                foreach ($observaciones as $o) {
                    $is_solventada = $this->Observaciones_model->is_solventada($o['observaciones_id']);
                    if ($is_solventada) {
                        array_push($return['solventadas'], $o['observaciones_id']);
                    } else {
                        array_push($return['pendientes'], $o['observaciones_id']);
                    }
                }
            }
        }
        return $return;
    }

    function get_status_de_recomendaciones($auditorias_id) {
        $return = array(
            OBSERVACIONES_STATUS_NO_SOLVENTADA => array(),
            OBSERVACIONES_STATUS_SOLVENTADA => array(),
            OBSERVACIONES_STATUS_NO_SE_ACEPTA => array(),
            OBSERVACIONES_STATUS_ATENDIDA => array(),
            OBSERVACIONES_STATUS_NO_ATENDIDA => array()
        );
        if (!empty($auditorias_id)) {
            $observaciones = $this->Observaciones_model->get_observaciones($auditorias_id);
            if (is_array($observaciones) && !empty($observaciones)) {
                foreach ($observaciones as $o) {
                    $recomendaciones = $this->Recomendaciones_model->get_recomendaciones($o['observaciones_id']);
                    foreach ($recomendaciones as $r) {
                        $auditorias_status_id_id = intval($r['recomendaciones_status_id']);
                        array_push($return[$auditorias_status_id_id], $r['recomendaciones_id']);
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Devuelve un listado de auditorías que cumplen con los parámentros especificados
     * @param array $auditorias_anios Número entero que indica el año de creación de la auditoría
     * @param array $auditorias_status_id Indentificador de status de la auditoría
     * @param array $auditorias_area Identificador del área de la auditoría
     * @param boolean $solo_con_numero TRUE indica que solo se devolveran las auditorías con número, FALSE indica que se incluirán las auditorias que no tienen número.
     * @param boolean $mas_datos TRUE Indica que devolverá datos del Auditor Líder,Enlace Disignado, Involucrados y Observaciones.
     * @return array Devuelve un arreglo con las auditorías
     */
    function get_auditorias($auditorias_anios = NULL, $auditorias_status_id = NULL, $auditorias_area = NULL, $solo_con_numero = TRUE, $mas_datos = FALSE) {
        $return = array();
        if (!empty($auditorias_anios)) {
            if (is_array($auditorias_anios)) {
                $this->db->where_in($this->table_prefix . '.auditorias_anio', $auditorias_anios);
            } else {
                $this->db->where($this->table_prefix . '.auditorias_anio', $auditorias_anios);
            }
        }
        if (!empty($auditorias_status_id)) {
            if (is_array($auditorias_status_id)) {
                $this->db->where_in($this->table_prefix . '.auditorias_status_id', $auditorias_status_id);
            } else {
                $this->db->where($this->table_prefix . '.auditorias_status_id', $auditorias_status_id);
            }
        }
        if ($solo_con_numero) {
            $this->db->where($this->table_prefix . '.auditorias_numero IS NOT NULL');
        }
        $result = $this->db
                //->select("MATCH (empleados_nombre, empleados_apellido_paterno, empleados_apellido_materno) AGAINST ('" . $search['value'] . "' IN NATURAL LANGUAGE MODE) AS 'relevancia'")
                ->select($this->table_prefix . ".*")
                ->select("CONCAT(IF(" . $this->table_prefix . ".auditorias_segundo_periodo=1,'2',''), aa.auditorias_areas_siglas, '/', at.auditorias_tipos_siglas, '/', LPAD(a.auditorias_numero,3,'0'), '/', " . $this->table_prefix . ".auditorias_anio) AS 'numero_auditoria'")
                ->join("auditorias_areas aa", "aa.auditorias_areas_id = a.auditorias_area", "INNER")->select("aa.auditorias_areas_siglas, aa.auditorias_areas_nombre")
                ->join("auditorias_tipos at", "at.auditorias_tipos_id = a.auditorias_tipo", "INNER")->select("at.auditorias_tipos_nombre, at.auditorias_tipos_siglas")
                ->join("auditorias_fechas af", "af.auditorias_fechas_auditorias_id = " . $this->table_prefix . ".auditorias_id", "LEFT")->select("af.*")
                ->join("auditorias_status as", "as.auditorias_status_id = " . $this->table_prefix . ".auditorias_status_id", "LEFT")->select("as.auditorias_status_nombre")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = " . $this->table_prefix . ".auditorias_auditor_lider", "LEFT")->select("e.*, CONCAT(e.empleados_nombre, ' ',e.empleados_apellido_paterno, ' ', e.empleados_apellido_materno) AS 'auditor_lider_nombre_completo'")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".puestos p", "p.puestos_id = empleados_puestos_id", "LEFT")->select("puestos_nombre")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".titulos t", "t.titulos_id = empleados_titulos_id", "LEFT")->select("t.titulos_masculino_siglas, t.titulos_masculino_nombre, t.titulos_femenino_siglas, t.titulos_femenino_nombre")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".centros_costos cc", "cc.cc_id = " . $this->table_prefix . ".auditorias_cc_id ", "LEFT")->select("cc.*")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "LEFT")->select("direcciones_nombre, direcciones_is_descentralizada, direcciones_ubicacion, direcciones_tipos_ua_id")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".tipos_ua tua", "tua.tipos_ua_id = d.direcciones_tipos_ua_id", "LEFT")->select("tua.tipos_ua_nombre, tua.tipos_ua_genero")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "LEFT")->select("subdirecciones_nombre")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "LEFT")->select("departamentos_nombre")
                ->order_by("auditorias_anio", "ASC")
                ->order_by("auditorias_numero", "ASC")
                ->order_by("auditorias_tipos_siglas", "ASC")
                ->get($this->table_name . " " . $this->table_prefix);
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
            if ($mas_datos) {
                foreach ($return as $index => $r) {
                    $auditorias_id = $r['auditorias_id'];
                    $return[$index]['nombre_completo'] = $return[$index]['auditor_lider_nombre_completo'];
                    forma_nombre_completo_de_ua($return[$index]);
                    get_nombre_titulado($return[$index]);
                    $return[$index]['auditoria_equipo'] = $this->get_equipo_auditoria($auditorias_id, TIPO_PERMISO_EQUIPO_TRABAJO);
                    $return[$index]['auditoria_permisos_adicionales'] = $this->get_equipo_auditoria($auditorias_id, TIPO_PERMISO_ADICIONAL);
                    $return[$index]['enlace_designado'] = $this->SAC_model->get_empleado($return[$index]['auditorias_enlace_designado']);
                    $return[$index]['empleados_involucrados'] = $this->Auditorias_involucrados_model->get_empleados_involucrados_en_auditoria($auditorias_id);
                    $return[$index]['observaciones'] = $this->Observaciones_model->get_observaciones($auditorias_id);
                }
            }
        }
        return $return;
    }

    function get_catalogo_anios_de_auditorias() {
        $return = array();
        $result = $this->db
                ->select("auditorias_anio")
                ->distinct(TRUE)
                ->order_by('auditorias_anio', 'DESC')
                ->get($this->table_name);
        if ($result) {
            $return = $result->result_array();
        }
        return $return;
    }

}
