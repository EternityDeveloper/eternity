<?php 
if (!isset($protect)){
	exit;	
}

if (!isset($_REQUEST['id'])){
	exit;
}
$recibo=json_decode(System::getinstance()->Decrypt($_REQUEST['id']));
 
 SystemHtml::getInstance()->includeClass("caja","Recibos");  
$recibos= new Recibos($protect->getDBLINK());   
$filter=array("action"=>'getRecibo',"SERIE"=>$recibo->SERIE,"NO_DOCTO"=>$recibo->NO_DOCTO);
$rc_detalle=$recibos->getListadoRecibo($filter); 
?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">DETALLE RECIBO</h4>
      </div>
      <div class="modal-body">
     
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="120"><table width="600" border="0" cellspacing="0" cellpadding="0" class="table table-bordered table-striped"  >
                <tr>
                  <td width="120" align="left"><strong>NO RECIBO:</strong></td>
                  <td width="324"><?php echo $recibo->SERIE."-".$recibo->NO_DOCTO;?><?php echo strtoupper($row['TMOVIMIENTO']);?></td>
                </tr>
                <tr>
                  <td align="left"><strong>MONTO:</strong></td>
                  <td><?php echo number_format($rc_detalle[0]['MONTO'],2)?>&nbsp;</td>
                </tr>
                <tr>
                  <td align="left"><strong>TASA:</strong></td>
                  <td>
                  <input type="text" name="tasa_cambio" id="tasa_cambio" value="<?php echo $rc_detalle[0]['TIPO_CAMBIO']?>" class="form-control"></td>
                </tr>
                <tr>
                  <td colspan="2" align="center"><button type="button" class="orangeButton" id="doSaveEditRecibo">Guardar</button></td>
                </tr>
                <tr>
                  <td colspan="2" align="center">&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="2" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="cursor:pointer" class="table table-bordered table-striped table-hover" >
                    <tr>
                      <td width="150">TIPO</td>
                      <td width="100">MONTO</td>
                      <td width="100">TASA</td>
                      <td width="100">REFERENCIA</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <?php 
			$forma_pago=$recibos->getReciboFormaPago($recibo->SERIE,$recibo->NO_DOCTO);		
			foreach($forma_pago as $key=>$fp_row){ 
				$id=System::getInstance()->Encrypt(json_encode(array("SERIE"=>$recibo->SERIE,"NO_DOCTO"=>$recibo->NO_DOCTO,"FORMA_PAGO"=>$fp_row['FORMA_PAGO'])));
			?>
                    <tr id="<?php echo $id;?>" class="fpago_evnt">
                      <td>
                      <select name="<?php echo $fp_row['forpago']?>_forma_pago" id="<?php echo $fp_row['forpago']?>_forma_pago">
                          <option value="" >Seleccione..</option>
                          <?php 
				$SQL="SELECT * FROM `formas_pago`";
				$rs=mysql_query($SQL);
				while($row=mysql_fetch_assoc($rs)){
				?>
                          <option value="<?php echo $row['forpago']?>" <?php echo $row['forpago']==$fp_row['forpago']?'selected="selected"':'';?> ><?php echo $row['descripcion_pago']?></option>
                          <?php } ?>
                        </select></td>
                      <td><?php echo number_format($fp_row['MONTO']*$fp_row['TIPO_CAMBIO'],2);?></td>
                      <td><input type="text" name="<?php echo $fp_row['forpago'];?>_tasa" id="<?php echo $fp_row['forpago'];?>_tasa" value="<?php echo $fp_row['TIPO_CAMBIO']?>" class="form-control"></td>
                      <td>
                        <input type="text" name="ref" id="ref" value="<?php echo $fp_row['AUTORIZACION'];?>" class="form-control btsave_edit_recibo"></td>
                      <td><button type="button" class="orangeButton btsavedetalle_fpago" id="<?php echo $fp_row['forpago'];?>" value="<?php echo $id;?>">Guardar</button></td>
                    </tr>
                    <?php } ?>
                  </table></td>
                </tr>
                <tr>
                  <td align="left">&nbsp;</td>
                  <td>&nbsp;</td>
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