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
        <h4 class="modal-title" id="myModalLabel">COMENTARIOS</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>Descripción</td>
          </tr>
          <tr>
            <td><textarea name="doc_descripcion" id="doc_descripcion" style="height:60px;" cols="25" rows="5" class="form-control"></textarea></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="save_comentary" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>