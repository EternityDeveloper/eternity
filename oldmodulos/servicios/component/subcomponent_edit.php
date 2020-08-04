<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}

if (!isset($_REQUEST['id'])){
	echo "Debe seleccionar un componente!";
	exit;
}

$data=json_decode(System::getInstance()->Request("id"));
 //print_r($data->data_component);
if (!isset($data->id_componente)){
	echo "Debe seleccionar un componente!";
	exit;
}
 
 
if (!isset($data->sub_subcomponente)){
	echo "Debe seleccionar un Sub-componente!";
	exit;
}
 
 
?>
<form action="" method="post" enctype="multipart/form-data" name="form_sub_component_edit" class="fsForm  fsSingleColumn" id="form_sub_component_edit">
  <table width="100%" border="1" class="fsPage">
    <tr>
      <td align="right"><strong>ID Sub Componente:</strong></td>
      <td><input name="component" type="text" disabled="disabled" id="component" value="<?php echo $data->sub_subcomponente?>" />
        <input type="hidden" name="sub_subcomponente" id="sub_subcomponente" value="<?php echo $data->sub_subcomponente?>" />
        <input name="id_component" type="hidden" id="id_component" value="<?php echo $data->data_component; ?>" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Descripción:</strong></td>
      <td><input type="text" name="sub_descripcion" id="sub_descripcion"  value="<?php echo $data->sub_descripcion?>" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Costo:</strong></td>
      <td><input type="text" name="sub_costos" id="sub_costos"  value="<?php echo $data->sub_costos?>" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Cta contable:</strong></td>
      <td><input type="text" name="sub_cta_contable" id="sub_cta_contable"  value="<?php echo $data->sub_cta_contable?>" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Precio venta:</strong></td>
      <td><input type="text" name="sub_precio_venta" id="sub_precio_venta"  value="<?php echo $data->sub_precio_venta?>" /></td>
    </tr>
    <tr>
      <td align="right"><strong>Foto:</strong></td>
      <td><img src="images/servicios/<?php echo $data->imagen?>" width="164" height="104"  /><br />
        <input type="file" name="imagen_upload_component" id="imagen_upload_component" /></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><input name="submit_subcomponent_edit" type="hidden" id="submit_subcomponent_edit" value="1" /></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><button type="button" class="positive" id="bt_save_sub_c"> <img src="images/apply2.png" alt=""/> Guardar</button>
        <button type="button" class="positive" id="bt_cancel_sub_c"> <img src="images/cross.png" alt=""/> Cancel</button></td>
    </tr>
  </table>
</form>