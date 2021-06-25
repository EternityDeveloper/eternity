<?php
if (!isset($protect)){
	exit;
}
 
?>
 
<form name="frm_general" id="frm_general" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong>Codigo Documento:</strong></td>
    <td><input  name="TIPO_DOC" type="text" class="textfield textfieldsize" id="TIPO_DOC" style="width:110px;padding-right:10px;" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="descripcion" class="textfield textfieldsize" id="descripcion" style="height:50px;"></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Fiscal:</strong></td>
    <td><input name="fiscal" type="checkbox" id="fiscal" value="1" /></td>
  </tr>
  <tr >
    <td align="right"><strong>Anula Movimiento:</strong></td>
    <td><input name="anula_mov" type="checkbox" id="anula_mov" value="1" /></td>
  </tr>
  <tr >
    <td align="right"><strong>Reporte de Venta:</strong></td>
    <td><input name="repo_venta" type="checkbox" id="checkbox4" value="1" /></td>
  </tr>
  <tr >
    <td align="right"><strong>Impresion:</strong></td>
    <td><input name="imprime" type="checkbox" id="imprime" value="1" /></td>
  </tr>


  <tr>
    <td colspan="2"><input name="save_new_doc" type="hidden" id="save_new_doc" value="1"></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_g_add"> Guardar</button>
      <button type="button" class="redButton" id="bt_g_cancel"> Cancel</button></td>
  </tr> 
</table>
 </form>