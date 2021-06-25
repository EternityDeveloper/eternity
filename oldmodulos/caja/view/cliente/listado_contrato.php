<?php 
if (!isset($data)){
	exit;	
}

if (count($data['data'])>0){
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td style="background:#CCC;height:30px;"><strong>LISTADO DE CONTRATOS</strong></td>
  </tr>
  <tr>
    <td><table border="0" width="100%" class="search_list table table-striped table-hover" >
      <thead>
        <tr>
          <th align="center">Contrato</th>
          <th align="center">Nombre </th>
          <th align="center">Apellido</th>
          <th align="center">Empresa</th>
          <th height="20" align="center">Estatus</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data['data'] as $key =>$row){
	$contrato=array("serie_contrato"=>$row['serie_contrato'],"no_contrato"=>$row['no_contrato'],"id_nit"=>$row['id_nit_']);
 
	$encriptID=System::getInstance()->Encrypt(json_encode($contrato)); 	
	?>
        <tr class="item_select_contrato" id="<?php echo $encriptID?>" style="cursor:pointer">
          <th><?php echo ($row['contrato'])?></th>
          <th><?php echo $row['nombre']?></th>
          <th><?php echo $row['apellido']?></th>
          <th><?php echo $row['empresa']?></th>
          <th height="30"><?php echo $row['estatus']?></th>
          <th>&nbsp;</th>
        </tr>
        <?php } ?>
      </tbody>
    </table></td>
  </tr>
</table>
<?php } ?>  
