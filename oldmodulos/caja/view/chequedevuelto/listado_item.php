<?php 
if (!isset($protect)){
	exit;	
} 

$cdv= new ChequesDevuelto($protect->getDBLink()); 
//	echo json_encode($cdv->getListFormaPagoCheque($_REQUEST)); 
 
?>
<table border="0" class="table table-bordered table-striped table-hover" >
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Contrato</th>
      <th>Documento </th>
      <th>Autorizacion</th>
      <th>Monto</th>
      <th>Banco </th>
      <th></th>
    </tr>
  </thead>
  <tbody>
<?php 
$monto=0;
$rt=$cdv->getListItemAdded();
foreach($rt as $key =>$rs){
	foreach($rs as $keys =>$row){ 
		$monto=$monto+$row->_MONTO;
?>  
    <tr>
      <td><?php echo $row->FECHA?></td>
      <td><?php echo $row->contrato?></td>
      <td><?php echo $row->doc_id?></td>
      <td><?php echo $row->AUTORIZACION?></td>
      <td><?php echo number_format($row->_MONTO,2);?></td>
      <td><?php echo $row->banco?></td>
      <td></td>
    </tr> 
<?php
	}
 } ?>   
    <tr>
      <td colspan="4" align="right"><strong>Monto total:</strong></td>
      <td><?php echo number_format($monto,2);?></td>
      <td colspan="2">&nbsp;</td>
    </tr>  
  </tbody>
</table>
 