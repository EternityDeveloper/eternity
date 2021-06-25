<?php 
if (!isset($protect)){
	exit;
}	
 
if (!$protect->getIfAccessPageById(156)){
//	echo "No tiene permisos";
//	exit;
}
if (!isset($_REQUEST['request'])){
	echo "Error";
	exit;
}
$row=json_decode(System::getInstance()->Decrypt($_REQUEST['request']));
 

$SQL="SELECT 
	  CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) AS contrato,
	  contratos.codigo_asesor, 
	 contratos.`nombre_asesor`, 
	 contratos.`nombre_cliente`,
	 contratos.nombre_producto as  producto,
	 (no_productos) AS PRODUCTOS,
	 (descuento) AS DESCUENTOS,
	 (contratos.`nombre_gerente`) AS CANTIDAD, 
	 (((precio_lista-monto_capitalizado) - descuento) * tipo_cambio) AS MONTO,
	 ((SELECT SUM(MONTO) AS MONTO FROM (SELECT 
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
  	AND contratos.codigo_asesor='".$row->codigo_asesor."'   
	AND contratos.fecha_venta  BETWEEN '".$row->day_from."' and '".$row->day_to."' ";
		  
$rsar=mysql_query($SQL);
while($rowar= mysql_fetch_assoc($rsar)){ 

 ?>  
  <tr style="display:none;background:#AEFFAE" class="GR<?php echo $row->codigo_gerente;?> ASE<?php echo  $row->codigo_gerente.$row->codigo_asesor;?>">
    <td height="25" colspan="1" style="padding-left:60px;"><?php echo $rowar['nombre_cliente']?>&nbsp;(<?php echo $rowar['contrato']?>) (<?php echo $rowar['producto']?>)</td>
    <td colspan="6" align="center" style="background:#AEFFAE"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-hover">
      <tr>
        <td width="10%" height="25" align="center"><?php echo $rowar['estatus'];?></td>
        <td width="10%" align="center">1</td>
        <td width="10%" align="center"><?php echo $rowar['PRODUCTOS'];?></td>
        <td width="10%" align="center"><?php echo number_format($rowar['INICIAL'],2);?></td>
        <td width="10%" align="center"><?php echo number_format($rowar['DESCUENTOS'],2);?></td>
        <td width="10%" align="center">$<?php echo number_format($rowar['MONTO'],2);?></td>
      </tr> 
    </table></td>
  </tr>
<?php }  ?> 