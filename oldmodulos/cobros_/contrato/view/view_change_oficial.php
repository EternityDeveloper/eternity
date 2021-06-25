<?php 
if (!isset($protect)){
	exit;	
}
if (!($protect->getIfAccessPageById(178))){
	echo "No tiene permiso para realizar esta operacion (178)";
	exit;
}


$contrato=json_decode(System::getInstance()->Decrypt($_REQUEST['contrato']));
$meta=json_decode(System::getInstance()->Decrypt($_REQUEST['meta']));
SystemHtml::getInstance()->includeClass("cobros","Cobros"); 
$cobros= new Cobros($protect->getDBLINK()); 
$oficial=$cobros->getOficial();
  
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Cambio de Oficial</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            
            <tr>
              <td width="120"><strong>OFICIAL ACTUAL</strong></td>
              <td><?php  echo $meta->nombre_oficial;?></td>
            </tr>
            <tr>
              <td><strong>MOTORIZADO ACTUAL</strong></td>
              <td><?php  echo $meta->nombre_motorizado;?></td>
            </tr>
            <tr>
              <td><strong>NUEVO MOTORIZADO</strong>:</td>
              <td><select name="motorizado_n" id="motorizado_n" class="form-control">
                <option value="">Seleccione</option>
                <?php foreach($oficial['motorizado'] as $key =>$row){?>
                <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $key==$meta->nitmotorizado?' selected ':''?>><?php echo $row?></option>
                <?php } ?>
              </select></td>
            </tr>
            <tr>
              <td><strong>NUEVO OFICIAL</strong>:</td>
              <td><select name="oficial_n" id="oficial_n" class="form-control">
                <option value="">Seleccione</option>
                <?php foreach($oficial['oficial'] as $key =>$row){?>
               	 <option value="<?php echo System::getInstance()->Encrypt($key);?>" <?php echo $key==$meta->nitoficial ?' selected ':''?>><?php echo $row?></option>
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