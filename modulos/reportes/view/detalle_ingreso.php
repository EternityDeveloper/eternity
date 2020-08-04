<?php

if (!isset($protect)){
	exit;
}	
 
if (!$protect->getIfAccessPageById(157)){
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
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-hover" style="font-size: 10px; font-weight: bold;">
  <tr style="background:#CCC">
    <td align="left"><strong>OFERTA</strong></td>
    <td height="25" align="left"><strong>NOMBRE CLIENTE</strong></td>
    <td align="center"><strong>ASESOR</strong></td>
    <td align="center"><strong>GERENTE</strong></td>
    <td align="center"><strong>NO. PRODUCTOS</strong></td>
    <td align="center"><strong>PRECIO LISTA</strong></td>
    <td align="center"><strong>PRECIO NETO</strong></td>
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
CONCAT(ase.primer_nombre,' ',ase.segundo_nombre,
		' ',ase.`primer_apellido`,' ',ase.`segundo_apellido`) AS _ASESOR,
		CONCAT(gerente.primer_nombre,' ',gerente.segundo_nombre,
		' ',gerente.`primer_apellido`,' ',gerente.`segundo_apellido`) AS _GERENTE,	
	contratos.*
	FROM contratos 
	INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus) 
	INNER JOIN sys_personas ON (sys_personas.id_nit=contratos.`id_nit_cliente`)
	INNER JOIN `sys_asesor` ON (`sys_asesor`.codigo_asesor=contratos.codigo_asesor)
	INNER JOIN `sys_personas` AS ase ON (ase.id_nit=sys_asesor.id_nit)	
	INNER JOIN `sys_gerentes_grupos` ON (`sys_gerentes_grupos`.codigo_gerente_grupo=sys_asesor.codigo_gerente_grupo)
	INNER JOIN `sys_personas` AS gerente ON (gerente.id_nit=sys_gerentes_grupos.id_nit)		
	WHERE  
		contratos.fecha_venta BETWEEN '".$day_from." 00:00:00' and '".$day_to." 23:59:59'  
		AND	contratos.estatus IN (1,13)
	ORDER BY sys_asesor.codigo_asesor asc  ";
 
$rsar=mysql_query($SQL);
$tt_precio_lista=0;
$precio_neto=0;
$no_producto=0;
$descuento=0;
$enganche=0;
$interes=0;
$valor_cuota=0;
$cuotas=0;

while($rowar= mysql_fetch_assoc($rsar)){ 
	$tt_precio_lista=$tt_precio_lista+$rowar['precio_lista'];
	$precio_neto=$precio_neto+($rowar['precio_neto']+$rowar['enganche']);
	$no_producto=$no_producto+$rowar['PRODUCTOS'];
	$descuento=$descuento+$rowar['descuento'];
	$enganche=$enganche+$rowar['enganche'];
	$interes=$interes+$rowar['interes'];
	$valor_cuota=$valor_cuota+$rowar['valor_cuota'];
	$cuotas=$cuotas+$rowar['cuotas'];
 ?>
  <tr>
    <td width="80" align="left"><?php echo $rowar['contrato']?></td>
    <td height="25" align="left"><?php echo utf8_encode($rowar['nombre_cliente']);?>&nbsp;</td>
    <td align="center"><?php echo utf8_encode($rowar['_ASESOR']);?></td>
    <td align="center"><?php echo utf8_encode($rowar['_GERENTE']);?></td>
    <td align="center"><?php echo $rowar['PRODUCTOS']?></td>
    <td align="center"><?php echo number_format($rowar['precio_lista'],2);?></td>
    <td align="center"><?php echo number_format($rowar['precio_neto']+$rowar['enganche'],2);?></td>
    <td align="center"><?php echo number_format($rowar['descuento'],2);?></td>
    <td align="center"><?php echo number_format($rowar['enganche'],2);?></td>
    <td align="center"><?php echo number_format($rowar['interes'],2);?></td>
    <td align="center"><?php echo $rowar['cuotas']?></td>
    <td align="center"><?php echo number_format($rowar['valor_cuota'],2);?></td>
    <td width="80" align="center"><?php echo $rowar['fecha_venta']?></td>
  </tr>
  <?php }  ?>
    <tr>
    <td align="left">&nbsp;</td>
    <td height="25" align="left">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><?php echo number_format($tt_precio_lista,2);?></td>
    <td align="center"><?php echo number_format($precio_neto,2) ;?></td>
    <td align="center"><?php echo number_format($descuento,2) ;?></td>
    <td align="center"><?php echo number_format($enganche,2) ;?></td>
    <td align="center"><?php echo number_format($interes,2) ;?></td>
    <td align="center"><?php echo number_format($cuotas,2) ;?></td>
    <td align="center"><?php echo number_format($valor_cuota,2) ;?></td>
    <td align="center">&nbsp;</td>
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