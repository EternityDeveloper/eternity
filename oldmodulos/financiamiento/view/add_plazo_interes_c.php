<?php
if (!isset($protect)){
	exit;
}	
 
?>
<form name="form_plazo_i_c" id="form_plazo_i_c" method="post" action="" class="fsForm  fsSingleColumn">

<table width="400" border="1" cellpadding="5" cellspacing="5" class="fsPage table" id="plan_load" style="border-spacing:8px;padding:10px">
  <tr>
    <td align="right"><strong>Situacion:</strong></td>
    <td><select name="necesidad_pre" id="necesidad_pre">
      <option value="">Seleccione</option>
      <option value="PRE">PRENECESIDAD</option>
      <option value="NSD">NECESIDAD</option>
    </select></td>
  </tr>
  <tr>
    <td align="right"><strong>Empresa:</strong></td>
    <td><select name="EM_ID" id="EM_ID"  class="textfield "  style="height:30px;">
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT EM_ID,`EM_NOMBRE`,`por_interes_local`,`por_interes_dolares`,`por_enganche`,`por_impuesto` FROM `empresa` ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){  
	  ?>
      <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>" ><?php echo $row['EM_NOMBRE'] ?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr class="plan_detalle">
    <td align="right" valign="middle"><strong>Plazo:</strong></td>
    <td id="td"><input name="plazo_desde" type="text" class="textfield " id="plazo_desde" placeholder="" autocomplete="off" style="width:50px;" />
      -
      <input name="plazo_hasta" type="text" class="textfield " id="plazo_hasta" placeholder="" autocomplete="off" style="width:50px;" /></td>
  </tr>
  <tr class="plan_detalle">
    <td align="right" valign="middle"><strong>% Interes local:</strong></td>
    <td id=""> 
      <input name="interes_local" type="text" class="textfield " id="interes_local" placeholder="" autocomplete="off"  style="width:100px;"/>
      </td>
  </tr>
  <tr class="plan_detalle" >
    <td align="right" valign="middle"><strong>% Interes dolares:</strong></td>
    <td align="left" id=""><span class="finder">
      <input name="interes_dolares" type="text" class="textfield " id="interes_dolares" placeholder="" autocomplete="off"  style="width:100px;"/>
      </span></td>
  </tr>
  <tr>
    <td align="right"><strong>% Comision:</strong></td>
    <td><span class="finder">
      <input type="text" class="textfield " name="Comision" id="Comision" placeholder="" autocomplete="off" style="width:100px;" />
    </span></td>
  </tr>
 
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_pro_f_save"> Guardar</button>
      <button type="button" class="redButton" id="bt_pro_f_cancel">Cancel</button></td>
  </tr>
</table>
</form>