<?php
if (!isset($protect)){
	exit;
}

if (!(isset($_REQUEST['serie_contrato'])) && (isset($_REQUEST['no_contrato']))){
	echo "Datos invalidos";
	exit;
}

?><table border="0" class="table table-bordered table-striped table-hover"  style="font-size:13px">
  <thead>
    <tr>
      <th><span >Tipo</span></th>
      <th>Descripcion</th>
      <th>Empresa</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php
 
$SQL="SELECT * FROM `scan_contratos` 
INNER JOIN `tipo_scan` ON (`tipo_scan`.`idtipo_scan`=scan_contratos.`idtipo_scan`)
LEFT JOIN `empresa` ON (`empresa`.`EM_ID`=scan_contratos.`EM_ID`)
WHERE scan_contratos.`no_contrato`='".System::getInstance()->Decrypt($_REQUEST['no_contrato'])."' AND `scan_contratos`.`serie_contrato`='".System::getInstance()->Decrypt($_REQUEST['serie_contrato'])."' and scan_contratos.estatus=1 ";
 
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){ 
	$id=System::getInstance()->Encrypt(json_encode($row));
?>
    <tr  >
      <td><?php echo $row['tipo_documento'];?></td>
      <td><?php echo $row['descripcion'];?></td>
      <td><?php echo $row['EM_NOMBRE'];?></td>
      <td><a href="./?mod_contratos/listar&download&id=<?php echo $id;?>" target="_contrato">Ver</a></td>
      <td><img src="images/cross.png" class="document_id"  id="<?php echo $id?>" width="16" height="16" style="cursor:pointer"></td>
    </tr>
<?php 
}
 ?>
  </tbody>
</table>
