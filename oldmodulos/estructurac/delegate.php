<?php
if (!isset($protect)){
	exit;
}
 
/*MAIN DASHBOARD COBRO*/
if (isset($_REQUEST['cierre'])){  
	include("cierres/listado_cierre.php"); 
	exit;
} 


if (isset($_REQUEST['processCierres'])){
	
	$SQL="SELECT * FROM `cierres` WHERE ano='2014'";
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){ 
		$format=$row['ano']."_".$row['mes'];
		$obj= new objectSQL();
		$obj->setTable("cierres");
		$counter=0;
		$retur=array("mensaje"=>'No se pudo realizar la operación','error'=>true);
		
		if (validateField($_REQUEST,'fecha_inicio_ventas_'.$format) && validateField($_REQUEST,'fecha_cierre_ventas_'.$format)){
			$obj->fecha_inicio_ventas="STR_TO_DATE('".$_REQUEST['fecha_inicio_ventas_'.$format]."','%d-%m-%Y')";
			$obj->fecha_fin_ventas="STR_TO_DATE('".$_REQUEST['fecha_cierre_ventas_'.$format]."','%d-%m-%Y')";
			$counter=1;
		}	
		
		if (validateField($_REQUEST,'fecha_inicio_cobros_'.$format) && validateField($_REQUEST,'fecha_cierre_cobro_'.$format)){
			$obj->fecha_inicio_cobros="STR_TO_DATE('".$_REQUEST['fecha_inicio_cobros_'.$format]."','%d-%m-%Y')";
			$obj->fecha_fin_cobros="STR_TO_DATE('".$_REQUEST['fecha_cierre_cobro_'.$format]."','%d-%m-%Y')";  
			$counter++;
		} 
	 
		if ($counter>0){
			$SQL=$obj->toSQL("update"," where ano='".$row['ano']."' and mes='".$row['mes']."'");
			mysql_query($SQL);	
		}		
	}
	$retur['mensaje']="Registro actualizado correctamente!";
	$retur['error']=false;		
	echo json_encode($retur);
	exit;
}

?>