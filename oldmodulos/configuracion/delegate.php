<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
 
/*CARGA LOS TIPOS DE DOCUMENTOS*/
if (isset($_REQUEST['tipo_documento'])){ 
	include("view/tipo_documento/tipo_documento_list.php"); 
	exit;
}

/*CARGA LOS TIPO DE MOVIMIENTOS*/
if (isset($_REQUEST['tipo_movimiento'])){ 
	include("view/tipo_movimiento/tipo_movimiento_list.php"); 
	exit;
}
 
 
 
?>