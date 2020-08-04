<?php 
function runsql($sql){
	$con=mysql_connect("22","22","22");
	mysql_select_db("22",$con);
	$run=mysql_query($sql,$con);
	return $run;
}

$page = $_GET['plan'];

$D_fecha=$_GET['D_fecha'];
$H_fecha=$_GET['H_fecha'];

	if (substr($_GET['D_fecha'],4,1)=='-'){
		$mifechai =  explode("-", $_GET['D_fecha']);
		$mifechae =  explode("-", $_GET['H_fecha']);
		$D_fecha = $mifechai[0]."-".$mifechai[1]."-".$mifechai[2];
		$H_fecha = $mifechae[0]."-".$mifechae[1]."-".$mifechae[2];
		
	}else if (substr($_GET['D_fecha'],2,1)=='-'){
		$D_fecha=$_GET['D_fecha'];
		$H_fecha=$_GET['H_fecha'];

	}else{
	$mifechai =  explode("/", $D_fecha );
	$mifechae =  explode("/", $H_fecha );
 	 $D_fecha =  str_replace(" ","",$mifechai[2])."-".sprintf("%02d",$mifechai[0])."-".sprintf("%02d",$mifechai[1]);
 	 $H_fecha =  str_replace(" ","",$mifechae[2])."-".sprintf("%02d",$mifechae[0])."-".sprintf("%02d",$mifechae[1]);	
	}

//echo $D_fecha . " ". $H_fecha;

$lstStatus=$_GET['lstStatus'];
$Suscripcion=$_GET['Suscripcion'];

$Tipo_suscrip=$_GET['Tipo_suscrip'];
$dia=$_GET['dia'];

$SQL="SELECT 
CONCAT(client_personal_data.name, ' ', client_personal_data.lastname) AS client, DATE_FORMAT(client_services.activation_date, '%d-%m-%Y') AS fecha_subscripcion, DATE_FORMAT(client_services.end_date, '%d-%m-%Y') AS fecha_vencimiento,  client_personal_data.id,  CONCAT(reseller_data.nombre, ' ', reseller_data.apellido) AS representante,  client_services.`status` AS estatus,  reseller_services_template.descripcion,  system_table_metod.code AS metodo_suscripcion,  client_services.destination_phone,  client_services.asignated_phone,  client_personal_data.`address 1` AS address,count(*) as total, DATEDIFF(DATE_FORMAT('".$H_fecha."', '%Y-%m-%d'),DATE_FORMAT(client_services.end_date, '%Y-%m-%d')) as dias FROM  client_services,  system_table_metod,  client_personal_data,  reseller_data,  `reseller_services_template` WHERE  client_services.susc_method_id = system_table_metod.id AND   client_personal_data.create_by = reseller_data.id AND  client_services.`sus_type_id` = `reseller_services_template`.id AND   client_personal_data.id = client_services.client_id  ";

	if ($_GET['checkclient']==2){
$SQL .= " client_services.create_by = $page_protect->id AND (CONCAT(client_personal_data.name, ' ', client_personal_data.lastname) like '%".$_GET['search_field']."%' or  client_services.`asignated_phone` like '%".$_GET['search_field']."%')  ";
	}

	if ($lstStatus=='2'){
		//$SQL .=" AND  client_services.`end_date` = '".$D_fecha."'";
		$SQL .=" AND client_services.end_date>='".$D_fecha."'  AND  client_services.end_date<='".$H_fecha."'";
	}else if ($D_fecha!='' and $H_fecha!=''){
	//orden 2007/1/2
		$SQL .=" AND client_services.activation_date>='".$D_fecha."'  AND  client_services.activation_date<='".$H_fecha."'";
	}
	
	
	if ($lstStatus!='NA'){	
 
		if ($lstStatus==2) {
			$SQL .=" AND  (client_services.`status` = '0' or client_services.`status` = '1')";
		}else{
			$SQL .=" AND  client_services.`status` = '".$lstStatus."'";
		}
	}
		
	if ($Suscripcion!='NA'){
		$SQL .=" and system_table_metod.id='".$Suscripcion ."'";
	}
	
	if ($Tipo_suscrip!='NA'){
	//$SQL .=" and `reseller_services_template`.id='".$Tipo_suscrip."'";
		$SQL .=" and client_services.sus_type_id='".$Tipo_suscrip ."'";
	}
	
	if ($dia!='NA'){
	//$SQL .=" and `reseller_services_template`.id='".$Tipo_suscrip."'";
		$SQL .=" and client_services.end_date = DATE_ADD(CURDATE(),INTERVAL '". $dia."' DAY) ";
	}
	if ($_GET['Region']!='NA'){
	//$SQL .=" and `reseller_services_template`.id='".$Tipo_suscrip."'";
		$SQL .=" and reseller_services_template.`region_code` = '". $_GET['Region']."' ";
	}
	
	$SQL.=" GROUP BY client_id";
	
	
?>
<html>
<head>
</head>
<body>
<h3>
<table width="100%" border="1" align="center" cellpadding="1" cellspacing="1" bordercolor="#EEEEEE">
  <tr bgcolor="#777777">
    <td><div align="center" ><font color="#FFFFFF">Nombre</font></div></td>
    <td bgcolor="#777777"><div align="center" ><font color="#FFFFFF">T. Asignado</font></div></td>
    <td><div align="center" ><font color="#FFFFFF">T. Destino </font></div></td>
    <td><div align="center" ><font color="#FFFFFF">T. Plan</font> </div></td>
    <td><div align="center" ><font color="#FFFFFF">F. Suscripci&oacute;n</font> </div></td>
    <td><div align="center" ><font color="#FFFFFF">F. Vencimiento </font></div></td>
    <td><div align="center" ><font color="#FFFFFF">Representante</font></div></td>
    </tr>
<?
$LinkMetod=runsql($SQL);

	while($Data=mysql_fetch_array($LinkMetod)){
?> 
  <tr bgcolor="#FCFCFC">
    <td align="center" ><?=$Data['client']?></td>
    <td align="center" ><?=$Data['asignated_phone']?></td>
    <td align="center" ><?=$Data['destination_phone']?></td>
    <td align="center" ><?=$Data['descripcion']?></td>
    <td align="center" ><?=$Data['fecha_subscripcion']?></td>
    <td align="center" ><?=$Data['fecha_vencimiento']?></td>
    <td align="center" ><?=$Data['representante']?></td>
    </tr>
<?
	}
?>
  <tr>
    <td colspan="7" bgcolor="#777777" height="8"></td>
  </tr>
</table>
</h3>
</body>
</html>
