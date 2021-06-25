<?php 
if (!isset($protect)){
	exit;	
}
SystemHtml::getInstance()->includeClass("client","PersonalData");

$rcb=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 
$person= new PersonalData($protect->getDBLink(),$_REQUEST);
$email=$person->getEmails($rcb->ID_NIT);
 

?>
<div class="modal fade" id="detalleDialogMail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:1000px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">ENVIO DE FACTURA POR CORREO</h4>
      </div>
      <div class="modal-body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>      
              <table width="100" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><strong>CORREO:</strong></td>
                  <td><select name="select" id="select" class="form-control" style="width:400px">
                    <?php
		foreach($email as $key=>$val){ 
			$id=System::getInstance()->Encrypt(json_encode($val));
?>
                    <option value="<?php echo $id;?>"><?php echo $val['direccion'];?></option>
                    <?php } ?>
                  </select></td>
                  <td>&nbsp;</td>
                  <td><button type="button" id="agregar_recibo_mail" id_nit="<?php echo  System::getInstance()->Encrypt(json_encode(array("id_nit"=>$rcb->ID_NIT)));?>"" class="btn btn-primary">Agregar</button></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td><strong>DETALLE CORREO</strong></td>
          </tr>
          <tr>
            <td></td>
          </tr>          
          <tr>
            <td><textarea id="input" style="width:900px; height:200px"></textarea></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
      
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>