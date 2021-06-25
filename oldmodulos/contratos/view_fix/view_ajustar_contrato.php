<?php 
if (!isset($protect)){
	exit;	
}
 
 if (!isset($_REQUEST['id'])){
	exit;
} 

SystemHtml::getInstance()->includeClass("contratos","Contratos"); 
 
$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
$con=new Contratos($protect->getDBLink());  
$cdata=$con->getInfoContrato($contrato->serie_contrato,$contrato->no_contrato);
if (count($cdata)<=0){
	exit;	
}	 
$capita_interes=$con->getCapitalInteresCuotaFromContrato($contrato->serie_contrato,$contrato->no_contrato);	


?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Ajustar contrato</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td style="width:200px;"><strong>MONTO A FINANCIAR</strong></td>
            <td><?php echo  number_format($capita_interes->capital_total,2)?></td>
          </tr>
          <tr>
            <td><strong>MONEDA</strong></td>
            <td><span style="margin:0px;"><?php echo  $cdata->tipo_moneda;?></span></td>
          </tr>
          <tr>
            <td><strong>CUOTAS</strong></td>
            <td><input name="_cuotas" type="text" class="form-control" id="_cuotas" value="<?php echo  $cdata->cuotas;?>"></td>
          </tr>
          <tr>
            <td><strong>% INTERES</strong></td>
            <td><span style="margin:0px;">
              <input name="_por_interes" type="text" class="form-control" id="_por_interes" value="<?php echo number_format($cdata->porc_interes,2);?>">
            </span></td>
          </tr>
          <tr>
            <td width="150"><strong>MONTO INTERES</strong></td>
            <td><span style="margin:0px;" id="_monto_interes"><?php echo number_format($cdata->interes,2);?></span></td>
          </tr>
          <tr>
            <td><strong>INICIAL</strong></td>
            <td><span style="margin:0px;"><?php echo number_format($capita_interes->INICIAL,2);?></span></td>
          </tr>
          <tr>
            <td><strong>COMPROMISO</strong></td>
            <td><span style="margin:0px;" id="_compromiso"><?php echo  number_format($cdata->valor_cuota,2);?></span></td>
          </tr>
          <tr>
            <td><strong>Comentarios</strong></td>
            <td><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="aplicar_Cambio_contrato" class="btn btn-primary">Aplicar</button>
      </div>
    </div>
  </div>
</div>
 
