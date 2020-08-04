<?php
if (!isset($protect)){
	exit;
}	
 
?>
<form name="form_plan_financiamiento" id="form_plan_financiamiento" method="post" action="" class="fsForm  fsSingleColumn">

<table id="plan_load" width="400" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td align="right"><strong>Codigo:</strong></td>
    <td><span class="finder">
      <input type="text" class="form-control  " name="CODIGO_TP" id="CODIGO_TP" placeholder="" autocomplete="off" />
    </span></td>
  </tr>
  <tr>
    <td align="right"><strong>Empresa:</strong></td>
    <td><select name="plan_empresa" id="plan_empresa"  class="form-control  "  >
      <option value="">Seleccionar</option>
      <?php 
	 	 $SQL="SELECT EM_ID,`EM_NOMBRE`,`por_interes_local`,`por_interes_dolares`,`por_enganche`,`por_impuesto` FROM `empresa` ";
		$rs=mysql_query($SQL);
		while($row=mysql_fetch_assoc($rs)){
			$dt=array(
				"INTERES_LOCAL"=>$row['por_interes_local'],
				"INTERES_DOLARES"=>$row['por_interes_dolares'],
				"IMPUESTO"=>$row['por_impuesto'] 
			);
			
	  ?>
      <option value="<?php echo System::getInstance()->Encrypt(json_encode($row));?>*_*<?php echo  base64_encode(json_encode($dt));?>" ><?php echo $row['EM_NOMBRE'] ?></option>
      <?php } ?>
      </select></td>
  </tr>
  <tr  class="plan_moneda">
    <td align="right"><strong>Moneda:</strong></td>
    <td><select name="MONEDA_TP" id="MONEDA_TP"  class="form-control  "  style="height:30px;width:120px;">
      <option value="">Seleccionar</option>
      <option value="LOCAL">LOCAL</option>
      <option value="DOLARES">DOLARES</option>
      </select></td>
  </tr>
  <tr class="plan_detalle">
    <td align="right"><strong>Impuesto:</strong></td>
    <td> 
      <input name="IMPUESTO_TP" type="text" class="form-control  " id="IMPUESTO_TP" placeholder="" autocomplete="off"  /> </td>
  </tr>
  <tr class="plan_detalle">
    <td align="right" valign="middle"><strong>% Impuesto:</strong></td>
    <td id=""> 
      <input name="plan_por_impuesto" type="text" disabled="disabled" class="form-control  " id="plan_por_impuesto" placeholder="" autocomplete="off" />
      </td>
  </tr>
  <tr class="plan_detalle" >
    <td align="right" valign="middle"><strong>% Interes:</strong></td>
    <td align="left" id=""><span class="finder">
      <input name="plan_por_interes" type="text" disabled="disabled" class="form-control  " id="plan_por_interes" placeholder="" autocomplete="off" />
      </span></td>
  </tr>
  <tr>
    <td align="right"><strong>Precio:</strong></td>
    <td><span class="finder">
      <input type="text" class="form-control  " name="PRECIO_TP" id="PRECIO_TP" placeholder="" autocomplete="off" />
    </span></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Capital:</strong></td>
    <td align="left"><span class="finder">
      <input type="text" class="form-control  form-control " name="CAPITAL_TP" id="CAPITAL_TP" placeholder="" autocomplete="off" />
    </span></td>
  </tr>
  <tr >
    <td align="right" valign="middle"><strong>Enganches:</strong></td>
    <td align="left" valign="top" id="enganche_content"></td>
  </tr>
  <tr>
    <td colspan="2"><input name="type_form" type="hidden" id="type_form" value="add" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_pro_f_save"> Guardar</button>
      <button type="button" class="redButton" id="bt_pro_f_cancel">Cancel</button></td>
  </tr>
</table>
</form>
