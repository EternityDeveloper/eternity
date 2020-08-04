<?php

if (!isset($protect)){
	exit;
}	
 
if (!$protect->getIfAccessPageById(172)){
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
    <td width="41%" rowspan="2" align="center" valign="middle"><b>ASESORES</b></td>
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


$SQL="SELECT codigo_gerente_grupo AS codigo_gerente,
CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,
	' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_gerente 
 FROM `sys_gerentes_grupos`	
INNER JOIN sys_personas ON (sys_personas.id_nit=sys_gerentes_grupos.id_nit)
WHERE sys_gerentes_grupos.status=1 AND 
	sys_gerentes_grupos.`codigo_gerente_grupo`='".$protect->getComercialID()."' ";

$i=1;

$rs=mysql_query($SQL);  
 
while($row= mysql_fetch_object($rs)){
		 
?>  
  <tr   style="cursor:pointer" onclick="toggle('GR<?php echo $row->codigo_gerente;?>');">
    <td height="25" colspan="1" style="padding-left:15px;" ><strong><?php echo $row->nombre_gerente;//." ".$row->codigo_gerente ?></strong></td>
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
	(((contratos.precio_lista-monto_capitalizado)-contratos.descuento)*contratos.tipo_cambio) AS MONTO 
FROM contratos_ventas as contratos 
INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus)
WHERE  
	contratos.fecha_venta  BETWEEN '".$day_from."' and '".$day_to."' and 
		contratos.codigo_gerente='".$row->codigo_gerente."'  AND contratos.estatus IN (1,13,20,54) 
		 ) AS CT  ";
		 
 	 
 
$rsx=mysql_query($SQL);
while($rowx= mysql_fetch_assoc($rsx)){
	$_MONTO=$_MONTO+$rowx['MONTO'];
	$_CANTIDAD=$_CANTIDAD+$rowx['CANTIDAD'];
	$_PRODUCTO=$_PRODUCTO+$rowx['PRODUCTOS'];
 
if ($rowx['id_status']==-1){	
?>  
  <tr> 
    <td width="100" height="25" align="center"><?php echo $rowx['estatus'];?></td>
    <td width="100" align="center"><?php echo $rowx['CANTIDAD'];?></td>
    <td width="100" align="center"><?php echo $rowx['PRODUCTOS'];?></td>
    <td width="100" align="center">$<?php echo number_format($rowx['MONTO'],2);?></td>
  </tr>
<?php } ?>  
<?php 	
//if ($rowx['id_status']==1){
?>
  <tr >
    <td  width="100" height="25" align="center"><?php echo $rowx['estatus'];?></td>
    <td  width="100" align="center"><?php echo $rowx['CANTIDAD'];?></td>
    <td width="100"  align="center"><?php echo $rowx['PRODUCTOS'];?></td>
    <td width="100"  align="center">$<?php echo number_format($rowx['MONTO'],2);?></td>
  </tr>
<?php // } ?>      
<?php } ?>
</table>   
    
    </td> 
    </tr>
<?php 
 
$SQL="
	SELECT  sys_asesor.codigo_asesor  ,
		CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,' ',
		sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_asesor 
	FROM contratos_ventas as contratos
	INNER JOIN sys_asesor ON (sys_asesor.codigo_asesor=contratos.codigo_asesor)
	INNER JOIN sys_personas ON (sys_personas.id_nit=sys_asesor.id_nit)	
	WHERE  contratos.codigo_gerente='".$row->codigo_gerente."' AND 
	contratos.fecha_venta BETWEEN '".$day_from."' and '".$day_to."'  AND contratos.estatus IN (1,13,20,54)
	GROUP BY  sys_asesor.codigo_asesor  
";

 
$rsa=mysql_query($SQL);
 
while($rsA= mysql_fetch_assoc($rsa)){
		$_request=array(
				"codigo_gerente"=>$row->codigo_gerente,
				"codigo_asesor"=>$rsA['codigo_asesor'],
				"day_from"=>$day_from,
				"day_to"=>$day_to
			); 
?>    

  <tr style="background:#ACCAF7;cursor:pointer" class="GR<?php echo $row->codigo_gerente;?>"  
  onclick="toggle_ase('ASE<?php echo $row->codigo_gerente.$rsA['codigo_asesor'];?>','<?php echo System::getInstance()->Encrypt(json_encode($_request))?>','detalle_asesor_<?php echo $rsA['codigo_asesor'];?>');"
  	 id="detalle_asesor_<?php echo $rsA['codigo_asesor'];?>" is_tongle="0">
   <td height="25" colspan="1" style="padding-left:30px;"><?php echo utf8_encode($rsA['nombre_asesor'])?></td>
   <td colspan="4" align="center" style="background:#ACCAF7">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-hover">
<?php
 
$SQL=" SELECT 
		SUM(PRODUCTOS) AS PRODUCTOS,
		SUM(MONTO)AS MONTO,
		COUNT(MONTO) AS CANTIDAD,
		estatus,
		id_status
	 FROM (SELECT   
	 		id_status,
			sys_status.descripcion AS estatus,
			contratos.no_productos AS PRODUCTOS,
			(((contratos.precio_lista-monto_capitalizado)-contratos.descuento)*contratos.tipo_cambio) AS MONTO 
	FROM contratos_ventas as contratos 
		INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus)
	WHERE  
		contratos.fecha_venta BETWEEN '".$day_from."' and '".$day_to."'  
		and contratos.codigo_asesor='".$rsA['codigo_asesor']."' AND contratos.estatus IN (1,13,20,54) 
			) AS CT 
	GROUP BY estatus ";


$rsar=mysql_query($SQL);
while($rowar= mysql_fetch_assoc($rsar)){ 
?> 
  <tr> 
    <td width="10%" height="25" align="center"><?php echo $rowar['estatus'];?></td>
    <td width="10%" align="center"><?php echo $rowar['CANTIDAD'];?></td>
    <td width="10%" align="center"><?php echo $rowar['PRODUCTOS'];?></td>
    <td width="10%" align="center">$<?php echo number_format($rowar['MONTO'],2);?></td>
  </tr>
<?php } ?>  
  </table>
  
  	</td>
  </tr>


<?php } ?>

    
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
    <td><b>VENTAS POR PLANES DEL PERIODO <?php echo  date("d/m/Y", strtotime($day_from)); ?></b> AL <b><?php echo date("d/m/Y", strtotime($day_to)); ?></b></td>
  </tr>
</table>


</div>