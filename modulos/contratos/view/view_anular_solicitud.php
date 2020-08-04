<?php 
if (!isset($protect)){
	exit;
}

$serie=System::getInstance()->Decrypt($_REQUEST['serie_contrato']);
$no_contrato=System::getInstance()->Decrypt($_REQUEST['no_contrato']);

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Anular solicitud</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td colspan="2">Este proceso anulará la solicitud<strong>.</strong>.</td>
            </tr>
            <tr>
              <td width="120"><strong>CONTRATO</strong></td>
              <td><?php echo $serie; ?> <?php echo $no_contrato; ?></td>
            </tr>
            <tr>
              <td colspan="2"><strong>DESCRIPCION:</strong></td>
            </tr>
            <tr>
              <td colspan="2"><textarea name="anul_comentario" id="anul_comentario" class="form-control" cols="45" rows="5"></textarea></td>
            </tr>
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="doAnularSolicitud" class="btn btn-primary">Proceder</button>
      </div>
    </div>
  </div>
</div>