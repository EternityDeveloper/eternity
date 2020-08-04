<?php 
if (!isset($protect)){
	exit;	
}
 
 
SystemHtml::getInstance()->includeClass("papeleria","Recibos"); 
$pap= new Recibos($protect->getDBLINK());   
?>
<div class="modal fade" id="view_modal_lote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">CREAR DOCUMENTO</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="444" border="0" cellspacing="0" cellpadding="0" class="table" >
                <tr>
                  <td align="left"><strong>TIPO MONEDA:</strong></td>
                  <td width="324"> 
                    <select name="tipo_moneda" id="tipo_moneda" class="form-control" style="width:200px;">
                      <option value="0" selected="selected">Seleccionar</option> 
                      <option value="LOCAL">LOCAL</option> 
                      <option value="DOLARES">DOLARES</option> 
                  </select></td>
                </tr>
                <tr>
                  <td align="left"><strong>APLICA PARA:</strong></td>
                  <td><select name="aplica_para" id="aplica_para" class="form-control" style="width:200px;">
                    <option value="0" selected="selected">Seleccionar</option>
                    <option value="PRODUCTO">PRODUCTO</option> 
                    <option value="SERVICIO">SERVICIO FUNERARIO</option> 
                  </select></td>
                </tr>
                <tr>
                  <td width="120" align="left"><strong>NOMBRE:</strong></td>
                  <td><input  name="pap_nombre" type="text" class="form-control"  id="pap_nombre" style="width:300px;" /></td>
                </tr>
            </table></td>
            </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_acta" class="btn btn-primary">Procesar</button>
      </div>
    </div>
  </div>
</div>