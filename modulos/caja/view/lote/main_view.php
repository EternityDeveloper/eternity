<?php 
if (!isset($protect)){
	exit;	
} 

SystemHtml::getInstance()->includeClass("caja","Caja");  

STCSession::GI()->setSubmit("doCarLote",array());


?>
<div class="modal fade" id="modal_pago_cuota" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:830px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="title_template">PAGO DE CUOTA</h4>
      </div>
      <div class="modal-body">
        <table width="800" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td id="mensaje" style="display:none;color:#F00">&nbsp;</td>
          </tr>
          <tr>
            <td> CODIGO BARRA</td>
          </tr>
          <tr>
            <td><input type="text" name="no_codigo_barra" id="no_codigo_barra" class="form-control"></td>
          </tr>
          <tr>
            <td><button type="button" class="add_recibo_manual orangeButton" value="<?php echo $solicitud;?>" style="display:none">Agregar recibo manual</button></td>
          </tr>  
 
          <tr>
            <td align="center"  >
              <table width="300" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><strong>Cantidad de recibos:</strong> <span class="day_restantes" id="cantidad_total">0</span>&nbsp;</td>
                  <td><strong>Monto a anular:</strong> <span class="day_restantes" id="monto_total_f">0</span>&nbsp;</td>
                </tr>
            </table></td>
          </tr>   
    
          <tr>
            <td align="center" >&nbsp;</td>
          </tr>       
          <tr>
            <td id="detalle_item"></td>
          </tr>         


          <tr>
            <td align="left" id="forma_pago_view"> </td>
          </tr>

          <tr>
            <td><h2>OBSERVACION</h2></td>
          </tr>
          <tr>
            <td align="center"><textarea name="observacion" id="observacion" cols="45" rows="3" style="width:95%" class="textfield_input"></textarea></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="center"><button type="button" class="greenButton" id="bt_caja_process">&nbsp;Anular</button>
              <button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button>
              &nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
          </tr>
        </table>
      
      </div>
       
    </div>
  </div>
</div>