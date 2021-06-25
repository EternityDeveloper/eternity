<?php 
if (!isset($protect)){
	exit;	
}
 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Gestión Abono a saldo</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td width="150"><strong>Monto inicial RD$</strong></td>
            <td>
            <input name="monto_inicial" type="text" class="form-control" id="monto_inicial"></td>
          </tr>
          <tr>
            <td><strong>Fecha</strong></td>
            <td><input name="nuevo_saldo" type="text" disabled class="form-control" id="nuevo_saldo"></td>
          </tr>
          <tr>
            <td><strong>Recibo de venta</strong></td>
            <td><input type="text" name="recibo_venta" id="recibo_venta" class="form-control"></td>
          </tr>
          <tr>
            <td><strong>Comentarios</strong></td>
            <td><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_abono_saldo" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
 