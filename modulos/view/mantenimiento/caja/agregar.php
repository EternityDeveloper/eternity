<?php
if (!isset($protect)){
	exit;
}
 
?>
 
<form name="frm_caja" id="frm_caja" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong>ID Caja:</strong></td>
    <td><input  name="id_caja" type="text" class="textfield textfieldsize" id="id_caja" style="width:110px;padding-right:10px;" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="descripcion" class="textfield textfieldsize" id="descripcion" style="height:50px;"></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Cajero:</strong></td>
    <td><input  name="cajero" type="text" class="textfield textfieldsize" style="width:200px;padding-right:10px;" id="cajero"></td>
  </tr>
  <tr >
    <td align="right"><strong>IP Equipo:</strong></td>
    <td><input  name="ip_caja" type="text" class="textfield textfieldsize" style="width:200px;padding-right:10px;" id="ip_caja"></td>
  </tr>
  <tr >
    <td align="right"><strong>Monto Incial:</strong></td>
    <td><input  name="monto_inicial" type="text" class="textfield textfieldsize" style="width:200px;padding-right:10px;" id="monto_inicial"></td>
  </tr>


  <tr>
    <td colspan="2"><input name="save_new_caja" type="hidden" id="save_new_caja" value="1">
      <input name="id_cajero" type="hidden" id="id_cajero"></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_caja_add"> Guardar</button>
      <button type="button" class="redButton" id="bt_caja_cancel"> Cancel</button></td>
  </tr> 
</table>
 </form>
 
