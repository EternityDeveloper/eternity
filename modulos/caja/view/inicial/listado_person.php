<?php 
if (!isset($data)){
	exit;	
}

//print_r($data);
?><table border="0" width="100%" id="caja_table_list" class="table table-striped table-hover">
  <thead>
    <tr>
      <th height="20">Documento</th>
      <th>Nombre </th>
      <th>Apellido</th>
      <th>&nbsp;</th>
    </tr>
  </thead> 
 
  <tbody>
<?php foreach($data['data'] as $key =>$row){?>     
    <tr class="item_select" id="<?php echo $row['encode_nit']?>">
      <th height="30"><?php echo ($row['id_nit'])?></th>
      <th><?php echo $row['nombre']?></th>
      <th><?php echo $row['apellido']?></th>
      <th>&nbsp;</th>
    </tr>
<?php } ?>    
  </tbody>
</table> 