<?php 
if (!isset($protect)){
	exit;	
}

if (!validateField($_REQUEST,"lote")){
	exit;
}
$lote=json_decode(System::getInstance()->Decrypt($_REQUEST['lote']));
if (!validateField($lote,"pap_codigo_lote")){
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
        <h4 class="modal-title" id="myModalLabel">ASIGNAR LOTE</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="444" border="0" cellspacing="0" cellpadding="0" class="table" >
                <tr>
                  <td width="120" align="left"><strong>LOTE:</strong></td>
                  <td width="324"><?php echo $lote->pap_codigo_lote?></td>
                </tr>
                <tr>
                  <td align="left"><strong>DISPONIBLE:</strong></td>
                  <td><?php echo $lote->DISPONIBLE?></td>
                </tr>
                <tr>
                  <td align="left"><strong>CANTIDAD:</strong></td>
                  <td><input  name="cantidad" type="text" class="form-control"  id="cantidad" style="width:200px;" maxlength="5"/></td>
                </tr>
                <tr>
                  <td align="left"><strong>DESDE</strong>:</td>
                  <td><input  name="pap_desde" type="text" class="form-control"  id="pap_desde" style="width:200px;" maxlength="5" readonly="readonly"/></td>
                </tr>
                <tr>
                  <td align="left"><strong>HASTA</strong>:</td>
                  <td><input  name="pap_hasta" type="text" class="form-control"  id="pap_hasta" style="width:200px;" maxlength="5" readonly="readonly"/></td>
                </tr>
                <tr>
                  <td align="left"><strong>ASESOR:</strong></td>
                  <td><input type="hidden" name="txt_oficial" id="txt_oficial" style="width:300px;"></td>
                </tr>
            </table></td>
            </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_asg_dist" class="btn btn-primary">Procesar</button>
      </div>
    </div>
  </div>
</div>