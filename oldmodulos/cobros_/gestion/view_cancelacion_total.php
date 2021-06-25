<?php 
if (!isset($protect)){
	exit;	
} 

if (!isset($_REQUEST['contrato'])){
	exit;
}
$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));


if (!$contrato->serie_contrato){
	exit;
}
SystemHtml::getInstance()->includeClass("contratos","Contratos"); 

$con=new Contratos($protect->getDBLink()); 
$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
$capita_interes=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);
 
$monto_saldo=$capita_interes->capital_pagado+$capita_interes->INICIAL;

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cancelacion Total</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td width="150"><strong>Monto saldado    </strong></td>
            <td>
            <input name="monto_saldo" type="text" disabled="disabled" class="form-control" id="monto_saldo" value="<?php echo number_format($monto_saldo,2);?>"></td>
          </tr>
          <tr>
            <td><strong>Capital pendiente  </strong></td>
            <td><strong><?php echo number_format($capita_interes->capital_pendiente,2);?></strong></td>
          </tr>
          <tr>
            <td><strong>% Descuento:</strong></td>
            <td><input name="por_descuento" type="text" class="form-control" id="por_descuento" value="0" style="width:110px;" /></td>
          </tr>
          <tr>
            <td><strong>Monto Descuento:</strong></td>
            <td id="monto_descuento">0</td>
          </tr>
          <tr>
            <td><strong>Comentarios</strong></td>
            <td><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_cancelacion_total" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<?php 
$html=ob_get_contents();
ob_clean();

echo json_encode(array("html"=>$html,"monto_capital_pendiente"=>$capita_interes->capital_pendiente,"por_descuento"=>0));

?>
 