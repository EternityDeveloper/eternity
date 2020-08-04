<?php
if (!isset($protect)){
	exit;
}	

$tb_precio=json_decode(System::getInstance()->Decrypt($_REQUEST['tb_precio']));
 
?>
<form name="form_plan_financiamiento" id="form_plan_financiamiento" method="post" action="" class="fsForm  fsSingleColumn">

<table id="plan_load" width="400" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td align="right"><strong>Codigo:</strong></td>
    <td><span class="finder">
      <input name="CODIGO_TP" type="text" disabled="disabled" class="textfield textfieldsize" id="CODIGO_TP" placeholder="" autocomplete="off" value="<?php echo $tb_precio->CODIGO_TP?>" readonly="readonly" />
    </span></td>
  </tr>
  <tr >
    <td align="right"><strong>Impuesto:</strong></td>
    <td> 
      <input name="IMPUESTO_TP" type="text" class="textfield textfieldsize" id="IMPUESTO_TP" placeholder="" autocomplete="off" value="<?php echo $tb_precio->IMPUESTO_TP?>" /> </td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>% Impuesto:</strong></td>
    <td id=""> 
      <input name="plan_por_impuesto" type="text" class="textfield textfieldsize" id="plan_por_impuesto" placeholder="" autocomplete="off" value="<?php echo $tb_precio->POR_IMPUESTO_TP?>" />
      </td>
  </tr>
  <tr   >
    <td align="right" valign="middle"><strong>% Interes:</strong></td>
    <td align="left" id=""><span class="finder">
      <input name="plan_por_interes" type="text" class="textfield textfieldsize" id="plan_por_interes" placeholder="" autocomplete="off" value="<?php echo $tb_precio->POR_INTERES?>" />
      </span></td>
  </tr>
  <tr>
    <td align="right"><strong>Precio:</strong></td>
    <td><span class="finder">
      <input name="PRECIO_TP" type="text" class="textfield textfieldsize" id="PRECIO_TP" placeholder="" autocomplete="off" value="<?php echo $tb_precio->PRECIO_TP?>" />
    </span></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Capital:</strong></td>
    <td align="left"><span class="finder">
      <input name="CAPITAL_TP" type="text" class="textfield textfieldsize" id="CAPITAL_TP" placeholder="" autocomplete="off" value="<?php echo $tb_precio->CAPITAL_TP?>" />
    </span></td>
  </tr>

  <tr >
    <td align="right" valign="middle"><strong>Enganches:</strong></td>
    <td align="left" valign="top" id="enganche_content"></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Estatus</strong></td>
    <td align="left" valign="top"><select name="estado" id="estado" class="required">
      <option value="">Seleccione</option>
      <?php 

$SQL="SELECT * FROM `sys_status` where id_status in (1,2)";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt($row['id_status']);
?>
      <option value="<?php echo $encriptID?>"  <?php echo $tb_precio->ESTATUS==$row['id_status']?'selected="selected"':''?>><?php echo $row['descripcion']?></option>
      <?php } ?>
    </select></td>
  </tr>
  <tr>
    <td colspan="2"><input type="hidden" name="enganche_hi" id="enganche_hi" value="<?php echo $tb_precio->ENGACHE?>" />
      <input type="hidden" name="tb_precio_id" id="tb_precio_id" value="<?php echo $_REQUEST['tb_precio']?>" />
      <input name="type_form" type="hidden" id="type_form" value="edit" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_pro_f_save"> Guardar</button>
      <button type="button" class="redButton" id="bt_pro_f_cancel">Cancel</button></td>
  </tr>
</table>
</form>