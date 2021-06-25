<?php 
if (!isset($protect)){
	exit;	
} 
?><div class="modal fade" id="moda_ncd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Notas de credito</h4>
      </div>
      <div class="modal-body">
             <center> <button type="button" class="btn btn-warning" id="_do_capitalizar" data-dismiss="modal">DESEO CAPITALIZAR ESTE PAGO</button>
        <button type="button" id="_do_nota_comprobante" class="btn btn-primary"  data-dismiss="modal">DESEO GENERAR UN COMPROBANTE FISCAL</button></center>
      </div>
      <div class="modal-footer">

      </div>
    </div>
  </div>
</div>
<?php
$ob=ob_get_contents();
ob_clean();
echo json_encode(array("html"=>$ob,"monto"=>round($row->MONTO,2)));

?>