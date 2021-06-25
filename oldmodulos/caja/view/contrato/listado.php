<?php 
if (!isset($data)){
	exit;	
}

//print_r($data);
?><table border="0" width="100%" id="caja_table_list"   class="table table-striped table-hover" >
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
<?php foreach($data['data'] as $key =>$row){?>     
    <tr class="item_select" id="<?php echo $row['contrato_id']?>">
      <th><?php echo ($row['contrato'])?></th>
      <th><?php echo $row['nombre']?></th>
      <th><?php echo $row['apellido']?></th>
      <th><?php echo $row['empresa']?></th>
      <th height="30"><?php echo $row['estatus']?></th>
      <th>&nbsp;</th>
    </tr>
<?php } ?>    
  </tbody>
</table> 