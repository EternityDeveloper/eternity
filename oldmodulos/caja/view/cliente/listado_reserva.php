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
    <td style="background:#CCC;height:30px;"><strong>LISTADO DE RESERVAS</strong></td>
  </tr>
  <tr>
    <td><table border="0" width="100%" id="caja_table_list"  class="search_list table table-striped table-hover" >
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
        <tr  <?php echo $row['ANULADO']=="S"?'style="color:red"':''?> class="item_select_reserva" id="<?php echo $row['enc_reserva']?>" id_nit="<?php echo System::getInstance()->Encrypt($row['id_nit'])?>"  style="cursor:pointer">
          <th><?php echo ($row['no_reserva'])?></th>
          <th><?php echo $row['nombre']?></th>
          <th><?php echo $row['apellido']?></th>
          <th><?php echo $row['total_reserva']?></th>
          <th height="30"><?php echo $row['estatus']?></th>
          <th>&nbsp;</th>
        </tr>
        <?php } ?>
      </tbody>
    </table></td>
  </tr>
</table>
<?php } ?>    
