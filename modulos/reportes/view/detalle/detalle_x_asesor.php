<?php 
if (!isset($protect)){
	exit;
}	 

if ((!$protect->getIfAccessPageById(156)) && (!$protect->getIfAccessPageById(198))){
	echo "No tiene permisos";
	exit;
}
if (!isset($_REQUEST['request'])){
	echo "Error";
	exit;
}
$row=json_decode(System::getInstance()->Decrypt($_REQUEST['request']));
 
 
 
  
$SQL="SELECT 
	  contratos.codigo_asesor, 
	 contratos.`nombre_asesor`, 
	 contratos.`nombre_cliente`,
	 SUM(no_productos) AS PRODUCTOS,
	 SUM(descuento) AS DESCUENTOS,
	 COUNT(contratos.`nombre_gerente`) AS CANTIDAD, 
	 SUM(((precio_lista-monto_capitalizado) - descuento) * tipo_cambio) AS MONTO,
	 SUM((SELECT SUM(MONTO) AS MONTO FROM (SELECT 
		(m.MONTO*m.TIPO_CAMBIO) AS MONTO,
		m.serie_contrato,
		m.no_contrato,
		m.FECHA 
	FROM `movimiento_caja` AS m
		WHERE m.ANULADO='N' AND m.INICIAL='S' 
		AND m.TIPO_DOC='RBC' 
		AND m.FECHA BETWEEN '".$row->day_from."' and '".$row->day_to."' )
	 AS MC WHERE 
	  MC.no_contrato=contratos.no_contrato AND 
		MC.serie_contrato=contratos.serie_contrato )) AS INICIAL,
	 estatus.descripcion  AS estatus
 FROM  contratos_ventas AS contratos 
   INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
 WHERE  contratos.estatus IN ('1','13', '20','54') AND
  contratos.codigo_gerente='".$row->codigo_gerente."'    
	AND contratos.fecha_venta  BETWEEN '".$row->day_from."' and '".$row->day_to."'
  GROUP BY contratos.codigo_asesor "; 
  
$rsa=mysql_query($SQL); 
while($rsA= mysql_fetch_assoc($rsa)){
		$_request=array(
					"codigo_gerente"=>$row->codigo_gerente,
					"codigo_asesor"=>$rsA['codigo_asesor'],
					"day_from"=>$row->day_from,
					"day_to"=>$row->day_to
				); 
?>     
  <tr style="display:none;background:#ACCAF7;cursor:pointer" class="GR<?php echo $row->codigo_gerente;?>"  
  onclick="toggle_ase('ASE<?php echo $row->codigo_gerente.$rsA['codigo_asesor'];?>','<?php echo System::getInstance()->Encrypt(json_encode($_request))?>','detalle_asesor_<?php echo $rsA['codigo_asesor'];?>');" id="detalle_asesor_<?php echo $rsA['codigo_asesor'];?>" is_tongle="0">
   <td height="25" colspan="1" style="padding-left:30px;" ><?php echo utf8_encode($rsA['nombre_asesor'])?></td>
   <td colspan="6" align="center" style="background:#ACCAF7">

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-hover">
<?php
 
?> 
  <tr> 
    <td width="10%" height="25" align="center"><?php echo $rowar['estatus'];?></td>
    <td width="10%" align="center"><?php echo $rsA['CANTIDAD'];?></td>
    <td width="10%" align="center"><?php echo $rsA['PRODUCTOS'];?></td>
    <td width="10%" align="center"><?php echo number_format($rsA['INICIAL'],2);?></td>
    <td width="10%" align="center"><?php echo number_format($rsA['DESCUENTOS'],2);?></td>
    <td width="10%" align="center">$<?php echo number_format($rsA['MONTO'],2);?></td>
  </tr>
 
  </table>   
   
   </td>
  </tr>
 
<?php } ?>    