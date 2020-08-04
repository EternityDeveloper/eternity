<?php 
	if (!isset($protect)){exit;}
	
	
if ((validateField($_REQUEST,"type")) && (validateField($_REQUEST,"token"))){
	if ($_REQUEST['type']=="edit"){
		$carrito->setToken($_REQUEST['token']);
		$fn=$carrito->getFinanciamiento();  
	}
}

 

 
?>
<table width="300" border="0" align="center" cellpadding="0" cellspacing="0" class="detalle_costo" style="background:#FFF;">
  <tr>
    <td height="25" style="color:#FFF;background:#009900;padding-left:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><strong>FINANCIAMIENTO</strong></td>
        <td><button type="button" class="orangeButton" id="bt_c_financiamiento_find" style="float:right;margin-right:20%;display:none">&nbsp;Agregar&nbsp;</button></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
      <tr >
        <td><strong>PLAN</strong></td>
        <td id="p_plan"><?php echo isset($fn->codigo)?$fn->codigo:'N/A'?></td>
      </tr>
      <tr >
        <td><strong>MONEDA</strong></td>
        <td id="p_mondeda"><?php echo isset($fn->moneda)?$fn->moneda:'N/A'?></td>
      </tr>
      <tr style="">
        <td width="49%" style=""><strong>PRECIO LISTA</strong></td>
        <td width="51%"><input type="text" name="p_precio_lista" id="p_precio_lista" value="<?php echo isset($fn->precio)?number_format($fn->precio,2):'0'?>" style="width:80px;" /></td>
      </tr>
      <tr >
        <td ><strong>PLAZO</strong></td>
        <td id="p_plazo"><?php echo isset($fn->plazo)?$fn->plazo:'0'?></td>
      </tr>
      <tr >
        <td ><strong>INTERES</strong></td>
        <td id="p_iteneres"><?php echo isset($fn->por_interes)?$fn->por_interes:'0'?></td>
      </tr>
      <tr >
        <td ><strong>% ENGANCHE</strong></td>
        <td id="p_enganche"><?php echo isset($fn->por_enganche)?$fn->por_enganche:'0'?></td>
      </tr>
    </table></td>
  </tr>
</table>
 