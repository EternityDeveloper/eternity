<?php 
if (!isset($protect)){
	exit;	
}
if (!isset($_REQUEST['id'])){
	exit;	
}
$ct=json_decode(System::getInstance()->Decrypt($_REQUEST['id']));
 

  
?>
<div class="modal fade" id="view_modal_edit_document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DETALLE</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td width="110"><strong>CONTRATO:</strong></td>
                      <td><?php echo $ct->serie_contrato." ".$ct->no_contrato;?></td>
                    </tr>
                    <tr class="table">
                      <td align="left"><strong>MOTIVO:</strong></td>
                      <td><select name="motivo" id="motivo" class="form-control" style="width:250px;">
                        <option value="IMPRESION_CONTRATO">IMPRESION CONTRATO</option> 
                      </select></td>
                    </tr>
                    <tr class="table">
                      <td align="left"><strong>DOCUMENTO:</strong></td>
                      <td><select name="tipo_documento" id="tipo_documento" class="form-control" style="width:400px;">
                       <option value="">Seleccione..</option>
<?php
			$SQL="SELECT * FROM `pape_formato_documentos`";
			$rs=mysql_query($SQL);
			while($row=mysql_fetch_assoc($rs)){ ?> 
                        <option value="<?php echo System::getInstance()->Encrypt($row['ID']);?>"><?php echo $row['NOMBRE_DOC'];?></option>
<?php } ?>
                      </select></td>
                    </tr>
                    <tr>
                      <td colspan="2"><strong>COMENTARIO:</strong></td>
                    </tr>
                    <tr>
                      <td colspan="2"><textarea name="comentario" id="comentario" class="form-control" cols="45" rows="5"></textarea></td>
                    </tr>
                    <tr class="table">
                      <td align="left">&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td align="center"><button type="button" id="pap_print_doct" class="btn btn-primary">Imprimir</button></td>
                </tr>
              </table></td>
            </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
           
          </table>
      </div>
   
    </div>
  </div>
</div>