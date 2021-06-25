<?php 
if (!isset($protect)){
	exit;	
}
if (!isset($_REQUEST['id_acta'])){
	exit;
}

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:850px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DETALLE DE ACTA</h4>
      </div>
      <div class="modal-body">
			<iframe src="./?mod_cobros/delegate&cierre_acta&showViewActa&id_acta=<?php echo $_REQUEST['id_acta'];?>" width="820px" height="450px;"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>