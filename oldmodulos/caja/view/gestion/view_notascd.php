<?php 
if (!isset($protect)){
	exit;	
} 

if (!isset($_REQUEST['documento'])){
	exit;
}
$row=json_decode(System::getInstance()->Decrypt($_REQUEST['documento']));

if ($row==""){
	exit;
}  

?>
<div class="modal fade" id="moda_ncd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Notas de credito</h4>
      </div>
      <div class="modal-body">
        <table width="90%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle table">
          <tr>
            <td colspan="2"><table style="font-size:12px;border-spacing:1px;" class="table">
              <thead>
                <tr  style="background-color:#CCC;height:30px;" >
                  <td width="208" align="center"><strong>FECHA DE PAGO</strong></td>
                  <td width="276" align="center"><strong>CONTRATO</strong></td>
                  <td width="276" align="center"><strong>TIPO MOVIMIENTO</strong></td>
                  <td width="276" align="center"><strong>TIPO DE DOCUMENTO</strong></td>
                  <td width="276" align="center"><strong>NO. FACTURA</strong></td>
                  <td width="276" align="center"><strong>SERIE DOCUMENTO</strong></td>
                  <td width="276" align="center"><strong>NO DOCUMENTO</strong></td>
                  <td width="276" align="center"><strong>TIPO DE CAMBIO</strong></td>
                  <td width="276" align="center"><strong>MONTO</strong></td>
                  <td width="276" align="center"><strong>MONTO RD$</strong></td>
                  <td width="276" align="center"><strong>CAJA</strong></td>
                  </tr>
              </thead>
              <tbody>
                <tr>
                  <td align="center"><?php echo $row->FECHA;?></td>
                  <td align="center"><?php echo $row->SERIE_CONTRATO." ".$row->NO_CONTRATO;?></td>
                  <td align="center"><?php echo $row->TMOVIMIENTO?></td>
                  <td align="center"><?php echo $row->DOCUMENTO?></td>
                  <td align="center"><?php echo $row->SERIE_FACTURA?><?php echo $row->NO_DOC_FACTURA?></td>
                  <td align="center"><?php echo $row->SERIE?></td>
                  <td align="center"><?php echo $row->NO_DOCTO?></td>
                  <td align="center"><?php echo $row->TIPO_CAMBIO?></td>
                  <td align="center"><?php echo number_format($row->MONTO,2)?></td>
                  <td align="center"><?php echo number_format($row->MONTO*$row->TIPO_CAMBIO,2)?></td>
                  <td align="center"><?php echo $row->CAJA?></td>
                  </tr>
              </tbody>
            </table></td>
          </tr>
          <tr>
            <td width="150"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="100"><input type="text" name="moto_nota_cd" id="moto_nota_cd" /></td>
                  <td width="100">&nbsp;</td>
                  <td width="100"><button type="button" class="orangeButton" id="agregar_nota_monto">&nbsp;Agregar&nbsp;</button></td>
                  <td>&nbsp;</td>
                </tr>
            </table></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" id="detalle_trans_nc">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2"><textarea name="notas_cometario" id="notas_cometario" class="form-control" cols="45" rows="5"></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_nota_credito" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<?php
$ob=ob_get_contents();
ob_clean();
echo json_encode(array("html"=>$ob,"monto"=>round($row->MONTO,2)));

?>