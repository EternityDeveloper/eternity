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

$monto_saldo=$capita_interes->capital_pagado;
 

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Reactivación</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td width="150"><strong>Capital saldado    </strong></td>
            <td>
            <input name="monto_saldo" type="text" disabled="disabled" class="form-control" id="monto_saldo" value="<?php echo number_format($monto_saldo,2);?>"></td>
          </tr>
          <tr>
            <td><strong> % Penalidad:  </strong></td>
            <td><input name="porcient_penalidad" type="text" class="form-control" id="porcient_penalidad" value="30" maxlength="2"></td>
          </tr>
          <tr>
            <td><strong>Monto reactivación</strong></td>
            <td><input name="monto_reactivacion" type="text" class="form-control" id="monto_reactivacion" value="<?php echo number_format($monto_saldo*30/100,2);?>"></td>
          </tr>
          <tr>
            <td><strong>Cuotas actuales</strong></td>
            <td><input name="cuotas_pendientes" type="text" disabled class="form-control" id="cuotas_pendientes" value="<?php echo $cdata->plazo_restante?>"></td>
          </tr>
          <tr>
            <td><strong>Distribucion mensual</strong></td>
            <td id="distribucion_m"><?php echo  number_format((($monto_saldo*30/100)/$cdata->plazo_restante),2);?></td>
          </tr>
          <tr>
            <td><strong>Monto cuota</strong></td>
            <td id="monto_cuota_"><?php echo  number_format((($monto_saldo*30/100)/$cdata->plazo_restante)+$cdata->valor_cuota,2);?></td>
          </tr>
          <tr>
            <td><strong>Comentarios</strong></td>
            <td><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_abono_saldo" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<?php
$html=ob_get_clean();

echo json_encode(array(
						"html"=>$html,
						"capital_pagado"=>$monto_saldo,
						"mes_faltante"=>$cdata->plazo_restante,
						"distribucion_mensual"=>(($monto_saldo*30/100)/$cdata->plazo_restante),
						"monto_cuota_new"=>(($monto_saldo*30/100)/$cdata->plazo_restante)+$cdata->valor_cuota,
						"monto_cuota"=>$cdata->valor_cuota,
						"penalidad"=>30
						)
						
					);


?> 