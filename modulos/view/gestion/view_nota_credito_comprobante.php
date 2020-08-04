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
            <td width="150"><table style="font-size:12px;border-spacing:1px;width:350px;" class="table">
              <tbody>
                <tr>
                  <td width="100" align="left" style="background-color:#CCC;height:30px;width:150px" ><strong>FECHA DE PAGO</strong></td>
                  <td width="276" align="center"><?php echo $row->FECHA;?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>CONTRATO</strong></td>
                  <td align="center"><?php echo $row->SERIE_CONTRATO." ".$row->NO_CONTRATO;?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>TIPO MOVIMIENTO</strong></td>
                  <td align="center"><?php echo $row->TMOVIMIENTO?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>TIPO DE DOCUMENTO</strong></td>
                  <td align="center"><?php echo $row->DOCUMENTO?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>NO. FACTURA</strong></td>
                  <td align="center"><?php echo $row->SERIE_FACTURA?><?php echo $row->NO_DOC_FACTURA?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>SERIE DOCUMENTO</strong></td>
                  <td align="center"><?php echo $row->SERIE?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>NO DOCUMENTO</strong></td>
                  <td align="center"><?php echo $row->NO_DOCTO?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>TIPO DE CAMBIO</strong></td>
                  <td align="center"><?php echo $row->TIPO_CAMBIO?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>MONTO</strong></td>
                  <td align="center"><?php echo number_format($row->MONTO,2)?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>MONTO RD$</strong></td>
                  <td align="center"><?php echo number_format($row->MONTO*$row->TIPO_CAMBIO,2)?></td>
                </tr>
                <tr>
                  <td align="left" style="background-color:#CCC;height:30px;" ><strong>CAJA</strong></td>
                  <td align="center"><?php echo $row->CAJA?></td>
                </tr>
              </tbody>
            </table></td>
            <td valign="top"><textarea name="notas_cometario" id="notas_cometario" class="form-control" cols="45" rows="5"></textarea>      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="close_view" data-dismiss="modal">Cerrar</button>
        <button type="button" id="aplicar_nota_credito" class="btn btn-primary">Aplicar</button>
      </div></td>
          </tr>
          <tr>
            <td colspan="2" id="detalle_trans_nc">&nbsp;</td>
          </tr>
        </table>
      </div>

    </div>
  </div>
</div>
<?php
$ob=ob_get_contents();
ob_clean();
echo json_encode(array("html"=>$ob,"monto"=>round($row->MONTO,2)));

?>