<?php
/*
include("includes/config.inc.php"); 
include("class/lib/Database.class.php"); 

$db = new Database(DB_SERVER, DB_USER, DB_PWD, DB_DATABASE); 
$db->connect(); 


$SQL="select * from localizacion_ciudad ";

$rs=mysql_query($SQL);
while($row=mysql_fetch_object($rs)){
	$SQL="update localizacion_sector set localizacion_ciudad_id='".$row->id."' 
			where cod_ciudad='".$row->cod_ciudad."' and cod_municipio='".$row->municipio_id."'";
	print_r($row);
//	mysql_query($SQL);
	
}*/
//$my_db=mysql_connect("localhost","root","");
//mysql_select_db("comparacion",$my_db);

$ms_db = mssql_connect("USER-PC\DEBUG","jramos","A123456a");
//$ms_db = mssql_connect("DESARROLLO-PC","memorial","A123456a");
mssql_select_db("SERVICIOSM",$ms_db);


//updateStatusCPA_NF($ms_db,$my_db);
updateDate($ms_db,$my_db);

function updateDate($ms_db,$my_db){
	$error=0;
//	$SQL="SELECT CONVERT(VARCHAR(10), CPA_FECHA, 120) AS CPA_FECHA  FROM PRO_COMPROBANTES_BANCO where   CPA_FECHA between '2013-01-01' and '2013-08-12' group by CPA_FECHA  ";
	$SQL="SELECT CONVERT(VARCHAR(10), CPA_FECHA, 105) AS CPA_FECHA  FROM PRO_COMPROBANTES_BANCO where   CPA_FECHA between '2012-01-01' and '2012-12-31' group by CPA_FECHA  ";
	$rs=mssql_query($SQL,$ms_db);
	while($row=mssql_fetch_assoc($rs)){
		//print_r($row);
		//echo date("dd-mm-yy",strtotime($row['CPA_FECHA']));
		// echo date("Y-m-d", strtotime("2011-W17-6"));
	//	print_r(DateTime::createFromFormat('d-m-Y', $row['CPA_FECHA'])->format('Y-m-d')."\n");
		//exit;
		updateStatusCPA_NF($ms_db,$row['CPA_FECHA']);
	//	
	}
	
}

function updateStatusCPA_NF($ms_db,$date){
	$error=0;
	
	$format_dat=DateTime::createFromFormat('d-m-Y',$date)->format('Y-m-d');
	
	$day=DateTime::createFromFormat('d-m-Y', $date)->format('Y-m-l');
	if ($day!="Sunday"){
	
			$SQL="SELECT *
				FROM
				  dbo.PRO_COMPROBANTES_BANCO_DET
				WHERE
		  CPD_MONTO IN (SELECT LTRIM(RTRIM(Str(SUM(CPD_MONTO), 12, 2))) AS CPD_MONTO_TOTAL FROM PRO_COMPROBANTES_BANCO, CON_TIPO_DOC_COBRO_PAGO, PRO_COMPROBANTES_BANCO_DET, MAE_CC_VENTAS, CON_TIPO_DOCUMENTO WHERE CPA_COD_CUENTA_BANCARIA = CPD_COD_CUENTA_BANCARIA AND CPA_CODIGO = CPD_COD_COMPROBANTE AND CPA_INGRESO = CPD_INGRESO AND VTA_COD_TIPO_DOCUMENTO = CPD_COD_TIPO_DOCUMENTO AND VTA_NUMERO_OFICIAL = CPD_NUMERO AND TDC_CODIGO = VTA_COD_TIPO_DOCUMENTO AND TDC_FISCAL = 0 AND DCB_CODIGO = CPA_COD_TIPO_DOCUMENTO AND CPA_ANULADO = 0 AND CPA_INGRESO = 1 AND CPA_IMPRESO = 1 AND CPA_COD_CUENTA_BANCARIA = 'CAJA 01 INGRESOS' AND CPA_INGRESO = 1 AND 
		  (CPA_FECHA = CONVERT(VARCHAR(10), '".$date."', 105) ))
		  and PRO_COMPROBANTES_BANCO_DET.CPD_COD_CUENTA_BANCARIA='CAJA 01 INGRESOS' ";
		  
		  
		  if ($format_dat=="2012-01-06"){
		 		echo $SQL;
				exit;
		  }
		  
			$rs=mssql_query($SQL,$ms_db);
			$count=0;
			while($row=mssql_fetch_assoc($rs)){
				
			
				$count++;
				$SQL="UPDATE 
						PRO_COMPROBANTES_BANCO SET CPA_NF=1 
					  WHERE 
						CPA_COD_CUENTA_BANCARIA='".$row['CPD_COD_CUENTA_BANCARIA']."' 
						AND CPA_CODIGO='".$row['CPD_COD_COMPROBANTE']."'  
						and (CPA_DESCRIPCION='BOVEDA AC' OR CPA_DESCRIPCION='BOVEDA')";
			//	echo $date."\n";
				mssql_query($SQL,$ms_db);
			
				
			
				$SQL="SELECT COUNT(*) AS TOTAL
						FROM
						PRO_COMPROBANTES_BANCO 
						WHERE 
						CPA_COD_CUENTA_BANCARIA='".$row['CPD_COD_CUENTA_BANCARIA']."' 
						AND CPA_CODIGO='".$row['CPD_COD_COMPROBANTE']."'  
						and (CPA_DESCRIPCION='BOVEDA AC' OR CPA_DESCRIPCION='BOVEDA') ";
				// echo $SQL."\n";
				$rsx=mssql_query($SQL,$ms_db);
				$rowx=mssql_fetch_assoc($rsx);
				if ($rowx['TOTAL']<=0){
					//print_r($rowx);
					echo "No existe ". $format_dat."\n";
				}
				//echo $SQL."\
				
			//	print_r($row);
			}
			
			if ($count<=0){
				echo "No existe ". $format_dat."\n";
			}
			
	}
	
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