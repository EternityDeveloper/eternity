<?php
if (!isset($protect)){
	exit;
}	


$rand=$_REQUEST['rand'];

?>
<style>
.fsPage{
	margin-bottom:10px;	
}
table.display2 {}
</style>
<div id="contrato_div"  >
 
  <table width="100%" border="0" cellpadding="0" cellspacing="0"  >
    <tr>
      <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50%" valign="top"><h2 style="margin:0px;">PRODUCTO</h2></td>
          <td width="50%" valign="top" >&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="fsPage"  style="margin-left:0px;"	>
            <tr>
              <td><button type="button" class="orangeButton" id="bt_c_add_producto">&nbsp;Agregar&nbsp;</button></td>
            </tr>
            <tr>
              <td id="display_product" style="display:none"></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="center" valign="top" id="mensaje_producto" style="display:none" ></td>
        </tr>
        <tr>
          <td colspan="2" align="center" valign="top"><table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td valign="top"><table width="300" border="0" align="center" cellpadding="0" cellspacing="0" class="detalle_costo" style="background:#FFF;">
                <tr>
                  <td height="25" style="color:#FFF;background:#009900;padding-left:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td><strong>FINANCIAMIENTO</strong></td>
                      <td><button type="button" class="orangeButton" id="bt_c_financiamiento_find" style="float:right;margin-right:20%;">&nbsp;Agregar&nbsp;</button></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-spacing:8px;">
                    <tr >
                      <td><strong>PLAN</strong></td>
                      <td id="p_plan">N/A</td>
                    </tr>
                    <tr >
                      <td><strong>MONEDA</strong></td>
                      <td id="p_mondeda">N/A</td>
                    </tr>
                    <tr style="">
                      <td width="73%" style=""><strong>PRECIO LISTA</strong></td>
                      <td width="27%" id="p_precio_lista">0</td>
                    </tr>
                    <tr >
                      <td ><strong>PLAZO</strong></td>
                      <td id="p_plazo">0</td>
                    </tr>
                    <tr >
                      <td ><strong>INTERES</strong></td>
                      <td id="p_iteneres">0</td>
                    </tr>
                    <tr >
                      <td ><strong>ENGANCHE</strong></td>
                      <td id="p_enganche">0</td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
              <td valign="top"><table width="300"  border="0" align="right" cellpadding="0" cellspacing="0"  style="background:#FFF;">
                <tr class="detalle_costo">
                  <td height="25" style="color:#FFF;background:#009900;padding-left:10px;"><strong>DETALLE</strong></td>
                </tr>
                <tr class="detalle_costo">
                  <td valign="baseline" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;"><table width="100%" border="0" cellspacing="1" cellpadding="0" style="padding-left:8px;;padding-right:5px;vertical-align: baseline;">
                    <tr>
                      <td width="65%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PRECIO DE LISTA</strong></td>
                      <td width="35%" style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="pro_total_a_pagar">0</td>
                    </tr>
                    <tr>
                      <td colspan="2" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                          <td id="descuento_x_prociento" style="display:none">&nbsp;</td>
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
                      <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><span style="padding-top:2px;padding-left:8px;padding-right:0px;"><strong>Capital a financiar con descuento</strong></span></td>
                      <td style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;" id="precio_lista_menos_TotalDescuentoMonto">0</td>
                    </tr>
                  </table></td>
                </tr>
                <tr class="detalle_costo">
                  <td style="padding-left:8px;padding-right:5px;padding-bottom:5px;" ><table width="100%" border="0" cellspacing="1" cellpadding="0" style="border-top:#09C solid 1px;padding-left:8px;;padding-right:5px;vertical-align: baseline;">
                    <tr>
   <tr>
    <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>Monto Inicial (<code id="monto_inicial_por">0</code>%)</strong></td>
    <td style="padding-bottom:3px; padding-top:10px;border-bottom-style:double;border-bottom:#333 solid 1px;" id="p_monto_enganche"><input name="txt_monto_incial" type="text" id="txt_monto_incial" style="width:70px;padding-left:5px;" value="0" disabled="disabled"/></td>
  </tr>
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
                    <tr  style="border:#09C solid 1px;">
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
                </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" align="center" valign="top" id="alert_monto" style="display:none"><div style="background-color:#C30;color:#FFF;width:800px;padding:5px;margin:5px;border-radius:2px;font-size:18px;">El Monto inicial es menor al 10%</div></td>
        </tr>
        <tr>
          <td colspan="2" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left:0px;">
            <tr>
              <td align="center">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top" >&nbsp;</td>
        </tr>
        <tr>
          <td valign="top">&nbsp;</td>
          <td valign="top" >&nbsp;</td>
        </tr>
      </table></td>
      <td valign="top">&nbsp;</td>
    </tr>
 
    <tr>
      <td align="center" valign="top"><button type="button" class="greenButton" id="bt_c_add_product">&nbsp;Agregar&nbsp;</button>
        <button type="button" class="redButton" id="bt_produc_cancel">Cerrar</button>&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
      </tr>
    <tr>
      <td align="center" valign="top" id="cuadro_producto" style="display:none"><table width="200" border="0" cellspacing="0" cellpadding="0" class="fsPage detalle_costo" id="producto_<?php echo $rand;?>" style="width:200px;">
        <tr>
          <td class="titlest">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="80%" id="title_<?php echo $rand;?>">x</td>
                <td><img class="edit_product" id="edit_<?php echo $rand;?>" style="cursor:pointer;float:right" src="images/subtract_from_cart.png" /></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td ><table width="100%" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td width="50%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PLAN:</strong></td>
                  <td width="50%"  id="detalle_plan_<?php echo $rand;?>"  style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;">&nbsp;</td>
                </tr>
<!--                <tr>
                  <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>MONEDA:</strong></td>
                  <td style="padding-bottom:3px; padding-top:10px;padding-left:5px;border-bottom-style:double;" id="detalle_moneda_<?php echo $rand;?>">0</td>
                </tr>
                <tr>
                  <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>ENGANCHE:</strong></td>
                  <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="detalle_enganche_<?php echo $rand;?>">0</td>
                </tr>-->
                <tr>
                  <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>PLAZO:</strong></td>
                  <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="detalle_plazo_<?php echo $rand;?>">0</td>
                </tr>
<!--                <tr>
                  <td width="52%" align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>TOTAL DE DESCUENTO:</strong></td>
                  <td width="48%" style="padding-top:15px;padding-left:5px;" id="detalle_descuento_<?php echo $rand;?>">0</td>
                </tr>
                <tr>
                  <td align="right" style="padding-top:10px;padding-left:8px;padding-right:10px;"><strong>MENSUALIDADES:</strong></td>
                  <td style="padding-top:10px;padding-left:5px;border-bottom-style:double;" id="detalle_mensualidades_<?php echo $rand;?>">0</td>
                </tr>-->
              </table></td>
            </tr>
            <tr>
              <td id="detalle_plan_<?php echo $rand;?>2">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
      </table></td>
      <td align="center" valign="top" id="cuadro_producto" style="display:none">&nbsp;</td>
    </tr>
  </table>
 
</div>