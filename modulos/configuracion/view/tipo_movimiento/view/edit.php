<?php
if (!isset($protect)){
	exit;
}

if (!validateField($_REQUEST,"id")){
	echo "Debe de seleccionar una caja!";
	exit;
}

$mov=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
  
?>
 
<form name="frm_caja" id="frm_general" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr >
    <td align="right" ><strong>Codigo Movimiento:</strong></td>
    <td><input  name="mov" type="text" disabled="disabled" class="textfield textfieldsize" id="mov" style="width:110px;padding-right:10px;" value="<?php echo $mov->TIPO_MOV?>" readonly="readonly"  /></td>
  </tr>
  <tr >
    <td align="right" valign="top"><strong>Descripción:</strong></td>
    <td><textarea name="DESCRIPCION" class="textfield textfieldsize" id="DESCRIPCION" style="height:50px;"><?php echo $mov->DESCRIPCION?></textarea></td>
  </tr>
  <tr >
    <td align="right" ><strong>Cuenta contable:</strong></td>
    <td><input  name="CTA_CONTABLE" type="text" class="textfield textfieldsize" id="CTA_CONTABLE" style="width:110px;padding-right:10px;" value="<?php echo $mov->CTA_CONTABLE?>" maxlength="5" /></td>
  </tr>
  <tr >
    <td align="right" ><strong>Interno:</strong></td>
    <td><input name="INTERNO" type="checkbox" id="INTERNO" value="1"  <?php echo $mov->INTERNO=="S"?'checked':'';?>  /></td>
  </tr>
  <tr >
    <td align="right"><strong>CASH:</strong></td>
    <td><input name="CASH" type="checkbox" id="CASH" value="1"  <?php echo $mov->CASH=="S"?'checked':'';?> /></td>
  </tr>
  <tr >
    <td align="right"><strong>Autorización:</strong></td>
    <td><input name="AUTORIZACION" type="checkbox" id="AUTORIZACION" value="1"  <?php echo $mov->AUTORIZACION=="S"?'checked':'';?> /></td>
  </tr>
  <tr >
    <td align="right"><strong>Afecta contrato:</strong></td>
    <td><input name="AFEC_CONTRATO" type="checkbox" id="AFEC_CONTRATO" value="1"  <?php echo $mov->AFEC_CONTRATO=="S"?'checked':'';?> /></td>
  </tr>
  <tr >
    <td align="right"><strong>Afecta reserva:</strong></td>
    <td><input name="AFEC_RESERVA" type="checkbox" id="AFEC_RESERVA" value="1"  <?php echo $mov->AFEC_RESERVA=="S"?'checked':'';?> /></td>
  </tr>
  <tr >
    <td align="right"><strong>Afecta cliente:</strong></td>
    <td><input name="AFEC_CLIENTE" type="checkbox" id="AFEC_CLIENTE" value="1" <?php echo $mov->AFEC_CLIENTE=="S"?'checked':'';?>/></td>
  </tr>
  <tr >
    <td align="right"><strong>Operacion:</strong></td>
    <td><label>
      <input type="radio" name="OPERACION" value="S" id="OPERACION"  <?php echo $mov->OPERACION=="S"?'checked':'';?>  />
      SUMA</label>
      <label>
        <input type="radio" name="OPERACION" value="R" id="OPERACION" <?php echo $mov->OPERACION=="R"?'checked':'';?>  />
      RESTA</label></td>
  </tr>
  <tr >
    <td align="right"><strong>Afecta Cuotas:</strong></td>
    <td><input name="AFEC_CUOTA" type="checkbox" id="AFEC_CUOTA" value="1"  <?php echo $mov->AFEC_CUOTA=="S"?'checked':'';?> /></td>
  </tr>
  <tr >
    <td align="right"><strong>Afecta Mora:</strong></td>
    <td><input name="AFEC_MORA" type="checkbox" id="AFEC_MORA" value="1"  <?php echo $mov->AFEC_MORA=="S"?'checked':'';?> /></td>
  </tr>
  <tr >
    <td align="right"><strong>Afecta Mantenimiento:</strong></td>
    <td><input name="AFEC_MANTE" type="checkbox" id="AFEC_MANTE" value="1"  <?php echo $mov->AFEC_MANTE=="S"?'checked':'';?> /></td>
  </tr>
  <tr>
    <td colspan="2"><input name="save_edit_mov" type="hidden" id="save_edit_mov" value="1" />
      <input name="TIPO_MOV" type="hidden" id="TIPO_MOV" value="<?php echo $_REQUEST['id'];?>" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_g_add"> Guardar</button>
      <button type="button" class="redButton" id="bt_g_cancel"> Cancel</button></td>
  </tr>
</table>
</form>