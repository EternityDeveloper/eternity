<?php

if (!isset($protect)){
	exit;
}	
 
if (!$protect->getIfAccessPageById(170)){
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
<table width="98%" border="0"  class="tb_detalle fsDivPage">
  <tr>
    <td width="41%" rowspan="2" align="center" valign="middle"><b>SUPERVISOR</b></td>
    <td colspan="4" align="center"><b>VENTAS</b></td>
  </tr>
  <tr>
    <td width="14%" align="center"><strong>Estado</strong></td>
    <td width="14%" align="center"><strong>Cantidad</strong></td>
    <td width="14%" align="center"><strong>Productos</strong></td>
    <td width="14%" align="center"><strong>Monto</strong></td>
  </tr>
<?php 

$_MONTO=0;
$_CANTIDAD=0;
$_PRODUCTO=0;

$SQL="SELECT  
	contratos.id_nit_ingreso,
	COUNT(contratos.id_nit_ingreso)  AS cantidad,
	CONCAT(sp.`primer_nombre`,' ',sp.`segundo_nombre`,
	' ',sp.`primer_apellido`,' ',sp.segundo_apellido) AS ingresado_por 
 FROM `contratos`
INNER JOIN sys_personas AS sp ON (sp.id_nit=contratos.id_nit_ingreso) 
WHERE  contratos.fecha_ingreso  BETWEEN '".$day_from." 00:00:00' and '".$day_to." 23:59:59' AND
contratos.id_nit_ingreso='".UserAccess::getInstance()->getIDNIT()."'
 GROUP BY contratos.id_nit_ingreso";

$i=1;

$rs=mysql_query($SQL);  
 
while($row= mysql_fetch_object($rs)){
		 
?>  
  <tr   style="cursor:pointer" onclick="toggle('GR<?php echo $row->id_nit_ingreso;?>');">
    <td height="25" colspan="1" style="padding-left:15px;" ><strong><?php echo utf8_encode($row->ingresado_por); ?></strong></td>
    <td colspan="4" align="center">
 <table width="100%" border="0" cellspacing="0" cellpadding="0"  >
<?php
$SQL="SELECT SUM(PRODUCTOS) AS PRODUCTOS,
	SUM(MONTO)AS MONTO,
	COUNT(MONTO) AS CANTIDAD,
	estatus,
	id_status
 FROM (SELECT   id_status,
 	sys_status.descripcion AS estatus,
	contratos.no_productos AS PRODUCTOS,
	((contratos.precio_lista-contratos.descuento)*contratos.tipo_cambio) AS MONTO 
FROM contratos 
INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus)
WHERE  
	contratos.fecha_ingreso  BETWEEN '".$day_from." 00:00:00' and '".$day_to." 23:59:59' and 
		contratos.id_nit_ingreso='".$row->id_nit_ingreso."'  AND contratos.estatus IN (1,13) 
		 ) AS CT 
GROUP BY estatus	"; 

$rsx=mysql_query($SQL);
while($rowx= mysql_fetch_assoc($rsx)){
	$_MONTO=$_MONTO+$rowx['MONTO'];
	$_CANTIDAD=$_CANTIDAD+$rowx['CANTIDAD'];
	$_PRODUCTO=$_PRODUCTO+$rowx['PRODUCTOS'];
  
if ($rowx['id_status']==13){	
?>  
  <tr> 
    <td width="100" height="25" align="center"><?php echo $rowx['estatus'];?></td>
    <td width="100" align="center"><?php echo $rowx['CANTIDAD'];?></td>
    <td width="100" align="center"><?php echo $rowx['PRODUCTOS'];?></td>
    <td width="100" align="center">$<?php echo number_format($rowx['MONTO'],2);?></td>
  </tr>
<?php } ?>  
<?php 	
if ($rowx['id_status']==1){
?>
  <tr >
    <td  width="100" height="25" align="center"><?php echo $rowx['estatus'];?></td>
    <td  width="100" align="center"><?php echo $rowx['CANTIDAD'];?></td>
    <td width="100"  align="center"><?php echo $rowx['PRODUCTOS'];?></td>
    <td width="100"  align="center">$<?php echo number_format($rowx['MONTO'],2);?></td>
  </tr>
<?php } ?>      
<?php } ?>
</table>   
    
    </td> 
    </tr> 
  <tr style="display:none;background:#AEFFAE" class="GR<?php echo $row->id_nit_ingreso;?>">
    <td height="25" colspan="5">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-hover" style="font-size:10px;">
      <tr>
        <td align="left"><strong>OFERTA</strong></td>
        <td height="25" align="left"><strong>NOMBRE CLIENTE</strong></td>
        <td align="center"><strong>PRODUCTOS</strong></td>
        <td align="center"><strong>PRECIO LISTA</strong></td>
        <td align="center"><strong>PRECIO NETO</strong></td>
        <td align="center"><strong>NO.PRODUCTO</strong></td>
        <td align="center"><strong>DESCUENTO</strong></td>
        <td align="center"><strong>ENGANCHE</strong></td>
        <td align="center"><strong>INTERES</strong></td>
        <td align="center"><strong>CUOTAS</strong></td>
        <td align="center"><strong>VALOR CUOTA</strong></td>
        <td align="center"><strong>FECHA VENTA</strong></td>
      </tr>
<?php
$SQL="SELECT  
	CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) AS contrato,
	sys_status.descripcion AS estatus,
	contratos.no_productos AS PRODUCTOS,
	((contratos.precio_lista-contratos.descuento)*contratos.tipo_cambio) AS MONTO,
	
	CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,
	' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_cliente,
	contratos.*
	FROM contratos 
	INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus) 
	INNER JOIN sys_personas ON (sys_personas.id_nit=contratos.`id_nit_cliente`)
	WHERE  
		contratos.fecha_ingreso BETWEEN '".$day_from." 00:00:00' and '".$day_to." 23:59:59'  
		and contratos.id_nit_ingreso='".$row->id_nit_ingreso."'  AND 
		contratos.estatus IN (1,13) ";

$rsar=mysql_query($SQL);
while($rowar= mysql_fetch_assoc($rsar)){ 

 ?>       
      <tr>
        <td align="left"><?php echo $rowar['contrato']?></td>
        <td height="25" align="left"><?php echo utf8_encode($rowar['nombre_cliente']);?>&nbsp;</td>
        <td align="center"><?php echo $rowar['PRODUCTOS']?></td>
        <td align="center"><?php echo number_format($rowar['precio_lista'],2);?></td>
        <td align="center"><?php echo number_format($rowar['precio_neto'],2);?></td>
        <td align="center"><?php echo number_format($rowar['descuento'],2);?></td>
        <td align="center"><?php echo $rowar['descuento']?></td>
        <td align="center"><?php echo number_format($rowar['enganche'],2);?></td>
        <td align="center"><?php echo number_format($rowar['interes'],2);?></td>
        <td align="center"><?php echo $rowar['cuotas']?></td>
        <td align="center"><?php echo number_format($rowar['valor_cuota'],2);?></td>
        <td align="center"><?php echo $rowar['fecha_venta']?></td>
      </tr>
<?php }  ?>           
    </table></td>
    </tr>
<?php 
$i++;
} ?>    
  <tr>
    <td height="25" colspan="2" align="right"><b>TOTALES DEL PERIODO <?php echo  date("d/m/Y", strtotime($day_from)); ?></b> AL <b><?php echo date("d/m/Y", strtotime($day_to)); ?></b></td>
    <td align="center"><?php echo $_CANTIDAD;?></td>
    <td align="center"><?php echo $_PRODUCTO;?></td>
    <td align="center"><b>RD$<?php echo number_format($_MONTO,2);?></b></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><b>INGRESO DE NEGOCIOS <?php echo  date("d/m/Y", strtotime($day_from)); ?></b> AL <b><?php echo date("d/m/Y", strtotime($day_to)); ?></b></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>


</div>