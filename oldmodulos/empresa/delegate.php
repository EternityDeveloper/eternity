<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	

/*VISTA DE RESERVA*/
if (isset($_REQUEST['listar'])){ 
	include("view/listar.php"); 
	exit;
}

/*QUERY PARA BUSQUEDA EMPRESA */
if (isset($_REQUEST['_search'])){ 
	SystemHtml::getInstance()->includeClass("empresa","MEmpresa"); 
	$man= new MEmpresa($protect->getDBLink()); 
	$result=$man->getList(); 
	echo json_encode($result); 
 	exit; 
}

/*VISTA DE RESERVA*/
if (isset($_REQUEST['listar'])){ 
	include("view/listar.php"); 
	exit;
}

/*VISTA DE RESERVA*/
if (isset($_REQUEST['emp_edit'])){ 
	include("view/edit.php"); 
	exit;
}
  
 
?>