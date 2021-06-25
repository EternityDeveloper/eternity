<?php 
if (!isset($protect)){
	exit;	
}
if (!isset($_REQUEST)){
	exit;
}
$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['recibo']));
SystemHtml::getInstance()->includeClass("caja","Recibos");
$recibos= new Recibos($protect->getDBLINK()); 

$valid=$recibos->validarSiElReciboTieneLaborDeCobro($recibo->SERIE,$recibo->NO_DOCTO);
 
?><div class="modal fade" id="view_modal_recibo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Anular Recibo</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0"> 
            <tr>
              <td width="120"><strong>NO RECIBO</strong></td>
              <td><?php  echo ($recibo->SERIE." ".$recibo->NO_DOCTO);?></td>
            </tr>
          <?php if ($valid>0){?>  
            <tr>
              <td><strong>CODIGO RECIBO</strong></td>
              <td><input type="text" name="codigo_recibo" id="codigo_recibo" class="form-control"/></td>
            </tr>
          <?php } ?>  
            <tr>
              <td colspan="2"><strong>MOTIVO:</strong></td>
            </tr>
            <tr>
              <td colspan="2"><textarea name="remove_comentario_rb" id="remove_comentario_rb" class="form-control" cols="45" rows="5"></textarea></td>
            </tr>
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="doAnularRecb" class="btn btn-primary">Proceder</button>
      </div>
    </div>
  </div>
</div>