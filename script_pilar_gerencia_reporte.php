<?php
 
include("includes/config.inc.php"); 
include("includes/function.php"); 
include($_PATH."class/lib/class.ObjectSQL.php"); 
include($_PATH."class/lib/Database.class.php"); 
include($_PATH."class/lib/class.userAccess.php");
include($_PATH."class/lib/class.System.php");
include($_PATH."class/lib/class.Security.php");
$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect(); 
$protect = new UserAccess($db);



function getReport($pilar,$asesor_id){
	
	$SQL="SELECT * FROM `listado_prospectos` 
INNER JOIN `sys_personas` ON (`sys_personas`.id_nit=listado_prospectos.id_nit)
INNER JOIN `sys_asesor` ON (sys_asesor.`sys_gerentes_grupos_idgrupos`=listado_prospectos.id_comercial)
WHERE pilar_inicial='".$pilar."' AND listado_prospectos.id_status NOT IN (12,16) AND 
((listado_prospectos.id_comercial='".$asesor_id."' AND `sys_asesor`.`sys_gerentes_grupos_idgrupos`='".$asesor_id."') OR `sys_asesor`.`idgerente_grupo`='".$asesor_id."') "; 

	$rs=mysql_query($SQL);  
	$_INFO=array(); 
	while($row= mysql_fetch_assoc($rs)){
		//$data[$pilar]=
		$data=array(
			"B"=>0,
			'A'=>0,
			'C'=>0,
			'CITA'=>0,
			'PRE'=>0,
			'CIE'=>0,
			'RES'=>0,
			'FECHA'=>""
		);		
		if (trim($row['pilar_inicial'])!=""){
		//array_push($tipo_p,$row);
		 
			if (!isset($data[$pilar])){
				$data[$pilar]=0;
			}
			$data[$pilar]+=1;
			$data['FECHA']=$row['fecha_inicio'];
		 
			if (trim($row['clasificacion_cliente'])!=""){
				$data[trim($row['clasificacion_cliente'])]++;
			}
			
			if (trim($row['actividad'])!=""){
				$data[$row['actividad']]+=1;
			}
			array_push($_INFO,$data);	
		}
		  
		//$tipo_p
	}
	 
	//print_r($data);
	return $_INFO;
}

function convertToUnixTime($date){
	$date=split("-",$date);
	return mktime(1, 0, 0,$date[1],$date[2],$date[0]);	
} 
function convertToUnixTimeAddDay($date,$day){
	$date=split("-",$date);
	return mktime(1, 0, 0,$date[1],$date[2]+$day,$date[0]);	
}
 
$data=array();
$SQL="SELECT * FROM `listado_prospectos`  ";
$rs=mysql_query($SQL);
while($row= mysql_fetch_assoc($rs)){
	
	$ob= new ObjectSQL();
	$ob->push($row);
	$ob->setTable("cache_listado_prospectos"); 
	$SQL=$ob->toSQL("insert");
	print_r($ob);
	mysql_query($SQL); 
}
 
 
 	// print_r($_DATA);	
?>