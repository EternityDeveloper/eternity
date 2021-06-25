<?php
/*
OTRO QUERY PARA EJECUTAR

UPDATE 
  PRO_LIBRO_DIARIO_DETALLE
SET DDT_NF=1  
WHERE
  DDT_COD_CUENTA IN (SELECT MAE_CUENTAS.CTA_CODIGO FROM MAE_CUENTAS WHERE CTA_COD_DESTINO = 2 AND CTA_DESCRIPCION LIKE '%NF%')
  
 OTRO QUERY 
UPDATE 
  PRO_LIBRO_DIARIO_DETALLE
SET DDT_NF=1  
WHERE
  DDT_COD_CUENTA IN (SELECT MAE_CUENTAS.CTA_CODIGO FROM MAE_CUENTAS WHERE CTA_COD_DESTINO = 1 AND CTA_DESCRIPCION LIKE '%NF%')  
*/

$ms_db = mssql_connect("USER-PC\DEBUG","jramos","A123456a");
//$ms_db = mssql_connect("DESARROLLO-PC","memorial","A123456a");
mssql_select_db("SERVICIOSM",$ms_db);


//updateStatusCPA_NF($ms_db,$my_db);
updateDate($ms_db,$my_db);

function updateDate($ms_db,$my_db){ 
	$error=0;
//	$SQL="SELECT CONVERT(VARCHAR(10), CPA_FECHA, 120) AS CPA_FECHA  FROM PRO_COMPROBANTES_BANCO where   CPA_FECHA between '2013-01-01' and '2013-08-12' group by CPA_FECHA  ";
	$SQL="SELECT 
  PRO_LIBRO_DIARIO_DETALLE.DDT_COMPROBANTE,
  PRO_LIBRO_DIARIO_DETALLE.DDT_CORRELATIVO,
  PRO_LIBRO_DIARIO_DETALLE.DDT_COD_CUENTA,
  CONVERT(VARCHAR(10), DDT_FECHA, 105) AS DDT_FECHA
FROM
  MAE_CUENTAS,
  PRO_LIBRO_DIARIO_DETALLE,
  PRO_LIBRO_DIARIO
WHERE
  DIA_COMPROBANTE = DDT_COMPROBANTE AND 
  CTA_CODIGO = DDT_COD_CUENTA AND 
  DIA_ANULADO = 0 AND 
    DDT_FECHA >= '2012-01-01' AND 
  DDT_FECHA <= '2012-12-31' AND 
  CTA_DESCRIPCION LIKE '%NF'  ";
  
  /*
    DDT_FECHA >= '2012-01-01' AND 
  DDT_FECHA <= '2012-12-31' AND 
  
  
    DDT_FECHA >= '01-01-2012' AND 
  DDT_FECHA <= '31-12-2012' AND 
  */
 // echo $SQL;
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		//print_r($row);
		updateStatusCPA_NF($ms_db,$row);
		//exit;
	//	
	}
	
}

function updateStatusCPA_NF($ms_db,$row){
	$error=0;
	
	$format_dat=DateTime::createFromFormat('m-d-Y',$row['DDT_FECHA'])->format('Y-m-d');
	
	$SQL="UPDATE PRO_LIBRO_DIARIO_DETALLE SET DDT_NF=1 WHERE  DDT_COMPROBANTE='".$row['DDT_COMPROBANTE']."' and DDT_CORRELATIVO='".$row['DDT_CORRELATIVO']."' and DDT_FECHA='".$row['DDT_FECHA']."' and DDT_COD_CUENTA='".$row['DDT_COD_CUENTA']."'";
	//	$SQL="select * from PRO_LIBRO_DIARIO_DETALLE WHERE  DDT_COMPROBANTE='".$row['DDT_COMPROBANTE']."' and DDT_CORRELATIVO='".$row['DDT_CORRELATIVO']."' and DDT_FECHA='".$row['DDT_FECHA']."' and DDT_COD_CUENTA='".$row['DDT_COD_CUENTA']."'";	
		mssql_query($SQL);
		//echo $SQL;
		print_r($row['DDT_COD_CUENTA']."\n");
			
	//}
	
}

function optainCPA($ms_db,$my_db){
	$error=0;
	$counter=0;
	$SQL="select VTA_NUMERO_OFICIAL FROM MAE_CC_VENTAS WHERE VTA_COD_TIPO_DOCUMENTO   ='PA' ";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		$data=optainComprobanteDetCPA($ms_db,$row['VTA_NUMERO_OFICIAL']);
	//	print_r($row);
		if ($data['return']=="1"){
			$counter++;
		}
	//	print_r($data);
	}
	
	if ($error==0){
	//	echo "No hubo cambios en los registros!". "\n";
	}
	
	print_r("Registros totales=> ".$counter);
}



function optainComprobanteDetCPA($ms_db,$id){
	$error=0;
	$retrun=array("return"=>0,"data"=>array());
	$SQL="select * FROM PRO_COMPROBANTES_BANCO_DET WHERE CPD_NUMERO='".$id."'";
	//echo $SQL."\n";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
	//	print_r($row);
		$retrun['return']=1;
		array_push($retrun['data'],$row);
	}
	
	if ($error==0){
	//	echo "No hubo cambios en los registros!". "\n";

	}
	return $retrun;
}



function getTotalFromComparer($my_db,$name){
	$SQL="SELECT  * FROM  comparer where name='".$name."'";
	$rs=mysql_query($SQL,$my_db);
	$row=mysql_fetch_assoc($rs);
	return $row['total'];
}


function actualizarTotales($ms_db,$my_db){
	$SQL="SELECT  * FROM sys.Tables";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		print_r($row['name']."  --  ". getTotal_rows($row['name'])."\n");
		mysql_query("update comparer set total='".getTotal_rows($row['name'])."' where name='".$row['name']."'");
	}
}

function insertarTablas($ms_db,$my_db){
	$SQL="SELECT  * FROM sys.Tables";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		print_r($row['name']."  --  ". getTotal_rows($row['name'])."\n");
		mysql_query("insert into comparer (name) values ('".$row['name']."')");
	}
}

?>