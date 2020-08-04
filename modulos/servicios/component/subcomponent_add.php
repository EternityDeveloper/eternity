<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
//print_r($_REQUEST);
if (!isset($_REQUEST['id'])){
	echo "Debe seleccionar un componente!";
	exit;
}

$data=json_decode(System::getInstance()->Request("id"));

if (!isset($data->id_componente)){
	echo "Debe seleccionar un componente!";
	exit;
}

 
 
 ?>
<form action="" method="post" enctype="multipart/form-data" name="form_add_subcomponent" class="fsForm  fsSingleColumn" id="form_add_subcomponent">
   <table width="100%" border="1" class="fsPage">
 
  <tr>
    <td align="right">ID Sub-Componente:</td>
    <td><input type="text" name="sub_subcomponente" id="sub_subcomponente">
      <input name="id_componente" type="hidden" id="id_componente" value="<?php echo $data->id_componente?>"></td>
  </tr>
  <tr>
    <td align="right">Descripción:</td>
    <td><input type="text" name="sub_descripcion" id="sub_descripcion"></td>
  </tr>
  <tr>
    <td align="right">Costo:</td>
    <td>
      <input type="text" name="sub_costos" id="sub_costos"></td>
  </tr>
  <tr>
    <td align="right">Cta contable:</td>
    <td><input type="text" name="sub_cta_contable" id="sub_cta_contable"></td>
  </tr>
  <tr>
    <td align="right">Precio venta:</td>
    <td><input type="text" name="sub_precio_venta" id="sub_precio_venta"></td>
  </tr>
  <tr>
    <td align="right">Foto:</td>
    <td><input type="file" name="imagen_upload" id="imagen_upload"></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_subcomponent_add" type="hidden" id="submit_subcomponent_add" value="1" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="positive" id="sub_bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                       <button type="button" class="positive" id="sub_bt_cancel">
                        <img src="images/cross.png" alt=""/> 
                        Cancel</button>  
      </td>
    </tr>
</table>
 
</form>