<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class CYSA_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->table_name = NULL;
        $this->id_field = NULL;
        $this->table_prefix = NULL;
        $this->model_name = __CLASS__;
    }

    function get_director_de_contraloria($periodos_id = NULL) {
        $return = array();
        if (empty($periodos_id)) {
            $p = $this->SAC_model->get_ultimo_periodo();
            $periodos_id = intval($p['periodos_id']);
        }
        $this->dbSAC = $this->getDatabase(APP_NAMESPACE_SAC);
        $result = $this->dbSAC
                ->where("cc_periodos_id", $periodos_id)
                ->where("cc_direcciones_id", APP_DIRECCION_CONTRALORIA)
                ->where("cc_etiqueta_subdireccion", 1)
                ->where("cc_etiqueta_departamento", 1)
                ->where("cc.fecha_delete IS NULL")
                ->order_by("cc_periodos_id", "DESC")
                ->limit(1)
                ->get("centros_costos cc");
        if ($result && $result->num_rows() > 0) {
            $cc = $result->row_array();
            $return = $this->SAC_model->get_empleado($cc['cc_empleados_id']);
        }
        return $return;
    }

    function get_subdirector_de_contraloria($periodos_id = NULL) {
        $return = array();
        if (empty($periodos_id)) {
            $p = $this->SAC_model->get_ultimo_periodo();
            $periodos_id = intval($p['periodos_id']);
        }
        $this->dbSAC = $this->getDatabase(APP_NAMESPACE_SAC);
        $result = $this->dbSAC
                ->where("cc_periodos_id", $periodos_id)
                ->where("cc_direcciones_id", APP_DIRECCION_CONTRALORIA)
                ->where("cc_etiqueta_subdireccion", 2)
                ->where("cc_etiqueta_departamento", 1)
                ->where("cc.fecha_delete IS NULL")
                ->order_by("cc_periodos_id", "DESC")
                ->limit(1)
                ->get("centros_costos cc");
        if ($result && $result->num_rows() > 0) {
            $cc = $result->row_array();
            $return = $this->SAC_model->get_empleado($cc['cc_empleados_id']);
        }
        return $return;
    }

    /**
     * Devuelve el texto predeterminado que se usa como CCP en los oficios generados por CYSA.
     */
    function get_ccp_template() {
        $ccp_texto_plantilla = "";
        $alcalde = get_presidente_municipal();
        if (!empty($alcalde)) {
            $alcalde = $this->SAC_model->get_empleado($alcalde['empleados_id']);
            $ccp_texto_plantilla = capitalizar($alcalde['nombre_completo']) . " / " . capitalizar($alcalde['puestos_nombre']) . ".";
        } else {
            $ccp_texto_plantilla = "Nombre del Presidente Municipal/ Puesto.";
        }
        $sindico = get_sindico();
        if (!empty($sindico)) {
            $sindico = $this->SAC_model->get_empleado($sindico['empleados_id']);
            $ccp_texto_plantilla .= PHP_EOL . capitalizar($sindico['nombre_completo']) . " / " . capitalizar($sindico['puestos_nombre']) . ".";
        } else {
            $ccp_texto_plantilla .= PHP_EOL . "Nombre del Síndico / Puesto.";
        }
        $ccp_texto_plantilla .= PHP_EOL . "Nombre(s) del Titular(es) involucrado(s) en la auditoría/ Puesto.";
        $return = $ccp_texto_plantilla;
        return $return;
    }

    function get_mision($misiones_id = NULL) {
        $return = "";
        if(empty($misiones_id)){

        }
        $return = "Gobernar el Municipio de Mérida, con un enfoque de vanguardia que procure el desarrollo humano sustentable, con
        servicios públicos de calidad, una infraestructura funcional y una administración austera y eficiente, que
        promueva la participación ciudadana y consolide un crecimiento sustentable de su territorio para mejorar la
        calidad de vida y el acceso en igualdad de oportunidades a todos sus habitantes.";
        return $return;
    }

}
