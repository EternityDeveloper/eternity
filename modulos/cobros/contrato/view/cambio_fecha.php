<?php 
if (!isset($protect)){
	exit;	
}

$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cambio Fecha Pago</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td colspan="2">Este proceso Cambiara la fecha de pago del cliente.</td>
            </tr>
   
            <tr>
              <td width="120"><strong>CONTRATO</strong></td>
              <td><?php  echo ($contrato->serie_contrato." ".$contrato->no_contrato);?></td>
            </tr>
            <tr>
              <td colspan="2"><strong>Fecha de Pago:</strong></td>
            </tr>
            <tr>
			  <td><input  name="newfechapago" type="text" class="form-control"  id="newfechapago"   value="<?php echo date("d-m-Y");?>" /></td>
            </tr>
			            <tr>
              <td colspan="2"><strong>Dia de Pago:</strong></td>
            </tr>
            <tr>
			  <td><input  name="newdiapago" type="text" class="form-control"  id="newdiapago"  /></td>
            </tr>
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="aplicar_cambio" class="btn btn-primary">Proceder</button>
      </div>
    </div>
  </div>
</div>