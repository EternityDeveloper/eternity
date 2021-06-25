<?php

print_r($_REQUEST);
echo "fsda";
exit;
//include($_PATH."class/lib/class.System.php");
include("class/lib/excel/class_excel_ms.php");
include("class/lib/class.ObjectSQL.php");

 
$db_link =mssql_connect("DESARROLLO-PC","memorial","A123456a");
mssql_select_db("SERVICIOSM",$db_link);


function existDateContrat($db_link,$contract,$date_number){
$rt=false;
 $SQL="SELECT * FROM dbo.REPORTE_CANCELADOS_CACHE WHERE CONTRATO='".$contract."' AND  
 	FECHA_CANCELACION=DATEADD(day,".$date_number.",'2012-12-31')";
	//print_r($SQL);
	$rs=mssql_query($SQL);
	while($row=mssql_fetch_assoc($rs)){
		
		$rt=true;
		break;
	}
	return $rt;
}

function getResultData($db_link,$date_number){
 $data=array();
 $SQL="SELECT  
	CONTRATOS_CANCELADOS.CONTRATO,
	CONTRATOS_CANCELADOS.CTT_PRECIO PRECIO,
	MAE_MATERIALES.PRO_DESCRIPCION as PRODUCTO,
	CLI_DESCRIPCION AS NOMBRE_CLIENTE,
	CONTRATOS_CANCELADOS.DEU_FECHA_CANC AS FECHA_CANCELACION
FROM CONTRATOS_CANCELADOS
INNER JOIN MAE_CC_CLIENTES as CLIENTES_CC  ON  (CLIENTES_CC.CLI_CODIGO = CONTRATOS_CANCELADOS.CTT_COD_CLIENTE )
INNER JOIN PRO_PLANES_VENTA ON (PLN_CODIGO = CTT_COD_PLAN)
INNER JOIN MAE_MATERIALES ON (MAE_MATERIALES.PRO_CODIGO = PRO_PLANES_VENTA.PLN_COD_SERVICIO)
 WHERE CTT_COD_TD in ('CM','CO') AND DEU_FECHA_CANC BETWEEN DATEADD(day,".$date_number.",'2012-12-31') AND DATEADD(day,".$date_number.",'2012-12-31') ";
	
	$rs=mssql_query($SQL);
	while($row=mssql_fetch_assoc($rs)){
	//	print_r($row);	
		array_push($data,$row);
	}
	return $data;
}

/*
for ($i=0;$i<=365;$i++){
	$data=getResultData($db_link,$i);
//	print_r($data);
	foreach($data as $key => $val){
		
		if (!existDateContrat($db_link,$val['CONTRATO'],$i)){
			print_r($val);
			$obj= new ObjectSQL();
			$obj->phush_obj($val);
			$SQL=$obj->getSQL("insert","REPORTE_CANCELADOS_CACHE");
		//	echo $SQL;
			mssql_query($SQL);
		}else{
			print_r("Existe!\n");	
		}
 
	}
 
}
exit;
*/



 
if (isset($_POST['report'])){
 
	$db_link =mssql_connect("DESARROLLO-PC","memorial","A123456a");
	mssql_select_db("SERVICIOSM",$db_link);
	
///	print_r($_POST);
	
	switch($_POST['report']){
		case "cancelados":
		
		   if ($_REQUEST['desde']!="" && $_REQUEST['hasta']!=""){
				 $SQL="SELECT * FROM REPORTE_CANCELADOS_CACHE  WHERE 
				CONVERT(DATETIME, FECHA_CANCELACION, 103) BETWEEN CONVERT(DATETIME, convert(VARCHAR, '".$_REQUEST['desde']."', 103), 103) AND 
				  CONVERT(DATETIME, convert(VARCHAR, '".$_REQUEST['hasta']."', 103), 103) ";
				$mid_excel = new MID_SQLPARAExel;
				$mid_excel->mid_sqlparaexcel($SQL, "CANCELADOS_");
				
				//print_r($SQL);
				exit;
		   }
			
		break;	
	}
	

}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Reportes</title>
</head>
<link type="text/css" href="css/style.css" rel="stylesheet"/>
<link type="text/css" href="css/style_page.css" rel="stylesheet"/>
<link type="text/css" href="css/jquery-ui-1.8.16.custom.css" rel="stylesheet"/>
<link type="text/css" href="css/base/jquery.ui.all.css" rel="stylesheet"/>
<link type="text/css" href="css/showLoading.css" rel="stylesheet"/>
<link type="text/css" href="css/demo_page.css" rel="stylesheet"/>
<link type="text/css" href="css/demo_table.css" rel="stylesheet"/>
<script type="text/javascript" src="script/jquery/jquery-1.9.1.js"></script>
<script type="text/javascript" src="script/functions.js"></script>
<script type="text/javascript" src="script/jquery/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.tabs.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="script/ui//jquery.ui.draggable.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.button.js"></script>
<script type="text/javascript" src="script/ui/jquery.ui.dialog.js"></script>
<script type="text/javascript" src="script/jquery.jstree.js"></script>
<script type="text/javascript" src="script/jquery/jquery.cookie.js"></script>
<script type="text/javascript" src="script/jquery/jquery.hotkeys.js"></script>
<script type="text/javascript" src="script/jquery.showLoading.min.js"></script>


<style>
.dataTables_processing{
	top:20%;	
	left:40%;
	width:500px;
}
</style>

<script>

$(function(){
	$("#desde,#hasta").datepicker({
					changeMonth: true,
					changeYear: true,
					yearRange: '1900:2050',
					monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'], 
					monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'], 
					dateFormat: 'dd-mm-yy',  
					dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'], 
					dayNamesMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'], 
					dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'], 
						
				});
				
				
	$("#cancelados,#desistidos,#cancelados").click(function(){
 
		$("#report").val($(this).attr("id"));	
		showLoading(1);
		setTimeout("showLoading(0);",100000);
	});


});


function  showLoading(val){
	switch(val){
		case 0:
			$('#content_dialog').hideLoading();
		break;
		case 1:
			$('#content_dialog').showLoading();
		break;
	}
}


</script>
<body>

<form id="form1" name="form1" method="post" action="">
  <table width="500" border="0" align="center" class="dataTables_processing">
    <tr>
      <td>Desde:
        <input type="text" name="desde" id="desde" />
Hasta:
<input type="text" name="hasta" id="hasta" /></td>
    </tr>
    <tr>
      <td bgcolor="#CCCCCC" style="color:#000;font-size:16px;"><strong>Exportar a Excel
        <input name="report" type="hidden" id="report" value="0" />
      </strong></td>
    </tr>
    <tr>
      <td align="center"><button type="submit"  id="ventas" >VENTAS</button>
        <button type="submit"  id="desistidos" >DESISTIDOS</button>
      <button type="submit"  id="cancelados" >CANCELADOS</button></td>
    </tr>
  </table>
  <div id="content_dialog" ></div>
</form>

</body>
</html>