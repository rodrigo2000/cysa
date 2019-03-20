<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class SAC_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->dbSAC = $this->getDatabase(APP_NAMESPACE_SAC);
    }

    function get_ultimo_periodo() {
        $return = array();
        $result = $this->dbSAC
                ->where("fecha_delete IS NULL")
                ->order_by("periodos_fecha_fin", "DESC")
                ->limit(1)
                ->get("periodos");
        if ($result && $result->num_rows() == 1) {
            $return = $result->row_array();
        }
        return $return;
    }

    /**
     * Obtiene los periodos registrados en la base de datos
     * @param boolean $incluir_eliminados TRUE indica que se incluirán los registros que incluso tiene fecha_delete con valor. FALSE para ocultar esos registros
     * @return array Arreglo que contiene los periodos del ayuntamiento
     */
    function get_periodos($incluir_eliminados = FALSE) {
        $result = $this->dbSAC
                ->order_by("periodos_fecha_inicio", "ASC")
                ->get("periodos");
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
        }
        return $return;
    }

    /**
     * Obtiene las direcciones de un período
     * @param integer $periodos_id Identificador del período
     * @param boolean $incluir_eliminados TRUE indica que se incluirán los registros que incluso tiene fecha_delete con valor. FALSE para ocultar esos registros
     * @return array Arreglo que contiene las direcciones
     */
    function get_direcciones_de_periodo($periodos_id = NULL, $incluir_eliminados = FALSE) {
        $return = array();
        if (!empty($periodos_id)) {
            if (!$incluir_eliminados) {
                $this->dbSAC->where("cc.fecha_delete IS NULL");
            }
            $result = $this->dbSAC
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")
                    ->where("cc_periodos_id", $periodos_id)
                    ->group_by('cc_direcciones_id')
                    ->order_by("cc_etiqueta_direccion", "ASC")
                    ->order_by("direcciones_nombre", "ASC")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    /**
     * Obtiene las subdirecciones de una dirección y un período proporcionado
     * @param integer $periodos_id Identificador del período
     * @param intener $direcciones_id Identificador de la dirección
     * @param boolean $incluir_eliminados TRUE indica que se incluirán los registros que incluso tiene fecha_delete con valor. FALSE para ocultar esos registros
     * @return array Arreglo que contiene las subdirecciones
     */
    function get_subdirecciones_de_direccion($periodos_id = NULL, $direcciones_id = NULL, $incluir_eliminados = FALSE) {
        $return = array();
        if (!empty($periodos_id) && !empty($direcciones_id)) {
            if (!$incluir_eliminados) {
                $this->dbSAC->where('cc.fecha_delete', NULL);
            }
            $result = $this->dbSAC
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")
                    ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "INNER")
                    ->where("cc.cc_periodos_id", $periodos_id)
                    ->where("cc.cc_direcciones_id", $direcciones_id)
                    ->group_by("cc.cc_subdirecciones_id")
                    ->order_by("cc_etiqueta_direccion", "ASC")
                    ->order_by("cc_etiqueta_subdireccion", "ASC")
                    ->order_by("subdirecciones_nombre", "ASC")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    /**
     * Obtiene los departamentos de una subdirección de una dirección y un período proporcionado
     * @param integer $periodos_id Identificador del período
     * @param intener $direcciones_id Identificador de la dirección
     * @param integet $subdirecciones_id Identificador de la subdirección
     * @param boolean $incluir_eliminados TRUE indica que se incluirán los registros que incluso tiene fecha_delete con valor. FALSE para ocultar esos registros
     * @return array Arreglo que contiene los departamentos
     */
    function get_departamentos_de_subdireccion($periodos_id = NULL, $direcciones_id = NULL, $subdirecciones_id = NULL, $incluir_eliminados = FALSE) {
        $return = array();
        if (!empty($periodos_id) && !empty($direcciones_id) && !empty($subdirecciones_id)) {
            if (!$incluir_eliminados) {
                $this->dbSAC->where('cc.fecha_delete', NULL);
            }
            $result = $this->dbSAC
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")
                    ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "INNER")
                    ->join("departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "INNER")
                    ->where("cc.cc_periodos_id", $periodos_id)
                    ->where("cc.cc_direcciones_id", $direcciones_id)
                    ->where("cc.cc_subdirecciones_id", $subdirecciones_id)
//                ->group_by("cc.cc_departamentos_id")
                    ->order_by("cc_etiqueta_direccion", "ASC")
                    ->order_by("cc_etiqueta_subdireccion", "ASC")
                    ->order_by("cc_etiqueta_departamento", "ASC")
                    ->order_by("departamentos_nombre", "ASC")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_empleados_de_cc2($periodos_id = NULL, $direcciones_id = NULL, $subdirecciones_id = NULL, $departamentos_id = NULL, $incluir_eliminados = FALSE) {
        $return = array();
        if (!empty($periodos_id)) {
            if (!empty($direcciones_id)) {
                $this->dbSAC->where("cc_direcciones_id", $direcciones_id);
            }
            if (!empty($subdirecciones_id)) {
                $this->dbSAC->where("cc_subdirecciones_id", $subdirecciones_id);
            }
            if (!empty($departamentos_id)) {
                $this->dbSAC->where("cc_departamentos_id", $departamentos_id);
            }
            $result = $this->dbSAC->select("cc.*")
                    ->where("cc.fecha_delete IS NULL")
                    ->where("e.fecha_delete IS NULL")
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")
                    ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "INNER")
                    ->join("departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "INNER")
                    ->join("empleados e", "e.empleados_cc_id = cc_id")->select("e.*, CONCAT(empleados_nombre, ' ', e.empleados_apellido_paterno, ' ', e.empleados_apellido_materno) AS 'empleados_nombre_completo'")
                    ->order_by("departamentos_nombre", "ASC")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_direccion($direcciones_id) {
        $return = array();
        if (!empty($direcciones_id)) {
            $result = $this->dbSAC
                    ->where("direcciones_id", $direcciones_id)
                    ->limit(1)
                    ->get("direcciones");
            if ($result && $result->num_rows() == 1) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function get_cc_direcciones($periodos_id) {
        $return = array();
        if (!empty($periodos_id)) {
            $result = $this->dbSAC->select("cc.*")
                    ->where("cc.fecha_delete IS NULL")
                    ->where("cc_periodos_id", $periodos_id)
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")->select("d.*")
                    ->order_by("direcciones_id", "ASC")
                    ->group_by("cc.cc_direcciones_id")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_cc_subdirecciones($direcciones_id) {
        $return = array();
        if (!empty($direcciones_id)) {
            $result = $this->dbSAC->select("cc.*")
                    ->where("cc.fecha_delete IS NULL")
                    ->where("cc_direcciones_id", $direcciones_id)
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")
                    ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "INNER")->select("s.*")
                    ->order_by("direcciones_nombre", "ASC")
                    ->group_by("cc.cc_subdirecciones_id")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_cc_empleados($direcciones_id, $subdirecciones_id, $departamentos_id) {
        $return = array();
        if (!empty($direcciones_id) && !empty($subdirecciones_id) && !empty($departamentos_id)) {
            $result = $this->dbSAC->select("cc.*")
                    ->where("cc.fecha_delete IS NULL")
                    ->where("e.fecha_delete IS NULL")
                    ->where("cc_direcciones_id", $direcciones_id)
                    ->where("cc_subdirecciones_id", $subdirecciones_id)
                    ->where("cc_departamentos_id", $departamentos_id)
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")
                    ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "INNER")
                    ->join("departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "INNER")
                    ->join("empleados e", "e.empleados_cc_id = cc_id")->select("e.*, CONCAT(empleados_nombre, ' ', e.empleados_apellido_paterno, ' ', e.empleados_apellido_materno) AS 'empleados_nombre_completo'")
                    ->order_by("departamentos_nombre", "ASC")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_cc_departamentos($direcciones_id, $subdirecciones_id) {
        $return = array();
        if (!empty($direcciones_id) && !empty($subdirecciones_id)) {
            $result = $this->dbSAC
                    ->where("cc.fecha_delete IS NULL")
                    ->where("cc_direcciones_id", $direcciones_id)
                    ->where("cc_subdirecciones_id", $subdirecciones_id)
                    ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "INNER")
                    ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "INNER")
                    ->join("departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "INNER")
                    ->order_by("departamentos_nombre", "ASC")
                    ->get("centros_costos cc");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_cc($cc_id) {
        $return = array();
        if (!empty($cc_id)) {
            $result = $this->dbSAC
                    ->where("cc_id", $cc_id)
                    ->where("fecha_delete IS NULL")
                    ->limit(1)
                    ->get("centros_costos");
            if ($result && $result->num_rows() > 0) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function get_cc2($periodos_id, $direcciones_id, $subdirecciones_id, $departamentos_id) {
        $return = array();
        if (empty($subdirecciones_id)) {
            $subdirecciones_id = 1;
        }
        if (empty($departamentos_id)) {
            $departamentos_id = 1;
        }
        if (!empty($periodos_id) && !empty($direcciones_id) && !empty($subdirecciones_id) && !empty($departamentos_id)) {
            $result = $this->dbSAC
                    ->where("cc_periodos_id", $periodos_id)
                    ->where("cc_etiqueta_direccion", $direcciones_id)
                    ->where("cc_etiqueta_subdireccion", $subdirecciones_id)
                    ->where("cc_etiqueta_departamento", $departamentos_id)
                    ->get("centros_costos");
            if ($result && $result->num_rows() == 1) {
                $return = $result->row_array();
            }
        }
        return $return;
    }

    function get_cc_asociados_a_direccion($direcciones_id, $periodos_id = NULL) {
        $return = FALSE;
        if (empty($periodos_id)) {
            $periodos_id = 2;
        }
        if (!empty($periodos_id) && !empty($direcciones_id)) {
            $result = $this->dbSAC
                    ->select("cc_id")
                    ->where("cc_periodos_id", $periodos_id)
                    ->where("cc_etiqueta_direccion", $direcciones_id)
                    ->get("centros_costos");
            if ($result && $result->num_rows() > 0) {
                $return = $result->result_array();
            }
        }
        return $return;
    }

    function get_auditores($mostrar_bajas = FALSE) {
        $return = array();
        $puestos = array(
            PUESTO_AUDITOR,
            PUESTO_JEFE_DEPARTAMENTO,
            PUESTO_COORDINADOR,
            PUESTO_COORDINADOR_AUDITORIA,
            PUESTO_AUXILIAR_DE_AUDITORIA,
            PUESTO_SUBDIRECTOR
        );
        if (!$mostrar_bajas) {
            $this->dbSAC
                    ->group_start()
                    ->where('e.fecha_delete', NULL)
                    ->or_where('e.empleados_fecha_baja', NULL)
                    ->group_end();
        }
        $this->dbSAC->where_in("e.empleados_puestos_id", $puestos)
                ->order_by("nombre_completo", "ASC");
        $return = $this->get_empleados($mostrar_bajas);
        return $return;
    }

    /**
     * Devuelve los empleados de un centro de costos
     * @param integer $periodos_id Identificador del período
     * @param integer $direcciones_id Identificador de la dirección
     * @param integer $subdirecciones_id Identificador de la subdirección
     * @param integer $departamentos_id Identificador del departamento
     * @param bool $mostrar_bajas TRUE para regresar también empleados de baja. Por default solo muestra empleados activos
     * @return array Listado de empleados
     */
    function get_empleados_de_UA($periodos_id = NULL, $direcciones_id = NULL, $subdirecciones_id = NULL, $departamentos_id = NULL, $mostrar_bajas = FALSE) {
        $return = array();
        $session = $this->session->userdata(APP_NAMESPACE);
        if (empty($periodos_id)) {
            $periodos_id = $this->get_ultimo_periodo();
        }
        if (empty($direcciones_id)) {
            $direcciones_id = $session['direcciones_id'];
        }
        if (empty($subdirecciones_id)) {
            $subdirecciones_id = $session['subdirecciones_id'];
        }
        if (empty($departamentos_id)) {
            $departamentos_id = $session['departamentos_id'];
        }
        if (!$mostrar_bajas) {
            $this->dbSAC->where("e.empleados_fecha_baja IS NULL");
        }
        $result = $this->dbSAC
                ->join("empleados e", "e.empleados_cc_id = cc.cc_id", "INNER")->select("e.*, CONCAT(e.empleados_nombre,' ',e.empleados_apellido_paterno, ' ',e.empleados_apellido_materno) AS 'nombre_completo'")
                ->join("titulos t", "t.titulos_id = e.empleados_titulos_id", "LEFT")->select("t.*")
                ->join("puestos p", "p.puestos_id = e.empleados_puestos_id", "LEFT")->select("p.puestos_nombre")
                ->where('cc_periodos_id', $periodos_id)
                ->where('cc_direcciones_id', $direcciones_id)
                ->where('cc_subdirecciones_id', $subdirecciones_id)
                ->where('cc_departamentos_id', $departamentos_id)
                ->get("centros_costos cc");
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
        }
        return $return;
    }

    function get_auditores_agrupados_por_cc() {
        $return = array();
        $periodo = $this->get_ultimo_periodo();
        $puestos = array(7, 269, 13);
        $result = $this->dbSAC
                ->join("centros_costos cc", "cc.cc_id = e.empleados_cc_id", "INNER")
                ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "INNER")
                ->join("departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "INNER")
                ->where_in("empleados_puestos_id", $puestos)
                ->where("cc_periodos_id", 2)
                ->where("e.fecha_delete IS NULL")
                ->where("cc.fecha_delete IS NULL")
                ->order_by("cc_etiqueta_subdireccion", "ASC")
                ->order_by("cc_etiqueta_departamento", "ASC")
                ->order_by("empleados_nombre", "ASC")
                ->get("empleados e");
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
        }
        return $return;
    }

    /**
     * Busca los empleados dentro del sistema
     * @param boolean $incluirEliminados
     * @return array Devuelve la información relacionada con los empleados. FALSE en cualquier otro caso.
     */
    function get_empleados($incluirEliminados = FALSE) {
        $return = array();
        $this->dbSAC->select("e.*, CONCAT(e.empleados_nombre,' ',e.empleados_apellido_paterno, ' ',e.empleados_apellido_materno) AS 'nombre_completo'")
                ->join("titulos t", "t.titulos_id = " . "e.empleados_titulos_id", "LEFT")->select("t.*")
                ->join("titulos subt", "subt.titulos_id = " . "e.empleados_titulos_id", "LEFT")->select("subt.*")
                ->join("centros_costos cc", "cc.cc_id = " . "e.empleados_cc_id", "LEFT")->select("cc.*")
                ->join("puestos p", "p.puestos_id = " . "e.empleados_puestos_id", "LEFT")->select("p.puestos_nombre")
//                ->join("empleados_cc_historico ecch", "ecch.historico_" . $this->id_field . " = " . "e." . $this->id_field, "LEFT")->select("ecch.historico_fecha_baja")->where("ecch.historico_fecha_baja IS NULL")
                ->join("direcciones d", "d.direcciones_id = cc.cc_direcciones_id", "LEFT")->select("direcciones_nombre, direcciones_nombre_generico, direcciones_ubicacion, direcciones_is_descentralizada")
                ->join("subdirecciones s", "s.subdirecciones_id = cc.cc_subdirecciones_id", "LEFT")->select("s.subdirecciones_nombre")
                ->join("departamentos dd", "dd.departamentos_id = cc.cc_departamentos_id", "LEFT")->select("dd.departamentos_nombre");
        if (!$incluirEliminados) {
            $this->dbSAC->where("e.fecha_delete IS NULL");
        }
        $result = $this->dbSAC->get("empleados e");
        if ($result && $result->num_rows() > 0) {
            $return = $result->result_array();
            foreach ($return as $index => $row) {
                get_nombre_titulado($return[$index]);
                get_cargo_de_empleado($return[$index]);
                get_siglas_de_empleado($return[$index]);
            }
        }
        return $return;
    }

    /**
     * Devuelve la información de un empleado
     * @param integer $empleados_id Identificador del empleado
     * @param boolen $is_numero_empleado TRUE cuando el valor de
     * @return array Información del empleado
     */
    function get_empleado($empleados_id, $is_numero_empleado = FALSE) {
        $return = array();
        if (!empty($empleados_id)) {
            if ($is_numero_empleado) {
                $this->dbSAC->where("empleados_numero_empleado", $empleados_id);
            } else {
                $this->dbSAC->where("empleados_id", $empleados_id);
            }
            $aux = $this->get_empleados();
            if (!empty($aux)) {
                $return = $aux[0];
            }
        }
        return $return;
    }

    /**
     * Devuelve los empleados que pertenecen al centro de costos
     * @param integer $cc_id Identificador del centro de costos
     * @param boolean $incluir_bajas TRUE para devolver tambien los empleados que tiene fecha de baja. De forma predeterminada solo devuelve los empleados activos
     * @param integer $puestos_id Identificador del puesto. Cuando se especifica, filtro a los empleados que tienen ese puesto
     * @return arary Listado de empleados
     */
    function get_empleados_de_cc($cc_id, $incluir_bajas = FALSE, $puestos_id = NULL) {
        $return = array();
        if ($incluir_bajas === FALSE) {
            $this->dbSAC->where("e.empleados_fecha_baja IS NULL");
        }
        if (!empty($puestos_id)) {
            $this->dbSAC->where("e.empleados_puestos_id", $puestos_id);
        }
        if (!empty($empleados_id)) {
            $return = $this->get_empleados();
        }
        return $return;
    }

    /**
     * Obtiene la información del director de una Unidad Administrativa
     * @param intener $direcciones_id Identificador de la dirección
     * @param integer $periodos_id IDentificador del período
     * @return array
     */
    function get_director_de_ua($direcciones_id, $periodos_id = NULL) {
        $return = array();
        if (empty($periodos_id)) {
            $p = $this->get_ultimo_periodo();
            $periodos_id = $p['periodos_id'];
        }
        if (!empty($direcciones_id)) {
            $this->dbSAC
                    ->where("cc.cc_periodos_id", $periodos_id)
                    ->where("cc.cc_direcciones_id", $direcciones_id)
                    ->where("e.empleados_puestos_id", PUESTO_DIRECTOR)
                    ->where("e.empleados_fecha_baja IS NULL");
            $aux = $this->get_empleados();
            if (!empty($aux)) {
                $return = $aux[0];
            }
        }
        return $return;
    }

    function get_subdirector_de_empleado($empleados_id) {
        $return = array();
        $empleado = $this->get_empleado($empleados_id);
        if (!in_array($empleado['empleados_puestos_id'], array(PUESTO_SUBDIRECTOR, PUESTO_DIRECTOR))) {
            $this->dbSAC
                    ->where("cc.cc_periodos_id", $empleado['cc_periodos_id'])
                    ->where("cc.cc_direcciones_id", $empleado['cc_direcciones_id'])
                    ->where("cc.cc_subdirecciones_id", $empleado['cc_subdirecciones_id'])
                    ->where("e.empleados_puestos_id", PUESTO_SUBDIRECTOR)
                    ->where("e.empleados_fecha_baja IS NULL");
            $aux = $this->get_empleados();
            if (!empty($aux)) {
                $return = $aux[0];
            }
        }
        return $return;
    }

    function get_jefe_de_empleado($empleados_id) {
        $return = array();
        $empleado = $this->get_empleado($empleados_id);
        if (!in_array($empleado['empleados_puestos_id'], array(PUESTO_JEFE_DEPARTAMENTO, PUESTO_SUBDIRECTOR, PUESTO_DIRECTOR))) {
            $this->dbSAC
                    ->where("cc.cc_periodos_id", $empleado['cc_periodos_id'])
                    ->where("cc.cc_direcciones_id", $empleado['cc_direcciones_id'])
                    ->where("cc.cc_subdirecciones_id", $empleado['cc_subdirecciones_id'])
                    ->where("cc.cc_departamentos_id", $empleado['cc_departamentos_id'])
                    ->where("e.empleados_puestos_id", PUESTO_JEFE_DEPARTAMENTO)
                    ->where("e.empleados_fecha_baja IS NULL");
            $aux = $this->get_empleados();
            if (!empty($aux)) {
                $return = $aux[0];
            }
        }
        return $return;
    }

    function get_coordinador_de_empleado($empleados_id, $auditorias_id) {
        $return = array();
        $empleado = $this->get_empleado($empleados_id);
        if (!in_array($empleado['empleados_puestos_id'], array(PUESTO_COORDINADOR, PUESTO_COORDINADOR_AUDITORIA, PUESTO_JEFE_DEPARTAMENTO, PUESTO_SUBDIRECTOR, PUESTO_DIRECTOR))) {
            $this->dbSAC
                    ->join(APP_DATABASE_PREFIX . APP_DATABASE_CYSA . ".auditorias_equipo ae", "ae.auditorias_equipo_empleados_id = e.empleados_numero_empleado", "INNER")
                    ->where("cc.cc_periodos_id", $empleado['cc_periodos_id'])
                    ->where("cc.cc_direcciones_id", $empleado['cc_direcciones_id'])
                    ->where("cc.cc_subdirecciones_id", $empleado['cc_subdirecciones_id'])
                    ->where("cc.cc_departamentos_id", $empleado['cc_departamentos_id'])
                    ->where("e.empleados_puestos_id", PUESTO_COORDINADOR_AUDITORIA)
                    ->where("ae.auditorias_equipo_auditorias_id", $auditorias_id)
                    ->where("e.empleados_fecha_baja IS NULL");
            $aux = $this->get_empleados();
            if (!empty($aux)) {
                $return = $aux[0];
            }
        }
        return $return;
    }

}
