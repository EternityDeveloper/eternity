<?php
if (!isset($protect)){
	exit;
}

if (!isset($_REQUEST['id'])){exit;} 
 
$accion=json_decode(System::getInstance()->Decrypt($_REQUEST['id'])); 
 
if (!isset($accion->idaccion)){exit;} 
 
?> 
<form name="frm_cobro" id="frm_cobro" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong>ID Acción:</strong></td>
    <td><input  name="idaccions" type="text" disabled class="textfield textfieldsize" id="idaccions" style="width:110px;padding-right:10px;" value="<?php echo $accion->idaccion?>" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="accion" class="textfield textfieldsize" id="accion" style="height:50px;"><?php echo $accion->accion?></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Genera gestión:</strong></td>
    <td><input name="gen_gestion" type="checkbox" id="gen_gestion" value="1" <?php echo $accion->gen_gestion=="S"?'checked':''?> ></td>
  </tr>


  <tr>
    <td colspan="2"><input name="save_new_caja" type="hidden" id="save_new_caja" value="1">
      <input name="id_accion" type="hidden" id="id_accion" value="<?php echo $_REQUEST['id'];?>" /></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_cobro_add">Guardar</button>
      <button type="button" class="redButton" id="bt_caja_cancel"> Cancel</button></td>
  </tr> 
</table>
 </form>