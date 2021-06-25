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
        <h4 class="modal-title" id="myModalLabel">Gestión Abono a capital</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td><strong>Monto saldado    </strong></td>
            <td>
            <input name="monto_saldo" type="text" disabled="disabled" class="form-control" id="monto_saldo" value="<?php echo number_format($monto_saldo,2);?>"></td>
          </tr>
          <tr>
            <td><strong>Monto a Abonar  </strong></td>
            <td><input type="text" name="monto_a_abonar" id="monto_a_abonar" class="form-control"></td>
          </tr>
          <tr>
            <td><strong>Nuevo Saldo        </strong></td>
            <td><input name="nuevo_saldo" type="text" disabled class="form-control" id="nuevo_saldo"></td>
          </tr>
          <tr>
            <td width="150"><strong>Cambiar   Plazo</strong></td>
            <td align="left"> 
             
                <input type="radio" class="plazo_l" name="tipo_plazo" value="D" id="tipo_plazo">
                Disminuir  
                <input type="radio" class="plazo_l" name="tipo_plazo" value="A" id="tipo_plazo">
                Incrementar  
            </td>
          </tr>
          <tr>
            <td><strong>Nuevo Plazo    </strong></td>
            <td><input name="nuevo_plazo" type="text" disabled="disabled" class="form-control" id="nuevo_plazo"></td>
          </tr>
          <tr>
            <td><strong>Nueva Cuota $ </strong></td>
            <td><input name="nueva_cuota" type="text" disabled="disabled" class="form-control" id="nueva_cuota"></td>
          </tr>
          <tr>
            <td><strong>Comentarios</strong></td>
            <td><textarea name="cp_comentarios" class="form-control" id="cp_comentarios"></textarea></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_abono" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
 