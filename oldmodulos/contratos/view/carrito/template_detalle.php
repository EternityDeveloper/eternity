<?php 
	if (!isset($protect)){exit;}
	
	
if ((validateField($_REQUEST,"type")) && (validateField($_REQUEST,"token"))){
	if ($_REQUEST['type']=="edit"){
		$carrito->setToken($_REQUEST['token']);
		$fn=$carrito->getFinanciamiento();  
	}
}
?><table width="301"  border="0" align="right" cellpadding="0" cellspacing="0"  style="background:#FFF;">
  <tr class="detalle_costo">
    <td width="301" height="25" style="color:#FFF;background:#009900;padding-left:10px;"><strong>DETALLE</strong></td>
  </tr>
  <tr class="detalle_costo">
    <td valign="baseline" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;"><table width="100%" border="0" cellspacing="1" cellpadding="0" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;">
      <tr>
        <td width="65%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PRECIO DE LISTA</strong></td>
        <td width="35%" style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="pro_total_a_pagar">0</td>
      </tr>
      <tr>
        <td colspan="2" align="left" style="display:none"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr class="detalle_costo">
            <td valign="baseline" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;">&nbsp;</td>
          </tr>
          <tr class="detalle_costo">
            <td valign="baseline" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;"><table width="100%" border="0" cellspacing="0" cellpadding="0"  >
              <tr>
                <td><strong>DESCUENTO x MONTO</strong></td>
                <td><button type="button" class="orangeButton" id="bt_c_descuento_x_monto" style="float:right;margin-right:20%;">&nbsp;Agregar&nbsp;</button></td>
              </tr>
            </table></td>
          </tr>
          <tr class="detalle_costo">
            <td id="descuento_x_monto">&nbsp;</td>
          </tr>
          <tr class="detalle_costo">
            <td style="padding-left:8px;">&nbsp;</td>
          </tr>
          <tr class="detalle_costo">
            <td style="padding-left:8px;padding-right:5px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><strong>DESCUENTO x PORCIENTO</strong></td>
                <td><button type="button" class="orangeButton" id="bt_c_descuento_x_porciento" style="float:right;margin-right:20%;">&nbsp;Agregar&nbsp;</button></td>
              </tr>
            </table></td>
          </tr>
          <tr class="detalle_costo">
            <td id="descuento_x_prociento">&nbsp;</td>
          </tr>
          <tr class="detalle_costo">
            <td style="padding-left:8px;padding-right:5px;padding-bottom:5px;" ><table width="100%" border="0" cellspacing="0" cellpadding="0" >
              <tr>
                <td width="74%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>TOTAL DE DESCUENTO</strong></td>
                <td width="28%" style="padding-bottom:3px;padding-top:15px;padding-left:5px;border-bottom:#333 solid 1px;" id="pro_total_descuento">0</td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;display:none"><span style="padding-top:2px;padding-left:8px;padding-right:0px;"><strong>Capital a financiar con descuento</strong></span></td>
        <td style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;display:none" id="precio_lista_menos_TotalDescuentoMonto" >0</td>
      </tr>
    </table></td>
  </tr>
  <tr class="detalle_costo">
    <td style="padding-left:8px;padding-right:5px;padding-bottom:5px;" ><table width="100%" border="0" cellspacing="1" cellpadding="0" style="border-top:#09C solid 1px;padding-left:8px;;padding-right:5px;vertical-align: baseline;">
      <tr> </tr>
      <tr>
        <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>Monto Inicial (<code id="monto_inicial_por">0</code>%)</strong></td>
        <td style="padding-bottom:3px; padding-top:10px;border-bottom-style:double;border-bottom:#333 solid 1px;" id="p_monto_enganche"><input name="txt_monto_incial" type="text" id="txt_monto_incial" style="width:70px;padding-left:5px;" value="0" disabled="disabled"/></td>
      </tr>
      <tr>
        <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><span style="padding-top:2px;padding-left:8px;padding-right:0px;"><strong>Capital neto a financiar</strong></span></td>
        <td id="capital_neto_a_pagar" style="padding-top:10px;padding-left:5px;border-bottom-style:double;">0</td>
      </tr>
      <!--                    <tr>
                      <td width="73%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;">Total Intereses Anuales</td>
                      <td width="27%" id="total_interes_anual" style="padding-top:10px;padding-left:5px;border-bottom-style:double;">0</td>
                    </tr>
                    <tr>
                      <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;">Total Monto Intereses Anuales</td>
                      <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="total_interes_monto_anual">0</td>
                    </tr>-->
      <tr>
        <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>Intereses total a financiar</strong></td>
        <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="total_interes_a_pagar">0</td>
      </tr>
      <tr  >
        <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>TOTAL A FINANCIAR</strong></td>
        <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="sub_total_a_pagar">0</td>
      </tr>
      <tr>
        <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;border-top:#09C solid 1px;"><strong>MENSUALIDADES</strong></td>
        <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;border-top:#09C solid 1px;" id="mensualidades">0 </td>
      </tr>
      <tr>
        <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>MONTO TOTAL DEL NEGOCIO</strong></td>
        <td style="padding-top:10px;padding-left:5px;" id="total_a_pagar">0</td>
      </tr>
    </table></td>
  </tr>
  <tr class="detalle_costo">
    <td  >&nbsp;</td>
  </tr>
  <tr class="detalle_costo">
    <td  >&nbsp;</td>
  </tr>
</table>