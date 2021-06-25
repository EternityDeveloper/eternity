<?php
if (!isset($protect)){
	exit;
}

if (!validateField($_REQUEST,"id")){
	echo "Debe de seleccionar una caja!";
	exit;
}

$doc=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
 
?>
 
<form name="frm_general" id="frm_general" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong>Codigo Documento:</strong></td>
    <td><input  name="tODC" type="text" disabled class="textfield textfieldsize" id="tODC" style="width:110px;padding-right:10px;" value="<?php echo $doc->TIPO_DOC?>" maxlength="5" readonly></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="descripcion" class="textfield textfieldsize" id="descripcion" style="height:50px;"><?php echo $doc->DOCUMENTO?></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Fiscal:</strong></td>
    <td><input name="fiscal" type="checkbox" id="fiscal" value="1" <?php echo $doc->FISCAL=="S"?'checked':'';?>  /></td>
  </tr>
  <tr >
    <td align="right"><strong>Anula Movimiento:</strong></td>
    <td><input name="anula_mov" type="checkbox" id="anula_mov" value="1" <?php echo $doc->ANULA_MOVI=="S"?'checked':'';?>/></td>
  </tr>
  <tr >
    <td align="right"><strong>Reporte de Venta:</strong></td>
    <td><input name="repo_venta" type="checkbox" id="checkbox4" value="1" <?php echo $doc->REP_VENTA=="S"?'checked':'';?> /></td>
  </tr>
  <tr >
    <td align="right"><strong>Impresion:</strong></td>
    <td><input name="imprime" type="checkbox" id="imprime" value="1" <?php echo $doc->IMPRESION=="S"?'checked':'';?>/></td>
  </tr>


  <tr>
    <td colspan="2"><input name="save_edit_doc" type="hidden" id="save_edit_doc" value="1">
    <input name="TIPO_DOC" type="hidden" id="TIPO_DOC" value="<?php echo $_REQUEST['id'];?>"></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_g_add"> Guardar</button>
      <button type="button" class="redButton" id="bt_g_cancel"> Cancel</button></td>
  </tr> 
</table>
 </form>