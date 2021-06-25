<?php
if (!isset($protect)){
	exit;
}
 
?><table width="100%" border="0" cellspacing="0" cellpadding="0" class="fsPage fsDivPage">
  <tr>
        <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><table width="500" border="1" cellpadding="5"  style="border-spacing:8px;">
      <tr >
        <td align="right"><strong>CODIGO ZONA:</strong></td>
        <td><input  name="txt_cod_zona" type="text" class="required" id="txt_cod_zona" /></td>
      </tr>
      <tr >
        <td align="right"><strong>ZONA:</strong></td>
        <td><input  name="txt_zona" type="text" class="required" id="txt_zona" /></td>
      </tr>
      <tr >
        <td align="right" ><strong>OFICIAL:</strong></td>
        <td><input  name="txt_oficial" type="hidden" class="required" id="txt_oficial" style="width:300px;" /></td>
      </tr>
      <tr >
        <td align="right" ><strong>MOTORIZADO:</strong></td>
        <td><p>
          <input  name="txt_motorizado" type="hidden" class="required" id="txt_motorizado" style="width:300px;" />
          </p></td>
      </tr>
      <tr >
        <td colspan="2" align="center" >&nbsp;</td>
      </tr>
      <tr >
        <td colspan="2" align="center" ><input type="submit" name="btz_create" id="btz_create" class="btn btn-primary bt-sm" value="CREAR ZONA" /></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="68%"><strong>IDENTIFICAR ZONA EN EL MAPA</strong></td>
        <td width="8%"><strong>CLIENTES:</strong></td>
        <td width="8%" ><span id="z_clientes" class="badge alert-danger">0</span></td>
        <td width="10%"><strong>CONTRATOS:</strong></td>
        <td width="6%" ><span  id="z_contratos"class="badge alert-danger">0</span></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left"><div id="map_zona" style="height:400px;"></div></td>
  </tr>
</table>
 
