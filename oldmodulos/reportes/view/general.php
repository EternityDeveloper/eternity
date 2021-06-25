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


function toggle(id,request,paint){  
	var x= new Class({});
	var f = new x(); 
   if ($("#"+paint).attr("is_tongle")==0){
	   f.Block(); 
	   $.get("./?mod_reportes/report&type=2",{
				"filter":1,
				"request":request
			},function(data){
 			$("#"+paint).after(data);	
		    $("."+id).toggle();
			$("#"+paint).attr("is_tongle",1);
			f.unBlock();	   
	   });
   }else{
	   $("."+id).toggle();	
	   $("#"+paint).attr("is_tongle",1);   
    }

}
function toggle_ase(id,request,paint){  
	var x= new Class({});
	var f = new x();
	
   if ($("#"+paint).attr("is_tongle")==0){
	   f.Block(); 
	   $.get("./?mod_reportes/report&type=3",{
				"filter":2,
				"request":request
			},function(data){ 
 			$("#"+paint).after(data);	
		    $("."+id).toggle();
			$("#"+paint).attr("is_tongle",1);	
			f.unBlock();   
	   });
   }else{
	   $("."+id).toggle();	
	   $("#"+paint).attr("is_tongle",1);   
    }

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
	 sum((contratos.descuento*contratos.tipo_cambio)) as DESCUENTOS,
	 COUNT(contratos.`nombre_gerente`) AS CANTIDAD,
	 tipo_moneda AS MONEDA,
	 tipo_cambio AS TASA,
	 SUM(((precio_lista-monto_capitalizado) - descuento) * tipo_cambio) AS MONTO,
	 SUM(((SELECT SUM(MONTO) AS MONTO FROM (SELECT 
	(m.MONTO*m.TIPO_CAMBIO) AS MONTO,
	m.serie_contrato,
	m.no_contrato,
	m.FECHA 
FROM `movimiento_caja` AS m
INNER JOIN `movimiento_factura` AS mf ON  (m.`SERIE`=mf.`CAJA_SERIE` 
			AND m.`NO_DOCTO`=mf.`CAJA_NO_DOCTO`)
	WHERE m.ANULADO='N'  AND (m.INICIAL='S'  OR mf.TIPO_MOV='INI')
	AND m.TIPO_DOC='RBC' 
	AND m.FECHA BETWEEN '".$day_from."' AND '".$day_to."'  )
 AS MC WHERE 
  MC.no_contrato=contratos.no_contrato AND 
	MC.serie_contrato=contratos.serie_contrato ))) AS INICIAL,
	 estatus.descripcion  AS estatus
	 FROM  contratos_ventas AS contratos 
	   INNER JOIN sys_status AS estatus ON (estatus.id_status = contratos.estatus) 
	 WHERE  contratos.estatus IN ('1','13', '20','54') AND 
	 contratos.fecha_venta  BETWEEN '".$day_from."' and '".$day_to."'
	 GROUP BY contratos.`codigo_gerente`  ";  
	 
  
$i=1;

$rs=mysql_query($SQL);   
while($row= mysql_fetch_assoc($rs)){
	$_MONTO=$_MONTO+$row['MONTO'];
	$_CANTIDAD=$_CANTIDAD+$row['CANTIDAD'];
	$_PRODUCTO=$_PRODUCTO+$row['PRODUCTOS'];
	$_INICIAL=$_INICIAL+$row['INICIAL'];
	$_DESCUENTO=$_DESCUENTO+$row['DESCUENTOS'];		
	
	$_request=array(
					"codigo_gerente"=>$row['codigo_gerente'],
					"day_from"=>$day_from,
					"day_to"=>$day_to
				); 
?>  
  <tr   style="cursor:pointer" onclick="toggle('GR<?php echo $row['codigo_gerente'];?>','<?php echo System::getInstance()->Encrypt(json_encode($_request))?>','detalle_gerente_<?php echo $row['codigo_gerente'];?>');" id="detalle_gerente_<?php echo $row['codigo_gerente'];?>" is_tongle="0">
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
	((contratos.descuento*contratos.tipo_cambio)) as descuento,
	(((contratos.precio_lista-monto_capitalizado)-contratos.descuento)*contratos.tipo_cambio) AS MONTO,
	
 (((SELECT SUM(MONTO) AS MONTO FROM (SELECT 
	(m.MONTO*m.TIPO_CAMBIO) AS MONTO,
	m.serie_contrato,
	m.no_contrato,
	m.FECHA 
FROM `movimiento_caja` AS m
INNER JOIN `movimiento_factura` AS mf ON  (m.`SERIE`=mf.`CAJA_SERIE` 
			AND m.`NO_DOCTO`=mf.`CAJA_NO_DOCTO`)
	WHERE m.ANULADO='N'  AND (m.INICIAL='S'  OR mf.TIPO_MOV='INI')
	AND m.TIPO_DOC='RBC' 
	AND m.FECHA BETWEEN '".$day_from."' AND '".$day_to."'  )
 AS MC WHERE 
  MC.no_contrato=contratos.no_contrato AND 
	MC.serie_contrato=contratos.serie_contrato ))) AS INICIAL


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