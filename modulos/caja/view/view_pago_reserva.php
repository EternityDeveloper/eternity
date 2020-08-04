<?php 
if (!isset($protect)){
	exit;	
}
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 

$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato'])); 
if (!(validateField($contrato,"serie_contrato")&& validateField($contrato,"no_contrato"))){
	echo "No hay contrato!";
	exit;	
}

?><table  width="100%" border="1" style="background:#FFF">
  <tr>
    <td><h2 id="h_2">Abonos realizados</h2></td>
  </tr>
 
  <tr>
    <td><table id="tb_abonos_reservas" width="100%" border="1" class="display">
      <thead>
        <tr>
          <td align="center"><strong>Fecha</strong></td>
          <td align="center"><strong>Forma de pago</strong></td>
          <td align="center"><strong>Serie recibo</strong></td>
          <td align="center"><strong>No. Recibo</strong></td>
          <td align="center"><strong>No. Rerpote venta</strong></td>
          <td align="center"><strong>Monto</strong></td>
          </tr>
      </thead>
      <tbody>
        <?php
   
$SQL="SELECT *,
	DATE_FORMAT(pago_reservas.fecha, '%d-%m-%Y') AS fecha,
	DATE_FORMAT(pago_reservas.fecha, '%h:%i:%s') AS hora FROM `inventario_jardines` 
INNER JOIN `reserva_inventario` ON (reserva_inventario.`no_reserva`=inventario_jardines.`no_reserva` AND 
reserva_inventario.`id_reserva`=inventario_jardines.`id_reserva`)
INNER JOIN `pago_reservas` ON (pago_reservas.`no_reserva`=inventario_jardines.`no_reserva` AND 
pago_reservas.`id_reserva`=inventario_jardines.`id_reserva`)
INNER JOIN `formas_pago` ON (formas_pago.`forpago`=pago_reservas.`forpago` )
WHERE inventario_jardines.`serie_contrato`='".$contrato->serie_contrato ."' AND  inventario_jardines.`no_contrato`='".$contrato->no_contrato."' AND 
reserva_inventario.`estatus`='1'";
	 
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_object($rs)){
		$id_pago=System::getInstance()->Encrypt(json_encode($row));
 
   ?>
        <tr>
          <td align="center" class="display"><?php echo $row->fecha;?></td>
          <td align="center" class="display"><?php echo $row->descripcion_pago; ?></td>
          <td align="center" class="display"><?php echo $row->serie_recibo;?></td>
          <td align="center" class="display" ><?php echo $row->no_recibo;?></td>
          <td align="center" class="display" ><?php echo $row->reporte_venta ;?></td>
          <td align="center" class="display" ><?php echo number_format($row->monto,2);?></td>
          </tr>
        <?php 
	} ?>
      </tbody>
    </table></td>
  </tr>
  <tr>
    <td align="center"><button type="button" class="redButton" id="bt_abono_cancel">Cerrar</button></td>
  </tr>
</table>
