<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Catalogos_model extends MY_Model {

    function getDirecciones() {
        $dbSAC = $this->getDatabase("sac");
        return $dbSAC->where("direccionActiva", 1)
                        ->order_by("denDireccion", "ASC")
                        ->get("ayunta_direccion")
                        ->result_array();
    }

    function getSubdirecciones($idDireccion = NULL) {
        $return = array();
        if (!empty($idDireccion)) {
            $dbSAC = $this->getDatabase("sac");
            $return = $dbSAC
                    ->where("clv_dir", $idDireccion)
                    ->where("subdirActiva", 1)
                    ->order_by("denSubdireccion", "ASC")
                    ->get("ayunta_subdireccion")
                    ->result_array();
        }
        return $return;
    }

    function getDepartamentos($idDireccion = NULL, $idSubdireccion = NULL) {
        $return = array();
        if (!empty($idDireccion) && !empty($idSubdireccions)) {
            $dbSAC = $this->getDatabase("sac");
            $return = $dbSAC
                    ->where("clv_dir", $idDireccion)
                    ->where("clv_subdir", $idSubdireccion)
                    ->where("deptoActivo", 1)
                    ->order_by("denDepartamento", "ASC")
                    ->get("ayunta_departamento")
                    ->result_array();
        }
        return $return;
    }

    function get_auditores_lider($liderAuditoria = NULL) {
        return $this->getAuditoresLider($liderAuditoria);
    }

    function getAuditoresLider($liderAuditoria = NULL) {
        $return = array();
        $dbSAC = $this->getDatabase("sac");
//        $sql = "SELECT dcon.idEmpleado, CONCAT(dcon.aPaterno, ' ', dcon.aMaterno, ' ', dcon.nombre) AS nombreCompleto
//                  FROM preprod_sac.dcont_empleado AS dcon  LEFT JOIN preprod_cysa.cat_funcionario
//                  AS func ON dcon.idEmpleado=func.idEmpleado 
//                  WHERE dcon.idPuesto NOT IN (45,106) AND func.fechaBaja IS NULL
//                  
//                  UNION
//                  
//                  SELECT dcon.idEmpleado, CONCAT(dcon.aPaterno, ' ', dcon.aMaterno, ' ', dcon.nombre)              AS nombreCompleto 
//                  FROM preprod_sac.dcont_empleado AS dcon  LEFT JOIN preprod_cysa.cat_funcionario
//                  AS func ON dcon.idEmpleado=func.idEmpleado 
//                  WHERE dcon.idPuesto NOT IN (59,45,106) AND dcon.idEmpleado = 33
//                  ORDER BY nombreCompleto ASC";
        $subquerys[] = $dbSAC->select("dcon.idEmpleado, CONCAT(dcon.aPaterno, ' ', dcon.aMaterno, ' ', dcon.nombre) AS nombreCompleto")
                ->from('dcont_empleado dcon')
                ->join('preprod_cysa.cat_funcionario func', 'dcon.idEmpleado = func.idEmpleado ', 'LEFT')
                ->where_not_in("dcon.idPuesto", array(49, 106))
                ->where('func.fechaBaja IS NULL')
                ->get_compiled_select();

        $subquerys[] = $dbSAC->select("dcon.idEmpleado, CONCAT(dcon.aPaterno, ' ', dcon.aMaterno, ' ', dcon.nombre) AS nombreCompleto")
                ->from('dcont_empleado dcon')
                ->join('preprod_cysa.cat_funcionario func', 'dcon.idEmpleado = func.idEmpleado ', 'LEFT')
                ->where_not_in("dcon.idPuesto", array(49, 59, 106))
                ->where('dcon.idEmpleado', $liderAuditoria)
                ->order_by("nombreCompleto", "ASC")
                ->get_compiled_select();
        $sql = implode(' UNION ', $subquerys);
        $return = $dbSAC->query($sql)->result_array();
        return $return;
        ;
    }

    function get_auditorias_de_puesto($idPuesto = NULL) {
        $return = array();

        switch ($idPuesto) {
            case PUESTO_COORDINADOR_AUDITORIA:
//                if ($this->session->id_subdireccion == AUDIT_INTERNA) {
//                    $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idGrupo IN (SELECT idGrupo FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idEmpleado=' .
//                            $this->session->id_empleado . ')) OR b.idEmpleado IN(SELECT idEmpleado FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idGrupo IN (SELECT idGrupo FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idEmpleado=' .
//                            $this->session->id_empleado . ')) )';
//                } else {
//                    $joinn = ' LEFT JOIN ' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . " AND (a.idEmpleado=$idEmpleado OR b.idEmpleado=$idEmpleado)";
//                }
                break;
            case PUESTO_JEFE_DEPARTAMENTO:
//                if ($this->session->id_empleado == 10520) {
//                    $joinn = 'WHERE ' . $filtro_where;
//                } else {
//                    $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' .
//                            dcont_empleado . ' WHERE clv_subdir=' . $this->session->id_subdireccion .
//                            ' AND clv_depto=' . $this->session->id_departamento .
//                            ') OR b.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' . dcont_empleado .
//                            ' WHERE clv_subdir=' . $this->session->id_subdireccion .
//                            ' AND clv_depto=' . $this->session->id_departamento . ') )';
//                }
                break;
            case PUESTO_SUBDIRECTOR:
//                $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                        ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' .
//                        dcont_empleado . ' WHERE clv_subdir=' . $this->session->id_subdireccion .
//                        ') OR b.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' . dcont_empleado .
//                        ' WHERE clv_subdir=' . $this->session->id_subdireccion . ') )';
                break;
            case PUESTO_DIRECTOR:
//                $this->db
//                        ->where_in("tipo", array("AP", "AE", "SA"))
//                        ->where_not_in("statusAudit", array(0));
                break;
            default:
//                if ($this->session->id_empleado == 5951 or $this->session->id_empleado == 10520 or $this->session->id_empleado == 11657 or $this->session->id_empleado == 14442) {
//                    $joinn = 'WHERE ' . $filtro_where;
//                } else {
//                    $joinn = ' LEFT JOIN ' . BD_CYSA . '.' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . " AND (a.idEmpleado=" . $this->session->id_empleado . " OR b.idEmpleado=" . $this->session->id_empleado . ")";
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

    function get_auditorias_de_empleado($idEmpleado = NULL) {
        $return = array();
        $dbSAC = $this->getDatabase("sac");

        $dbSAC->where('statusAudit', 1)
                ->where('CURDATE() >= DATE_ADD( FROM_UNIXTIME(fechaIniReal ,\'%Y-%m-%d\' ), INTERVAL -15 DAY');

        switch ($this->session->id_puesto) {
            case PUESTO_COORDINADOR_AUDITORIA:
                if ($this->session->id_subdireccion == AREA_AUDITORIA_INTERNA) {
//                    $joinn = ' LEFT JOIN ' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . $excepcionAudit . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idGrupo IN (SELECT idGrupo FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idEmpleado=' .
//                            $this->session->id_empleado . ')) OR b.idEmpleado IN(SELECT idEmpleado FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idGrupo IN (SELECT idGrupo FROM ' .
//                            preprod_sac . '.' . dcont_grupo_integrantes . ' WHERE idEmpleado=' .
//                            $this->session->id_empleado . ')) )';
                    $this->db->join("cat_auditoria_equipo cae", "cae.idAuditoria = a.idAuditoria", "LEFT")
                            ->or_where("a.idEmpleado", $idEmpleado)
                            ->or_where("cae.idEmpleado", $idEmpleado);
                } else {
//                    $joinn = ' LEFT JOIN ' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . " AND (a.idEmpleado=$idEmpleado OR b.idEmpleado=$idEmpleado)";
                    $this->db->join("cat_auditoria_equipo cae", "cae.idAuditoria = a.idAuditoria", "LEFT")
                            ->where("a.idEmpleado", $idEmpleado)
                            ->where("cae.idEmpleado", $idEmpleado);
                }
                break;
            case PUESTO_JEFE_DEPARTAMENTO:
//                if ($this->session->id_empleado == 10520) {
////                    $joinn = 'WHERE ' . $filtro_where;
//                } else {
//                    $joinn = ' LEFT JOIN ' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' .
//                            dcont_empleado . ' WHERE clv_subdir=' . $this->session->id_subdireccion .
//                            ' AND clv_depto=' . $this->session->id_departamento .
//                            ') OR b.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' . dcont_empleado .
//                            ' WHERE clv_subdir=' . $this->session->id_subdireccion;
//                    $this->db->join("cat_auditoria_equipo cae", "cae.idAuditoria = a.idAuditoria", "LEFT")
//                            ->where("a.idEmpleado", $idEmpleado)
//                            ->where("cae.idEmpleado", $idEmpleado)
//                            ->where("clv_subdir", $this->session->id_subdireccion);
//
//                    if ($this->session->id_empleado != 13675) { //Se solicito que Rosario Chim tenga acceso a las auditorias de su Sub. 02/07/2013
//                        $joinn.=' AND clv_depto=' . $this->session->id_departamento . '))';
//                    } else {
//                        /* A solicitud de Rosario Chim, se elimno el permiso para ver todas las auditorias de la subdireccion. 24/11/2014 */
//                        /* 					$joinn.=') OR a.idEmpleado IN(SELECT idEmpleado FROM '.preprod_sac.'.'.dcont_empleado.
//                          ' WHERE clv_subdir='.$this->session->id_subdireccion.'))';
//                         */
//                        $joinn.=' AND clv_depto=' . $this->session->id_departamento . '))';
//                    }
//                }
                break;
            case PUESTO_SUBDIRECTOR:
//                $joinn = ' LEFT JOIN ' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                        ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' .
//                        dcont_empleado . ' WHERE clv_subdir=' . $this->session->id_subdireccion .
//                        ') OR b.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' . dcont_empleado .
//                        ' WHERE clv_subdir=' . $this->session->id_subdireccion . ') )';
                break;
            case PUESTO_DIRECTOR:
//                $joinn = 'WHERE ' . $filtro_where;
                break;
            default:
            /* TEMPORALMENE TANIA TIENE PERMISOS IGUALES A LOS DE LA DIRECTORA */
//                if ($this->session->id_empleado == 10520 or $this->session->id_empleado == 11657 or $this->session->id_empleado == 14442 or $this->session->id_empleado == 5951) {
//                    $joinn = 'WHERE ' . $filtro_where;
//                } elseif ($this->session->id_empleado == 13691 || $this->session->id_empleado == 10077) { //Se le dio permiso a Juan Manuel y Orlando para audit.
//                    $joinn = ' LEFT JOIN ' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . ' AND (a.idEmpleado IN(SELECT idEmpleado FROM
//				' . preprod_sac . '.' .
//                            dcont_empleado . ' WHERE clv_subdir=' . $this->session->id_subdireccion .
//                            ' AND clv_depto=' . $this->session->id_departamento .
//                            ') OR b.idEmpleado IN(SELECT idEmpleado FROM ' . preprod_sac . '.' . dcont_empleado .
//                            ' WHERE clv_subdir=' . $this->session->id_subdireccion .
//                            ' AND clv_depto=' . $this->session->id_departamento . ') )';
//                } else {
//                    $joinn = ' LEFT JOIN ' . cat_auditoria_equipo . ' b USING(idAuditoria) ' .
//                            ' WHERE ' . $filtro_where . $excepcionAudit . " AND (a.idEmpleado=$idEmpleado OR b.idEmpleado=$idEmpleado)";
//                }
        }

        $res = $this->db
                ->select("a.idAuditoria AS id, a.rubroAudit AS rubro, a.fechaIniReal AS fecha, CONCAT(IF(segundoPeriodo=1,'2',''), a.area, '/', a.tipo, '/', a.numero, '/', a.anio) AS num, a.tipo AS tipo, a.numero as valnum")
                ->group_by("a.numero, id, rubro, num")
                ->order_by("a.numero", "ASC")
                ->order_by("tipo", "ASC")
                ->order_by("anio", "ASC")
                ->order_by("num", "ASC")
                ->get("cat_auditoria a");
        if ($res->num_rows() > 0) {
            $return = $res->result_array();
        }

        return $return;
    }

}
