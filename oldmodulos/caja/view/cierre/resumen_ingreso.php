<?php
/* esto es por si alguien accede a este link directamente*/
if (!isset($protect)){
	exit;
}	
?><table border="1"  class="table table-bordered table-striped table-hover" style="width:600px;">
  <tr  style="background:#CCC" >
    <td width="215" height="36"><strong>DOCUMENTO</strong></td>
    <td width="212" align="right"><strong>FORMA DE PAGO</strong></td>
    <td width="164" align="right"><strong>INGRESOS</strong></td>
    <td width="181" align="right"><strong>EGRESOS</strong></td>
  </tr>
  <?php
 
 $data=$cierre->getPreviewCierre($filter);
$total_monto=0;
	foreach($data as $key =>$row){ 
 		
?>
  <tr>
    <td colspan="4"><strong><?php echo $key; ?></strong></td>
  </tr>
  <?php 
		$acumulado_ingreso=0;
		foreach($row as $keys =>$data){

			$acumulado_ingreso=$acumulado_ingreso+($data['MONTO']);
			$total_monto=$total_monto+($data['MONTO']);
		?>
  <tr>
    <td align="center"><?php echo $data['TOTALES']?></td>
    <td align="right"><?php echo $data['forma_pago'];?></td>
    <td align="right"><?php echo  number_format($data['MONTO'],2)?></td>
    <td align="right"><?php  ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td></td>
    <td align="right">&nbsp;</td>
    <td align="right"><strong><?php echo number_format($acumulado_ingreso,2);?></strong></td>
    <td align="right"><strong>0.00</strong></td>
  </tr>
  <?php } ?>
  <tr >
    <td>&nbsp;</td>
    <td align="right">CIERRE PARCIAL</td>
    <td align="right"><strong><?php echo number_format($monto_cierre_parcial,2); ?></strong></td>
    <td align="right"><strong><?php echo number_format($monto_cierre_parcial_egresos,2); ?></strong></td>
  </tr>
  <tr >
    <td>&nbsp;</td>
    <td align="right">TOTALES</td>
    <td align="right"><strong><?php echo number_format($monto_cierre_parcial+$total_monto,2); ?></strong></td>
    <td align="right"><strong>0.00</strong></td>
  </tr>
  <tr >
    <td>&nbsp;</td>
    <td align="right">GRAN TOTAL</td>
    <td align="right"><strong>0.00</strong></td>
    <td align="right"><strong>0.00</strong></td>
  </tr>
</table>

