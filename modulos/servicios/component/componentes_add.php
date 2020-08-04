<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}


 
 ?>
<form action="" method="post" enctype="multipart/form-data" name="form_user_edit" class="fsForm  fsSingleColumn" id="form_user_edit">
   <table width="100%" border="1" class="fsPage">
 
  <tr>
    <td align="right">ID Componente:</td>
    <td><input type="text" name="id_componente" id="id_componente"></td>
  </tr>
  <tr>
    <td align="right">Descripción:</td>
    <td><input type="text" name="descripcion_comp" id="descripcion_comp"></td>
  </tr>
  <tr>
    <td align="right">Costo:</td>
    <td>
      <input type="text" name="costos_comp" id="costos_comp"></td>
  </tr>
  <tr>
    <td align="right">Cta contable:</td>
    <td><input type="text" name="cta_contable_comp" id="cta_contable_comp"></td>
  </tr>
  <tr>
    <td align="right">Precio venta:</td>
    <td><input type="text" name="precio_venta_comp" id="precio_venta_comp"></td>
  </tr>
  <tr>
    <td align="right">Foto:</td>
    <td><input type="file" name="imagen_upload" id="imagen_upload"></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><input name="submit_component_add" type="hidden" id="submit_component_add" value="1" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="positive" id="bt_save">
                        <img src="images/apply2.png" alt=""/> 
                        Guardar</button>
                       <button type="button" class="positive" id="bt_cancel">
                        <img src="images/cross.png" alt=""/> 
                        Cancel</button>  
      </td>
    </tr>
</table>
 
</form>