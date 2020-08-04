<?php
if (!isset($protect)){
	exit;
}
      
 
?><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px">
  <thead>
    <tr>
      <th>No. Recibo</th>
      <th>Contrato</th>
      <th>Descripcion</th>
      <th>Monto</th>
      <th>Monto RD$</th>
      <th>Fecha </th>
      <th width="170">Creado por</th>
      <th>Motorizado</th>
      <th>Accion</th>
    </tr>
  </thead>
  <tbody>
<?php
 
 
foreach($sb as $key =>$row){  
	$solicitud=$row['NO_CODIGO_BARRA']; 
?>
    <tr style="cursor:pointer" id="<?php echo $solicitud;?>" class="no_recibo_fact">
      <td><?php echo $row['SERIE']." ".$row['NO_DOCTO'];?></td>
      <td><?php echo $row['SERIE_CONTRATO']." ".$row['NO_CONTRATO'];?></td>
      <td><?php echo $row['tmovimiento'];?></td>
      <td><?php echo number_format($row['MONTO_TOTAL'],2);?></td>
      <td><?php echo number_format($row['MONTO_LOCAL'],2);?></td>
      <td><?php echo $row['FECHA'];?></td>
      <td><?php echo utf8_encode($row['oficial']);?></td>
      <td><?php echo utf8_encode($row['motorizado']);?></td>
      <td><?php if ($row['SERIE']!='ND'){?><button type="button" class="recibo_remove orangeButton" value="<?php echo $solicitud;?>">	Remover</button><?php } ?></td>
    </tr>
 
<?php 
}
 ?>
  </tbody>
</table>
<?php

?>
