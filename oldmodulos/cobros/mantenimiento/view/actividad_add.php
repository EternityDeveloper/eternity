<?php
if (!isset($protect)){
	exit;
}

if (!isset($_REQUEST['id_gestion'])){exit;} 
 
?> 
<form name="frm_actividad_" id="frm_actividad_" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong>ID Actividad:</strong></td>
    <td><input  name="idtipoact" type="text" class="textfield textfieldsize" id="idtipoact" style="width:110px;padding-right:10px;" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="actividad" class="textfield textfieldsize" id="actividad" style="height:50px;"></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Tipo actividad</strong></td>
    <td><p>
      <label>
        <input type="radio" name="act_int_ext" value="I" id="Radiogroup1_0">
        Interno</label>
  
      <label>
        <input type="radio" name="act_int_ext" value="E" id="Radiogroup1_1">
        Externo</label>
      <br>
    </p></td>
  </tr>
  <tr >
    <td align="right" ><strong>Orden</strong></td>
    <td><input  name="orden" type="text" class="textfield textfieldsize" id="orden" style="width:110px;padding-right:10px;" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right"><strong>Tiempo Maximo:</strong></td>
    <td><input  name="act_tiempo_max" type="text" class="textfield textfieldsize" style="width:200px;padding-right:10px;" id="act_tiempo_max"></td>
  </tr>
  <tr >
    <td align="right"><strong>Asignar a:</strong></td>
    <td><select name="asignar_actividad_a" id="asignar_actividad_a">
      <option value="NINGUNO" selected="selected">NINGUNO</option>
      <option value="ELEGIR">ELEGIR</option>
      <option value="OFICIAL">OFICIAL</option>
      <option value="MOTORIZADO">MOTORIZADO</option>
    </select></td>
  </tr>
  <tr >
    <td align="right"><strong>Escalamiento 1:</strong></td>
    <td><input  name="act_escalamiento1" type="text" class="textfield textfieldsize" style="width:200px;padding-right:10px;" id="act_escalamiento1"></td>
  </tr>
  <tr >
    <td align="right"><strong>Escalamiento 2:</strong></td>
    <td><input  name="act_escalamiento2" type="text" class="textfield textfieldsize" style="width:200px;padding-right:10px;" id="act_escalamiento2"></td>
  </tr>


  <tr>
    <td colspan="2"><input name="act_escalamiento1_code" type="hidden" id="act_escalamiento1_code" value="">
      <input name="act_escalamiento2_code" type="hidden" id="act_escalamiento2_code" value="">
      <input name="act_id_gestion" type="hidden" id="act_id_gestion" value="<?php echo $_REQUEST['id_gestion'];?>" /></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="act_add">Guardar</button>
      <button type="button" class="redButton" id="act_cancel"> Cancel</button></td>
  </tr> 
</table>
 </form>
 
