<?php 
if (!isset($protect)){
	exit;	
}
?>
<div class="modal fade" id="DetalleImprimir" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">IMPRIMIR</h4>
      </div>
      <div class="modal-body">
     <iframe id="detalle_imprimir" src="?mod_caja/delegate&recibo_nfactura_cobro" width="950" height="300"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>