<?php
if (!isset($protect)){
	exit;
}

SystemHtml::getInstance()->addTagStyle("css/bootstrap.min.css");


function getMetasFromAsesor($id_comercial,$ano_meta){

	$MES=array(
		"1"=>0,
		"2"=>0,
		"3"=>0,
		"4"=>0,
		"5"=>0,
		"6"=>0,
		"7"=>0,
		"8"=>0,
		"9"=>0,
		"10"=>0,
		"11"=>0,
		"12"=>0
	);

	$SQL="SELECT MES_META,MONTO_META FROM `metas_ventas` 
			WHERE `ID_COMERCIAL`='".$id_comercial."' AND ANO_META='".$ano_meta."' "; 
	
	$rs=mysql_query($SQL); 
	while($row= mysql_fetch_assoc($rs)){ 
		$MES[$row['MES_META']]=$row['MONTO_META'];
	}
	 
	return $MES;
}

function getRealizadosFromAsesor($id_comercial,$day_from,$day_to){

	$MES=array(
		"1"=>0,
		"2"=>0,
		"3"=>0,
		"4"=>0,
		"5"=>0,
		"6"=>0,
		"7"=>0,
		"8"=>0,
		"9"=>0,
		"10"=>0,
		"11"=>0,
		"12"=>0
	);

	$SQL="SELECT 
		SUM(contratos.precio_neto) AS MONTO,
		MONTH(contratos.fecha_ingreso) AS MES
	 FROM `contratos` 
	INNER JOIN sys_personas ON (sys_personas.id_nit=contratos.`id_nit_cliente`)
	INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus)
	WHERE contratos.asesor='".$id_comercial."' AND  contratos.fecha_ingreso between '".$day_from."' and '".$day_to."'
	GROUP BY contratos.fecha_ingreso   "; 
	
	$rs=mysql_query($SQL); 
	while($row= mysql_fetch_assoc($rs)){ 
		$MES[$row['MES']]=$row['MONTO'];
	}
	
	return $MES;
}


?>
<div style="padding-left:20px;">
<?php 
include("menu.php");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-bordered">
  <tr>
    <td>&nbsp;</td>
  
    <td align="center">Mes 1</td>
    <td align="center">Mes 2</td>
    <td align="center">Mes 3</td>
    <td align="center">Mes 4</td>
    <td align="center">Mes 5</td>
    <td align="center">Mes 6</td>
    <td align="center">Mes 7</td>
    <td align="center">Mes 8</td>
    <td align="center">Mes 9</td>
    <td align="center">Mes 10</td>
    <td align="center">Mes 11</td>
    <td align="center">Mes 12</td>
    
  </tr>
<?php

	$SQL="SELECT id_comercial,
CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) AS nombre_completo
 FROM `asesores_g_d_gg_view`
INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit) 
INNER JOIN `contratos` ON (asesores_g_d_gg_view.id_comercial=contratos.asesor)
WHERE  contratos.fecha_ingreso between '".$day_from."' and '".$day_to."'
GROUP BY asesores_g_d_gg_view.id_comercial ";

 
$rqs=mysql_query($SQL);  
while($rows= mysql_fetch_assoc($rqs)){	
	$METAS=getMetasFromAsesor($rows['id_comercial'],date("Y"));
	$REALIZADO=getRealizadosFromAsesor($rows['id_comercial'],$day_from,$day_to);
	
	
?>  
  <tr>
    <td width="160" height="40" align="center"><strong><?php echo $rows['nombre_completo']?></strong></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
 
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[1],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[1],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[2],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[2],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[3],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[3],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[4],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[4],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[5],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[5],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[6],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[6],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[7],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[7],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[8],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[8],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[9],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[9],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[10],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[10],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[11],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[11],2);?></td>
      </tr>
    </table></td>
    <td align="center"><table width="150" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50" align="center">Meta</td>
        <td width="50" align="center">Realizado</td>
      </tr>
      <tr>
        <td align="center"><?php echo number_format($METAS[12],2);?></td>
        <td align="center"><?php echo number_format($REALIZADO[12],2);?></td>
      </tr>
    </table></td>
  </tr>
<?php } ?>  
</table>
</div>
