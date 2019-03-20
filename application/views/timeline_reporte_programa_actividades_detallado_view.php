<?php
require_once('../../_funciones/validaSesion.php');
require_once('../../_funciones/configSAC.php');
require_once('../modelo/globalAuditoria.php');
include_once('timeline_funciones_5_2.php');

$dbCYSA = conectarBD();
$strSQL = "SELECT distinct(area) area FROM cat_auditoria WHERE anio =2016 ORDER BY area ASC";
$result = $dbCYSA->ejecutaQuery($strSQL);
$areas = array();
if ($result && mysql_num_rows($result) > 0) {
    while ($r = mysql_fetch_assoc($result)) {
	array_push($areas, $r);
    }
}

$strSQL = "SELECT distinct(tipo) tipo FROM cat_auditoria WHERE anio =2016 ORDER BY tipo ASC";
$result = $dbCYSA->ejecutaQuery($strSQL);
$tipos = array();
if ($result && mysql_num_rows($result) > 0) {
    while ($r = mysql_fetch_assoc($result)) {
	array_push($tipos, $r);
    }
}
?>
<!DOCTYPE html>
<html lang="">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1">
        <link rel="icon" href="../../timeline/images/ico/32x32.png" type="image/png">
        <title><?= $auditoria['nombreAuditoria']; ?></title>
        <!--<link href="../../timeline/styles/app.min.css" rel="stylesheet" type="text/css"/>-->
        <link href="../../timeline/styles/personalizados.css" rel="stylesheet" type="text/css"/>
        <link href="../../timeline/styles/personalizados_cysa.css" rel="stylesheet" type="text/css"/>
        <script src="../../_js/timeline/jquery-3.1.0.min.js" type="text/javascript"></script>
	<!-- SweetAlert 2 -->
	<script src="../../timeline/plugins/sweetalert2/es6-promise.min.js" type="text/javascript"></script>
	<link href="../../timeline/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css"/>
	<script src="../../timeline/plugins/sweetalert2/sweetalert2.js" type="text/javascript"></script>
	<!--<script src="../../timeline/plugins/sweetalert2/sweetalert2.common.js" type="text/javascript"></script>-->
	<!-- Promise.finally support -->
	<script src="https://cdn.jsdelivr.net/promise.prototype.finally/1.0.1/finally.js"></script>
	<!-- Bootrstrap -->
	<script src="../../timeline/plugins/app.min.js" type="text/javascript"></script>
	<!-- Tether 1.3.3 -->
	<link href="../../timeline/plugins/tether-1.3.3/css/tether.min.css" rel="stylesheet" type="text/css"/>
	<script src="../../timeline/plugins/tether-1.3.3/js/tether.min.js" type="text/javascript"></script>
	<!-- DatePicker -->
	<link href="../../timeline/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css" rel="stylesheet" type="text/css"/>
	<script src="../../timeline/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
	<script src="../../timeline/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js" type="text/javascript"></script>
	<!-- DateRangePicker -->
	<script src="../../timeline/plugins/moment/min/moment.min.js" type="text/javascript"></script>
	<script src="../../timeline/plugins/moment/locale/es.js" type="text/javascript"></script>
	<link href="../../timeline/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
	<script src="../../timeline/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>
	<!-- Bootbox.js -->
	<script src="../../timeline/plugins/bootstrap-bootbox/bootbox.min.js" type="text/javascript"></script>

	<link href="../../timeline/plugins/jquery-labelauty/source/jquery-labelauty.css" rel="stylesheet" type="text/css"/>
	<script src="../../timeline/plugins/jquery-labelauty/source/jquery-labelauty.js" type="text/javascript"></script>
	<link href="_css/generico.css" rel="stylesheet" type="text/css" />
	<style>
	    /*body { background-color: white; }*/
	    .formato {
		font-family:Arial, Helvetica, sans-serif; 
		font-size:11pt;
		width:20%;
	    }
	</style>
    </head>
    <body>
	<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0" style="empty-cells:show;background-color:#FFFFFF; background:url(_imagenes/bgTop_2.jpg);">
	    <tr>
		<td valign="middle" style="padding:0px; margin:0px; background:url(_imagenes/topCedObs_izq.png); background-repeat:no-repeat">
		    <div align="left" style="padding:10px 15px 5px 15px; margin:0px; color:#FFFFFF; width:100%; border-left:solid 1px #FFFFFF;border-top:solid 1px #FFFFFF" class="titAudit">
			Reporte de Programa de Actividades Detallado<br/>
			<form method="post" action="../controlador/timeline_reporte_programa_actividades_detallado.php">
			    <table cellspacing="0" cellpadding="0" width="60%" align="right" border="0" style="margin-top:20px">
				<tr>
				    <td align="left" class="formato">&Aacute;rea
					<select name="area" id="area">
					    <option value="*">TODAS</option>
					    <?php foreach ($areas as $a): ?>
    					    <option value="<?= $a['area']; ?>"><?= $a['area']; ?></option>
					    <?php endforeach; ?>
					</select>
				    </td>
				    <td align="left" class="formato">Tipo
					<select name="tipo" id="tipo">
					    <option value="*">TODOS</option>
					    <?php foreach ($tipos as $t): ?>
    					    <option value="<?= $t['tipo']; ?>"><?= $t['tipo']; ?></option>
					    <?php endforeach; ?>
					</select>
				    </td>
				    <td align="rigth" colspan="2" valign="bottom" class="formato">
					<button type="submit">Generar reporte</button>
				    </td>
				    </form>
				</tr>
			    </table>
			</form>
		    </div>
		</td>
		<td valign="top" style="padding:0; border-right:solid 1px #FFFFFF" align="right" width="30"><img src="../vista/_imagenes/topCedObs_der.png" width="30" height="30" /><br />
		</td>
	    </tr>
	</table>
    </body>
</html>