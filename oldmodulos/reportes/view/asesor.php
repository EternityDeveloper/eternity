<?php
if (!isset($protect)){
	exit;
}	

$VENTAS=0;
$PRODUCTO=0;
$MONTO=0;
$INICIAL=0;
$DESCUENTOS=0;
$PLAZO=0;

?>
<div style="padding-left:20px;">
<?php 
include("menu.php");
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
   
    
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php

	$SQL="SELECT id_comercial,
CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) AS nombre_completo
 FROM `asesores_g_d_gg_view`
INNER JOIN sys_personas ON (sys_personas.id_nit=asesores_g_d_gg_view.id_nit) 
INNER JOIN `contratos` ON (asesores_g_d_gg_view.id_comercial=contratos.asesor)
WHERE  contratos.fecha_ingreso between '".$day_from."' and '".$day_to."'
and  (id_comercial  LIKE '%".$protect->getComercialID()."%' AND id_comercial!='".$protect->getComercialID()."')  
GROUP BY asesores_g_d_gg_view.id_comercial ";

 
$rqs=mysql_query($SQL);  
while($rows= mysql_fetch_assoc($rqs)){	

?> 
      <tr>
        <td height="20" colspan="6"><strong><?php echo $rows['nombre_completo']?></strong></td>
        </tr>
      <tr>
        <td height="25"><strong>Cliente</strong></td>
        <td align="center"><strong>Producto</strong></td>
        <td align="center"><strong>Plazo</strong></td>
        <td align="center"><strong>Monto (Inc. Dscto)</strong></td>
        <td align="center"><strong>Incial (Inc. Dscto)</strong></td>
        <td align="center"><strong>Descuentos</strong></td>
      </tr>
      <?php



$SQL="SELECT  
CONCAT(sys_personas.`primer_nombre`,' ',sys_personas.`primer_apellido`) AS nombre_completo,
contratos.precio_neto AS MONTO,
contratos.no_productos  AS PRODUCTOS,
contratos.enganche as INICIAL,
sys_status.descripcion,
contratos.`cuotas`, 
(((contratos.precio_lista * contratos.porc_descuento)/100) +contratos.descuento) AS DESCUENTOS,
(
SELECT SUM(inventario_jardines.`cavidades`) AS total FROM `producto_contrato` 
INNER JOIN `inventario_jardines` ON (inventario_jardines.bloque=producto_contrato.bloque AND 
inventario_jardines.lote=producto_contrato.lote AND inventario_jardines.id_fases=producto_contrato.id_fases AND 
inventario_jardines.id_jardin =producto_contrato.id_jardin )
WHERE producto_contrato.serie_contrato=contratos.serie_contrato AND producto_contrato.no_contrato=contratos.no_contrato 
 ) AS CANTIDAD	
 
 FROM `contratos` 
INNER JOIN sys_personas ON (sys_personas.id_nit=contratos.`id_nit_cliente`)
INNER JOIN `sys_status` ON (sys_status.id_status=contratos.estatus)
WHERE contratos.asesor='".$rows['id_comercial']."' AND  contratos.fecha_ingreso between '".$day_from."' and '".$day_to."' 
GROUP BY contratos.`serie_contrato`,contratos.`no_contrato`
 ";

 
$rs=mysql_query($SQL);
$i=-1;
while($row= mysql_fetch_assoc($rs)){ 
	$i++;
	$PLAZO=$PLAZO+$row['cuotas'];
	$PRODUCTO=$PRODUCTO+ $row['PRODUCTOS'];
	$MONTO=$MONTO+$row['MONTO'];
	$INICIAL=$INICIAL+$row['INICIAL'];
	$DESCUENTOS=$DESCUENTOS+$row['DESCUENTOS'];
	
?>
      <tr class="<?php echo ($i%2)?'line_two':'line_one'?>">
        <td width="200" height="25"><?php echo $row['nombre_completo']?></td>
        <td align="center"><?php echo $row['PRODUCTOS']?></td>
        <td align="center"><?php echo $row['cuotas']?></td>
        <td align="center"><?php echo number_format($row['MONTO'],2)?></td>
        <td align="center"><?php echo number_format($row['INICIAL'],2)?></td>
        <td align="center"><?php echo number_format($row['DESCUENTOS'],2)?></td>
      </tr>
      <?php } ?>
      <tr>
        <td height="10" align="right">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
        <td align="center">&nbsp;</td>
      </tr>      
<?php }   ?>      

      <tr>
        <td height="25" align="right"><strong>TOTALES</strong> &nbsp;</td>
        <td align="center"><?php echo $PRODUCTO;?></td>
        <td align="center"><?php echo $PLAZO;?></td>
        <td align="center"><strong>RD <?php echo number_format($MONTO,2)?></strong></td>
        <td align="center"><strong>RD <?php echo number_format($INICIAL,2);?></strong></td>
        <td align="center"><strong>RD <?php echo number_format($DESCUENTOS,2);?></strong></td>
      </tr>
    </table>

    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><b>VENTAS POR PLANES DEL PERIODO <?php echo  date("d/m/Y", strtotime($day_from)); ?></b> AL <b><?php echo date("d/m/Y", strtotime($day_to)); ?></b></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</div>