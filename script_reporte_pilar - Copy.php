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



function getReport($pilar,$asesor_id,$date){
	$SQL="SELECT 
		prospecto_comercial.id_comercial,
		sys_clasificacion_persona.descripcion,
		sys_clasificacion_persona.id_clasificacion,
		tracking_prospecto.id_actividad  AS actividad,
		prospecto_comercial.pilar_inicial AS `idtipo_prospecto`,
		prospectos.`pilar_final`,
		prospecto_comercial.`fecha_inicio`
	FROM `prospectos`
	INNER JOIN `prospecto_comercial` ON(`prospecto_comercial`.`id_nit`=prospectos.id_nit) 
INNER JOIN `tipos_prospectos` ON(`tipos_prospectos`.idtipo_prospecto=prospecto_comercial.pilar_inicial) 
	INNER JOIN `sys_personas` ON(`sys_personas`.`id_nit`=prospectos.id_nit)
	LEFT JOIN `sys_clasificacion_persona` ON(`sys_clasificacion_persona`.`id_clasificacion`=sys_personas.id_clasificacion)
	INNER JOIN `sys_status` ON(`sys_status`.`id_status`=prospectos.`Estatus`)
	LEFT JOIN `tracking_prospecto` ON(`tracking_prospecto`.`id_nit`=sys_personas.`id_nit`)
	
	WHERE   prospecto_comercial.estatus NOT IN (12,16)  AND prospecto_comercial.id_comercial  LIKE '%".$asesor_id."%' AND  (tracking_prospecto.actividad_proxima IS NULL OR tracking_prospecto.actividad_proxima ='')
	AND prospecto_comercial.pilar_inicial='".$pilar."' 
	AND prospecto_comercial.id_comercial='".$asesor_id."'  AND prospecto_comercial.fecha_inicio='".$date."' "; 

	$rs=mysql_query($SQL);
	$data=array(
		"B"=>0,
		'A'=>0,
		'C'=>0,
		'CITA'=>0,
		'PRE'=>0,
		'CIE'=>0,
		'RES'=>0
	);
	while($row= mysql_fetch_assoc($rs)){
		//$data[$pilar]=
		if (trim($row['idtipo_prospecto'])!=""){
		//array_push($tipo_p,$row);
		 
			if (!isset($data[$pilar])){
				$data[$pilar]=0;
			}
			$data[$pilar]+=1;
			
			$data[$row['descripcion']]+=1;
			
			if (trim($row['actividad'])!=""){
				$data[$row['actividad']]+=1;
			}
			
			
		}
		//$tipo_p
	}
	 
	//print_r($data);
	return $data;
}

function convertToUnixTime($date){
	$date=split("-",$date);
	return mktime(1, 0, 0,$date[1],$date[2],$date[0]);	
} 
function convertToUnixTimeAddDay($date,$day){
	$date=split("-",$date);
	return mktime(1, 0, 0,$date[1],$date[2]+$day,$date[0]);	
}

 
$tipo_p=array();
$SQL="SELECT idtipo_prospecto as tipo,`Descrip_tipoprospecto` FROM `tipos_prospectos`  ";
$rs=mysql_query($SQL);
while($row= mysql_fetch_assoc($rs)){
	array_push($tipo_p,$row);
}




$data=array();
$SQL="SELECT  * FROM `asesores_g_d_gg_view`  
INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit) ";
$rs=mysql_query($SQL);
while($row= mysql_fetch_assoc($rs)){
	array_push($data,$row);
}
 
  

$_DATA=array();

  
$i=1;
foreach($data as $key=>$val){

	$SQL="SELECT `fecha_inicio_ventas`,`fecha_fin_ventas` FROM `cierres`";
	$rsx=mysql_query($SQL);
	while($rowx=mysql_fetch_assoc($rsx)){
		//$imeta=$rowx['fecha_inicio_ventas']
		// echo date("Y-m-d",strtotime($rowx['fecha_inicio_ventas']." 1 day"));
		for($imeta=convertToUnixTime($rowx['fecha_inicio_ventas']);
			$imeta<=convertToUnixTime($rowx['fecha_fin_ventas']);
			$imeta=convertToUnixTimeAddDay(gmdate("Y-m-d",$imeta),1))
		{  
		 	//echo $imeta."\n";
			echo gmdate("Y-m-d",$imeta)."\n";
			
			//$imeta=convertToUnixTimeAddDay(gmdate("Y-m-d",$imeta));
			//exit;
			//$imeta=strtotime(date("Y-m-d",$imeta)." 1 day");
			//echo date("Y-m-d",$imeta)."\n";
		
		 	$_INFO=array();
			$_INFO['NO']=$i;
			$_INFO['ASESOR_VENTAS']= $val['primer_nombre']." ".$val['segundo_nombre'];
			$_INFO['PILAR']="N/A";
			$_INFO['CONTACTO']=0;
			$_INFO['A']=0;
			$_INFO['B']=0;
			$_INFO['C']=0;
			$_INFO['CITA']=0;
			$_INFO['PRESENTACION']=0;
			$_INFO['CIERRE']=0;
			$_INFO['RESERVAS']=0;		 
			
			foreach($tipo_p as $key =>$row){
				$_INFO['PILAR']= $row['tipo']; 
				 if (count($data)>0){ 
				//	foreach($data as $key=>$val){  
					$detalle = getReport($row['tipo'],$val['id_comercial'],gmdate("Y-m-d",$imeta)); 
					
					if ($_INFO['CONTACTO']>0 || $_INFO['A']>0 || $_INFO['B']>0 || $_INFO['C']>0 || $_INFO['CITA']>0
						|| $_INFO['PRESENTACION']>0 || $_INFO['CIERRE']>0 || $_INFO['RESERVAS']>0){
						
						$ob= new ObjectSQL();
						$ob->ASESOR_VENTAS=$_INFO['ASESOR_VENTAS'];
						$ob->PILAR=$_INFO['PILAR'];
						$ob->CONTACTO=$detalle['A']+$detalle['B']+$detalle['C'];
						$ob->A=$detalle['A'];
						$ob->B=$detalle['B'];
						$ob->C=$detalle['C'];
						$ob->CITA=$detalle['CITA'];
						$ob->PRESENTACION=$detalle['PRE'];
						$ob->CIERRE=$detalle['CIE'];
						$ob->RESERVAS=$detalle['RES'];
						$ob->id_comercial=$val['id_comercial'];
						$ob->fecha="STR_TO_DATE('".gmdate("Y-m-d",$imeta)."','%Y-%m-%d')";
						
						$ob->setTable("reporte_cache_pilar"); 
						$SQL=$ob->toSQL("insert");
					//	mysql_query($SQL);
					
						print_r($ob);		 
					}
				}    
			  
			}  
			$i++;
		}
	}
}
 
 
 	// print_r($_DATA);	
?>