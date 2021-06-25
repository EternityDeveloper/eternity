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
    <td style="background:#CCC;height:30px;"><strong>LISTADO DE PERSONAS</strong></td>
  </tr>
  <tr>
    <td><table border="0" width="100%" id="caja_table_list" class="search_list table table-striped table-hover">
      <thead>
        <tr>
          <th height="20">Documento</th>
          <th>Nombre </th>
          <th>Apellido</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data['data'] as $key =>$row){
	//print_r($row);
	?>
        <tr class="item_select" id="<?php echo $row['encode_nit']?>"  style="cursor:pointer">
          <th height="30"><?php echo ($row['id_nit'])?></th>
          <th><?php echo $row['nombre']?></th>
          <th><?php echo $row['apellido']?></th>
          <th>&nbsp;</th>
        </tr>
        <?php } ?>
      </tbody>
    </table></td>
  </tr>
</table>
<?php } ?>    
