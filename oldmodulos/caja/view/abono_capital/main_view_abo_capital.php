<?php 
if (!isset($protect)){
	exit;	
} 
?>
<div class="modal fade" id="modal_abono_a_capital" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:730px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">ABONO A CAPITAL</h4>
      </div>
      <div class="modal-body">
      <form method="post"  action="" id="caja_payment"  name="caja_payment" class="fsForm"> 
        <table width="600" border="0" cellspacing="0" cellpadding="0"> 
          <tr>
            <td id="detalle_general" style="display:none">&nbsp;xxx</td>
          </tr>
          <tr>
            <td id="forma_pago_view">&nbsp;</td>
          </tr>
          <tr>
            <td id="factura_view_">&nbsp;</td>
          </tr>
          <tr>
            <td id="payment_mensaje_td" style="display:none"></td>
          </tr>
          <tr>
            <td align="center"><span class="label label-danger" id="p_err_message" style="display:none"></span></td>
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
            <td align="center"><button type="button" class="greenButton" id="bt_caja_process">&nbsp;Procesar</button>
              <button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button>
              &nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
        </form>
      </div>
       
    </div>
  </div>
</div>