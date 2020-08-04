<?php
if (!isset($protect)){
	exit;
}
 
if (isset($_REQUEST['listado_cxc'])){
	include("asesores/cxc/listado_cxc.php");
	exit;	
}

if (isset($_REQUEST['listado_cxc_gerente'])){
	include("gerentes/cxc/listado_cxc.php");
	exit;	
}

?>