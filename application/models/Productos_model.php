<?php

class Productos_model extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->model_name = "Productos_model";
    }

    function get_auditorias($idPuesto) {
        define("COORDINADOR_AUDITORIA", 269);
        define("JEFE_DEPARTAMENTO", 59);
        define("SUBDIRECTOR", 106);
        define("DIRECTOR", 45);
        $return = array();

        switch ($idPuesto) {
            case COORDINADOR_AUDITORIA:
//                if ($_SESSION['usuario']->getIdSubdireccion() == AUDIT_INTERNA) {
//                    $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . CAT_EQUIPO_AUDITORIA . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' .
//                            $bd_Cont . '.' . $tb_Grupo_Integrantes . ' WHERE idGrupo IN (SELECT idGrupo FROM ' .
//                            $bd_Cont . '.' . $tb_Grupo_Integrantes . ' WHERE idEmpleado=' .
//                            $_SESSION['usuario']->getIdEmpleado() . ')) OR b.idEmpleado IN(SELECT idEmpleado FROM ' .
//                            $bd_Cont . '.' . $tb_Grupo_Integrantes . ' WHERE idGrupo IN (SELECT idGrupo FROM ' .
//                            $bd_Cont . '.' . $tb_Grupo_Integrantes . ' WHERE idEmpleado=' .
//                            $_SESSION['usuario']->getIdEmpleado() . ')) )';
//                } else {
//                    $joinn = ' LEFT JOIN ' . CAT_EQUIPO_AUDITORIA . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . " AND (a.idEmpleado=$idEmpleado OR b.idEmpleado=$idEmpleado)";
//                }
                break;
            case JEFE_DEPARTAMENTO:
//                if ($_SESSION['usuario']->getIdEmpleado() == 10520) {
//                    $joinn = 'WHERE ' . $filtro_where;
//                } else {
//                    $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . CAT_EQUIPO_AUDITORIA . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' . $bd_Cont . '.' .
//                            $tb_Empleado . ' WHERE clv_subdir=' . $_SESSION['usuario']->getIdSubdireccion() .
//                            ' AND clv_depto=' . $_SESSION['usuario']->getIdDepto() .
//                            ') OR b.idEmpleado IN(SELECT idEmpleado FROM ' . $bd_Cont . '.' . $tb_Empleado .
//                            ' WHERE clv_subdir=' . $_SESSION['usuario']->getIdSubdireccion() .
//                            ' AND clv_depto=' . $_SESSION['usuario']->getIdDepto() . ') )';
//                }
                break;
            case SUBDIRECTOR:
//                $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . CAT_EQUIPO_AUDITORIA . ' b USING(idAuditoria) ' .
//                        ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' . $bd_Cont . '.' .
//                        $tb_Empleado . ' WHERE clv_subdir=' . $_SESSION['usuario']->getIdSubdireccion() .
//                        ') OR b.idEmpleado IN(SELECT idEmpleado FROM ' . $bd_Cont . '.' . $tb_Empleado .
//                        ' WHERE clv_subdir=' . $_SESSION['usuario']->getIdSubdireccion() . ') )';
                break;
            case DIRECTOR:
//                $this->db
//                        ->where_in("tipo", array("AP", "AE", "SA"))
//                        ->where_not_in("statusAudit", array(0));
                break;
            default:
//                if ($_SESSION['usuario']->getIdEmpleado() == 5951 or $_SESSION['usuario']->getIdEmpleado() == 10520 or $_SESSION['usuario']->getIdEmpleado() == 11657 or $_SESSION['usuario']->getIdEmpleado() == 14442) {
//                    $joinn = 'WHERE ' . $filtro_where;
//                } else {
//                    $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . CAT_EQUIPO_AUDITORIA . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . " AND (a.idEmpleado=" . $_SESSION['usuario']->getIdEmpleado() . " OR b.idEmpleado=" . $_SESSION['usuario']->getIdEmpleado() . ")";
//                }
                break;
        }

        $res = $this->db
                ->select("a.idAuditoria AS id, a.rubroAudit AS rubro")
                ->select("CONCAT(IF(segundoPeriodo=1,'2',''), a.area, '/', a.tipo, '/', a.numero, '/', a.anio) AS num, a.statusAudit as estado")
                ->where_in("tipo", array("AP", "AE", "SA"))
                ->where_not_in("statusAudit", array(0))
                ->group_by("id, rubro, num")
                ->order_by("")
                ->get("cat_auditoria a");
        if ($res->num_rows() > 0) {
            $return = $res->result_array();
        }

        return $return;
    }

    function get_areas() {
        $return = array();
        $dbSAC = $this->getDatabase("sac");
        $res = $dbSAC->select("denDepartamento, clv_depto")
                ->join("dcont_areas_auditoria c", "c.idSubdireccion = a.clv_subdir AND idDepartamento=clv_depto", "LEFT")
                ->where(" a.clv_dir", 5)
                ->where(" c.idSubdireccion", 3)
                ->where("c.activo", 1)
                ->order_by("clv_depto", "ASC")
                ->get("ayunta_departamento a");
        if ($res->num_rows() > 0) {
            $return = $res->result_array();
        }

        return $return;
    }

    function get_motivos($idMotivo) {
        $return = array('success' => FALSE);
        $this->db->select("id_motivos, motivos");
        switch ($idMotivo) {
            case "proceso":
                $this->db->where("porProceso", 1);
                break;
            case "formato":
                $this->db->where("porFormato", 1);
                break;
        }
        $res = $this->db->order_by("motivos", "ASC")->get("cat_motivos");
        if ($res->num_rows() > 0) {
            $return = $res->result_array();
        }

        return $return;
    }

    function get_responsables() {
        $responsables = array();
        $dbSAC = $this->getDatabase("sac");
        $res = $dbSAC->select("DISTINCT (f.idEmpleado), f.clv_subdir, CONCAT(f.apF,' ',f.amF, ' ', f.nombreF) as nombre")
                ->join(APP_DATABASE_CYSA . ".cat_funcionario f", "a.idSubdireccion = f.clv_subdir", "LEFT")
                ->where("f.clv_dir", 5)
                ->where("f.fechaBaja", NULL)
                ->group_start()
                ->where("idPuesto", 45)
                ->or_where("idPuesto", 106)
                ->group_end()
                ->order_by("nombre", "ASC")
                ->get("dcont_areas_auditoria a");
        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $r) {
                $responsables[intval($r['clv_subdir'])] = $r['nombre'];
            }
        }

        $res = $dbSAC->select("DISTINCT(f.idEmpleado), f.clv_subdir, CONCAT(f.apF,' ',f.amF, ' ', f.nombreF) as nombre")
                ->join(APP_DATABASE_CYSA . ".cat_funcionario f", "a.idEmpleado = f.idEmpleado", "LEFT")
                ->where("a.clv_dir", 5)
                ->where("f.fechaBaja", NULL)
                ->get("ayunta_direccion a");
        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $r) {
                $responsables[intval($r['clv_subdir'])] = $r['nombre'];
            }
        }

        return $responsables;
    }

    function get_documentos() {
        define('ISO_DOCUMENTOS', 1); //diferentes documentos de la tabla documentos
        define('ISO_PAPELES', 2);
        define('ISO_NOISO', 0);
        $return = array();
        $res = $this->db
                ->where_in("clasDocumento", array(ISO_DOCUMENTOS, ISO_PAPELES))
                ->order_by("clasDocumento", "ASC")
                ->order_by("denDocto", "ASC")
                ->get("cat_documentos");
        if ($res->num_rows() > 0) {
            $return = $res->result_array();
        }

        return $return;
    }

}
