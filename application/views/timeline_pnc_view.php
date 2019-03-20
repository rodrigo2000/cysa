<?php
require_once('../../_funciones/validaSesion.php');
require_once(PATH . '/_funciones/conexion.php');
require_once('../modelo/configCYSA.php');
include_once('../vista/timeline_funciones.php');

function getPNC($idPNC) {
    if (empty($idPNC)) {
	return FALSE;
    }
    $return = array();
    $strSQL = "SELECT * FROM producto_noconforme WHERE id_folio = " . $idPNC;
    $dbCYSA = conectarBD();
    $res = $dbCYSA->ejecutaQuery($strSQL);
    if ($res && mysql_num_rows($res) == 1) {
	$return = mysql_fetch_assoc($res);
    }
    return $return;
}

$idPNC = $_POST['idPNC'];
$pnc = getPNC($idPNC);
//echo "<pre>";
//print_r($pnc);
//die();
?>
<html>
    <head>
	<style>
	    .descripcionPNC dt {
		font-size: 1.3em;
	    }
	</style>
    </head>
    <body>
	<dl class="descripcionPNC">
	    <dt>Folio</dt>
	    <dd><?= $pnc['id_folio']; ?></dd>
	    <dt>Fecha</dt>
	    <dd><?= mysqlDate2OnlyDate($pnc['fecha'], TRUE); ?></dd>
	    <dt>Descripción</dt>
	    <dd><?= utf8_encode($pnc['descripcion']); ?></dd>
	    <dt>Acción a realizar</dt>
	    <dd><?= utf8_encode($pnc['descAccRealiza']); ?></dd>
	    <dt>Justificación</dt>
	    <dd><?= utf8_encode($pnc['justificacion']); ?></dd>
	</dl>
    </body>
</html>