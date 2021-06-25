<?php

if (!isset($protect)){
	echo "Security error!";
	exit;
}
if (isset($_REQUEST['submit_add_tipo_pilar'])){
	/* CARGO LA LIBRERIA DE PILAR */
	SystemHtml::getInstance()->includeClass("prospectos","Pilar");
	
	$pilar= new Pilar($protect->getDBLink());
	$result=$pilar->addPilar($_REQUEST);
	echo json_encode($result);
	exit;	
}


 
?>
<form name="form_interfase" id="form_interfase" method="post" action="" class="fsForm  fsSingleColumn">
<table width="390" border="1" cellpadding="5" class="fsPage" style="border-spacing:8px;">
  <tr>
    <td align="right"><strong>Pilar:</strong></td>
    <td><input name="idtipo_pilar" type="text" class="textfield textfieldsize" id="idtipo_pilar" maxlength="5" ></td>
  </tr>
  <tr>
    <td align="right"><strong>Descripcion:</strong></td>
    <td>
      
      <input type="text" class="textfield textfieldsize"  name="dscrip_tipopilar" id="dscrip_tipopilar"></td>
  </tr>
  <tr>
    <td align="right"><strong>Dias de proteccion:</strong></td>
    <td><input type="text" class="textfield textfieldsize"  name="dias_proteccion" id="dias_proteccion"></td>
  </tr>
 
  <tr>
    <td colspan="2"><input name="submit_add_tipo_pilar" type="hidden" id="submit_add_tipo_pilar" value="1" />&nbsp;
      <input name="add_tipo_pilar" type="hidden" id="add_tipo_pilar" value="1"></td>
  </tr>
  <tr>
    <td colspan="2" align="center">   
                      <button type="button" class="greenButton" id="bt_save">
                        Guardar</button>
                       <button type="button" class="redButton" id="bt_cancel">
                        Cancel</button>  
        </td>
    </tr>
</table>
</form>