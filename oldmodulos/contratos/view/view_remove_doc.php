<?php

if (!isset($protect)){
	exit;
}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #FFF; font-weight: bold;">
  <tr>
    <td align="center" valign="top">Desea remover este documento?</td>
  </tr>
  <tr>
    <td align="left" valign="top">Por que?&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top"><textarea name="doc_descripcion_rmv" id="doc_descripcion_rmv" cols="45" rows="5" class="form-control"></textarea></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><button type="button"class="greenButton" id="bt_remove_doc">Guardar</button>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>
