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
                  <td align="left"><strong>CONTRATO:</strong></td>
                  <td><?php echo $rc_detalle[0]['SERIE_CONTRATO']." ".$rc_detalle[0]['NO_CONTRATO'];?></td>
                </tr>
                <tr>
                  <td width="120" align="left"><strong>NO RECIBO:</strong></td>
                  <td width="324"><?php echo $recibo->SERIE."-".$recibo->NO_DOCTO;?><?php echo strtoupper($row['TMOVIMIENTO']);?></td>
                </tr>
                <tr>
                  <td align="left"><strong>FORMA PAGO:</strong></td>
                  <td><?php echo $recibo->descripcion_pago?></td>
                </tr>
                <tr>
                  <td align="left"><strong>BANCO:</strong></td>
                  <td><?php echo $recibo->ban_descripcion?></td>
                </tr>
                <tr>
                  <td align="left"><strong>MONTO:</strong></td>
                  <td><?php echo number_format($rc_detalle[0]['MONTO'],2)?>&nbsp;</td>
                </tr>
                <tr>
                  <td align="left"><strong>AUTORIZACION:</strong></td>
                  <td><?php echo $recibo->AUTORIZACION;?>&nbsp;</td>
                </tr>
                <tr>
                  <td align="left" valign="top"><strong>BANCO DEPOSITO:</strong></td>
                  <td valign="top"><select name="banco_afecta" id="banco_afecta" class="form-control required" style="width:200px;" >
                    <option value="">Seleccione</option>
                    <?php 

$SQL="SELECT * FROM `bancos`";
$rs=mysql_query($SQL);
while($row=mysql_fetch_assoc($rs)){
	$encriptID=System::getInstance()->Encrypt(json_encode($row));
?>
                    <option value="<?php echo $encriptID?>" ><?php echo $row['ban_descripcion']?></option>
                    <?php } ?>
                  </select></td>
                </tr>
                <tr>
                  <td align="left" valign="top"><strong>FECHA REGISTRO:</strong></td>
                  <td valign="top"><input type="text" name="fecha_cheque" id="fecha_cheque" class="form-control" style="width:200px;" /></td>
                </tr>
                <tr>
                  <td align="left" valign="top"><strong>COMENTARIO:</strong></td>
                  <td valign="top"><textarea name="txt_comentario_cd" id="txt_comentario_cd" class="" cols="45" rows="5"></textarea></td>
                </tr>
                <tr>
                  <td colspan="2" align="center"><button type="button" class="orangeButton" id="doRegistrarCD">Registar</button></td>
                </tr>
            </table></td>
          </tr>
           
          </table>
      </div>
 
    </div>
  </div>
</div>