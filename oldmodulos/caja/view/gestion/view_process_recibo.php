<?php 
if (!isset($protect)){
	exit;	
} 

if (!isset($_REQUEST['id_nit'])){
	exit;
}
$id_nit=json_decode(System::getInstance()->Decrypt($_REQUEST['id_nit']));
if ($id_nit==""){
	exit;
}
//SystemHtml::getInstance()->includeClass("contratos","Contratos"); 

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:900px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Facturar</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage">
          <tr>
            <td colspan="2" id="listado_recibo"><center>No existen recibos para facturar</center></td>
          </tr>
          <tr>
            <td colspan="2" id="detalle_factura">&nbsp;</td>
          </tr>
          <tr class="sp_comentario">
            <td width="150"><strong>Comentarios</strong></td>
            <td>&nbsp;</td>
          </tr>
          <tr class="sp_comentario">
            <td colspan="2"><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
          <tr class="sp_comentario">
            <td colspan="2" align="center"><button type="button" class="greenButton" id="bt_caja_process">&nbsp;Procesar</button>
              <button type="button" class="redButton" id="bt_caja_cancel">Cancelar</button></td>
          </tr>
        </table>
      </div>
  
    </div>
  </div>
</div>