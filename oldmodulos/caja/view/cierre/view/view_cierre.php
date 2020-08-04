<?php 
if (!isset($protect)){
	exit;	
}

if (!isset($_REQUEST['id'])){
	exit;
}


$data=json_decode(System::getinstance()->Decrypt($_REQUEST['id']));

//print_r($data);

?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">TRANSFERENCIAS</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="600" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped"  >
                <tr>
                  <td align="left"><strong>FORMA DE PAGO:</strong></td>
                  <td><?php echo $data->data->forma_pago;?></td>
                </tr>
                <tr>
                  <td align="left"><strong>DOCUMENTOS:</strong></td>
                  <td><?php echo $data->data->TOTALES;?></td>
                </tr>
                <tr>
                  <td width="120" align="left"><strong>MONTO INGRESO:</strong></td>
                  <td width="324"><?php echo number_format($data->data->MONTO,2);?></td>
                </tr>
                <tr>
                  <td align="left"><strong>TIPO DOCUMENTOS:</strong></td>
                  <td><?php echo $data->filter->fc;?></td>
                </tr>
                <tr>
                  <td align="left"><strong>BANCO DESTINO:</strong></td>
                  <td><select name="_banco_destino" id="_banco_destino" class="form-control">
                    <option value="" >Seleccione..</option>
                    <?php 
					
				$rs=Siadcom::GI()->query("SELECT *  FROM FNCCONCI00");	
				
				//print_r($rs);
				foreach($rs as $key=>$row){
				?>
                    <option value="<?php echo System::getInstance()->Encrypt($row->NUM_CTA);?>"  ><?php echo $row->N1_BANCO?></option>
                    <?php } ?>
                  </select></td>
                </tr>
                <tr>
                  <td align="left"><strong>MONTO:</strong></td>
                  <td><input type="text" name="monto_envio" id="monto_envio" value="<?php echo $rc_detalle[0]['TIPO_CAMBIO']?>" class="form-control" /></td>
                </tr>
                <tr>
                  <td align="left"><strong>DESCRIPCION:</strong></td>
                  <td> 
                  <textarea name="descripcion_envio" id="descripcion_envio" class="form-control" cols="45" rows="2"></textarea></td>
                </tr>
                <tr>
                  <td colspan="2" align="center"><button type="button" class="orangeButton" id="doAgregarCierre">Agregar</button></td>
                </tr>
                <tr>
                  <td align="left">&nbsp;</td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td align="left"><strong> </strong></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2" align="center">&nbsp;</td>
                </tr>
            </table></td>
          </tr>
           
          </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button> 
      </div>
    </div>
  </div>
</div>