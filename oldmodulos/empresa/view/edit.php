<?php
if (!isset($protect)){
	exit;
}

if (!validateField($_REQUEST,"id")){
	echo "Debe de seleccionar una caja!";
	exit;
}

$data=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
// print_r($data);
?>
 
<form name="frm_caja" id="frm_caja" method="post" action=""> 
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
 
  <tr >
    <td align="right" ><strong>EMP ID:</strong></td>
    <td><input  name="EM_ID" type="text" disabled class="textfield textfieldsize" id="EM_ID" style="width:110px;padding-right:10px;" value="<?php echo $data->EM_ID?>" maxlength="5"></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Nombre:</strong></td>
    <td><input name="EM_NOMBRE" type="text" class="textfield textfieldsize" id="EM_NOMBRE"   value="<?php echo $data->EM_NOMBRE?>"></td>
  </tr>
  <tr >
    <td align="right" ><strong>NIT:</strong></td>
    <td><input  name="EM_NIT" type="text" class="textfield textfieldsize" id="EM_NIT" style="width:200px;padding-right:10px;" value="<?php echo $data->EM_NIT?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>Interes PG:</strong></td>
    <td><input  name="INTERES_PG" type="text" class="textfield textfieldsize" id="INTERES_PG" style="width:200px;padding-right:10px;" value="<?php echo $data->INTERES_PG?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>% Interes local:</strong></td>
    <td><input  name="por_interes_local" type="text" class="textfield textfieldsize" id="por_interes_local" style="width:200px;padding-right:10px;" value="<?php echo $data->por_interes_local?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>% interes Dolar:</strong></td>
    <td><input  name="por_interes_dolares" type="text" class="textfield textfieldsize" id="monto_inicial3" style="width:200px;padding-right:10px;" value="<?php echo $data->por_interes_dolares ?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>% Enganche:</strong></td>
    <td><input  name="por_enganche" type="text" class="textfield textfieldsize" id="monto_inicial4" style="width:200px;padding-right:10px;" value="<?php echo $data->por_enganche?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>% Impuesto:</strong></td>
    <td><input  name="por_impuesto" type="text" class="textfield textfieldsize" id="monto_inicial5" style="width:200px;padding-right:10px;" value="<?php echo $data->por_impuesto?>"></td>
  </tr>
  <tr >
    <td align="right"><strong>Pre-necesidad:</strong></td>
    <td><input type="checkbox" name="prenecesidad" id="prenecesidad" <?php echo $data->prenecesidad=="S"?'checked':'';?> ></td>
  </tr>
  <tr >
    <td align="right"><strong>Necesidad:</strong></td>
    <td><input type="checkbox" name="necesidad" id="necesidad" <?php echo $data->necesidad=="S"?'checked':'';?> ></td>
  </tr>
 
  <tr>
    <td colspan="2"><input name="save_edit_caja" type="hidden" id="save_edit_caja" value="1">
      <input name="id_cajero" type="hidden" id="id_cajero" value="<?php echo System::getInstance()->Encrypt(json_encode(array("id_usuario"=>$data->id_usuario)));?>"><input  name="id_caja" type="hidden" id="id_caja" value="<?php echo System::getInstance()->Encrypt($data->id_caja);?>"  ></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_caja_add"> Guardar</button>
      <button type="button" class="redButton" id="bt_caja_cancel"> Cancel</button></td>
  </tr> 
</table>
 </form>
 
