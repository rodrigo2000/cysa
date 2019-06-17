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
        $this->db
                ->join("auditorias_status aus", "aus.auditorias_status_id=" . $this->table_prefix . ".auditorias_status_id", "INNER")->select("aus.auditorias_status_nombre")
                ->join("auditorias_areas aa", "aa.auditorias_areas_id = " . $this->table_prefix . ".auditorias_area", "INNER")->select("aa.auditorias_areas_siglas")
                ->join("auditorias_tipos at", "at.auditorias_tipos_id = " . $this->table_prefix . ".auditorias_tipo", "INNER")->select("at.auditorias_tipos_nombre, at.auditorias_tipos_siglas")
                ->join(APP_DATABASE_PREFIX . APP_DATABASE_SAC . ".empleados e", "e.empleados_id = a.auditorias_auditor_lider", "INNER");
        $return = parent::getResultados($limit, $start);
        return $return;
    }

    function get_documentos($auditorias_id = NULL, $documentos_tipos_id = NULL) {
        $return = array();
        if (empty($auditorias_id) && isset($this->session->{APP_NAMESPACE}[$this->id_field])) {
            $auditorias_id = $this->session->{APP_NAMESPACE}[$this->id_field];
        }
        if (!empty($auditorias_id)) {
            $return = $this->Documentos_model->get_documentos_de_auditoria($auditorias_id, $documentos_tipos_id);
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
        $result = $this->db
                ->select("a.auditorias_anio, COUNT(a.auditorias_id) AS 'total', a.auditorias_status_id")
                ->group_by("CASE WHEN " . $this->table_prefix . ".auditorias_status_id = 1 THEN 1 ELSE 2 END", FALSE)
                ->group_by($this->table_prefix . ".auditorias_anio")
                ->order_by($this->table_prefix . ".auditorias_status_id", "ASC")
                ->order_by($this->table_prefix . ".auditorias_anio", "DESC");
        $return = $this->get_mis_auditorias(NULL, $empleados_id, NULL, FALSE);
        return $return;
    }

    /**
     * Obtiene el listado de auditorias de un año específico
     * @param integer $anio Año en que inició la auditoría. Cuando año es vacío se regresan las auditorías de todos los años
     * @param integer $empleados_id Identificador del empleado, Cuando es NULL se obtiene el de todos los empleados
     * @param integer|array $status_id Arreglo con los identificadores de los status de auditoria. De forma predeterminada se devuelven todos los status
     * @param boolean $is_agrupado TRUE Indica que las auditorias que tendrá en GROUP BY a.auditorias_id. FALSE en caso contrario.
     * @return array Devuelve un arreglo con las auditorías
     */
    function get_mis_auditorias($anio = NULL, $empleados_id = NULL, $status_id = NULL, $is_agrupado = TRUE) {
        $return = array();
        if (!empty($anio)) {
            $this->db->where($this->table_prefix . ".auditorias_anio", $anio);
        }
        if (empty($empleados_id)) {
            $empleados_id = $this->session->userdata('empleados_id');
        }
        if ($is_agrupado) {
            $this->db->group_by($this->table_prefix . ".auditorias_id");
        }
        if (!is_null($status_id)) {
            if (is_array($status_id)) {
                $this->db->where_in($this->table_prefix . ".auditorias_status_id", $status_id);
            } else {
                $this->db->where($this->table_prefix . ".auditorias_status_id", $status_id);
            }
        } else {
            $this->db->where($this->table_prefix . ".auditorias_status_id >", 0);
        }
        $this->db->select($this->table_prefix . ".*")
                ->select("CONCAT(LPAD(a.auditorias_numero,3,'0'),' - ',IF(a.auditorias_segundo_periodo=1,'2',''), aa.auditorias_areas_siglas,'/',at.auditorias_tipos_siglas,'/', LPAD(a.auditorias_numero,3,'0'),'/',a.auditorias_anio) AS 'numero_auditoria'")
                ->select("CASE WHEN a.auditorias_tipo < 4 THEN 1 ELSE 2 END AS 'tipo_auditoria'")
                ->order_by("a.auditorias_anio", "DESC")
                ->order_by("tipo_auditoria", "ASC")
                ->order_by("a.auditorias_segundo_periodo", "DESC")
                ->order_by("a.auditorias_numero", "ASC");
        switch ($this->session->userdata('perfiles_id')) {
            case USUARIO_PERFIL_ADMNISTRADOR:
                break;
            case USUARIO_PERFIL_RESGUARDANTE_BODEGA:
                $this->db->where("auditorias_auditor_lider", $empleados_id);
                break;
            case USUARIO_PERFIL_EMPLEADO_CONTRALORIA:
                $puestos_id = intval($this->session->userdata('puestos_id'));
                $cc_id = intval($this->session->userdata('cc_id'));
                switch ($puestos_id) {
                    case PUESTO_SUBDIRECTOR:
                    case PUESTO_JEFE_DEPARTAMENTO:
                        $cc_label = $this->session->userdata('cc_label');
                        if ($cc_label !== '5.3.3') { // Si es de TI, entonces mostramos todos
                            list($d, $s, $dd) = explode(".", $cc_label);
                            $puestos = array(
                                PUESTO_COORDINADOR,
                                PUESTO_COORDINADOR_AUDITORIA,
                                PUESTO_AUDITOR,
                                PUESTO_AUXILIAR_DE_AUDITORIA
                            );
                            $empleados_cc = $this->SAC_model->get_empleados_cc_label($d, $s, $dd, $puestos);
                            $empleados_ids = array_column($empleados_cc, 'empleados_id', 'empleados_id');
                            $this->db
                                    ->join("auditorias_equipo ae", "ae.auditorias_equipo_auditorias_id = auditorias_id", "LEFT")
                                    ->group_start()
                                    ->where_in("auditorias_auditor_lider", $empleados_ids)
                                    ->or_where_in("ae.auditorias_equipo_empleados_id", $empleados_ids)
                                    ->group_end();
                        }
                        break;
                    case PUESTO_COORDINADOR:
                    case PUESTO_COORDINADOR_AUDITORIA:
                        list($d, $s, $dd) = explode(".", $this->session->userdata('cc_label'));
                        $puestos = array(
                            PUESTO_COORDINADOR,
                            PUESTO_COORDINADOR_AUDITORIA,
                            PUESTO_AUDITOR,
                            PUESTO_AUXILIAR_DE_AUDITORIA
                        );
                        $empleados_cc = $this->SAC_model->get_empleados_cc_label($d, $s, $dd, $puestos);
                        $empleados_ids = array_column($empleados_cc, 'empleados_id', 'empleados_id');
                        if (in_array($empleados_id, array(4418, 5358))) {
                            $empleados_ids = array(4418, 5358);
                        } else {
                            unset($empleados_ids[4418], $empleados_ids[5358]);
                        }
                        $this->db
                                ->join("auditorias_equipo ae", "ae.auditorias_equipo_auditorias_id = auditorias_id", "LEFT")
                                ->group_start()
                                ->where_in("auditorias_auditor_lider", $empleados_ids)
                                ->or_where_in("ae.auditorias_equipo_empleados_id", $empleados_ids)
                                ->group_end();
                        break;
                    case PUESTO_AUDITOR:
                    case PUESTO_AUXILIAR_DE_AUDITORIA:
                        $this->db->where("auditorias_auditor_lider", $empleados_id);
                        break;
                    default:
                }
                break;
            default:
                $this->db->where("auditorias_auditor_lider", $empleados_id);
                break;
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

    /**
     * Permite autorizar o desautorizar un documento
     * @param integer $documentos_id Identificador del documento
     * @param integer $valor 1=Autorizado, 0=Desautorizado, NULL=Cualquier otro caso
     * @return boolean Regresa TRUE cuando se pudo autorizar/desautorizar el documento
     */
    function autorizar_documento($documentos_id, $valor = NULL) {
        $return = FALSE;
        if (!empty($documentos_id)) {
            $r = $this->db
                    ->set("documentos_is_aprobado", $valor)
                    ->where("documentos_id", $documentos_id)
                    ->update("documentos");
            if ($this->db->affected_rows() == 1) {
                $return = TRUE;
            }
        }
        return $return;
    }

    function get_auditoria_de_seguimiento($auditorias_id = NULL) {
        $return = array();
        if (empty($auditorias_id)) {
            $cysa = $this->session->userdata('cysa');
            $auditorias_id = $cysa['auditorias_id'];
        }
        $result = $this->db->select($this->id_field)
                ->where("auditorias_origen_id", $auditorias_id)
                ->where("fecha_delete", NULL)
                ->get($this->table_name . " " . $this->table_prefix);
        if ($result && $result->num_rows() > 0) {
            $aux = $result->row()->{$this->id_field};
            $return = $this->get_auditoria($aux);
        }
        return $return;
    }

    function get_involucrados() {
        $cysa = $this->session->userdata('cysa');
        $auditorias_id = $cysa['auditorias_id'];
        $return = $this->Auditorias_model->get_involucrados($auditorias_id);
        return $return;
    }

    function get_etapa() {
        $cysa = $this->session->userdata(APP_NAMESPACE);
        $auditorias_id = $cysa['auditorias_id'];
        return $this->Auditorias_model->get_etapa_de_auditoria($auditorias_id);
    }

}
