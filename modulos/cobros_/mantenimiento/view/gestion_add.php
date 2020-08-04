<?php
if (!isset($protect)){
	exit;
}
 
?> 
<form name="frm_cobro" id="frm_cobro" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong>ID Tipo Gestion:</strong></td>
    <td><input  name="idtipogestion" type="text" class="textfield " id="idtipogestion" style="width:110px;padding-right:10px;" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="gestion" class="textfield " id="gestion" style="height:50px;"></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Genera Actividad:</strong></td>
    <td><input name="genera_actividad" type="checkbox" id="genera_actividad" value="1"></td>
  </tr>
  <tr >
    <td align="right"><strong>Tiempo Maximo:</strong></td>
    <td><input  name="Tiempo_max" type="text" class="textfield " style="width:200px;padding-right:10px;" id="Tiempo_max"></td>
  </tr>
  <tr >
    <td align="right"><strong>Escalamiento 1:</strong></td>
    <td><input  name="escalamiento1" type="text" class="textfield " style="width:200px;padding-right:10px;" id="escalamiento1"></td>
  </tr>
  <tr >
    <td align="right"><strong>Escalamiento 2:</strong></td>
    <td><input  name="escalamiento2" type="text" class="textfield " style="width:200px;padding-right:10px;" id="escalamiento2"></td>
  </tr>


  <tr>
    <td colspan="2"><input name="save_new_caja" type="hidden" id="save_new_caja" value="1">
      <input name="escalamiento1_code" type="hidden" id="escalamiento1_code" value="1">
      <input name="escalamiento2_code" type="hidden" id="escalamiento2_code" value="1">
      <input name="id_gestion" type="hidden" id="id_gestion" value="" /></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_cobro_add">Guardar</button>
      <button type="button" class="redButton" id="bt_caja_cancel"> Cancel</button></td>
  </tr> 
</table>
 </form>
 
