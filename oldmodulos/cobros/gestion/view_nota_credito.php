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

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Nota de debito/credito</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0"  class="tb_detalle fsDivPage table-hover">
          <tr>
            <td><strong>Documento</strong></td>
            <td><select name="t_movimiento" id="t_movimiento" class="form-control"  style="width:250px;">
              <option value="">Seleccionar..</option>
              <?php  
	$SQL="SELECT * FROM `tipo_documento` WHERE `TIPO_DOC` IN ('".NOTA_DEBITO."','".NOTA_CREDITO."')";
	$rs=mysql_query($SQL);
	while($row=mysql_fetch_assoc($rs)){
		$id=System::getInstance()->Encrypt(json_encode($row));
?>
              <option value="<?php echo $id?>"><?php echo $row['DOCUMENTO']?></option>
              <?php } ?>
            </select></td>
          </tr>
          <tr>
            <td width="150"><strong>Monto</strong></td>
            <td><input type="text" name="monto_a_abonar" id="monto_a_abonar" class="form-control"></td>
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
 