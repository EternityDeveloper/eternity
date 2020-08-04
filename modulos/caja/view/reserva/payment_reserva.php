<?php 
if (!isset($protect)){
	exit;	
}

SystemHtml::getInstance()->includeClass("inventario/reserva","Reserva"); 

$reserva= new Reserva($protect->getDBLink(),$_REQUEST);

$no_reserva=json_decode(System::getInstance()->Decrypt($_REQUEST['reserva'])); 
 
$total=$reserva->getTotalReserva($no_reserva);
 
/*CUIDADO CON ESTE DATO
ES LA FORMA DE TRANSFERIR EL TOTAL DE RESERVA*/ 
echo "<!--".$total."-->";
?>

<style>
.pay_item{
	float:right;
	margin-right:10px;
	cursor:pointer;
}
 
</style>
<div class="fsPage">
<form method="post"  action="" id="caja_payment"  name="caja_payment" class="fsForm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table width="600" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="25%"><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td><strong>Serie. Documento</strong></td>
                  </tr>
                <tr>
                  <td><input type="text" name="serie_documento" id="serie_documento"  class="textfield_input required"/></td>
                  </tr>
                </table></td>
              <td width="25%"><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td><strong>No. Documento</strong></td>
                  </tr>
                <tr>
                  <td><input type="text" name="no_documento" id="no_documento"  class="textfield_input required"/></td>
                  </tr>
                </table></td>
              <td width="25%"><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td><strong>Reporte de venta</strong></td>
                  </tr>
                <tr>
                  <td><input type="text" name="reporte_venta" id="reporte_venta"  class="textfield_input"/></td>
                  </tr>
                </table></td>
            </tr>
            </table></td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          </tr>
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td colspan="4"><h2 style="cursor:pointer" id="factura_mov_label">FACTURA<span class="pay_item ui-button-icon-primary ui-icon ui-icon-triangle-1-s"></span></h2></td>
              </tr>
            <tr id="mov_caja_factura" style="display:none">
              <td><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td><strong>Serie factura</strong></td>
                  </tr>
                <tr>
                  <td><input type="text" name="serie_factura" id="serie_factura"  class="textfield_input required"/></td>
                  </tr>
                </table></td>
              <td ><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td><strong>No. Factura</strong></td>
                  </tr>
                <tr>
                  <td><input type="text" name="no_factura" id="no_factura" class="textfield_input required" /></td>
                  </tr>
                </table></td>
              <td><table width="150" border="0" cellspacing="5" cellpadding="1">
                <tr>
                  <td><strong>Fecha factura</strong></td>
                  </tr>
                <tr>
                  <td><input type="text" name="fecha_factura" id="fecha_factura"  class="textfield_input required"/></td>
                  </tr>
                </table></td>
              <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        
        <tr>
          <td >&nbsp;</td>
        </tr>
        <tr>
          <td id="forma_pago_view">&nbsp;</td>
          </tr>
        <tr>
          <td><h2>OBSERVACION</h2></td>
        </tr>
        <tr>
          <td align="center"><textarea name="observacion" id="observacion" cols="45" rows="5" style="width:95%" class="textfield_input"></textarea></td>
          </tr>
        <tr>
          <td align="center">&nbsp;</td>
          </tr>
        <tr>
          <td align="center"><button type="button" class="greenButton" id="bt_caja_process">&nbsp;Realizar Abono&nbsp;</button>
            <button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button>&nbsp;</td>
          </tr>
        <tr>
          <td align="center">&nbsp;</td>
          </tr>
  </table>   
    </td>
    </tr>
</table>

</form>
</div>