<?php 
if (!isset($protect)){
	exit;	
}
 
 
SystemHtml::getInstance()->includeClass("papeleria","Recibos"); 
$pap= new Recibos($protect->getDBLINK());   
?>
<div class="modal fade" id="view_modal_lote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">CREAR LOTE</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="444" border="0" cellspacing="0" cellpadding="0" class="table" >
                <tr>
                  <td align="left"><strong>DOCUMENTO:</strong></td>
                  <td width="324"> 
                    <select name="pap_documento" id="pap_documento" class="form-control" style="width:300px;">
                      <option value="0" selected="selected">Seleccionar</option>
                    <?php 
					
						$listado_doc=$pap->getDocumento();
						foreach($listado_doc as $key =>$row){
							$id=System::getInstance()->Encrypt(json_encode($row));
					?>
                      <option value="<?php echo $id;?>"><?php echo $row['pap_descripcion']?></option>
					<?php }?>
                  </select></td>
                </tr>
                <tr>
                  <td align="left"><strong>TIPO</strong></td>
                  <td><select name="tipo_serv_prod" id="tipo_serv_prod" class="form-control" style="width:250px;">
                    <option value="0" selected="selected">Seleccionar</option>
                    <?php 
					
						$listado_doc=$pap->getTipoProductoServicio();
						foreach($listado_doc as $key =>$row){
							$id=System::getInstance()->Encrypt(json_encode($row));
					?>
                    <option value="<?php echo $id;?>"><?php echo $row['pap_descripcion']?></option>
                    <?php }?>
                  </select></td>
                </tr>
                <tr>
                  <td align="left"><strong>DESDE:</strong></td>
                  <td><input  name="pap_desde" type="text" class="form-control"  id="pap_desde" style="width:200px;" maxlength="10"  /></td>
                </tr>
                <tr>
                  <td width="120" align="left"><strong>HASTA:</strong></td>
                  <td><input  name="pap_hasta" type="text" class="form-control"  id="pap_hasta" style="width:200px;" maxlength="10" /></td>
                </tr>
            </table></td>
            </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="procesar_acta" class="btn btn-primary">Crear</button>
      </div>
    </div>
  </div>
</div>