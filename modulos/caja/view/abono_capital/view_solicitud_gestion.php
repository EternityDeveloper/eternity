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
      <th>Monto</th>
      <th>Gestión</th>
      <th>Descripción</th>
      <th>Creado por</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
<?php

SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobro= new Cobros($protect->getDBLINK());   
$data=$cobro->getSolicitudesAbonoCapital($ct->serie_contrato,$ct->no_contrato,33);
$monto_abonar=($data[0]['monto_abonar']);
foreach($data as $key =>$row){ 
	$solicitud=System::getInstance()->Encrypt(json_encode($row));
?>
    <tr  >
      <td><?php echo number_format($row['monto_abonar'],2);?></td>
      <td width="200"><?php echo $row['gestion'];?></td>
      <td><?php echo $row['descrip_general'];?></td>
      <td width="200"><?php echo $row['responsable'];?></td>
      <td><a href="./?mod_cobros/delegate&amp;solicitud_gestion_abono&amp;id=<?php echo $solicitud; ?>" target="new"><img src="images/document_preview.png" alt="" width="24" height="24" /></a><a href="./?mod_contratos/listar&download&id=<?php echo $id;?>" target="_contrato"></a></td>
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
