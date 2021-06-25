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
include("menu_spec.php");
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table table-hover" style="font-size: 10px; font-weight: bold;">
  <tr style="background:#CCC">
    <td align="left">&nbsp;</td>
    <td align="left">OFERTA</td>
    <td align="left"><strong>PRODUCTO</strong></td>
    <td height="25" align="left"><strong>NOMBRE CLIENTE</strong></td>
    <td align="center"><strong>NO. PRODUCTOS</strong></td>
    <td align="center"><strong>PRECIO LISTA</strong></td>
    <td align="center"><strong>PRECIO NETO</strong></td>
    <td align="center"><strong>DESCUENTO</strong></td>
    <td align="center"><strong>ENGANCHE</strong></td>
    <td align="center"><strong>INTERES</strong></td>
    <td align="center"><strong>CUOTAS</strong></td>
    <td align="center"><strong>VALOR CUOTA</strong></td>
    <td align="center">ESTATUS</td>
    <td align="center"><strong>FECHA VENTA</strong></td>
  </tr>
  <?php
$SQL="SELECT  
	(contratos.descuento*contratos.tipo_cambio) as descuento,
	CONCAT(contratos.serie_contrato,' ',contratos.no_contrato) AS contrato,
	sys_status.descripcion AS des_estatus,
	contratos.no_productos AS PRODUCTOS,
	contratos.fecha_venta,
	contratos.valor_cuota,
	contratos.cuotas,
	contratos.interes,
	(contratos.precio_lista*contratos.tipo_cambio) as precio_lista,
	(((contratos.precio_lista-monto_capitalizado)-contratos.descuento)*contratos.tipo_cambio) AS precio_neto,
	((SELECT SUM(MONTO) AS MONTO FROM (SELECT 
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
	MC.serie_contrato=contratos.serie_contrato )) as enganche,
	((contratos.precio_lista-contratos.descuento)*contratos.tipo_cambio) AS MONTO,
	
	CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`segundo_nombre`,
	' ',sys_personas.`primer_apellido`,' ',sys_personas.segundo_apellido) AS nombre_cliente,
CONCAT(ase.primer_nombre,' ',ase.segundo_nombre,
		' ',ase.`primer_apellido`,' ',ase.`segundo_apellido`) AS _ASESOR,
		CONCAT(gerente.primer_nombre,' ',gerente.segundo_nombre,
		' ',gerente.`primer_apellido`,' ',gerente.`segundo_apellido`) AS _GERENTE,	 
	(SELECT (CASE 
	WHEN pc.serv_codigo!='' THEN (SELECT serv_descripcion FROM `servicios` WHERE serv_codigo=pc.serv_codigo)	
	WHEN pc.id_jardin!='' THEN (SELECT jardin FROM jardines WHERE jardines.id_jardin=pc.id_jardin) END )  
	FROM `producto_contrato` AS pc WHERE pc.id_estatus=1 AND 
		pc.serie_contrato=contratos.serie_contrato AND pc.no_contrato=contratos.no_contrato LIMIT 1) AS producto
		
	FROM contratos_ventas as contratos 
	INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus) 
	INNER JOIN sys_personas ON (sys_personas.id_nit=contratos.`id_nit_cliente`)
	INNER JOIN `sys_asesor` ON (`sys_asesor`.codigo_asesor=contratos.codigo_asesor)
	INNER JOIN `sys_personas` AS ase ON (ase.id_nit=sys_asesor.id_nit)	
	INNER JOIN `sys_gerentes_grupos` ON (`sys_gerentes_grupos`.codigo_gerente_grupo=sys_asesor.codigo_gerente_grupo)
	INNER JOIN `sys_personas` AS gerente ON (gerente.id_nit=sys_gerentes_grupos.id_nit)		
	WHERE  
		contratos.fecha_venta BETWEEN '".$day_from."' and '".$day_to."'  ";
	
	if (isset($id)){
		if ($id!=""){
			$SQL.=" AND	contratos.estatus='".$id."' ";
		}else{
			$SQL.=" AND	contratos.estatus IN ('1','13', '20','54')   ";	
		}
	}else{
		$SQL.=" AND	contratos.estatus IN ('1','13', '20','54')  ";	
	}
//$id
$SQL.=" 	ORDER BY sys_asesor.codigo_asesor asc   ";
 
 
$rsar=mysql_query($SQL);
$tt_precio_lista=0;
$precio_neto=0;
$no_producto=0;
$descuento=0;
$enganche=0;
$interes=0;
$valor_cuota=0;
$cuotas=0;


$_GERENTE=array();
while($rowar= mysql_fetch_assoc($rsar)){ 
 
	if (!isset($_GERENTE[$rowar['_GERENTE']])){
		$_GERENTE[$rowar['_GERENTE']]=array();
	}
	if (!isset($_GERENTE[$rowar['_GERENTE']][$rowar['_ASESOR']])){
		$_GERENTE[$rowar['_GERENTE']][$rowar['_ASESOR']]=array();
	}
	
	array_push($_GERENTE[$rowar['_GERENTE']][$rowar['_ASESOR']],$rowar);

}
//print_r($_GERENTE);


$tt_precio_lista_g=0;
$precio_neto_g=0;
$no_producto_g=0;
$descuento_g=0;
$enganche_g=0;
$interes_g=0;
$valor_cuota_g=0;
$cuotas_g=0;

foreach($_GERENTE as $key => $row){
	$tt_precio_lista=0;
	$precio_neto=0;
	$no_producto=0;
	$descuento=0;
	$enganche=0;
	$interes=0;
	$valor_cuota=0;
	$cuotas=0;	
 ?>
  <tr>
    <td height="25" colspan="14" align="left" style="background:#E5E5E5"><?php echo $key;?></td>
    </tr>

<?php 



	foreach($row as $kk =>$ase){

?>    
  <tr>
    <td height="25" colspan="14" style="padding-left:20px;" align="left"><?php echo $kk;?></td>
    </tr>
<?php 


$_precio_lista=0;
$_precio_neto=0;
$_no_producto=0;
$_descuento=0;
$_enganche=0;
$_interes=0;
$_valor_cuota=0;
$_cuotas=0;
	
foreach($ase as $ky =>$rowar){
	
if ($rowar['producto']=="OSARIOS"){
	$rowar['PRODUCTOS']=0;
}
 
$_precio_lista=$_precio_lista+$rowar['precio_lista'];
$_precio_neto=$_precio_neto+($rowar['precio_neto']);
$_no_producto=$_no_producto+$rowar['PRODUCTOS'];
$_descuento=$_descuento+$rowar['descuento'];
$_enganche=$_enganche+$rowar['enganche'];
$_interes=$_interes+$rowar['interes'];
$_valor_cuota=$_valor_cuota+$rowar['valor_cuota'];
$_cuotas=$_cuotas+$rowar['cuotas'];	


			
$tt_precio_lista=$tt_precio_lista+$rowar['precio_lista'];
$precio_neto=$precio_neto+($rowar['precio_neto']);
$no_producto=$no_producto+$rowar['PRODUCTOS'];
$descuento=$descuento+$rowar['descuento'];
$enganche=$enganche+$rowar['enganche'];
$interes=$interes+$rowar['interes'];
$valor_cuota=$valor_cuota+$rowar['valor_cuota'];
$cuotas=$cuotas+$rowar['cuotas'];	



$tt_precio_lista_g=$tt_precio_lista_g+$rowar['precio_lista'];
$precio_neto_g=$precio_neto_g+($rowar['precio_neto']);
$no_producto_g=$no_producto_g+$rowar['PRODUCTOS'];
$descuento_g=$descuento_g+$rowar['descuento'];
$enganche_g=$enganche_g+$rowar['enganche'];
$interes_g=$interes_g+$rowar['interes'];
$valor_cuota_g=$valor_cuota_g+$rowar['valor_cuota'];
$cuotas_g=$cuotas_g+$rowar['cuotas'];	


		
?>    
  <tr>
    <td width="100" align="left">&nbsp;</td>
    <td width="80" align="left"><?php echo $rowar['contrato']?></td>
    <td width="150" align="left"><?php echo $rowar['producto']?></td>
    <td height="25" align="left"><?php echo utf8_encode($rowar['nombre_cliente']);?>&nbsp;</td>
    <td align="center"><?php echo $rowar['PRODUCTOS']?></td>
    <td align="center"><?php echo number_format($rowar['precio_lista'],2);?></td>
    <td align="center"><?php echo number_format($rowar['precio_neto'] ,2);?></td>
    <td align="center"><?php echo number_format($rowar['descuento'],2);?></td>
    <td align="center"><?php echo number_format($rowar['enganche'],2);?></td>
    <td align="center"><?php echo number_format($rowar['interes'],2);?></td>
    <td align="center"><?php echo $rowar['cuotas']?></td>
    <td align="center"><?php echo number_format($rowar['valor_cuota'],2);?></td>
    <td width="80" align="center"><?php echo $rowar['des_estatus'];?></td>
    <td width="80" align="center"><?php echo $rowar['fecha_venta']?></td>
  </tr>
 <?php  }  ?>  
  <tr>
    <td height="25" colspan="4" align="left" >&nbsp;</td>
    <td align="center"  style="background:#F0F0F0"><?php echo $_no_producto;?></td>
    <td align="center" style="background:#F0F0F0"><?php echo number_format($_precio_lista,2);?></td>
    <td align="center" style="background:#F0F0F0"><?php echo number_format($_precio_neto,2);?></td>
    <td align="center"  style="background:#F0F0F0"><?php echo number_format($_descuento,2);?></td>
    <td align="center"  style="background:#F0F0F0"><?php echo number_format($_enganche,2);?></td>
    <td align="center"  style="background:#F0F0F0"><?php echo number_format($_interes,2);?></td>
    <td align="center"  style="background:#F0F0F0"><?php echo $_cuotas?></td>
    <td align="center"  style="background:#F0F0F0"><?php echo number_format($_valor_cuota,2);?></td>
    <td align="center"  style="background:#F0F0F0">&nbsp;</td>
    <td align="center"  style="background:#F0F0F0">&nbsp;</td>
  </tr> 
 <?php  }  ?>

  <tr style="background:#E5E5E5">
    <td height="25" colspan="4" align="right" >TOTALES</td>
    <td align="center"><?php echo $no_producto?></td>
    <td align="center"><?php echo number_format($tt_precio_lista,2);?></td>
    <td align="center"><?php echo number_format($precio_neto,2);?></td>
    <td align="center"><?php echo number_format($descuento,2);?></td>
    <td align="center"><?php echo number_format($enganche,2);?></td>
    <td align="center"><?php echo number_format($interes,2);?></td>
    <td align="center"><?php  ?></td>
    <td align="center"><?php echo number_format($valor_cuota,2);?></td>
    <td width="80" align="center">&nbsp;</td>
    <td width="80" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td height="25" colspan="14" style="padding-left:20px;" align="left">&nbsp;</td>
    </tr>
 <?php  }  ?> 
  <tr style="background:#E5E5E5">
    <td height="25" colspan="4" align="right" >TOTAL GENERAL:</td>
    <td align="center"><?php echo $no_producto_g?></td>
    <td align="center"><?php echo number_format($tt_precio_lista_g,2);?></td>
    <td align="center"><?php echo number_format($precio_neto_g,2);?></td>
    <td align="center"><?php echo number_format($descuento_g,2);?></td>
    <td align="center"><?php echo number_format($enganche_g,2);?></td>
    <td align="center"><?php echo number_format($interes_g,2);?></td>
    <td align="center"><?php  ?></td>
    <td align="center"><?php echo number_format($valor_cuota_g,2);?></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
 
    <tr>
      <td align="left">&nbsp;</td>
      <td align="left">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td height="25" align="left">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
 
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