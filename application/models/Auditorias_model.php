<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed.');

class Auditorias_model extends MY_Model {

    function __construct() {
	parent::__construct();
	$this->table_name = "cat_auditoria";
	$this->id_field = "idAuditoria";
	$this->table_prefix = "a";
	$this->model_name = __CLASS__;
    }

    function record_count_ajax($data = NULL) {
	if (!isset($data['anio']) || empty($data['anio']) || $data['anio'] == 0) {
	    $data['anio'] = intval(date("Y"));
	}
	$this->db->where("anio", $data['anio']);

	if (isset($data['idTipo']) && !empty($data['idTipo']) && $data['idTipo'] != 0) {
	    $this->db->where("tipo", $data['idTipo']);
	}

	if (isset($data['idArea']) && !empty($data['idArea']) && $data['idArea'] != 0) {
	    $this->db->where("area", $data['idArea']);
	}

	if (isset($data['clv_dir']) && !empty($data['clv_dir']) && $data['clv_dir'] != 0) {
	    $this->db->where($this->table_prefix . '.clv_dir', $data['clv_dir']);
	}

	if (!empty($searchValue)) {
	    $this->db
		    ->group_start()
		    ->or_like('rubroAudit', $searchValue)
		    ->or_like($this->table_prefix . '.area', $searchValue)
		    ->or_like($this->table_prefix . '.tipo', $searchValue)
		    ->or_like($this->table_prefix . '.numero', $searchValue)
		    ->or_like('denDireccion', $searchValue)
		    ->or_like('denSubdireccion', $searchValue)
		    ->group_end();
	}

	if (isset($data['idStatus']) && !empty($data['idStatus']) && intval($data['idStatus']) != 0) {
	    switch ($data['idStatus']) {
		case 1:
		    $this->db->where('statusAudit', 0);
		    break;
		case 2:
		    $this->db->where('statusAudit', 1)
			    ->group_start()
			    ->where('fechaIniAudit', 'fechaIniReal', TRUE)
			    ->or_where('fechaSelloOEA IS NOT NULL', NULL, FALSE)
			    ->group_end();
		    break;
		case 3:
		    $this->db->where_not_in('statusAudit', array(0, 1));
		    break;
		case 4:
		    $this->db->where('statusAudit', 1)
			    ->where('fechaIniAudit !=', 'fechaIniReal', FALSE)
			    ->where('fechaSelloOEA IS NULL', NULL, FALSE);
		    break;
	    }
	}
	$this->db
		->join(APP_DATABASE_SAC . ".ayunta_direccion dir", "dir.clv_dir = " . $this->table_prefix . ".clv_dir", "LEFT")
		->join(APP_DATABASE_SAC . ".ayunta_subdireccion sub", "sub.clv_dir = " . $this->table_prefix . ".clv_dir AND sub.clv_subdir = " . $this->table_prefix . ".clv_subdir", "LEFT")
		->join(APP_DATABASE_SAC . ".dcont_empleado emp", "emp.idEmpleado=" . $this->table_prefix . ".idEmpleado", "LEFT")
		->where("anio IS NOT NULL", NULL, FALSE);
	return parent::record_count();
    }

    function getResultados($limit, $start) {
	$this->db
		->select("idAuditoria, rubroAudit AS rubroAuditoria, denDireccion, denSubdireccion, " . $this->table_prefix . ".anio, " . $this->table_prefix . ".tipo")
		->select("CONCAT(IF(segundoPeriodo=1,'2',''), " . $this->table_prefix . ".area, '/', " . $this->table_prefix . ".tipo, '/', " . $this->table_prefix . ".numero, '/', " . $this->table_prefix . ".anio) AS num")
		->select("CONCAT (emp.nombre,' ',emp.aPaterno,' ',emp.aMaterno) AS nombreAuditorLider")
		->select("IF(fechaSelloOEA IS NULL, fechaIniReal, UNIX_TIMESTAMP(fechaSelloOEA)) AS fecha, 
        IF(fechaSelloOEA IS NULL ,'Real', 'OEA') AS tipoFecha,
	FROM_UNIXTIME(fechaFinAudit,'%Y-%m-%d') AS fechaFinProgramadaAuditoria,
        FROM_UNIXTIME(fechaFinReal,'%Y-%m-%d') AS fechaFinReal, 
        IF(" . $this->table_prefix . ".tipo='SA', fechaAprovacionRev1,
	fechaAprovacion) AS fechaAprobacion, 
        statusAudit AS statusAuditoria, 
        fechaIniAudit AS fechaInicioProgramadaAuditoria, 
        fechaIniReal AS fechaInicioReal")
		->join(APP_DATABASE_SAC . ".ayunta_direccion dir", "dir.clv_dir = " . $this->table_prefix . ".clv_dir", "LEFT")
		->join(APP_DATABASE_SAC . ".ayunta_subdireccion sub", "sub.clv_dir = " . $this->table_prefix . ".clv_dir AND sub.clv_subdir = " . $this->table_prefix . ".clv_subdir", "LEFT")
		->join(APP_DATABASE_SAC . ".dcont_empleado emp", "emp.idEmpleado=" . $this->table_prefix . ".idEmpleado", "LEFT")
		->where("anio IS NOT NULL", NULL, FALSE);

	return parent::getResultados($limit, $start);
    }

    function getResultadosAjax($orderColumn, $order, $start, $length, $searchValue, $data) {
	$return = array();
	if (!isset($data['anio']) || empty($data['anio']) || $data['anio'] == 0) {
	    $data['anio'] = intval(date("Y"));
	}
	$this->db->where("anio", $data['anio']);

	if (isset($data['idTipo']) && !empty($data['idTipo']) && $data['idTipo'] != 0) {
	    $this->db->where("tipo", $data['idTipo']);
	}

	if (isset($data['idArea']) && !empty($data['idArea']) && $data['idArea'] != 0) {
	    $this->db->where("area", $data['idArea']);
	}

	if (isset($data['clv_dir']) && !empty($data['clv_dir']) && $data['clv_dir'] != 0) {
	    $this->db->where($this->table_prefix . '.clv_dir', $data['clv_dir']);
	}

	if (!empty($searchValue)) {
	    $this->db
		    ->group_start()
		    ->or_like('rubroAudit', $searchValue)
		    ->or_like($this->table_prefix . '.area', $searchValue)
		    ->or_like($this->table_prefix . '.tipo', $searchValue)
		    ->or_like($this->table_prefix . '.numero', $searchValue)
		    ->or_like('denDireccion', $searchValue)
		    ->or_like('denSubdireccion', $searchValue)
		    ->group_end();
	}

	if (isset($data['idStatus']) && !empty($data['idStatus']) && intval($data['idStatus']) != 0) {
	    switch ($data['idStatus']) {
		case 1:
		    $this->db->where('statusAudit', 0);
		    break;
		case 2:
		    $this->db->where('statusAudit', 1)
			    ->group_start()
			    ->where('fechaIniAudit', 'fechaIniReal', FALSE)
			    ->or_where('fechaSelloOEA IS NOT NULL', NULL, FALSE)
			    ->group_end();
		    break;
		case 3:
		    $this->db->where_not_in('statusAudit', array(0, 1));
		    break;
		case 4:
		    $this->db->where('statusAudit', 1)
			    ->where('fechaIniAudit != fechaIniReal', NULL, FALSE)
			    ->where('fechaSelloOEA IS NULL', NULL, FALSE);
		    break;
	    }
	}

	if ($orderColumn) {
	    $this->db->order_by($orderColumn, $order);
	} else {
	    $this->db
		    ->order_by($this->table_prefix . ".anio", "ASC")
		    ->order_by($this->table_prefix . ".numero", "ASC")
		    ->order_by($this->table_prefix . ".tipo", "ASC");
	}

	return $this->getResultados($length, $start);
    }

    function get_lider_auditoria($idAuditoria) {
	$lider = FALSE;
	if (!empty($idAuditoria)) {
	    $dbSAC = $this->getDatabase(APP_DATABASE_SAC);
	    $lider = $dbSAC->select("idEmpleado")
			    ->where('idAuditoria', $idAuditoria)
			    ->get($this->table_name . " " . $table->table_prefix)
			    ->row()
		    ->lider;
	}
	return $lider;
    }

    function get_equipo_auditoria($idAuditoria) {
	$equipo = array();
	if (!empty($idAuditoria)) {
	    $result = $this->db
		    ->select('idEmpleado', FALSE)
		    ->where('idAuditoria', $idAuditoria)
		    ->get('cat_auditoria_equipo');
	    if ($result->num_rows() > 0) {
		foreach ($result->result_array() as $r) {
		    $empleado = $this->Empleados_model->get_empleado($r['idEmpleado']);
		    array_push($equipo, $empleado);
		}
	    }
	}
	return $equipo;
    }

    function get_auditoria($idAuditoria) {
	$return = NULL;
	if (!empty($idAuditoria)) {
	    $result = $this->db->where($this->id_field, $idAuditoria)->limit(1)->get($this->table_name);
	    if ($result->num_rows() == 1) {
		$return = $result->result_row();
	    }
	}
	return $return;
    }

}
