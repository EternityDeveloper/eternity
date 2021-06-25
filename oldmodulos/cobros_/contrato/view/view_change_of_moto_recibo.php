<?php 
if (!isset($protect)){
	exit;	
}
if (!($protect->getIfAccessPageById(178))){
	echo "No tiene permiso para realizar esta operacion (178)";
	exit;
}
if (!isset($_REQUEST['recibo'])){
	echo "Debe de seleccionar un recibo!";
	exit;	
}
$rand=$_REQUEST['rand'];

$recibo=json_decode(System::getInstance()->Decrypt($_REQUEST['recibo']));
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
SystemHtml::getInstance()->includeClass("caja","Recibos"); 

 

$cobros= new Cobros($protect->getDBLINK()); 
$rcb= new Recibos($protect->getDBLINK()); 

$oficial=STCSession::GI()->isSubmit("oficial_view");
if (!is_array($oficial)){ 
	$oficial=$cobros->getOficial();
	STCSession::GI()->setSubmit("oficial_view",$oficial);
}
 
 
//$data=$rcb->getDetalleRecibo(array("action"=>"getRecibo","NO_DOCTO"=>$recibo->NO_DOCTO,"SERIE"=>$recibo->SERIE));
$drb=$rcb->getMFReciboCaja($recibo->SERIE,$recibo->NO_DOCTO); 
$motorizado="";
$oficial_nit="";
if ($drb['valid']){
	$motorizado=$drb['ID_NIT_MOTORIZADO'];
	$oficial_nit=$drb['ID_NIT_OFICIAL'];
}
  
?>
<div class="modal fade" id="myModal_<?php echo $rand?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cambio de Oficial/Motorizado</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td><strong> OFICIAL</strong>:</td>
              <td><select name="oficial_n" id="oficial_n" class="form-control">
                <option value="">Seleccione</option>
                <?php foreach($oficial['oficial'] as $key =>$row){  
				?>
                <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $oficial_nit==$key?' selected ':''?>><?php echo $row?></option>
                <?php } ?>
              </select></td>
            </tr>
            <tr>
              <td width="120"><strong> MOTORIZADO</strong>:</td>
              <td><select name="motorizado_n" id="motorizado_n" class="form-control">
                <option value="">Seleccione</option>
                <?php foreach($oficial['motorizado'] as $key =>$row){?>
                <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $motorizado==$key?' selected ':''?>><?php echo $row?></option>
                <?php } ?>
              </select></td>
            </tr>
            <tr>
              <td colspan="2"><strong>DESCRIPCION:</strong></td>
            </tr>
            <tr>
              <td colspan="2"><textarea name="comentario" id="comentario" class="form-control" cols="45" rows="5"></textarea></td>
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