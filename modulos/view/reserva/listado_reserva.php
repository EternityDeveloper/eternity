<?php 
if (!isset($data)){
	exit;	
}


?>
<table border="0" width="100%" id="caja_table_list"  class="table table-striped table-hover" >
  <thead>
    <tr>
      <th>No. Reserva</th>
      <th>Nombre </th>
      <th>Apellido</th>
      <th>Total productos <br />
      reservados</th>
      <th height="20">Estatus</th>
      <th>&nbsp;</th>
    </tr>
  </thead> 
 
  <tbody>
<?php foreach($data['data'] as $key =>$row){?>     
    <tr class="item_select" id="<?php echo $row['enc_reserva']?>" id_nit="">
      <th><?php echo ($row['no_reserva'])?></th>
      <th><?php echo $row['nombre']?></th>
      <th><?php echo $row['apellido']?></th>
      <th><?php echo $row['total_reserva']?></th>
      <th height="30"><?php echo $row['estatus']?></th>
      <th>&nbsp;</th>
    </tr>
<?php } ?>    
  </tbody>
</table> 