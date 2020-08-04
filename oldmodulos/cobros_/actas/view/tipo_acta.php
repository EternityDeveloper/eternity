<?php 
if (!isset($protect)){
	exit;	
}
 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"> ACTA</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="444" border="0" cellspacing="0" cellpadding="0" >
                <tr>
                  <td align="left"><strong>TIPO ACTA:</strong></td>
                  <td width="324"> 
                    <select name="tipo_acta" id="tipo_acta" class="form-control" style="width:200px;">
                      <option value="0" selected="selected">Seleccionar</option>
                      <option value="DES">DESISTIDO</option>
                      <option value="ANU">ANULADO</option>
                  </select></td>
                </tr>
                <tr>
                  <td width="120" align="left"><strong>PERIODO DE:</strong></td>
                  <td><input  name="p_fecha_desde" type="text" class="filter_ textfield date_pick" style="font-size:12px; cursor:pointer;background:url(images/calendar.png) no-repeat;background-position:95% 50%;width:110px;padding-right:10px;" id="p_fecha_desde" readonly /></td>
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