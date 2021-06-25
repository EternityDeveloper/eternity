<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	



$data=array();
$SQL="SELECT 
	`ASESOR_VENTAS`,
	`PILAR`,
	`CONTACTO`,
	`A`,
	`B`,
	`C`,
	`CITA`,
	`PRESENTACION`,
	`CIERRE`,
	`RESERVAS`
 FROM `reporte_cache_pilar`
WHERE (reporte_cache_pilar.id_comercial  LIKE '%".$protect->getComercialID()."%' AND reporte_cache_pilar.id_comercial!='".$protect->getComercialID()."')  ";
 

$mid_excel = new MID_SQLPARAExel;
//$sql = "SELECT * FROM asesor_reagenda";
$mid_excel->mid_sqlparaexcel("", "alunos", $SQL, "listado"); 

  
?>        
 
 
