<?php 
if (!isset($protect)){
	exit;	
}
$acta=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
// print_r($acta); 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DESEA CERRAR EL ACTA?</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="444" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td width="120" align="left"><strong>ACTA:</strong></td>
                  <td width="324"><?php echo $acta->idacta."-".$acta->secuencia;?></td>
                </tr>
                <tr>
                  <td colspan="2" align="left"><strong>COMENTARIO:</strong></td>
                </tr>
                <tr>
                  <td colspan="2" align="left"><label for="comentarios"></label>
                  <textarea name="comentarios" id="comentarios" class="form-control" cols="45" rows="5"></textarea></td>
                </tr>
            </table></td>
          </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="question_process" class="btn btn-primary">Procesar</button>
      </div>
    </div>
  </div>
</div>