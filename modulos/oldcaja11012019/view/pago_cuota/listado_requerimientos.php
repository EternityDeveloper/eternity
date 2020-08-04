<?php
if (!isset($protect)){
	exit;
}
if (!isset($_REQUEST['contrato'])){
	exit;
}
 
$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
if (!isset($ct->serie_contrato)){
	exit;
}
 
?><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px">
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th>No. Recibo</th>
      <th>Cuotas a cobrar</th>
      <th>Monto</th>
      <th>Fecha </th>
      <th width="170">Creado por</th>
      <th>Motorizado</th>
    </tr>
  </thead>
  <tbody>
<?php

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobro= new Cobros($protect->getDBLINK());    
$avico=$cobro->getAvisoCobroData($ct->serie_contrato,$ct->no_contrato); 
 
foreach($avico as $key =>$row){ 
	$solicitud=System::getInstance()->Encrypt(json_encode($row));
?>
    <tr  >
      <td><input class="listado_rc_" type="checkbox" name="checkbox" id="checkbox" value="<?php echo $solicitud;?>">
      <label for="checkbox"></label></td>
      <td><?php echo $row['serie'].$row['aviso_cobro'];?></td>
      <td><?php echo $row['cuotas_acobrar'];?></td>
      <td><?php echo number_format($row['monto_acobrar'],2);?></td>
      <td><?php echo $row['fecha'];?></td>
      <td><?php echo $row['nombre_oficial'];?></td>
      <td><?php echo $row['motorizado'];?></td>
    </tr>
<?php 
}
 ?>
  </tbody>
</table>
<?php
 
$data=ob_get_contents();
ob_clean();

echo json_encode(array("html"=>$data,"monto_a_pagar"=>$monto_abonar));

?>
