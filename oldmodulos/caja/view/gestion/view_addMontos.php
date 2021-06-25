<?php 
if (!isset($protect)){
	exit;	
} 
?>
<div class="modal fade" id="moda_add_montos_ncd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar monto nota</h4>
      </div>
      <div class="modal-body">
        <table width="90%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle table">
          <tr>
            <td width="150"><span class="modal-title">nota</span></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_ano" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>