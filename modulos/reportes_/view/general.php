<?php

if (!isset($protect)){
	exit;
}	
 
if (!$protect->getIfAccessPageById(156)){
	echo "No tiene permisos";
	exit;
}





?>

<script>
$(function(){
	$(".textfield").datepicker({
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
});


function toggle(id,total){
   $("."+id).toggle();
}
</script>
<div style="padding-left:20px;">
<?php 
include("menu.php");
 
?>
<table width="98%" border="0"  class="tb_detalle fsDivPage table-hover">
  <tr>
    <td width="250" rowspan="2" align="center" valign="middle"><b>SUPERVISOR</b></td>
    <td colspan="6" align="center"><b>VENTAS</b></td>
  </tr>
  <tr>
    <td width="100" height="30" align="center"><strong>Estado</strong></td>
    <td width="100" align="center"><strong>Cantidad</strong></td>
    <td width="100" align="center"><strong>Productos</strong></td>
    <td width="100" align="center"><strong>Inicial</strong></td>
    <td width="100" align="center"><strong>Descuentos</strong></td>
    <td width="100" align="center"><strong>Monto</strong></td>
  </tr>
<?php 

$_MONTO=0;
$_CANTIDAD=0;
$_PRODUCTO=0;
$_INICIAL=0;
$_DESCUENTO=0;

$SQL="SELECT 
	  contratos.codigo_gerente, 
	 contratos.`nombre_gerente`, 
	 SUM(no_productos) AS PRODUCTOS,
	 sum(descuento) as DESCUENTOS,
	 COUNT(contratos.`nombre_gerente`) AS CANTIDAD,
	 tipo_moneda AS MONEDA,
	 tipo_cambio AS TASA,
	 SUM(((precio_lista-monto_capitalizado) - descuento) * tipo_cambio) AS MONTO,
	 SUM((enganche-monto_capitalizado)* tipo_cambio) AS INICIAL,
	 estatus.descripcion  AS estatus
	 FROM  contratos_ventas AS contratos 
	   INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
	 WHERE  contratos.estatus IN ('1','13', '20','54') AND 
	 contratos.fecha_venta  BETWEEN '".$day_from."' and '".$day_to."'
	 GROUP BY contratos.`nombre_gerente`  ";  
$i=1;

$rs=mysql_query($SQL);   
while($row= mysql_fetch_assoc($rs)){
	$_MONTO=$_MONTO+$row['MONTO'];
	$_CANTIDAD=$_CANTIDAD+$row['CANTIDAD'];
	$_PRODUCTO=$_PRODUCTO+$row['PRODUCTOS'];
	$_INICIAL=$_INICIAL+$row['INICIAL'];
	$_DESCUENTO=$_DESCUENTO+$row['DESCUENTOS'];		 
?>  
  <tr   style="cursor:pointer" onclick="toggle('GR<?php echo $row['codigo_gerente'];?>');">
    <td height="25" colspan="1" style="padding-left:15px;" ><strong><?php echo $row['nombre_gerente'];//." ".$row->codigo_gerente ?></strong></td>
    <td colspan="6" align="center">
    
 <table width="100%" border="0" cellspacing="0" cellpadding="0"  > 
  <tr >
    <td  width="100" height="25" align="center"><?php echo $row['estatus'];?></td>
    <td  width="100" align="center"><?php echo $row['CANTIDAD'];?></td>
    <td width="100"  align="center"><?php echo $row['PRODUCTOS'];?></td>
    <td width="100"  align="center"><?php echo number_format($row['INICIAL'],2);?></td>
    <td width="100"  align="center"><?php echo number_format($row['DESCUENTOS'],2);?></td>
    <td width="100"  align="center">$<?php echo number_format($row['MONTO'],2);?></td>
  </tr>
     
</table>   
    
    </td> 
    </tr>
<?php 
 
$SQL="
	SELECT  sys_asesor.codigo_asesor  ,
		CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',
		sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_asesor 
	FROM  contratos_ventas as contratos
	INNER JOIN sys_asesor ON (sys_asesor.codigo_asesor=contratos.codigo_asesor)
	INNER JOIN sys_personas ON (sys_personas.id_nit=sys_asesor.id_nit)	
	WHERE  contratos.codigo_gerente='".$row['codigo_gerente']."' AND 
	contratos.fecha_venta BETWEEN '".$day_from."' and '".$day_to."'  AND contratos.estatus IN (1,13,20,54)
	GROUP BY  sys_asesor.codigo_asesor  
";

$SQL="SELECT 
	  contratos.codigo_gerente, 
	 contratos.`nombre_gerente`, 
	 SUM(no_productos) AS PRODUCTOS,
	 sum(descuento) as DESCUENTOS,
	 COUNT(contratos.`nombre_gerente`) AS CANTIDAD,
	 tipo_moneda AS MONEDA,
	 tipo_cambio AS TASA,
	 SUM(((precio_lista-monto_capitalizado) - descuento) * tipo_cambio) AS MONTO,
	 SUM((enganche-monto_capitalizado)* tipo_cambio) AS INICIAL,
	 estatus.descripcion  AS estatus
	 FROM  contratos_ventas AS contratos 
	   INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
	 WHERE  contratos.estatus IN ('1','13', '20','54') AND 
	 contratos.fecha_venta  BETWEEN '".$day_from."' and '".$day_to."'
	 GROUP BY contratos.`nombre_asesor`  ";    
	 
$SQL="SELECT 
	  contratos.codigo_asesor, 
	 contratos.`nombre_asesor`, 
	 SUM(no_productos) AS PRODUCTOS,
	 SUM(descuento) AS DESCUENTOS,
	 COUNT(contratos.`nombre_gerente`) AS CANTIDAD,
	 tipo_moneda AS MONEDA,
	 tipo_cambio AS TASA,
	 SUM(((precio_lista-monto_capitalizado) - descuento) * tipo_cambio) AS MONTO,
	 SUM((enganche-monto_capitalizado)* tipo_cambio) AS INICIAL,
	 estatus.descripcion  AS estatus
 FROM  contratos_ventas AS contratos 
   INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
 WHERE  contratos.estatus IN ('1','13', '20','54') AND
  contratos.codigo_gerente='".$row['codigo_gerente']."' 
  	AND contratos.fecha_venta  BETWEEN '".$day_from."' and '".$day_to."'
 GROUP BY contratos.`nombre_asesor` ";	 
 
$rsa=mysql_query($SQL);
 
while($rsA= mysql_fetch_assoc($rsa)){
?>    

  <tr style="display:none;background:#ACCAF7;cursor:pointer" class="GR<?php echo $row['codigo_gerente'];?>"  
  onclick="toggle('ASE<?php echo  $row->codigo_gerente.$rsA['codigo_asesor'];?>');">
   <td height="25" colspan="1" style="padding-left:30px;"><?php echo utf8_encode($rsA['nombre_asesor'])?></td>
   <td colspan="6" align="center" style="background:#ACCAF7">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-hover">
<?php
 
$SQL=" SELECT 
		SUM(PRODUCTOS) AS PRODUCTOS,
		SUM(MONTO)AS MONTO,
		SUM(descuento) AS DESCUENTOS,
		sum(enganche) as INICIAL,		
		COUNT(MONTO) AS CANTIDAD,
		estatus,
		id_status
	 FROM (SELECT   
	 		id_status,
			sys_status.descripcion AS estatus,
			contratos.no_productos AS PRODUCTOS,
			(((contratos.precio_lista-monto_capitalizado)-contratos.descuento)*contratos.tipo_cambio) AS MONTO ,
			contratos.descuento,
			contratos.enganche,
(SELECT (CASE 
 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 and 
  pc.serie_contrato=contratos.serie_contrato and pc.no_contrato=contratos.no_contrato LIMIT 1) as producto
  			
	FROM contratos_ventas as contratos 
		INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus)
	WHERE  
		contratos.fecha_venta BETWEEN '".$day_from."' and '".$day_to."' 
		and  contratos.codigo_gerente='".$row['codigo_gerente']."' 
		and contratos.codigo_asesor='".$rsA['codigo_asesor']."' AND contratos.estatus IN (1,13,20,54) 
			) AS CT 
	GROUP BY estatus ";
 
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
		AND m.FECHA BETWEEN '".$day_from."' and '".$day_to."' )
	 AS MC WHERE 
	  MC.no_contrato=contratos.no_contrato AND 
		MC.serie_contrato=contratos.serie_contrato )) AS INICIAL,
	 estatus.descripcion  AS estatus
 FROM  contratos_ventas AS contratos 
   INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
 WHERE  contratos.estatus IN ('1','13', '20','54') AND
  contratos.codigo_gerente='".$row['codigo_gerente']."' AND contratos.codigo_asesor='".$rsA['codigo_asesor']."'   
	AND contratos.fecha_venta  BETWEEN '".$day_from."' and '".$day_to."'
 GROUP BY estatus.descripcion ";
 
$rsar=mysql_query($SQL);
while($rowar= mysql_fetch_assoc($rsar)){ 
?> 
  <tr> 
    <td width="10%" height="25" align="center"><?php echo $rowar['estatus'];?></td>
    <td width="10%" align="center"><?php echo $rowar['CANTIDAD'];?></td>
    <td width="10%" align="center"><?php echo $rowar['PRODUCTOS'];?></td>
    <td width="10%" align="center"><?php echo number_format($rowar['INICIAL'],2);?></td>
    <td width="10%" align="center"><?php echo number_format($rowar['DESCUENTOS'],2);?></td>
    <td width="10%" align="center">$<?php echo number_format($rowar['MONTO'],2);?></td>
  </tr>
<?php } ?>  
  </table>
  
  	</td>
  </tr>
<?php
$SQL="SELECT  
	CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) AS contrato,
	sys_status.descripcion AS estatus,
	contratos.no_productos AS PRODUCTOS,
	(((contratos.precio_lista-monto_capitalizado)-contratos.descuento)*contratos.tipo_cambio) AS MONTO,
	contratos.descuento,
	contratos.enganche,
	CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,
	' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_cliente,
(SELECT (CASE 
	 WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo) 
	 WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END ) AS producto 
	 FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
	  pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) AS producto	
	FROM contratos_ventas as contratos 
	INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus) 
	INNER JOIN sys_personas ON (sys_personas.id_nit=contratos.`id_nit_cliente`)
	WHERE  
		contratos.fecha_venta BETWEEN '".$day_from."' and '".$day_to."' 
		and  contratos.codigo_gerente='".$row['codigo_gerente']."' 
		and contratos.codigo_asesor='".$rsA['codigo_asesor']."' 
		 AND contratos.estatus IN (1,13,20,54) ";

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
	 ((enganche-monto_capitalizado)* tipo_cambio) AS INICIAL,
	 estatus.descripcion  AS estatus
 FROM  contratos_ventas AS contratos 
   INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
 WHERE  contratos.estatus IN ('1','13', '20','54') AND
  contratos.codigo_gerente='".$row['codigo_gerente']."' 
  	AND contratos.codigo_asesor='".$rsA['codigo_asesor']."'   
	AND contratos.fecha_venta  BETWEEN '".$day_from."' and '".$day_to."' ";
		 

$rsar=mysql_query($SQL);
while($rowar= mysql_fetch_assoc($rsar)){ 

 ?>  
  <tr style="display:none;background:#AEFFAE" class="ASE<?php echo  $row->codigo_gerente.$rsA['codigo_asesor'];?>">
    <td height="25" colspan="1" style="padding-left:60px;"><?php echo $rowar['nombre_cliente']?>&nbsp;(<?php echo $rowar['contrato']?>) (<?php echo $rowar['producto']?>)</td>
    <td colspan="6" align="center" style="background:#AEFFAE"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-hover">
      <tr>
        <td width="10%" height="25" align="center"><?php echo $rowar['estatus'];?></td>
        <td width="10%" align="center">1</td>
        <td width="10%" align="center"><?php echo $rowar['PRODUCTOS'];?></td>
        <td width="10%" align="center"><?php echo number_format($rowar['enganche'],2);?></td>
        <td width="10%" align="center"><?php echo number_format($rowar['descuento'],2);?></td>
        <td width="10%" align="center">$<?php echo number_format($rowar['MONTO'],2);?></td>
      </tr> 
    </table></td>
  </tr>
<?php }  ?> 

<?php } ?>

    
<?php 
$i++;
} ?>    
  <tr>
    <td height="35" colspan="2" align="right"><b>TOTALES DEL PERIODO <?php echo  date("d/m/Y", strtotime($day_from)); ?></b> AL <b><?php echo date("d/m/Y", strtotime($day_to)); ?></b></td>
    <td width="100" align="center"><?php echo $_CANTIDAD;?></td>
    <td width="100" align="center"><?php echo $_PRODUCTO;?></td>
    <td width="100" align="center"><?php echo number_format($_INICIAL,2);?></td>
    <td width="100" align="center"><?php echo number_format($_DESCUENTO,2);?></td>
    <td width="100" align="center"><b>RD$<?php echo number_format($_MONTO,2);?></b></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><b>VENTAS POR PLANES DEL PERIODO <?php echo  date("d/m/Y", strtotime($day_from)); ?></b> AL <b><?php echo date("d/m/Y", strtotime($day_to)); ?></b></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="20"><strong>Plan</strong></td>
        <td align="center"><strong>Ventas</strong></td>
        <td align="center"><strong>Productos</strong></td>
        <td align="center"><strong>Monto (Inc. Dscto)</strong></td>
        <td align="center"><strong>Incial (Inc. Dscto)</strong></td>
        <td align="center"><strong>Descuentos</strong></td>
      </tr>
<?php
 
$SQL="SELECT 
	SUM(PRODUCTOS) AS PRODUCTOS,
	SUM(MONTO)AS MONTO,
	COUNT(MONTO) AS CANTIDAD, 
	SUM(descuento) AS DESCUENTOS,
	SUM(INICIAL) AS INICIAL,
	cuotas
 FROM (SELECT  
	cuotas,
	sys_status.descripcion AS estatus,
	contratos.no_productos AS PRODUCTOS,
	contratos.descuento,
	(((contratos.precio_lista-monto_capitalizado)-contratos.descuento)*contratos.tipo_cambio) AS MONTO ,
	( SELECT 
	SUM(m.MONTO*m.TIPO_CAMBIO) AS MONTO 
FROM `movimiento_caja` AS m
	INNER JOIN `movimiento_factura` AS mf ON (`mf`.`CAJA_SERIE`=m.SERIE AND 
mf.`CAJA_NO_DOCTO`=m.NO_DOCTO)
	WHERE m.ANULADO='N' AND m.TIPO_DOC='RBC'   
	AND m.no_contrato=contratos.no_contrato AND m.serie_contrato=contratos.serie_contrato
AND mf.TIPO_MOV IN ('INI','RES') ) AS INICIAL
FROM contratos_ventas as contratos 
INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus)
WHERE  
	contratos.fecha_venta BETWEEN '".$day_from."' and '".$day_to."' 
 
	 AND contratos.estatus IN (1,13,20,54) 
		) AS CT 
GROUP BY cuotas	";

$VENTAS=0;
$PRODUCTO=0;
$MONTO=0;
$INICIAL=0;
$DESCUENTOS=0;
$rs=mysql_query($SQL);
$i=-1;
while($row= mysql_fetch_assoc($rs)){ 
	$i++;
	$VENTAS=$VENTAS+$row['CANTIDAD'];
	$PRODUCTO=$PRODUCTO+ $row['PRODUCTOS'];
	$MONTO=$MONTO+$row['MONTO'];
	$INICIAL=$INICIAL+$row['INICIAL'];
	$DESCUENTOS=$DESCUENTOS+$row['DESCUENTOS'];
	 
	
?>       
      <tr class="<?php echo ($i%2)?'line_one':'line_two'?>">
        <td width="200" height="25"><?php echo $row['cuotas']==0?'CONTADO':'CREDITO A '.$row['cuotas'];?></td>
        <td align="center"><?php echo $row['CANTIDAD']?></td>
        <td align="center"><?php echo $row['PRODUCTOS']?></td>
        <td align="center"><?php echo number_format($row['MONTO'],2)?></td>
        <td align="center"><?php echo number_format($row['INICIAL'],2)?></td>
        <td align="center"><?php echo number_format($row['DESCUENTOS'],2)?></td>
      </tr>
<?php } ?>
      <tr>
        <td height="25" align="right"><strong>TOTALES</strong> &nbsp;</td>
        <td align="center"><?php echo $VENTAS;?> </td>
        <td align="center"><?php echo $PRODUCTO;?></td>
        <td align="center"><strong>RD  <?php echo number_format($MONTO,2)?></strong></td>
        <td align="center"><strong>RD <?php echo number_format($INICIAL,2);?></strong></td>
        <td align="center"><strong>RD <?php echo number_format($DESCUENTOS,2);?></strong></td>
      </tr>

    </table></td>
  </tr>
</table>


</div>