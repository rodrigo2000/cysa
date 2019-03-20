<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Auditoria_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = "auditorias";
        $this->id_field = $this->table_name . "_id";
        $this->table_prefix = "a";
        $this->model_name = __CLASS__;
    }

    function get_auditorias_para_select() {
        return $this->getResultados(NULL, NULL);
    }

    function get_anios_para_select() {
        $return = array();
        $anios = $this->get_anios_de_mis_auditorias();
        $en_proceso = $finalizadas = array();
        foreach ($anios as $a) {
            if (intval($a['auditorias_status_id']) == AUDITORIAS_STATUS_EN_PROCESO) {
                array_push($en_proceso, $a);
            } else {
                array_push($finalizadas, $a);
            }
        }
        $return = array(
            'en_proceso' => $en_proceso,
            'finalizadas' => $finalizadas
        );
        return $return;
    }

    function getResultados($limit = NULL, $start = NULL) {
        $empleados_id = $this->session->userdata('empleados_id');
        $this->db
                ->select($this->table_prefix . ".*")
                ->select("CONCAT(IF(a.auditorias_segundo_periodo=1,'2',''), aa.auditorias_areas_siglas,'/',at.auditorias_tipos_siglas,'/', LPAD(a.auditorias_numero,3,'0'),'/',a.auditorias_anio) AS 'numero_auditoria'")
                ->select("CASE WHEN a.auditorias_tipo < 4 THEN 1 ELSE 2 END AS 'tipo_auditoria'")
                ->join("auditorias_areas aa", "aa.auditorias_areas_id = " . $this->table_prefix . ".auditorias_area", "INNER")->select("aa.auditorias_areas_siglas")
                ->join("auditorias_tipos at", "at.auditorias_tipos_id = " . $this->table_prefix . ".auditorias_tipo", "INNER")->select("at.auditorias_tipos_nombre, at.auditorias_tipos_siglas")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = a.auditorias_auditor_lider", "INNER")//->select("e.empleados_cc_id")
                ->order_by("a.auditorias_anio", "DESC")
                ->order_by("tipo_auditoria", "ASC")
                ->order_by("a.auditorias_segundo_periodo", "DESC")
                ->order_by("a.auditorias_numero", "ASC");
        switch ($this->session->userdata('perfiles_id')) {
            case USUARIO_PERFIL_ADMNISTRADOR:
                break;
            case USUARIO_PERFIL_RESGUARDANTE_BODEGA:
                break;
            case USUARIO_PERFIL_EMPLEADO_CONTRALORIA:
                $puestos_id = intval($this->session->userdata('puestos_id'));
                $cc_id = intval($this->session->userdata('cc_id'));
                switch ($puestos_id) {
                    case PUESTO_SUBDIRECTOR:
                    case PUESTO_JEFE_DEPARTAMENTO:
                        $this->db
                                ->join("auditorias_equipo ae", "ae.auditorias_equipo_auditorias_id = auditorias_id", "INNER")
                                ->group_start()
                                ->where("auditorias_auditor_lider", $empleados_id)
                                ->or_where("ae.auditorias_equipo_empleados_id", $empleados_id)
                                ->or_where_in("e.empleados_cc_id", array(669))
                                ->group_end();
                        break;
                    case PUESTO_COORDINADOR:
                    case PUESTO_COORDINADOR_AUDITORIA:
                        $this->db
                                ->join("auditorias_equipo ae", "ae.auditorias_equipo_auditorias_id = auditorias_id", "INNER")
                                ->group_start()
                                ->where("auditorias_auditor_lider", $empleados_id)
                                ->or_where("ae.auditorias_equipo_empleados_id", $empleados_id)
                                ->group_end();
                        break;
                    case PUESTO_AUDITOR:
                    case PUESTO_AUXILIAR_DE_AUDITORIA:
                        $this->db->where("auditorias_auditor_lider", $empleados_id);
                        break;
                    default:
                }
                break;
                break;
            default:
                $this->db->where("auditorias_auditor_lider", $empleados_id);
                break;
        }
        $this->db->group_by("auditorias_id");
        $return = parent::getResultados($limit, $start);
        return $return;
    }

    function get_documentos($auditorias_id = NULL) {
        $return = array();
        if (empty($auditorias_id) && isset($this->session->{APP_NAMESPACE}[$this->id_field])) {
            $auditorias_id = $this->session->{APP_NAMESPACE}[$this->id_field];
        }
        if (!empty($auditorias_id)) {
            $return = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id);
        }
        return $return;
    }

    function get_real_auditoria($auditoria = NULL) {
        $return = $auditoria;
        if (is_numeric($auditoria)) {
            $return = intval($auditoria);
        } else {
            $aux = explode("-", $auditoria);
            switch (count($aux)) {
                case 0;
                case 1:
                case 2:
                case 3: break;
                case 5:
                    $auditorias_area = $aux[0] . "/" . $aux[1];
                    $auditorias_tipo = $aux[2];
                    $auditorias_numero = intval($aux[3]);
                    $auditorias_anio = intval($aux[4]);
                    break;
                default:
                    $auditorias_area = $aux[0];
                    $auditorias_tipo = $aux[1];
                    $auditorias_numero = intval($aux[2]);
                    $auditorias_anio = intval($aux[3]);
            }
            if (count($aux) >= 4) {
                $result = $this->db
                        ->select($this->id_field)
                        ->join("auditorias_areas aa", "aa.auditorias_areas_id = auditorias_area", "INNER")
                        ->join("auditorias_tipos at", "at.auditorias_tipos_id = auditorias_tipo", "INNER")
                        ->where("auditorias_areas_siglas", $auditorias_area)
                        ->where("auditorias_tipos_siglas", $auditorias_tipo)
                        ->where("auditorias_numero", $auditorias_numero)
                        ->where("auditorias_anio", $auditorias_anio)
                        ->get($this->table_name);
                if ($result && $result->num_rows() > 0) {
                    $return = $result->row()->{$this->id_field};
                }
            }
        }
        return $return;
    }

    /**
     * Obtiene los años de mis auditorias
     * @param integer $empleados_id Identificador del auditor lider. Por defaul se usa el del usuario de la sesión activa
     * @return array Devuelve un listado con los años de las auditorias, total de auditorías por año, status de las auditorías y la etiqueta del status
     */
    function get_anios_de_mis_auditorias($empleados_id = NULL) {
        $return = array();
        if (empty($empleados_id)) {
            $this->db->where($this->table_prefix . ".auditorias_auditor_lider", $this->session->userdata("empleados_id"));
        }
        $result = $this->db
                ->select("a.auditorias_anio, COUNT(a.auditorias_id) AS 'total', a.auditorias_status_id")
                ->join("auditorias_status aus", "aus.auditorias_status_id=" . $this->table_prefix . ".auditorias_status_id", "INNER")->select("aus.auditorias_status_nombre")
                ->where_in($this->table_prefix . ".auditorias_status_id", array(
                    AUDITORIAS_STATUS_EN_PROCESO,
                    AUDITORIAS_STATUS_FINALIZADA,
                    AUDITORIAS_STATUS_FINALIZADA_RESERVADA,
                    AUDITORIAS_STATUS_FINALIZADA_MANUAL
                ))
                ->group_by($this->table_prefix . ".auditorias_anio")
                ->group_by("CASE WHEN " . $this->table_prefix . ".auditorias_status_id = 1 THEN 1 ELSE 2 END", FALSE)
                ->order_by($this->table_prefix . ".auditorias_status_id", "ASC")
                ->order_by($this->table_prefix . ".auditorias_anio", "DESC")
                ->get($this->table_name . " " . $this->table_prefix);
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
        }
        return $return;
    }

    /**
     * Obtiene el listado de auditorias de un año específico
     * @param integer $anio Año en que inició la auditoría. Cuando año es vacío se regresan las auditorías de todos los años
     * @param integer $empleados_id Identificador del empleado, Cuando es NULL se obtiene el de todos los empleados
     * @param integer|array $status_id Arreglo con los identificadores de los status de auditoria. De forma predeterminada se devuelven todos los status
     * @return array Devuelve un arreglo con las auditorías
     */
    function get_mis_auditorias($anio = NULL, $empleados_id = NULL, $status_id = NULL) {
        $return = array();
        if (!empty($anio)) {
            $this->db->where("auditorias_anio", $anio);
        }
        if (empty($empleados_id)) {
            $empleados_id = $this->session->userdata('empleados_id');
        }
        $this->db->where($this->table_prefix . ".auditorias_auditor_lider", $empleados_id);
        if (!empty($status_id)) {
            if (is_array($status_id)) {
                $this->db->where_in("auditorias_status_id", $status_id);
            } else {
                $this->db->where("auditorias_status_id", $status_id);
            }
        } else {
            $this->db->where("auditorias_status_id >", 0);
        }
        $return = $this->getResultados(NULL, NULL);
        return $return;
    }

    function get_auditoria($auditorias_id) {
        return $this->Auditorias_model->get_auditoria($auditorias_id);
    }

    function asignar_enlace_designado($auditorias_id = NULL, $empleados_id = NULL) {
        if (empty($auditorias_id)) {
            $cysa = $this->session->userdata('cysa');
            $auditorias_id = $cysa['auditorias_id'];
        }
        return $this->Auditorias_model->asignar_enlace_designado($auditorias_id, $empleados_id);
    }

    function set_empleados_involucrados($auditorias_id = NULL, $empleados_id = NULL) {
        $return = array();
        if (empty($auditorias_id)) {
            $cysa = $this->session->userdata('cysa');
            $auditorias_id = $cysa['auditorias_id'];
        }
        return $this->Auditorias_involucrados_model->set_empleados_involucrados($auditorias_id, $empleados_id);
    }

}
