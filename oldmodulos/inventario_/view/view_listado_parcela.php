<?php 
if (!isset($protect)){
	exit;	
}
 
?>
<div class="modal fade" id="viewListadoParcela" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">LISTADO DE PARCELAS</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table border="0" class="table table-bordered table-striped table-hover" id="tb_listado_inv" style="width:100%">
                <thead>
                  <tr>
                    <th width="100">Jardin</th>
                    <th>Fase </th>
                    <th>Modulo</th>
                    <th>Parcela</th>
                    <th>Estatus </th>
                    <th>Contrato</th>
                    <th>Cavidades</th>
                    <th>Osarios</th>
                    <th>&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table></td>
            </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
       </div>
    </div>
  </div>
</div>