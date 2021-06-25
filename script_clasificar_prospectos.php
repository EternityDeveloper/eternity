<?php
include("includes/config.inc.php"); 
include("includes/function.php"); 
$_PATH="";
include($_PATH."class/lib/class.ObjectSQL.php"); 
include($_PATH."class/lib/Database.class.php"); 
include($_PATH."class/lib/class.userAccess.php");
 include($_PATH."class/lib/class.Security.php");
$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect(); 
$protect = new UserAccess($db);

SystemHtml::getInstance()->includeClass("prospectos","Prospectos");
 
 
 
$data=array();
$SQL="SELECT * FROM `listado_prospectos`  ";
$rs=mysql_query($SQL);
while($row= mysql_fetch_object($rs)){

	$prospecto= new Prospectos($protect->getDBLink(),$row); 
	$prospecto->doCreateReportCache($row->pilar_inicial,
										$row->codigo_asesor,
										$row->id_nit,
										$row->correlativo,
										$row->pilar_final,
										$row->ultima_actividad); 
											
												
	print_r($row);	
 	 
}
 
 
 
 	// print_r($_DATA);	
?>